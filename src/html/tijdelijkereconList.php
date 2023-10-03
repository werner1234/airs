<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 14 mei 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/02/27 08:52:04 $
    File Versie         : $Revision: 1.21 $
 		
    $Log: tijdelijkereconList.php,v $
    Revision 1.21  2020/02/27 08:52:04  cvs
    call 8433

    Revision 1.20  2019/11/06 07:21:04  cvs
    update 6-11-2019

    Revision 1.19  2018/09/23 17:14:23  cvs
    call 7175

    Revision 1.18  2018/03/28 12:38:10  cvs
    call 3503

    Revision 1.17  2017/07/22 18:20:50  rvv
    *** empty log message ***

    Revision 1.16  2017/06/19 08:45:34  cvs
    cal 5939


 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$cfg = new AE_config();
$field = "reconV3-import-status_".$USR;
$reconImportStatus = $cfg->getData($field);

if ($reconImportStatus != "import done" AND $_GET["override"] != "sure")
{
  $uri = explode("?", $_SERVER["REQUEST_URI"]);
  $link = $_SERVER["REQUEST_URI"];
  if (count($uri) != 2)
  {
    $link .= "?ddd=1";
  }
  $link .= "&override=sure";

  $_SESSION["NAV"] = "";
  echo template($__appvar["templateContentHeader"],$content);

  ?>
  <br/>
  <br/>
  <br/>
  <br/>
  <h1 style="color: red">LET OP:</h1>
  <h2>De laatste recon is niet correct verwerkt!</h2>
  <h3>Waarschijnlijk is de dataset hierdoor incompleet</h3>
  <br/>
  <br/>

  <a href="<?=$link?>">Toch doorgaan naar overzicht</a>
  <?
  echo template($__appvar["templateRefreshFooter"],$content);
  exit;
}


$subHeader     = "";
$mainHeader    = $reconImportStatus." tijdelijk Reconciliatie overzicht ";

$editScript = "tijdelijkereconEdit.php";
$allow_add  = true;


$list = new MysqlList2();

$list->idField = "id";
$list->idTable ='tijdelijkeRecon';

$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("TijdelijkeRecon","id",array("list_width"=>"100","search"=>false));

//$list->addColumn("","H",array("description"=>"H","search"=>false,"list_width"=>"40","search"=>false,"list_align"=>"center"));
//$list->addColumn("","popup",array("list_width"=>"35","description"=>" ",'list_nobreak'=>true,'list_order'=>false,"noClick" => true));
$list->addFixedField("TijdelijkeRecon","vermogensbeheerder",array("description"=>"VB","search"=>false,"list_width"=>"70","search"=>false,"list_align"=>"center"));

$list->addFixedField("TijdelijkeRecon","Accountmanager",array("description"=>"AccMan","list_width"=>"60","search"=>false,"list_align"=>"center"));
$list->addFixedField("TijdelijkeRecon","depotbank",array("description"=>"Depot","list_width"=>"75","search"=>false,"list_align"=>"center"));
$list->addFixedField("TijdelijkeRecon","client",array("list_width"=>"90","search"=>true));
$list->addFixedField("TijdelijkeRecon","portefeuille",array("list_width"=>"","search"=>true));
$list->addFixedField("TijdelijkeRecon","rekeningnummer",array("list_width"=>"","search"=>true,"description"=>"rekeningNr"));
$list->addFixedField("TijdelijkeRecon","cashPositie",array("description"=>"Cash","list_width"=>"30","list_align"=>"center","search"=>false));
$list->addFixedField("TijdelijkeRecon","isinCode",array("list_width"=>"50","search"=>false));
$list->addFixedField("TijdelijkeRecon","valuta",array("list_width"=>"","search"=>false,"list_width"=>"30","search"=>false,"list_align"=>"center"));
$list->addFixedField("TijdelijkeRecon","positieBank",array("list_width"=>"","search"=>false,"list_width"=>"110","search"=>false,"list_align"=>"right"));
$list->addFixedField("TijdelijkeRecon","positieAirs",array("list_width"=>"","search"=>false,"list_width"=>"100","search"=>false,"list_align"=>"right"));
$list->addFixedField("TijdelijkeRecon","verschil",array("list_width"=>"","search"=>false,"list_width"=>"80","search"=>false,"list_align"=>"right"));
$list->addFixedField("TijdelijkeRecon","fondsCodeMatch",array("list_width"=>"","search"=>false,"list_align"=>"center"));
$list->addFixedField("TijdelijkeRecon","reconDatum",array("list_width"=>"","search"=>false,"list_align"=>"center"));
//$list->addFixedField("TijdelijkeRecon","depotbankFondsCode",array("list_width"=>"","search"=>false,"list_align"=>"center"));
$list->addFixedField("Portefeuilles","Einddatum",array("list_width"=>"","search"=>false,"list_align"=>"center"));
$list->setJoin("LEFT JOIN Portefeuilles ON tijdelijkeRecon.portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0");

