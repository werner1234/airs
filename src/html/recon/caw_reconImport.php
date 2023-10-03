<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/02/05 13:57:56 $
 		File Versie					: $Revision: 1.1 $

 		$Log: caw_reconImport.php,v $
 		Revision 1.1  2020/02/05 13:57:56  cvs
 		call 8264
 		
 		Revision 1.1  2019/08/23 12:36:10  cvs
 		call 8025
 		


*/


include_once("wwwvars.php");
include_once("caw_reconFuncties.php");


session_start();
$_SESSION["NAV"] = "";
//listarray($__appvar);
$error    = array();
$content  = array("title"=>$PHP_SELF);

$filetype = "";



$prb = new ProgressBar();	// create new ProgressBar


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

if (!validateFile($_GET["file"]))
{
  listError($error);
  exit;
}

$recon = new reconcilatieClass("CAW",$_GET["manualBoekdatum"]);
$batch = "CAW".date("ymd_His");
$recon->batch = $batch;


?>
<br />
<ul>

<?
$starttijd = mktime();
echo " <li> gestart om ".date("H:i:s")."</li>";
echo " <li> depotbank ".$recon->depotbank."</li>";
ob_flush();flush(); 
$bankRecords = recon_readBank($_GET["file"]);

echo " <li> bankbestand bevatte ".$bankRecords." dataregels</li>";
echo " <li> ontbrekende rekeningen erbij zoeken</li>";

echo " <li> ".$airsOnly." AIRS rekeningen zonder bankposities</li>";
echo " <li> afgerond om ".date("H:i:s")." (= ".round(mktime()-$starttijd,0)." sec.) </li>";
ob_flush();flush(); 
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