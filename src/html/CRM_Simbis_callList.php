<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 16 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/23 17:14:23 $
    File Versie         : $Revision: 1.1 $

    $Log: CRM_Simbis_callList.php,v $
    Revision 1.1  2018/09/23 17:14:23  cvs
    call 7175



*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$fmt = new AE_cls_formatter();
$subHeader    = "";
$mainHeader   = "Simbis calls";

$editScript = "CRM_Simbis_callShow.php";
$allow_add  = false;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 500;

$list->addColumn("","id",array("list_width"=>"100","search"=>false, ));

$list->addColumn("","callnr",array("list_width"=>"50","search"=>false));
$list->addColumn("","datum",array("list_width"=>"100","search"=>false,"list_align"=>"left"));
$list->addColumn("","betreft",array("list_width"=>"500","search"=>false));
$list->addColumn("","gebruiker",array("list_width"=>"100","search"=>false));
$list->addColumn("","status",array("list_width"=>"","search"=>false));

$deb_id = $_GET['deb_id'];
if ($deb_id > 0)
{
  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = $deb_id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader = " bij <b>".$nawRec['naam'].", ".$nawRec['a_plaats']."</b>";

  $list->setWhere("rel_id = ".$deb_id);

  $_SESSION['submenu'] = "";


}
// default sortering

// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
//$list->selectPage($_GET['page']);


if(!is_a($_SESSION['submenu'],'Submenu'))
    $_SESSION['submenu']=new Submenu();


$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

echo template($__appvar["templateContentHeader"],$content);

$debnr= trim($nawRec["debiteurnr"]);
//$debnr = "900";

if ($debnr <> "")
{


  $db = new DB(DBsimbis);
  $query = "
  SELECT
    mCall_calls.add_date,
    
    mCall_calls.id,
    mCall_calls.rel_naam,
    
    mCall_calls.betreft,
    mCall_calls.gebruiker,
    
    mCall_calls.statuslog,
    naw.debiteurnr,
    selectievelden.omschrijving as `status`
  FROM
    mCall_calls
  INNER JOIN naw ON 
    mCall_calls.rel_id = naw.id
  INNER JOIN selectievelden ON 
    mCall_calls.`status` = selectievelden.prio
  WHERE
    naw.debiteurnr = '$debnr'
  AND 
    selectievelden.module = 'callStatus'
  ORDER BY 
    mCall_calls.id DESC
  ";
  $db->executeQuery($query);
}

if ($debnr <> "")
{
  ?>


  <table class="list_tabel" cellspacing="0" >

    <?=$list->printHeader();?>
<?
    while($raw = $db->nextRecord())
    {
    // debug($raw);
    $data = array(
    "id" => array("value" => $raw["id"], "list_invisible" => true),
    "callnr" => array("value" => $raw["id"]),
    "datum" => array("value" => $fmt->format("@D{form}", $raw["add_date"])),
    "betreft" => array("value" => $raw["betreft"]),
    "gebruiker" => array("value" => $raw["gebruiker"]),
    "status" => array("value" => $raw["status"]),


    );
    // $list->buildRow($data,$template="",$options="");

    echo $list->buildRow($data);
    }

?>
  </table>
<?
}
else
{
  echo "<h1>geen debnr om te koppelen</h1>";
}
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooterZonderMenu"],$content);

?>