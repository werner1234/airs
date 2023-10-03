<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/09/20 06:20:23 $
 		File Versie					: $Revision: 1.1 $

 		$Log: ubs_reconImport.php,v $
 		Revision 1.1  2017/09/20 06:20:23  cvs
 		megaupdate 2722
 		

 		
*/
include_once("wwwvars.php");
include_once("ubs_reconFuncties.php");


session_start();
$_SESSION[NAV] = ""; 		
//listarray($__appvar);
$error    = array();
$content  = array("title"=>$PHP_SELF);

$filetype = "";




$content['jsincludes'] .= '<link rel="stylesheet" href="../style/smoothness/jquery-ui-1.11.1.custom.css">';
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"../javascript/jquery-min.js\"></script>";
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"../javascript/jquery-ui-min.js\"></script>";
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
//debug($_GET);
if (!validateFile($_GET["file"],$_GET["file2"]))
{
  listError($error);
  exit;
}

$recon = new reconcilatieClass("UBS",$_GET["manualBoekdatum"]);
$batch = "UBS_".date("ymd_His");
$recon->batch = $batch;


ob_flush();flush();   
$starttijd = mktime();
echo " <li> gestart om ".date("H:i:s")."</li>";
echo " <li> depotbank ".$recon->depotbank."</li>";

echo " <li> stukken bestand inlezen</li>";
$frecords = recon_readBank($_GET["file"],"FND");

echo " <li> geld bestand inlezen</li>";
$crecords = recon_readBank($_GET["file2"],"CASH");

echo " <li> bankbestand bevatte ".$bankRecords." dataregels</li>";
echo " <li> ontbrekende rekeningen erbij zoeken</li>";

echo " <li> ".$airsOnly." AIRS rekeningen zonder bankposities</li>";
echo " <li> afgerond om ".date("H:i:s")." (= ".round(mktime()-$starttijd,0)." sec.) </li>";
?>
</ul>
<?
?>
<p>Ga naar het <a href="../tijdelijkereconList.php">overzicht</a></p>
<script>
  $("#running").hide(600);
</script>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>