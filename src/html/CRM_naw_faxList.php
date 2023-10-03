<?php
/*
    AE-ICT CODEX source module versie 1.2, 21 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/08/31 14:37:39 $
    File Versie         : $Revision: 1.4 $

    $Log: CRM_naw_faxList.php,v $
    Revision 1.4  2011/08/31 14:37:39  rvv
    *** empty log message ***

    Revision 1.3  2010/10/21 16:14:05  rvv
    *** empty log message ***

    Revision 1.2  2010/09/15 09:37:22  rvv
    *** empty log message ***

    Revision 1.1  2006/01/05 16:06:05  cvs
    eerste CRM test

    Revision 1.2  2005/12/14 12:35:13  cvs
    *** empty log message ***

    Revision 1.3  2005/11/22 14:53:48  cvs
    *** empty log message ***

    Revision 1.2  2005/11/22 14:31:07  cvs
    *** empty log message ***

    Revision 1.1  2005/11/21 16:35:06  cvs
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = "faxvoorvellen overzicht";

$editScript = "CRM_naw_faxEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("Naw_fax","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","pdf",array("list_width"=>"40","search"=>false));
$list->addColumn("Naw_fax","datum",array("list_width"=>"100","search"=>false));
$list->addColumn("Naw_fax","fax",array("list_width"=>"120","search"=>true));
$list->addColumn("Naw_fax","onderwerp",array("list_width"=>"","search"=>true));
$list->addColumn("Naw_fax","ref",array("list_width"=>"","search"=>true));
$list->addColumn("Naw_fax","tav",array("list_width"=>"","search"=>true));
$list->addColumn("Naw_fax","paginas",array("list_width"=>"40","search"=>false,"description"=>"pag"));


$deb_id = $_GET[deb_id];
if ($deb_id > 0)
{
  $NAW = new db();
  $q = "SELECT * FROM naw WHERE id = $deb_id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader = " bij <b>".$nawRec[naam].", ".$nawRec[a_plaats]."</b>";

  $list->setWhere("rel_id = ".$deb_id);


 // $_SESSION[submenu] = New Submenu();
 // $_SESSION[submenu]->addItem("Terug naar NAW ","CRM_nawEdit.php?action=edit&id=$deb_id&useSavedUrl=1");

}



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

$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content[javascript] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new&rel_id=$deb_id';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
  $data[datum][value] = kdbdatum($data[datum][value] );
  $data[pdf][value] = "<a href=\"naw_faxMakePDF.php?id=".$data[id][value]."\" target=\"pdfOutput\"><img src=\"images/pdf_20x20.gif\" border=\"0\"></a>";
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
echo template($__appvar["templateRefreshFooterZonderMenu"],$content);
?>