<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/01/15 13:42:23 $
 		File Versie					: $Revision: 1.3 $

 		$Log: kbc_reconImport.php,v $
 		Revision 1.3  2020/01/15 13:42:23  cvs
 		call 8152
 		
 		Revision 1.2  2019/10/21 07:19:27  cvs
 		call 8152
 		
 		Revision 1.1  2019/10/07 07:59:28  cvs
 		call 8152
 		
 		Revision 1.1  2018/05/30 14:44:28  cvs
 		call 6851
 		
 		Revision 1.2  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/06/22 08:05:49  cvs
 		*** empty log message ***
 		

 		
*/

include_once("wwwvars.php");
include_once("kbc_reconFuncties.php");


session_start();
$_SESSION["NAV"] = "";
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
$error = array();
validateFile($_GET["file"],"FND");
validateFile($_GET["file2"],"GLD");
if (count($error) > 0)
{
  listError($error);
}

//?><!--  -->
<!--  <script>  $("#running").hide(600); </script>-->
<?//
//  exit;
//}
$recon = new reconcilatieClass("KBC",$_GET["manualBoekdatum"]);
$batch = "KBC".date("ymd_His");
$recon->batch = $batch;



ob_flush();flush();  

echo " <li> depotbank ".$recon->depotbank."</li>";
ob_flush();flush();


$sRecords = recon_readBank($_GET["file"],"FND",true);
echo " <li><b> Positiebestand bevatte ".$sRecords." dataregels</b></li>";
ob_flush();flush();

$cRecords = recon_readBank($_GET["file2"],"GLD",true);
echo " <li><b> Geldbestand bevatte ".$cRecords." dataregels</b></li>";
ob_flush();flush();

echo " <li> ontbrekende rekeningen erbij zoeken</li>";
echo " <li> ".$airsOnly." AIRS rekeningen zonder bankposities</li>";
?>
</ul>
<?
ob_flush();flush();   
if (count($error) > 0)
{
  //debug($error);
}
?>
<p>Ga naar het <a href="../tijdelijkereconList.php">overzicht</a></p>

<script>
  $("#running").hide(600);
</script>

<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>