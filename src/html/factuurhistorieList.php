<?php

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "factuurhistorieEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

if(intval($_POST['verwerk'])==1||intval($_POST['verwerk'])==2)
{
  $ids = array();
  foreach ($_POST as $key => $value)
  {
    if (substr($key, 0, 6) == 'check_')
    {
      $ids[] = substr($key, 6);
    }
  }
  if(count($ids)>0)
  {
    $db=new DB();
    $query = "SELECT FactuurHistorie.id,FactuurHistorie.Portefeuille,FactuurHistorie.factuurDatum,FactuurHistorie.status FROM (FactuurHistorie) Join Portefeuilles ON FactuurHistorie.portefeuille = Portefeuilles.Portefeuille  WHERE FactuurHistorie.id IN('" . implode("','", $ids) . "')  ORDER BY Portefeuilles.Client ";
    $db->SQL($query);
    $db->query();
    $portefeuilles=array();
    while($fac=$db->nextRecord())
    {
      $portefeuilles[]=$fac;
    }
    foreach($portefeuilles as $index=>$Pdata)
    {
      if($_POST['verwerk']==1)
      {
        if ($Pdata['status'] == 0)
        {
          $query = "SELECT if((SELECT Depotbank FROM Portefeuilles WHERE Portefeuille='" . $Pdata['Portefeuille'] . "')='AAB', (SELECT FactuurHistorie.factuurNr+1 as factuurNr FROM FactuurHistorie Inner Join Portefeuilles ON FactuurHistorie.portefeuille = Portefeuilles.Portefeuille WHERE  Portefeuilles.Depotbank ='AAB' AND YEAR(FactuurHistorie.factuurDatum) = YEAR('" . $Pdata['factuurDatum'] . "') ORDER BY FactuurHistorie.factuurNr desc limit 1),
  if((SELECT Depotbank FROM Portefeuilles WHERE Portefeuille='" . $Pdata['Portefeuille'] . "')='AABB',(SELECT FactuurHistorie.factuurNr+1 as factuurNr FROM FactuurHistorie Inner Join Portefeuilles ON FactuurHistorie.portefeuille = Portefeuilles.Portefeuille WHERE  Portefeuilles.Depotbank ='AABB' AND YEAR(FactuurHistorie.factuurDatum) = YEAR('" . $Pdata['factuurDatum'] . "') ORDER BY FactuurHistorie.factuurNr desc limit 1),
  ( SELECT  FactuurHistorie.factuurNr+1 as factuurNr FROM FactuurHistorie Inner Join Portefeuilles ON FactuurHistorie.portefeuille = Portefeuilles.Portefeuille WHERE Portefeuilles.Depotbank NOT IN('AAB','AABB') AND YEAR(FactuurHistorie.factuurDatum) = YEAR('" . $Pdata['factuurDatum'] . "')
  ORDER BY FactuurHistorie.factuurNr desc limit 1 ) ) ) as factuurNr";
    
          $query = "SELECT factuurNr FROM ( (SELECT FactuurHistorie.factuurNr + 1 AS factuurNr	FROM FactuurHistorie WHERE YEAR(FactuurHistorie.factuurDatum)=YEAR ('" . $Pdata['factuurDatum'] . "')	ORDER BY FactuurHistorie.factuurNr DESC	LIMIT 1)
      UNION (SELECT 1 as factuurNr) ) a
ORDER BY factuurNr desc limit 1";
          $db->SQL($query);
          $db->query();
          $facNr = $db->nextRecord();
          $query = "UPDATE FactuurHistorie SET factuurNr=" . intval($facNr['factuurNr']) . ", status=1, change_user='$USR',change_date=now() WHERE id=" . intval($Pdata['id']);
          $db->SQL($query);
          $db->query();
        }
      }
      elseif($_POST['verwerk']==2)
      {
        if ($Pdata['status'] == 1)
        {
          $query = "UPDATE FactuurHistorie SET betaald=1, change_user='$USR',change_date=now() WHERE id=" . intval($Pdata['id']);
          $db->SQL($query);
          $db->query();
        }
      }
    }

  }
  

}

$editcontent['jsincludes'] .= '<script type="text/javascript" src="javascript/jquery/jquery-3.4.1.min.js"></script>';
$editcontent['jsincludes'] .= '<script type="text/javascript" src="javascript/jquery/jquery-ui.min.js"></script>';
$editcontent['style'] .= '<link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">';
$editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/bootstrap-colorpicker.min.js\"></script>";
$editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/bootstrap-4.3.1/bootstrap.min.js\"></script>";

$content['style'] = $editcontent['style'];

if($_GET['rel_id'])
{
  $db=new DB();
  $query="SELECT portefeuille FROM CRM_naw WHERE id='".$_GET['rel_id']."'";
  $db->SQL($query);
  $portefeuille=$db->lookupRecord();
  $portefeuille=$portefeuille['portefeuille'];
}