//$list->addFixedField("TijdelijkeRecon","depotbankFondsCode",array("list_width"=>"50","search"=>false));

//$list->addFixedField("TijdelijkeRecon","fonds",array("list_width"=>"","search"=>true));

//$list->addFixedField("TijdelijkeRecon","positieAirsGisteren",array("list_width"=>"","search"=>false,"list_width"=>"100","search"=>false,"list_align"=>"right"));



//$list->addFixedField("TijdelijkeRecon","Einddatum",array("list_width"=>"80","search"=>false,"list_align"=>"right"));
//$list->addFixedField("TijdelijkeRecon","reconDatum",array("list_width"=>"80","search"=>false,"list_align"=>"right"));
//$list->addColumn("TijdelijkeRecon","batch",array("list_width"=>"180","search"=>true));
//$list->addColumn("TijdelijkeRecon","koers",array("list_width"=>"","search"=>true,"list_align"=>"right"));
//$list->addFixedField("TijdelijkeRecon","koersDatum",array("list_width"=>"80","search"=>true));
//$list->addFixedField("TijdelijkeRecon","fondsImportcode",array("list_width"=>"80","search"=>true));


$html = $list->getCustomFields(array('TijdelijkeRecon'));  

if ($_GET["einddatum"] == "on")
{
  $extraWhere = " AND Portefeuilles.Einddatum > tijdelijkeRecon.reconDatum";
  $subHeader = " actieve portefeuilles";
}

if ($_GET["blockFilterAction"] == "diff")
{
  $extraWhere .= " AND verschil <> 0";
  $subHeader  .= " ( gefilterd op verschillen ) ";
}
else
{
  $subHeader .= "( ongefilterd )";
}
$batchArgs = "";
if ($_GET["batch"] != "")
{
  include_once "../classes/AIRS_cls_reconJob.php";
  $job = new AIRS_cls_reconJob($_GET["batch"]);
  $job->getJob();
  $list->setWhere(" tijdelijkeRecon.batch = '{$_GET["batch"]}' ".$extraWhere);
  $mainHeader    = vtb("tijdelijk Reconciliatie van batch: %s", array($_GET["batch"]));
  $batchArgs = "?batch=".$_GET["batch"];
}
else
{
  $jump = 0;
  $list->setWhere(" tijdelijkeRecon.add_user = '$USR' ".$extraWhere);
}



// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
if($_POST['toXls'] == '1')
{
  $query = "
    SELECT
      reconDatum
    FROM
      (tijdelijkeRecon)
    WHERE 
      add_user = '$USR'
    AND    
      reconDatum > '0000-00-00'
    ";
  $db = new DB();
  $rec = $db->lookupRecordByQuery($query);
  //$list->xlsFilename = str_replace("-","",$rec["reconDatum"])."_".date("Ymd")."_Recon.xls";
  $list->xlsFilename = str_replace("-","",$rec["reconDatum"])."_".date("Ymd").".xls";
}

