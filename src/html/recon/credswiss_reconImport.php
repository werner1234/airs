<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.5 $

 		$Log: credswiss_reconImport.php,v $
 		Revision 1.5  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.4  2015/05/08 12:10:28  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2015/03/16 12:40:16  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2014/11/25 07:55:50  cvs
 		dbs 2746
 		
 		Revision 1.2  2014/11/13 12:33:01  cvs
 		dbs 3118
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
*/

include_once("wwwvars.php");
include_once("credswiss_reconFuncties.php");


session_start();
$_SESSION[NAV] = ""; 		
//listarray($__appvar);
$error    = array();
$content  = array("title"=>$PHP_SELF);

$filetype = "";

//debug($_GET);

$prb = new ProgressBar();	// create new ProgressBar
//$prb->pedding = 2;	// Bar Pedding
//$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
//$prb->setFrame();          	                // set ProgressBar Frame
//$prb->bgr_color = "#F8F5AD";
//$prb->color = "#808080";
//$prb->frame['left'] = 50;	                  // Frame position from left
//$prb->frame['top'] = 	80;	                  // Frame position from top
//$prb->frame['width'] = 	550;	                  // Frame position from top
//$prb->frame['heigth'] = 	100;	                  // Frame position from top
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

if (!validateFile($_GET["file"],$_GET["file2"]))
{
  listError($error);
  exit;
}
$recon = new reconcilatieClass("CS",$_GET["manualBoekdatum"]);
$batch = "CS_".date("ymd_His");
$recon->batch = $batch;

ob_flush();flush();  

echo " <li> depotbank ".$recon->depotbank."</li>";
ob_flush();flush();

$cRecords .= recon_readBank($_GET["file2"],"GLD");
echo " <li> bankbestand GLD bevatte ".$cRecords." dataregels</li>";
ob_flush();flush();


$sRecords = recon_readBank($_GET["file"],"FND",true);
echo " <li> bankbestand FND bevatte ".$sRecords." dataregels</li>";
ob_flush();flush();

echo " <li> ontbrekende rekeningen erbij zoeken</li>";
echo " <li> ".$airsOnly." AIRS rekeningen zonder bankposities</li>";
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
?>