$list->addColumn("FactuurHistorie","id",array("list_width"=>"100","form_visible"=>false,'list_invisible'=>true));
$list->addColumn("","pdf",array("list_width"=>"30",'description'=>" ",'list_nobreak'=>true));
$list->addColumn("","sel",array("list_width"=>"30",'description'=>" ",'list_nobreak'=>true));
$list->addFixedField("FactuurHistorie","portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("FactuurHistorie","factuurNr",array("list_width"=>"100","search"=>false));
$list->addFixedField("FactuurHistorie","periodeDatum",array("list_width"=>"100","search"=>false));
$list->addFixedField("FactuurHistorie","grondslag",array("list_width"=>"100","search"=>false));
$list->addFixedField("FactuurHistorie","fee",array("list_width"=>"100","search"=>false));
$list->addFixedField("FactuurHistorie","btw",array("list_width"=>"100","search"=>false));
$list->addFixedField("FactuurHistorie","totaalIncl",array("list_width"=>"100","search"=>false));
$list->addFixedField("FactuurHistorie","add_date",array("list_width"=>"130","search"=>false));
$list->addFixedField("FactuurHistorie","status",array("list_width"=>"130","search"=>false));

$list->setJoin("JOIN Portefeuilles on Portefeuilles.Portefeuille = FactuurHistorie.portefeuille AND Portefeuilles.consolidatie=0 
LEFT JOIN CRM_naw on FactuurHistorie.Portefeuille = CRM_naw.portefeuille ");
$list->ownTables=array('FactuurHistorie');
$list->categorieVolgorde['FactuurHistorie']=array('Algemeen');
$list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels');
$list->categorieVolgorde['Naw']=array("Algemeen","Adres","Verzendadres","Telefoon","Internetgegevens","Bedrijfinfo","Persoonsinfo","Legitimatie","Informatie partner","Legitimatie partner","Adviseurs","geen",'Contract','Beleggen','Rapportage','Profiel','Relatie geschenk','Recordinfo');
$html = $list->getCustomFields(array('FactuurHistorie','Portefeuilles','Naw'),"fac_hist");

if($portefeuille <> '')
  $list->setWhere("FactuurHistorie.portefeuille = '$portefeuille'");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);



$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");

$_SESSION['submenu']->addItem("Print concept facturen","FactuurHistoriePdf.php?concept=1",array('target'=>'_blank'));
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem("Print ongeprinte facturen","FactuurHistoriePdf.php",array('target'=>'_blank'));

$db=new DB();
$query="SELECT Depotbank FROM Depotbanken";
$db->SQL($query);
$db->Query();
$depotbanken=array('alle');
while($data=$db->nextRecord())
  $depotbanken[]=$data['Depotbank'];


$html='
<style type="text/css">
.button {
	display: block;
	width: 120px;
	color: Black;
	font: 11px \'Arial\';
	text-decoration: NONE;
	background-color: #FFFFF0;
	border: 1px solid;
	border-color: #DCDCDC #DCDCDC #AAAAAA #AAAAAA;
	text-align: center;
}
</style>

<form action="FactuurHistoriePdf.php" method="GET" target="_blank">
<input type="hidden" name="concept" value="1">
<input type="hidden" name="rapportage" value="1">

Depotbank:
<select name="depotbank">';
foreach ($depotbanken as $depotBank)
  $html .='<option value="'.$depotBank.'">'.$depotBank.'</option>';
$html .='
</select>

Factuur maand:
<select name="maand">';
for($i=0;$i<13;$i++)
  $html .='<option value="'.$i.'">'.$i.'</option>';
$html .='
</select>
<input class="button" type="submit" value="Print factuur rapportage">
</form>';
$_SESSION['submenu']->addItem($html,'');

?>


<?


$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}

function checkAllP(optie)
{
  var theForm = document.factuurForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'check_')
   {
      if(optie == -1)
      {
        if(theForm[z].checked == true)
          theForm[z].checked=false;
        else
          theForm[z].checked=true;
      }
      else
      {
        theForm[z].checked = optie;
      }
   }
  }
}

";
echo template($__appvar["templateContentHeader"],$content);
?>


<?=$list->filterHeader();?>
  
  
  <div class="box box12" >
    <div class="btn-group" role="group" style="height:22px;">
      <div class="btn-new btn-default" style="width:100px;float:left;" onclick="checkAllP(1);">&nbsp;&nbsp;<img src="icon/16/checks.png" class="simbisIcon" /> Alles </div>
      <div class="btn-new btn-default" style="width:100px;float:left;" onclick="checkAllP(0);">&nbsp;&nbsp;<img src="icon/16/undo.png" class="simbisIcon" /> Niets </div>
      <div class="btn-new btn-default" style="width:100px;float:left;" onclick="checkAllP(-1);">&nbsp;&nbsp;<img src="icon/16/replace2.png" class="simbisIcon" /> Omkeren</div>
      <div class="btn-new btn-default" style="width:280px;float:left;" onclick="javascript: document.factuurForm.verwerk.value=1;document.factuurForm.submit();"><img src="icon/16/refresh.png" class="simbisIcon" /> Factuurselectie definitief maken</div>
      <div class="btn-new btn-default" style="width:280px;float:left;" onclick="javascript: document.factuurForm.verwerk.value=2;document.factuurForm.submit();"><img src="icon/16/refresh.png" class="simbisIcon" /> Factuur betaald zetten</div>
    </div>
  </div>
<br><br>
<form name="factuurForm" id="factuurForm" method="POST">
  <input type="hidden" name="verwerk" value="1">
<table class="list_tabel" cellspacing="0">


<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
//	listarray($data);
	$img='<img alt="pdf" src="images/pdf_20x20.gif" width="16" height="16" border="0">';
	$data['.pdf']['value']="<a href=\"FactuurHistoriePdf.php?id=".$data['id']['value']."\" target=\"_blank\"> $img </a>";
	//if($data['FactuurHistorie.status']['value']==0)
    $data['.sel']['value']="<input  type=\"checkbox\" name=\"check_".$data['id']['value']."\" value=\"1\">";
	//else
  //  $data['.sel']['value']='';

	echo $list->buildRow($data);
}
?>
</table>
</form>
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>