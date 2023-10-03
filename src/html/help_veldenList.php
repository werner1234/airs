<?php
/*
    AE-ICT CODEX source module versie 1.6, 23 juli 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/08/31 14:37:40 $
    File Versie         : $Revision: 1.3 $

    $Log: help_veldenList.php,v $
    Revision 1.3  2011/08/31 14:37:40  rvv
    *** empty log message ***

    Revision 1.2  2011/07/25 17:19:01  rvv
    *** empty log message ***

    Revision 1.1  2011/07/23 17:24:57  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "help_veldenEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 250;

if($_GET['aanmaken']==1)
{
  $velden=array();
  foreach ($__appvar['tabelObjecten'] as $objectnaam)
  {
    $object=new $objectnaam();
    $table=$object->data['table'];
    foreach ($object->data['fields'] as $veld=>$opties)
    {
      if($opties['form_visible']==true)
      {
        $velden[]=strtolower($table.".".$veld);
      }
    }
  }
  sort($velden);
  $db=new DB();
  foreach ($velden as $veld)
  {
    $query="SELECT id FROM help_velden WHERE veld='$veld'";
    if(!$db->QRecords($query))
    {
      $query="INSERT INTO help_velden SET veld='$veld',add_date=now(),change_date=now(),add_user='$USR',change_user='$USR'";
      $db->SQL($query);
      $db->Query();
    }
  }
}

$_SESSION['submenu'] = New Submenu();
if($__appvar['master'] ==true)
$_SESSION['submenu']->addItem('Onderdelen aanmaken',basename($PHP_SELF)."?aanmaken=1");


$list->addColumn("Help_velden","id",array("list_width"=>"100","search"=>false));
$list->addColumn("Help_velden","veld",array("list_width"=>"400","search"=>true));
$list->addColumn("Help_velden","change_date",array("list_width"=>"100","search"=>false));
$list->addColumn("","vulling",array("list_width"=>"100","search"=>false,'sql_alias'=>"LENGTH(help_velden.txt)"));


// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $list->perPage,$allow_add));
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
echo template($__appvar["templateContentHeader"],$content);
?>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
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
echo template($__appvar["templateRefreshFooter"],$content);
?>