$list->selectPage($_GET['page']);

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

$split = explode("LIMIT", $list->getSQL());

$_SESSION["queryTRL"] = $split[0];
/*
if ($list->records() <> 0)
{
	$_SESSION['submenu'] = New Submenu();
	$_SESSION['submenu']->addItem("Inlezen bestand","reconSelectDepotbank.php");
	$_SESSION['submenu']->addItem("<br>","");
}
else 
{
  */
  $_SESSION['submenu'] = New Submenu();
	$_SESSION['submenu']->addItem("Inlezen bestand","reconSelectDepotbank.php");
	$_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem("Verschil mutaties boeken","reconBoekVerschilmutaties.php".$batchArgs);
	$_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem("Lijst verrijken","reconVerrijkList.php");
  $_SESSION['submenu']->addItem("Vul Opmerkingen","reconFillOpmerking.php");
/*
}
*/
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");


$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content[javascript] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);

if ($job->currentJob)
{
  $j = $job->currentJob;
  $fmt = new AE_cls_formatter();
//  debug($j);
?>
  <link rel="stylesheet" href="style/fontAwesome/font-awesome.css" >
  <link rel="stylesheet" href="style/AIRS_default.css" type="text/css" media="screen">
  <fieldset>
    <legend> Info bij batch <?=$j["batchnr"]?> <i class="fa fa-angle-double-down" id="btnToggle" style="float:right"></i></legend>
    <div id="batchInfo">
      <table>
        <tr><td class="tKol1">Aangemaakt d.d.</td><td calss="tKol2"><?=$fmt->format("@D{form}",$j["add_date"])?> om <?=$fmt->format("@D{H}:{i}",$j["add_date"])?> door <?=$j["add_user"]?></td></tr>
        <tr><td class="tKol1">Verwerkt d.d.</td><td calss="tKol2"><?=$fmt->format("@D{form}",$j["change_date"])?> om <?=$fmt->format("@D{H}:{i}",$j["change_date"])?></td></tr>
        <tr><td class="tKol1">Naam</td><td calss="tKol2"><?=$j["naam"]?></td></tr>
        <tr><td class="tKol1">Depotbank</td><td calss="tKol2"><?=$j["depotbank"]?></td></tr>
        <tr><td class="tKol1">VB's</td><td calss="tKol2"><?=$j["vermogenbeheerders"]?></td></tr>
        <tr><td class="tKol1">Verwerkingtijd</td><td calss="tKol2"><?=$j["verwerkingsTijd"]?> sec</td></tr>
        <tr><td class="tKol1">Log</td><td calss="tKol2"><?=nl2br($j["log"])?></td></tr>

      </table>
    </div>
    <button id="btnDeleteBatch" class="btnRed" data-batch="<?=$j["batchnr"]?>">Verwijder deze batch</button>&nbsp;&nbsp;
    <button id="btnToBatch">Terug naar batchoverzicht</button>&nbsp;&nbsp;

  </fieldset>
  <br/>
<?
}

?>
  <script type="text/javascript" src="javascript/dragtable/jquery.dragtable.js"></script>
  <link href="style/jquery.css" rel="stylesheet" type="text/css" media="screen">
  <link href="style/smoothness/jquery-ui-1.11.1.custom.css" rel="stylesheet" type="text/css" media="screen">

