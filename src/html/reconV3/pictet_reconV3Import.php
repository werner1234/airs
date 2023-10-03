<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Import.php

*/

include_once("wwwvars.php");
include_once("pictet_reconV3Functies.php");


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
    <h2> Reconciliatie, moment a.u.b. </h2>
    <img src="../images/loading.gif" alt=""/>
  </div>
  <br/>

  <ul>
    <?
    $starttijd = mktime();
    echo " <li> gestart om " . date("H:i:s") . "</li>";
    ob_flush();
    flush();

    if (!pictet_validateFile($_GET["file"]))
    {
      listError($error);
      exit;
    }

    $recon = new AE_cls_reconV3("PIC", $_SESSION["vbArray"],$_GET["manualBoekdatum"]);
    $batch = "PICTET_" . date("ymd_His");
    $recon->batch = $batch;

    ob_flush();
    flush();


    echo " <li> depotbank " . $recon->depotbank . "</li>";
    ob_flush();
    flush();

    $starttijd = mktime();
    echo " <li> gestart om " . date("H:i:s") . "</li>";
    ob_flush();
    flush();
    $bankRecords = pictet_recon_readBank($_GET["file"]);

    ob_flush();
    flush();
    echo " <li> bankbestand bevatte " . $bankRecords . " dataregels</li>";
    $recon->bankPileToDB();
    echo " <li> inlezen bank records " . count($recon->bankPile) . " items (DIV regels overgeslagen)</li>";

//    debug($recon->fondsPile);
    echo " <li> Airsfondsen koppelen aan bankregels</li>";
    echo " <li> Airsfondsen gekoppeld ".count($recon->fondsPile)."<br/>".$recon->bankCodesInAirs()."</li>";
    echo " <li> Bankfondsen zonder koppeling ".count($recon->noFondsPile)."<br/>".$recon->bankCodesNotInAirs()."</li>";
    $recon->getAirsFondsForBankPile();
    ob_flush();
    flush();
    echo " <li> gestart om " . date("H:i:s") . "</li>";
    $recon->airsPileToDB();
//    debug($recon->portefeuilleVB,count($recon->portefeuilleVB));
    echo " <li> inlezen Airs records " . count($recon->airsPile) . " items </li>";
    echo "<li>stats<ul>
<li>bank portefeuilles: ".$recon->getBankPortefeuilles(false)."</li>
<li>bank rekeningnrs: ".$recon->getBankReknrs(false)."</li>
<li>airs portefeuilles: ".$recon->getAirsPortefeuilles(false)."</li>
<li>airs rekeningnrs: ".$recon->getAirsReknrs(false)."</li>
</ul>
</li>";
    ob_flush();
    flush();
    $recon->findUnmatched();
//    debug($recon->unmatchArray);

    echo " <li> " . $airsOnly . " AIRS rekeningen zonder bankposities</li>";
    echo " <li> afgerond om " . date("H:i:s") . " (= " . round(mktime() - $starttijd, 0) . " sec.) </li>";
    ?>
  </ul>
<?
echo $recon->matchPositions(true);
//debug($recon->trPile);
$recon->trPileToDb();
?>
  <p> <a href="../reconV3Start.php">nieuwe poging inlezen</a></p>
  <p>Ga naar het <a href="../tijdelijkereconList.php">overzicht</a></p>

  <script>
      $("#running").hide(600);
  </script>
<?
echo template("../" . $__appvar["templateRefreshFooter"], $content);

