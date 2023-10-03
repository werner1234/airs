<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "customTemplateEdit.php";

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("custom_templates","id",array("search"=>false));
$list->addField("custom_templates","naam",array("width"=>800,"search"=>false));
$list->addField("custom_templates","categorie",array("search"=>false));


//$_SESSION[submenu] = New Submenu();
//$_SESSION[submenu]->addItem($html,"");
//
//if(checkAccess($type))
//{
//  // superusers
//  $allow_add = true;
//}
//else
//{
//  // normale user
//  $allow_add = false;
//}

// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],true));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content[javascript] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
  <br>
<?=$list->filterHeader();?>
  <table class="list_tabel" cellspacing="0" width="5000">
    <?=$list->printHeader();?>
    <?php
    while($data = $list->getRow())
    {
      $data['filler']['value'] = "&nbsp;";
      $data['filler']['td_style'] = " style='border-bottom:none' ";
      echo $list->buildRow($data);
    }
    ?>
  </table>
<?
logAccess();
if($__debug) {
  echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>