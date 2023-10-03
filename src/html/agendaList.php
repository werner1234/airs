<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 18 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/08/31 14:37:39 $
    File Versie         : $Revision: 1.3 $

    $Log: agendaList.php,v $
    Revision 1.3  2011/08/31 14:37:39  rvv
    *** empty log message ***

    Revision 1.2  2010/09/15 09:37:22  rvv
    *** empty log message ***

    Revision 1.1  2010/01/24 17:05:54  rvv
    *** empty log message ***

    Revision 1.1  2008/09/23 08:04:42  cvs
    eerste commit vanuit simbis 23092008

    Revision 1.5  2006/03/16 14:15:30  cvs
    *** empty log message ***

    Revision 1.4  2005/12/14 08:33:17  cvs
    *** empty log message ***

    Revision 1.3  2005/11/22 14:31:07  cvs
    *** empty log message ***

    Revision 1.2  2005/11/21 16:35:06  cvs
    *** empty log message ***

    Revision 1.1  2005/11/21 10:08:25  cvs
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$addExtra      = "";                             // extra parameters voor insertrecord
$subHeader     = "";
$mainHeader    = "Agenda overzicht";

$editScript = "agendaEdit.php";
$allow_add  = false;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$whereArray = array();


//$list->addColumn("Agenda","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","id", array("sql_alias"=>"DISTINCT agenda.id "));
$list->addColumn("Agenda","plandate",array("description"=>"plandatum","list_width"=>"150","search"=>false));
$list->addColumn("Agenda","klant",array("list_width"=>"","search"=>true));
$list->addColumn("Agenda","kop",array("list_width"=>"","search"=>true));
$list->addColumn("Agenda","soort",array("list_width"=>"100","search"=>true,"list_align"=>"center"));
$list->addColumn("Agenda","done",array("list_width"=>"50","search"=>false,"list_align"=>"center"));
$list->addColumn("Agenda","plantime",array("list_width"=>"100","search"=>false,"list_invisible"=>"true"));
if ($_GET['do'] <> "mijzelf")
  $list->addColumn("","gebruiker",array("sql_alias"=>"agenda_gebruiker.user_id","list_width"=>"50","search"=>true,"list_align"=>"center","description"=>"wie"));


$list->setGroupBy("agenda.id");
$list->setJoin("LEFT JOIN agenda_gebruiker ON agenda.id=agenda_gebruiker.agenda_id  ".
               "LEFT JOIN Gebruikers ON agenda_gebruiker.user_id = Gebruikers.Gebruiker");

switch ($_GET['do'])
{
	case "alles":
		$subHeader = ", alle agendapunten";
		break;
	case "mijzelf":
    $whereArray[] = "Gebruikers.Gebruiker = '".$_SESSION['USR']."' ";
	  $subHeader = ", Mijn agendapunten";
	  break;
  default:
		$whereArray[] = "done = 0 ";
		$subHeader = ", openstaande agendapunten";
		break;
}

$deb_id = $_GET[deb_id];
if ($deb_id > 0)
{

  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = $deb_id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader .= " bij <b>".$nawRec[naam].", ".$nawRec[a_plaats]."</b>";

  $whereArray[] = " rel_id = ".$_GET['deb_id'];
  $addExtra = "&rel_id=".$_GET['deb_id'];

  $_SESSION[submenu] = New Submenu();
  $_SESSION[submenu]->addItem("Terug naar NAW ","nawEdit.php?action=edit&id=$deb_id&useSavedUrl=1");

}

if (count($whereArray) > 0)
{
  $operator = "";
  for ($wCount=0; $wCount < count($whereArray); $wCount++)
  {
    $whereString .= $operator." ".$whereArray[$wCount]." ";
    $operator = " AND ";
  }
  $list->setWhere($whereString);

}




// set default sort
$_GET['sort'][]      = "agenda.plandate";
$_GET['direction'][] = "DESC";
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
	parent.frames['content'].location = '".$editScript."?action=new".$addExtra."';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>


<table class="list_tabel" cellspacing="0" >



<?php
$DB = new DB();

$DB->SQL("SELECT Gebruiker,bgkleur FROM Gebruikers");
$DB->Query();
while ($usrData = $DB->nextRecord())
{
  $usrColors[$usrData["Gebruiker"]] = $usrData["bgkleur"];
}

function _makePlanDate($date,$time)
{
  $tijdArray = explode(":",$time);
  return kdbdatum($date).", ".$tijdArray[0].":".$tijdArray[1];
}

echo $list->printHeader();

while($data = $list->getRow())
{
  $data[plandate][form_type] = "text";
  $data[plandate][value] = _makePlanDate($data[plandate][value],$data[plantime][value]);

  if ($data[done][value] <> 0)
  {
    $data[tr_class] = "list_dataregel_zand";
  }
  if ($_GET['do'] <> "mijzelf")
    $data[gebruiker][td_style] = " style=\"background-color:#".$usrColors[$data[gebruiker][value]].";\" ";
  //listarray($data);
	$list->buildRow($data,$template="",$options="");
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