<style>
  .w10{  width: 10px;}
  .w20{  width: 20px;}
  .w50{  width: 50px;}
  .w75{  width: 75px;}
  .w100{ width: 100px;}
  .w125{ width: 125px;}
  .w150{ width: 150px;}
  .w175{ width: 175px;}
  .w200{ width: 200px;}
  .w250{ width: 25px;}
  .w300{ width: 300px;}
  .w400{ width: 400px;}
  .ac  { text-align: center !important; }
  .al  { text-align: left !important;}
  .ar  { text-align: right !important;}
  .b   { font-weight: bold !important;}
  .brdt{ border-top: 1px solid #333}
  #batchInfo{ display: none}
  tr{
    border-bottom: 1px solid #333;
  }

  td{
    font-family: arial;
    font-size:12px;
    border-bottom: 1px solid #DDD;


  }

  .iBtn{
    text-decoration: none;
    padding:2px;
    font-weight: bold;
    margin-right:5px;
  }
  .iBtn img{
    width: 14px;
    height: 14px;
  }
  .iBtn :hover{
    cursor: pointer;
  }
  .btnImg{
    width: 16px;
    height: 16px;
    margin-left: 0px;
  }
  #extraInfoPopup{
    background: beige;
    font-size: .7em;
  }

  .extraInfoTable
  {
    width: 90%;
  }
  .extraInfoTable td
  {
    padding-right: 10px;
    padding-left: 10px;
  }
  h1
  {
    font-size: 1.2em;
  }
  .trHeader td{
    background: #333;
    color: whitesmoke;
  }

  .extraInfoTable tr:nth-child(even) {background: white}
  .extraInfoTable tr:nth-child(odd) {background: whitesmoke}

  .list_dataregel_rose{
    background:#FF99CC;
  }
  .list_dataregel_oranje{
    background:orange;
  }
  .list_dataregel_groen{
    background:#80FF80;
  }
  .list_dataregel_cyaan{
    background:#80FFFF;
  }
  .legenda{
    padding:4px;
    border-radius: 5px;
    text-align: center;
    border:1px solid #333;
    width:700px;
  }
  .colorCoding{
    width:720px;
  }
  .filterRow{
    text-align: center;
    margin:10px;
    padding: 15px;
    width: 50%;
    border: 0;
    border-radius: 5px;
    background-image: -webkit-gradient(linear, top, bottom, color-stop(0, #c9c9c9), color-stop(1, #F5F5F5));
       background-image: -ms-linear-gradient(top, #c9c9c9, #F5F5F5);
       background-image: -o-linear-gradient(top, #c9c9c9, #F5F5F5);
       background-image: -moz-linear-gradient(top, #c9c9c9, #F5F5F5);
       background-image: -webkit-linear-gradient(top, #c9c9c9, #F5F5F5);
       background-image: linear-gradient(to bottom, #c9c9c9, #F5F5F5)
  }


</style>

  <a href="tijdelijkereconListCSV.php"><button>Exporteer naar .csv</button></a><br/><br/>
<?
//reconSelectDepotbank.php

$checkedOff = ($_GET["einddatum"] == "off")?" checked ":"";
$checkedOn  = ($checkedOff == " checked ")?"":" checked ";
?>
<?=$list->filterHeader();?>   

<fieldset class="filterRow ">
  <span style="float: left"><b>Filter opties:</b></span>
  <button id="filterNone"> Alles </button>
  <button id="filterDiff"> Verschillen </button>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <input type="radio" name="einddatum" class="einddatum" id="ed1" value="on" <?=$checkedOn?> /><label for="ed1" >actieve portefeuilles</label>
  &nbsp;&nbsp;&nbsp;
  <input type="radio" name="einddatum" class="einddatum" id="ed2" value="off" <?=$checkedOff?>/> <label for="ed2" >alle portefeuilles</label>
</fieldset>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php

while($data = $list->getRow())
{
  $list->editIconExtra = "<button class='iBtn' data-id='".$data["id"]["value"]."' data-cash='".$data["tijdelijkeRecon.cashPositie"]["value"]."'><img class='btnImg' src='images/16/information.png'/></button>";
  //$data[".popup"]["value"] = "<button class='iBtn' data-id='".$data["id"]["value"]."' data-cash='".$data["tijdelijkeRecon.cashPositie"]["value"]."'><img class='btnImg' src='images/16/information.png'/></button>";


  //listarray($data);
  if ( $data["tijdelijkeRecon.positieBank"]["value"] <> $data["tijdelijkeRecon.positieAirs"]["value"])     {     $data["tr_class"] = "list_dataregel_rood";  }
  if ( stristr($data["tijdelijkeRecon.fondsCodeMatch"]["value"], "Geen AIRS" ))           {     $data["tr_class"] = "list_dataregel_groen";  }
  if ( stristr($data["tijdelijkeRecon.fondsCodeMatch"]["value"], "Geen AIRS mutaties" ) OR
       stristr($data["tijdelijkeRecon.fondsCodeMatch"]["value"], "AIRS rekening INAKTIEF" )   )  {     $data["tr_class"] = "list_dataregel_cyaan";  }
	if ( stristr($data["tijdelijkeRecon.fondsCodeMatch"]["value"], "Geen bank" ))           {     $data["tr_class"] = "list_dataregel_rose";  }
	//if ( stristr($data["tijdelijkeRecon.fondsCodeMatch"]["value"], "Depotbank afwijking" )) {    $data["tr_class"] = "list_dataregel_oranje";  }
//debug ($data);
	echo $list->buildRow($data);
}

?>
</table>
<br />
<br />
<br />
<fieldset class="colorCoding">
  <legend>Legenda</legend>
  <p class="list_dataregel_rood legenda">saldo afwijking</p>
  <p class="list_dataregel_rose legenda">Geen bank</p>
  <p class="list_dataregel_groen legenda">Geen Airs</p>
  
  <p class="list_dataregel_cyaan legenda">Geen Airs mutaties of AIRS rekening INAKTIEF</p>
  <p class="list_dataregel_oranje legenda"> --</p>
</fieldset>
<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}

?>

<form id="blockFilterForm">
  <input type="hidden" name="page" value="<?=$_GET["page"]?>" />
  <input type="hidden" name="selectie" value="<?=$_GET["selectie"]?>" />
  <input type="hidden" name="einddatum" id="einddatum" value="<?=$_GET["einddatum"]?>" />
  <input type="hidden" name="batch" id="batch" value="<?=$_GET["batch"]?>" />
  <input type="hidden" id="blockFilterAction" name="blockFilterAction" value="" />
</form>



  <div id="extraInfoPopup" title="Details ">
    <div id="infoContent">

    </div>
  </div>

<script>
$(document).ready(function()
{

  infoDialog = $('#extraInfoPopup').dialog({
    autoOpen: false,
    height: 550,
    width: '80%',
    modal: true,
    buttons: {},
    close: function ()
    {
    }
  });
  $('#infoTabs').tabs();


  $(".iBtn").click(function(e)
  {
    e.preventDefault();
    var id = $(this).attr("data-id");
    var cash = $(this).attr("data-cash");
    infoDialog.dialog('open');
    $("#infoContent").load("tijdelijkeReconPopup.php?tab=1&cash"+cash+"&id=" + id);

  });


  $("#btnToggle").click(function () {
    $("#batchInfo").toggle(300);
  });

  $(".einddatum").change(function()
  {
    $("#einddatum").val($(this).val());
    $("#blockFilterForm").submit();
  });

  $("#filterNone").click(function()
  {
    $("#blockFilterForm").submit();
  });

  $("#filterDiff").click(function()
  {
    
    $("#blockFilterAction").val("diff");
    $("#blockFilterForm").submit();
  });

  $("#btnDeleteBatch").click(function(e){
    e.preventDefault();
    var b = $(this).data("batch");
    window.open("batch_jobManager.php?action=deleteBatch&jump=1&batch="+b,"content");
  });
  $("#btnToBatch").click(function(e){
    e.preventDefault();
    window.open("batch_jobManager.php?action=list&jump=1","content");
  });


});
</script>
<?
echo template($__appvar["templateRefreshFooter"],$content);
?>