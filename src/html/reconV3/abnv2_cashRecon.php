<?php
/*
    AE-ICT sourcemodule created 28 okt. 2020
    Author              : Chris van Santen
    Filename            : abnv2_cashRecon.php


*/

include_once("wwwvars.php");


$_GET = $_SESSION["cashRecon"];
include_once("abnv2_cashReconV3Functies.php");

session_start();
$_SESSION["NAV"] = "";


$_SESSION["reconv3"]["vb"] = $_SESSION["vbArray"];
$_SESSION["reconv3"]["file"] = $_GET["file"];
$_SESSION["reconv3"]["manualBoekdatum"] = $_GET["manualBoekdatum"];

$error    = array();
$content  = array("title" => $PHP_SELF);
$filetype = "";

$content['jsincludes'] .= '<link rel="stylesheet" href="../style/smoothness/jquery-ui-1.11.1.custom.css">';
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"../javascript/jquery-min.js\"></script>";
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"../javascript/jquery-ui-min.js\"></script>";
echo template("../" . $__appvar["templateContentHeader"], $content);

//aetodo: styles verhuizen naar CSS file
?>
<style>
  table{
    font-family: Consolas;
    font-size: 12px;
    padding:5px;
    border:2px solid #999;
  }
  .tdH{
    background: #0a246a;
    color: white;
  }
  .td1, .td2, .td3{
    padding: 5px;
    border-bottom: #BBB 1px solid;
  }
  .ar{ text-align: right}
  .rood{background: maroon; color: white}
</style>

  <div id="running">
    <h2> Reconciliatie cashonly, moment a.u.b. </h2>
    <img src="../images/loading.gif" alt=""/>
  </div>
  <br/>

  <ul>
    <?
    $starttijd = mktime();
    echo " <li> gestart om " . date("H:i:s") . "</li>";
    ob_flush();
    flush();

    if (!abnv2_validateFile($_GET["file"]))
    {
      listError($error);
      exit;
    }

    $recon = new AE_cls_reconV3("AAB", $_SESSION["vbArray"],$_GET["manualBoekdatum"]);
    $batch = "AAB_" . date("ymd_His");
    $recon->batch = $batch;
    $starttijd = mktime();

    echo " <li> depotbank " . $recon->depotbank . "</li>";
    ob_flush();
    flush();
    $recon->addToReconLog("ABN recon gestart, ".implode(", ",$_SESSION["vbArray"]));
    ob_flush();
    flush();

    echo " <li> depotbank " . $recon->depotbank . "</li>";
    ob_flush();
    flush();

    $recon->addToReconLog("abnv2_cashRecon_readBank");
    echo " <li>Bank rekeningen gestart om " . date("H:i:s") . "</li>";
    ob_flush();
    flush();
    $bankRecords = abnv2_cashRecon_readBank($_GET["file"]);

    echo " <li> bankbestand bevatte " . $bankRecords . " dataregels</li>";
    $recon->addToReconLog("bankPileToDB");
    $recon->bankPileToDB();
    $recon->bankPile = array();

    echo " <li> AIRS rekeningen gestart om " . date("H:i:s") . "</li>";
    $recon->getAirsCashOnly();
    echo " <li> inlezen Airs records " . count($recon->airsCashPile) . " items </li>";
    $recon->addToReconLog("airsPileToDB");

    $recon->airsPile = array();


    echo "<li>stats<ul>

<li>bank rekeningnrs: ".$recon->getBankReknrs(false)."</li>
<li>airs rekeningnrs: ".$recon->getAirsReknrs(false)."</li>
</ul>
</li>";
    ob_flush();
    flush();
    $recon->addToReconLog("findUnmatched");
    $recon->findUnmatchedCash();
//    debug($recon->unmatchArray);    echo " <li> inlezen Airs records " . count($recon->airsPile) . " items </li>";

    echo " <li> " . $airsOnly . " AIRS rekeningen zonder bankposities</li>";
    echo " <li> afgerond om " . date("H:i:s") . " (= " . round(mktime() - $starttijd, 0) . " sec.) </li>";
    ?>
  </ul>
<?
//$recon->addToReconLog("matchPositions");
echo $recon->matchCashPositions(true);
//debug($recon->trPile);
$recon->addToReconLog("trPileToDb");
$recon->trPileToDb();
$recon->addToReconLog("ABN recon klaar..");
?>
  <p> <a href="../reconV3Start.php">nieuwe poging inlezen</a></p>
  <p>Ga naar het <a href="../tijdelijkereconList.php">overzicht</a></p>

  <script>
      $("#running").hide(600);
  </script>
<?
echo template("../" . $__appvar["templateRefreshFooter"], $content);

