<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 22 september 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/05/14 15:10:25 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: doorkijk_categorieWegingenPerFondsList.php,v $
    Revision 1.3  2019/05/14 15:10:25  cvs
    call 7630

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("doorkijk categorie Wegingen Per Fonds");
$mainHeader    = vt("overzicht");

$editScript = "doorkijk_categorieWegingenPerFondsEdit.php";
$allow_add  = true;  // --- false of true ??

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("doorkijk_categoriewegingenPerFonds","id",array("search"=>false));
$list->addFixedField("doorkijk_categoriewegingenPerFonds","datumVanaf",array("search"=>false));
$list->addFixedField("doorkijk_categoriewegingenPerFonds","Fonds",array("search"=>false));

$list->addFixedField("doorkijk_categoriewegingenPerFonds","ISINCode",array("search"=>false));
$list->addFixedField("doorkijk_categoriewegingenPerFonds","Valuta",array("search"=>false));
$list->addFixedField("doorkijk_categoriewegingenPerFonds","msCategoriesoort",array("search"=>false));
$list->addFixedField("doorkijk_categoriewegingenPerFonds","msCategorie",array("search"=>false));
$list->addFixedField("doorkijk_categoriewegingenPerFonds","weging",array("search"=>false));

$html = $list->getCustomFields('doorkijk_categoriewegingenPerFonds','doorkijk_categoriewegingenPerFonds');

$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem($html,"");

if(checkAccess($type))
{
	// superusers
	$allow_add = true;
}
else
{
	// normale user
	$allow_add = false;
}

// set default sort
// $_GET['sort'][]      = "tablename.field";
// $_GET['direction'][] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
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
echo template($__appvar["templateContentHeader"],$content);
?>
<?php

$ms = new AE_cls_Morningstar();

if (!$ms->allowed(2,4))  // call 7630 filter optie beperken ivm rechten
{
    $list->filterHeaderOptions["doorkijk_categorieWegingenPerFonds.msCategoriesoort"] =
      array(
      "queryOverride"=>"
            SELECT 
              msCategoriesoort 
            FROM 
              doorkijk_categorieWegingenPerFonds
            WHERE 
              msCategoriesoort IN ('".implode("','",$ms->doorkijkStandaard)."')
            GROUP BY 
              msCategoriesoort 
            ORDER BY 
              msCategoriesoort");

}

?>
<?= $list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
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
