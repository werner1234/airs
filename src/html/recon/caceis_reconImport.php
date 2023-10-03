<?php
/*
    AE-ICT sourcemodule created 31 mrt. 2021
    Author              : Chris van Santen
    Filename            : caceis_reconImport.php


*/

include_once("wwwvars.php");
include_once("caceis_reconFuncties.php");


session_start();
$_SESSION["NAV"] = "";
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


if (!validateFile($_GET["file"]))
{
  listError($error);
  exit;
}


//$handle = fopen($file, "r");
//$data = fgetcsv($handle, 8192, ";");
//$i = array();
//$idx =0;
//foreach ($data as $key)
//{
//  $i[$key] = $idx;
//  $idx++;
//}
//debug($i,"veldmapping");

//fclose($handle);

$recon = new reconcilatieClass("KAS",$_GET["manualBoekdatum"]);
$batch = "KAS_".date("ymd_His");
$recon->batch = $batch;


?>
<br />
<ul>

<?
$starttijd = mktime();
echo " <li> gestart om ".date("H:i:s")."</li>";
echo " <li> depotbank ".$recon->depotbank."</li>";
ob_flush();flush(); 
$bankRecords = recon_readBank($_GET["file"],"FND",true);

echo " <li> bankbestand HOLD bevatte ".$bankRecords." dataregels</li>";

if (trim($_GET["file3"]) <> "")
{
  $sRecords = recon_readBank($_GET["file3"],"POS",true);
  echo " <li> bankbestand OPT bevatte ".$sRecords." dataregels ($dubbelPos dubbele overgeslagen)</li>";
  ob_flush();flush();
}


$cRecords .= recon_readBank($_GET["file2"],"GLD");

echo " <li> bankbestand BAL bevatte ".$cRecords." dataregels</li>";
echo " <li> ontbrekende rekeningen erbij zoeken</li>";
$recon->fillTableFormAIRS();
echo "<li>AIRS portefeuilles ophalen ($filetype)";
ob_flush();flush();
$airsOnly = $recon->getAirsPortefeuilles();
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