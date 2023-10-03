<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 16 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/02/05 17:24:35 $
    File Versie         : $Revision: 1.12 $

    $Log: CRM_naw_kontaktpersoonList.php,v $
    Revision 1.12  2020/02/05 17:24:35  rvv
    *** empty log message ***

    Revision 1.11  2017/12/16 18:42:38  rvv
    *** empty log message ***

    Revision 1.10  2015/03/04 16:29:31  rvv
    *** empty log message ***

    Revision 1.9  2013/02/13 17:04:56  rvv
    *** empty log message ***

    Revision 1.8  2011/11/05 16:03:45  rvv
    *** empty log message ***

    Revision 1.7  2011/08/31 14:37:39  rvv
    *** empty log message ***

    Revision 1.6  2010/10/24 10:28:48  rvv
    *** empty log message ***

    Revision 1.5  2010/10/21 16:14:05  rvv
    *** empty log message ***

    Revision 1.4  2010/09/15 09:37:22  rvv
    *** empty log message ***

    Revision 1.3  2010/03/17 14:58:01  rvv
    *** empty log message ***

    Revision 1.2  2006/01/25 11:50:17  cvs
    *** empty log message ***

    Revision 1.1  2006/01/05 16:06:05  cvs
    eerste CRM test

    Revision 1.2  2005/12/14 12:35:13  cvs
    *** empty log message ***

    Revision 1.3  2005/11/21 16:35:06  cvs
    *** empty log message ***

    Revision 1.2  2005/11/21 10:08:25  cvs
    *** empty log message ***

    Revision 1.1  2005/11/17 08:09:45  cvs
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader = "";
$mainHeader    = "Kontaktpersonen overzicht";

$editScript = "CRM_naw_kontaktpersoonEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("CRM_naw_kontaktpersoon","id",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_kontaktpersoon","naam",array("list_width"=>"","search"=>false));
$list->addColumn("CRM_naw_kontaktpersoon","naam1",array("list_width"=>"","search"=>false));
$list->addColumn("","mail",array("list_width"=>"30"));
$list->addColumn("","sjabloon",array("list_width"=>"60",'description'=>"sjabloon ",'list_nobreak'=>true));
$list->addColumn("CRM_naw_kontaktpersoon","functie",array("list_width"=>"","search"=>false));
$list->addColumn("CRM_naw_kontaktpersoon","tel1",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_kontaktpersoon","tel2",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_kontaktpersoon","email",array("list_width"=>"200"));

$deb_id = $_GET['deb_id'];
if ($deb_id > 0)
{

  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = $deb_id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader = " bij <b>".$nawRec[naam].", ".$nawRec[a_plaats]."</b>";

  $list->setWhere("rel_id = ".$deb_id);


 // $_SESSION[submenu] = New Submenu();
 // $_SESSION[submenu]->addItem("Terug naar NAW ","CRM_nawEdit.php?action=edit&id=$deb_id&useSavedUrl=1");
 // $_SESSION[submenu]->addItem("<br>","");


}
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

if($_GET['action'] == 'xls')
{
  $aanwezigeVelden=array();
  foreach($list->columns as $index=>$kolData)
  {
    $aanwezigeVelden[]=$kolData['name'];
  }

  $contact=new CRM_naw_kontaktpersoon();
  foreach ($contact->data['fields'] as $veld=>$veldData)
  {
    if(!in_array($veld,$aanwezigeVelden))
    {
      if($veldData['list_visible']==1 || $veldData['form_visible']==1)
        $list->addColumn("CRM_naw_kontaktpersoon",$veld);
    }
  }

  $list->setXLS();
  $list->getXLS();
}
else
{
  if(!is_a($_SESSION['submenu'],'Submenu'))
    $_SESSION['submenu']=new Submenu();
  $_SESSION['submenu']->addItem("XLS-lijst","$PHP_SELF?action=xls&".$_SERVER['QUERY_STRING']);
  $_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem("Kopieer relaties","CRM_naw_dossierCopy.php?tabel=CRM_naw_kontaktpersoon&relid=$deb_id");  
  
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

function mailTo(email)
{
  ml = 'mailto:'+email;
  window.location = ml;
}

";
echo template($__appvar["templateContentHeader"],$content);
?>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
  if ($data['email']['value'] <> "")
	  $data['mail']['value'] = "<a href=\"javascript:mailTo('".$data['email']['value']."')\" ><img src=\"images/16/internetMail.gif\" border=\"0\" align=\"bottom\"></a>";
	$data['sjabloon']['value'] = "<a href=\"CRM_naw_rtfMergeList.php?deb_id=$deb_id&contact=".$data['id']['value']."\" ><img src=\"images/16/template.gif\" border=\"0\" align=\"bottom\"></a>";
  $data['email']['value'] = "<a href=\"javascript:mailTo('".$data['email']['value']."')\" >".$data['email']['value']."</a>";
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