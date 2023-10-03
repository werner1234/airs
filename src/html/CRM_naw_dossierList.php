<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 16 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/05/05 19:15:14 $
    File Versie         : $Revision: 1.19 $

    $Log: CRM_naw_dossierList.php,v $
    Revision 1.19  2018/05/05 19:15:14  rvv
    *** empty log message ***

    Revision 1.18  2018/05/02 16:06:03  rvv
    *** empty log message ***

    Revision 1.17  2013/08/07 17:17:44  rvv
    *** empty log message ***

    Revision 1.16  2013/07/20 16:24:34  rvv
    *** empty log message ***

    Revision 1.15  2012/06/30 14:35:35  rvv
    *** empty log message ***

    Revision 1.14  2012/05/12 15:10:22  rvv
    *** empty log message ***

    Revision 1.13  2011/08/31 14:37:39  rvv
    *** empty log message ***

    Revision 1.12  2011/03/13 18:35:40  rvv
    *** empty log message ***

    Revision 1.11  2010/10/24 10:28:48  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader    = "";
$mainHeader   = "gespreksverslagen overzicht";

$editScript = "CRM_naw_dossierEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("Naw_dossier","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","print",array("list_width"=>"50",'description'=>"print ",'list_nobreak'=>true));
$list->addColumn("Naw_dossier","datum",array("list_width"=>"100","search"=>false,"list_align"=>"left","form_type"=>"calendar"));
$list->addColumn("Naw_dossier","kop",array("list_width"=>"","search"=>true));
$list->addColumn("Naw_dossier","type",array("list_width"=>"","search"=>true));
$list->addColumn("Naw_dossier","add_user",array("description"=>"toegevoegd door","list_width"=>"","search"=>false));
$list->addColumn("Naw_dossier","memo",array("list_width"=>"250","description"=>"memo","search"=>false));
$list->addColumn("Naw_dossier","clientGesproken",array("list_width"=>"250","search"=>false));

$deb_id = $_GET['deb_id'];
$dosiertypen=array('Alles'=>'Alles');
if ($deb_id > 0)
{
  $db = new db();
  $q = "SELECT naam FROM CRM_naw WHERE id = $deb_id";
  $db->SQL($q);
  $nawRec = $db->lookupRecord();
  $subHeader = " bij <b>".$nawRec['naam']."</b>";


  $query="SELECT `type` FROM CRM_naw_dossier WHERE  rel_id = '".$deb_id."' GROUP BY `type` ORDER BY `type`";
  $db->SQL($query);
  $db->query();

  while($dbdata=$db->nextRecord())
  {
    if($dbdata['type']=='')
      $dbdata['type']='Leeg';
    $dosiertypen[$dbdata['type']]=$dbdata['type'];
  }


  $typeFilter='';
  if($_GET['typeFilter']=='Alles')
    $filterWhere='';
  elseif(in_array($_GET['typeFilter'],$dosiertypen) || $_GET['typeFilter']=='Leeg')
  {
    if($_GET['typeFilter']=='Leeg')
      $filter='';
    else
      $filter=mysql_real_escape_string($_GET['typeFilter']);
    $filterWhere = "AND type='" . $filter . "'";
    $typeFilter=$_GET['typeFilter'];
  }
  else
    $filterWhere='';


  $list->setWhere("rel_id = '".$deb_id."' $filterWhere");


  $_SESSION['submenu'] = New Submenu();
 // $_SESSION['submenu']->addItem("Terug naar NAW ","CRM_nawEdit.php?action=edit&id=$deb_id&useSavedUrl=1");
 // $_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem("Print verslag","CRM_naw_dossierPrint.php?&relid=$deb_id&typeFilter=".$typeFilter,array('target'=>'_blank'));
  $_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem("Kopieer verslagen","CRM_naw_dossierCopy.php?&relid=$deb_id");

}
// default sortering
if(!isset($_GET['sort']))
{
  $_GET['sort'] = array("CRM_naw_dossier.datum","CRM_naw_dossier.id");
  $_GET['direction'] = array("DESC","DESC");
}
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

if($_GET['action'] == 'xls')
{
  $list->setXLS();
  $list->getXLS();
}
else
{
  if(!is_a($_SESSION['submenu'],'Submenu'))
    $_SESSION['submenu']=new Submenu();
  $_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem("XLS-lijst","$PHP_SELF?action=xls&".$_SERVER['QUERY_STRING']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new&rel_id=$deb_id';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<form>
<input type="hidden" id="deb_id" name="deb_id" value="<?=$deb_id?>">

  Filter op type:
<select name="typeFilter" id="typeFilter" onchange="javascript:submit();">

<?php
 foreach($dosiertypen as $categorie)
 {
   if($_GET['typeFilter']==$categorie)
     $selected='selected';
   else
     $selected='';
   if($categorie=='Leeg')
     $categorieOmschrijving=htmlspecialchars("<leeg>");
   else
     $categorieOmschrijving=$categorie;
   echo "<option $selected value=\"$categorie\">$categorieOmschrijving</option>";
 }
?>


</select>
</form>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{

	// $list->buildRow($data,$template="",$options="");
	$data['print']['value'] = "<a href=\"CRM_naw_dossierPrint.php?relid=$deb_id&id=".$data['id']['value']."\" target=\"_blank\" >".maakKnop('pdf.png',array('size'=>16))."</a>";
	$data['print']['value'] .=" <a href=\"CRM_mailer.php?relid=$deb_id&dosierId=".$data['id']['value']."\"  >".maakKnop('mail_add.png',array('size'=>16))."</a>";

	echo $list->buildRow($data);
}
?>
</table>
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooterZonderMenu"],$content);
}
?>