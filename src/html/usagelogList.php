<?php
/*
    AE-ICT CODEX source module versie 1.6, 17 augustus 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2014/02/02 10:48:31 $
    File Versie         : $Revision: 1.2 $

    $Log: usagelogList.php,v $
    Revision 1.2  2014/02/02 10:48:31  rvv
    *** empty log message ***

    Revision 1.1  2011/08/31 14:37:40  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "usagelogEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$__appvar['rowsPerPage']=250;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("UsageLog","object",array("list_width"=>"150","search"=>false));
$list->addColumn("UsageLog","filename",array("list_width"=>"600","search"=>false));
$list->addColumn("UsageLog","query",array("list_width"=>"600","search"=>false));
$list->addColumn("UsageLog","add_date",array("list_width"=>"150","search"=>false));
$list->addColumn("UsageLog","add_user",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields('UsageLog');

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);

// select page
$list->selectPage($_GET['page']);

$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content["javascript"] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);

$db=new DB();
$query="
SELECT 
  MIN(add_date) as eerste,
  MAX(add_date) as laatste, 
  add_user as user,
  COUNT(id) as aantal, 
  `object`,
  `filename`
FROM 
  usageLog 
WHERE 
  date(add_date) = date(now()) 
GROUP BY 
  add_user,object 
ORDER BY 
  `user`,
  `object`,
  `filename`";
$db->SQL($query);
$db->Query();

?>
<style>
  table{
    border: 1px solid black;
    width:800px;
  }
  td{
    border: 1px solid #999;
    padding:3px;
  }
</style>
<table >
  <tr>
    <td colspan=5><?=vt("Activiteit op")?> <?=date("d-m-Y")?>
    </td>
  </tr>
  <tr>
    <td><?=vt("Eerste activiteit")?></td>
    <td><?=vt("Laatste activiteit")?></td>
    <td><?=vt("Gebruiker")?></td>
    <td><?=vt("object")?></td>
    <td><?=vt("bestand")?></td>
    <td><?=vt("Aantal vermeldingen")?></td>
  </tr>

<?
while($data=$db->nextRecord())
{
  echo "
  <tr>
    <td>".$data['eerste']."</td>
    <td>".$data['laatste']."</td>
    <td>".$data['user']."</td>
    <td>".$data['object']."</td>
    <td>".$data['filename']."</td>
    <td>".$data['aantal']."</td>
  </tr>\n";
}

?>

</table>
<br>
<form name="editForm" method="POST">
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
    if(strlen($data['usageLog.query']['value']) > 75)
	  $data['usageLog.query']['value']=substr($data['usageLog.query']['value'],0,75)."...";
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
