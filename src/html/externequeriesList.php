<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 2 augustus 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/10/18 15:28:00 $
    File Versie         : $Revision: 1.13 $
 		
    $Log: externequeriesList.php,v $
    Revision 1.13  2017/10/18 15:28:00  rvv
    *** empty log message ***

  
 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("externequerierun.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "externequeriesEdit.php";
$__appvar['rowsPerPage']=100;


if($USR=='JBR' || $USR=='FEGT' || $USR=='AIRS'|| $USR=='MHO')
  $allow_add=true;
else
  $allow_add=false;  

$list = new MysqlList2();
$list->idField = "id";
$list->idTable='externeQueries';
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("ExterneQueries","id",array("list_width"=>"100","search"=>false));
//$list->addColumn("","run",array('description'=>' ',"list_width"=>"25","search"=>false));
$list->addFixedField("ExterneQueries","titel",array("list_width"=>"200","search"=>false));
$list->addFixedField("ExterneQueryCategorien","omschrijving",array("list_width"=>"200","search"=>false));
//$list->addColumn("ExterneQueries","omschrijving",array("list_width"=>"100","search"=>false));
if($__appvar['bedrijf']=='HOME')
  $list->addFixedField("ExterneQueries","homeOnly",array("list_width"=>"100","search"=>false));
$list->addFixedField("ExterneQueries","change_date",array("list_width"=>"100","search"=>false));
$list->addFixedField("ExterneQueries","change_user",array("list_width"=>"100","search"=>false));


$html = $list->getCustomFields(array('ExterneQueries','ExterneQueryCategorien'),'ExterneQuerieList');

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

$list->ownTables=array('externeQueries');
$list->setJoin("LEFT JOIN externeQueryCategorien ON externeQueries.categorie=externeQueryCategorien.categorie ");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort 

if(!$_GET['sort'])
{
  $_GET['sort'][]='volgorde';
  $_GET['direction'][]='ASC';
}

$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
//$list->setFilter();

$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";

$content["style2"] = "
<style>
.merkBtns{
  width:200px;
  margin-left:15px;
  float:left;
}
</style>";

$content['pageHeader'] .= "
<div id='wrapper' style='overflow:hidden;'> 
<div class='buttonDiv merkBtns'  onclick='checkAll(1);'>&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> ".vt("Alles selecteren")."</div>
<div class='buttonDiv merkBtns'  onclick='checkAll(0);'>&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> ".vt("Niets selecteren")."</div>
<div class='buttonDiv merkBtns'  onclick='checkAll(-1);'>&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /> ".vt("Selectie omkeren")."</div>
<div id='divExport' class='buttonDiv merkBtns' style='display:none;' onclick='document.editForm.submit();'>&nbsp;&nbsp;<img src='images/16/xls.gif' class='simbisIcon' /> ".vt("Export selectie")."</div>


</div>";



$content['javascript'] .= "

function checkAll(optie)
{
  var theForm = document.editForm.elements, z = 0;
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
  controleerVinkjes();
}

function controleerVinkjes()
{
  var theForm = document.editForm.elements, z = 0, toonExport=0 ;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'check_')
   {
      if(theForm[z].checked==true)
      {
        toonExport=1;
        break;
      }
   }
  }
  if(toonExport==1)
  {
    $('#divExport').show();  
  }
  else
  {
     $('#divExport').hide();  
  }
      
}
";

echo template($__appvar["templateContentHeader"],$content);

if(isset($_POST))
{
  $ids=array();
  foreach($_POST as $key=>$value)
  { 
    if(substr($key,0,6)=='check_')
    { 
      $ids[]=substr($key,6);
    }
  }
  if(count($ids) > 0)
  {
    $export= new externeQueryRun();
    logScherm("Aantal queries in batch:".count($ids));
    $n=1;
    foreach($ids as $id)
    {
      logScherm("Uitvoeren van query: ".$n);
      $n++;
      $export->verzamelData($id);
    }  
    logScherm("Excelfile aanmaken.");
    $filename=$export->exportAll();  

    if ($__BTR_CONFIG["CUSTOM_HTML_ELEMENTS"]) {
      echo '<div id="generated-file-name">'.$filename.'</div>';
    }

    echo "<br>\n<a href='showTempfile.php?show=1&filename=".$filename."'><b> download $filename </b></a><br>\n";
  }
}

echo str_replace('</form>','',$list->filterHeader());
?>

<br>
<br>

<table class="list_tabel" cellspacing="0">

<?php
echo $list->printHeader();
$list->editIconTd='style="width:70px"';
while($data = $list->getRow())
{
  // $data["run"]["value"] = ";
  if(isset($data['externeQueries.frequentie']))
  {
    $data['externeQueries.frequentie']['value']=$data['externeQueries.frequentie']['form_options'][$data['externeQueries.frequentie']['value']];
  }
  $list->editIconExtra="<a target=\"_blank\" href='externequerierun.php?queryid=".$data['id']['value']."'>".drawButton("xls","","Uitvoeren")."</a>
  <input type=\"checkbox\" name=\"check_".$data['id']['value']."\" id=\"check_".$data['id']['value']."\" value=1 onclick=\"javascript:controleerVinkjes();\">";

	// $list->buildRow($data,$template="",$options="");
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