<?php
/*
    AE-ICT CODEX source module versie 1.2, 23 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/08/31 14:37:39 $
    File Versie         : $Revision: 1.5 $

    $Log: CRM_naw_documentenList.php,v $
    Revision 1.5  2011/08/31 14:37:39  rvv
    *** empty log message ***

    Revision 1.4  2010/10/21 16:14:05  rvv
    *** empty log message ***

    Revision 1.3  2010/09/15 09:37:22  rvv
    *** empty log message ***

    Revision 1.2  2010/09/04 08:21:03  rvv
    *** empty log message ***

    Revision 1.1  2006/01/05 16:06:05  cvs
    eerste CRM test

    Revision 1.2  2005/12/14 12:35:13  cvs
    *** empty log message ***

    Revision 1.2  2005/11/23 19:23:08  cvs
    *** empty log message ***

    Revision 1.1  2005/11/23 09:29:48  cvs
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = "Document koppelingen overzicht";

$editScript = "CRM_naw_documentenEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("Naw_documenten","id",array("list_width"=>"100","search"=>false));
$list->addColumn("Naw_documenten","bestandsnaam",array("list_width"=>"200","search"=>true));
$list->addColumn("Naw_documenten","omschrijving",array("list_width"=>"","search"=>true));
$list->addColumn("","toegevoegd",array("list_width"=>"150","search"=>false));

$list->addColumn("Naw_documenten","add_date",array("list_width"=>"","search"=>false,"list_invisible"=>true));
$list->addColumn("Naw_documenten","add_user",array("list_width"=>"","search"=>false,"list_invisible"=>true));
$deb_id = $_GET[deb_id];
if ($deb_id > 0)
{

  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = $deb_id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader = " bij <b>".$nawRec[naam].", ".$nawRec[a_plaats]."</b>";

  $list->setWhere("rel_id = ".$deb_id);


  //$_SESSION[submenu] = New Submenu();
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
  $bestandsnaam = $data[bestandsnaam][value];
  if (strstr($bestandsnaam,"\\"))
    $tmpa = preg_split("[\\\]",$bestandsnaam);
  else
    $tmpa = preg_split("[/]",$bestandsnaam);

  $ndx = count($tmpa)-1;
  $filen = $tmpa[$ndx];
  if (substr($bestandsnaam,-1) == "\\")
    $filen = $row[filename];

  $fileLink = "<a href=\"file://$bestandsnaam\"  title=\"$bestandsnaam\">".lClip($filen,30)."</a>";
  $data[bestandsnaam][value] = $fileLink;
  $data[toegevoegd][value] = kdbdatum($data[add_date][value])." door ".$data[add_user][value];
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