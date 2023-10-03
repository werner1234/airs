<?php
/*
    AE-ICT sourcemodule created 21 apr. 2021
    Author              : Chris van Santen
    Filename            : _template_reconImport.php


*/

///////////////////////////////////////////////////////////////////////////////
///
/// TEMPLATE file voor bankimport, dit bestand niet aanpassen
/// maar opslaan als html/recon/FILEPREFIX_reconImport.php
///
///////////////////////////////////////////////////////////////////////////////
// settings voor import
$set = array(
  "banknaam"        => "Index People",              //  volledige banknaam
  "depot"           => "IND",                       //  depotbankcode v/d bank
  "filePrefix"      => "ip",                        //  fileprefix
  "fileDelimit"     => ",",                         //  CSV delimter
  "decimalSign"     => ".",                         //  decimaalteken in getallen
  "thousandSign"    => "",                         //  duizend scheidingsteken
  "headerRow"       => true,                        //  is de eerste regel een header?
//  "transactieCodes" => "cawTransactieCodes",        //  tabelnaam van de transactiecodes
  "bankCode"        => "Bucketcode"                 //  veldnaam bankcode in de Fondsentabel
);

// todo in html/reconSelectDepotbank.php
// aanmaken van een bankkeuze voor nieuwe partij

// todo in html/recon/PREFIX_reconFuncties.php
// ------------------------------------------------------------------------------------------------
//  CASH en SEC datamapping
//  validateFile() validatie van bestand
//
// in classes/reconcilatieClass.php
//  function findReconRow(), uitbreiden met zoekfunctie voor nieuwe bank
//


include_once("wwwvars.php");
include_once("{$set["filePrefix"]}_reconFuncties.php");


session_start();
$_SESSION["NAV"] = "";
//listarray($__appvar);
$error    = array();
$content  = array("title"=>$PHP_SELF);

$filetype = "";

$airsOnly = "";
//debug($_GET);

$prb = new ProgressBar();	// create new ProgressBar

$content['jsincludes'] = '
  <link rel="stylesheet" href="../style/smoothness/jquery-ui-1.11.1.custom.css">
  <script type="text/javascript" src="../javascript/jquery-min.js"></script>
  <script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>
  ';
echo template("../".$__appvar["templateContentHeader"],$content);
?>
<div id="running">
  <h2> Reconciliatie, moment a.u.b. </h2>
  <img src="../images/loading.gif" alt=""/>
</div>
<br />

<ul>
<?
$starttijd = mktime();
echo " <li> gestart om ".date("H:i:s")."</li>";
ob_flush();flush();
$error = array();
validateFile($_GET["file"]);

if (count($error) > 0)
{
  listError($error);
}

$recon = new reconcilatieClass($set["depot"],$_GET["manualBoekdatum"]);
$batch = $set["depot"].date("ymd_His");
$recon->batch = $batch;

ob_flush();flush();  

echo " <li> depotbank {$recon->depotbank}</li>";
ob_flush();flush();


$sRecords = recon_readBank($_GET["file"],true);
echo " <li><b> Positiebestand bevatte {$sRecords} dataregels</b></li>";
ob_flush();flush();

echo " <li> ontbrekende rekeningen erbij zoeken</li>";
echo " <li> {$airsOnly} AIRS rekeningen zonder bankposities</li>";
?>
</ul>
<?
ob_flush();flush();   
if (count($error) > 0)
{
  debug($error);
}
?>
<p>Ga naar het <a href="../tijdelijkereconList.php">overzicht</a></p>

<script>
  $("#running").hide(600);
</script>

<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
