<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/03/28 12:37:00 $
 		File Versie					: $Revision: 1.6 $

 		$Log: binck_reconImport.php,v $
 		Revision 1.6  2018/03/28 12:37:00  cvs
 		call 3503
 		
 		Revision 1.5  2015/05/08 12:10:28  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2015/03/16 12:40:16  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/11/13 12:33:01  cvs
 		dbs 3118
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
*/

include_once("wwwvars.php");
include_once("binck_reconFuncties.php");


session_start();
$_SESSION[NAV] = ""; 		
//listarray($__appvar);
$error    = array();
$content  = array("title"=>$PHP_SELF);

$filetype = "";



$prb = new ProgressBar();	// create new ProgressBar
//$prb->pedding = 2;	// Bar Pedding
//$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
//$prb->setFrame();          	                // set ProgressBar Frame
//$prb->bgr_color = "#F8F5AD";
//$prb->color = "#808080";
//$prb->frame['width'] = 	400;	                  // Frame position from top
//$prb->frame['heigth'] = 	80;	                  // Frame position from top
//$prb->frame['left'] = 50;	                  // Frame position from left
//$prb->frame['top'] = 	80;	                  // Frame position from top
//$prb->frame['color'] = 	"beige";	                  // Frame position from top
//$prb->addLabel('text','txt1','Inlezen bankbestand ...');	// add Text as Label 'txt1' and value 'Please wait'
//$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'

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

if (!binck_validateFile($_GET["file"]))
{
  listError($error);
  exit;
}
$recon = new reconcilatieClass("BIN",$_GET["manualBoekdatum"]);
$batch = "BINCK_".date("ymd_His");
$recon->batch = $batch;

ob_flush();flush();

echo " <li> depotbank ".$recon->depotbank."</li>";
ob_flush();flush();

$starttijd = mktime();
echo " <li> gestart om ".date("H:i:s")."</li>";
echo " <li> depotbank ".$recon->depotbank."</li>";
ob_flush();flush();
$bankRecords = binck_recon_readBank($_GET["file"]);
ob_flush();flush();
echo " <li> bankbestand bevatte ".$bankRecords." dataregels</li>";
echo " <li> ontbrekende rekeningen erbij zoeken</li>";
ob_flush();flush();
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