<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2016/05/30 08:00:44 $
 		File Versie					: $Revision: 1.7 $

 		$Log: kasbank_reconImport.php,v $
 		Revision 1.7  2016/05/30 08:00:44  cvs
 		call 4848: derde bestand Kasbankl
 		
 		Revision 1.6  2015/05/08 12:10:28  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2015/03/26 09:47:00  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2015/03/16 12:40:16  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/11/13 12:33:01  cvs
 		dbs 3118
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
*/

include_once("wwwvars.php");
include_once("kasbank_reconFuncties.php");


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

if (!validateFile($_GET["file"],$_GET["file2"]))
{
  listError($error);
  exit;
}

$recon = new reconcilatieClass("KAS",$_GET["manualBoekdatum"]);
$batch = "KAS_".date("ymd_His");
$recon->batch = $batch;


?>
<br />
<?
ob_flush();flush();

echo " <li> depotbank ".$recon->depotbank."</li>";
ob_flush();flush();
?>
<?

$cRecords .= recon_readBank($_GET["file2"],"GLD");
echo " <li> bankbestand GLD bevatte ".$cRecords." dataregels</li>";
ob_flush();flush();

if (trim($_GET["file3"]) <> "")
{
  $sRecords = recon_readBank($_GET["file3"],"POS",true);
  echo " <li> bankbestand OPT bevatte ".$sRecords." dataregels ($dubbelPos dubbele overgeslagen)</li>";
  ob_flush();flush();
}

$sRecords = recon_readBank($_GET["file"],"FND",false);
echo " <li> bankbestand FND bevatte ".$sRecords." dataregels</li>";
ob_flush();flush();





echo " <li> ontbrekende rekeningen erbij zoeken</li>";
echo " <li> ".$airsOnly." AIRS rekeningen zonder bankposities</li>";
echo " <li> afgerond om ".date("H:i:s")." (= ".round(mktime()-$starttijd,0)." sec.) </li>"; 
?>
</ul>
<?
ob_flush();flush();
?>
<p>Ga naar het <a href="../tijdelijkereconList.php">overzicht</a></p>

<script>
  $("#running").hide(600);
</script>
 
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>