<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2015/12/01 09:03:06 $
 		File Versie					: $Revision: 1.1 $

 		$Log: pictet_reconImport.php,v $
 		Revision 1.1  2015/12/01 09:03:06  cvs
 		update 2540, call 4352
 		
 		Revision 1.2  2015/05/08 12:10:28  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		

*/

/*
 * dataindex
 * 
  0 = PORFOLIO NUMBER 
  1 = VALUATION CURRENCY 
  2 = NOMINAL CURRENCY 
  3 = QUANTITY 
  4 = SECURITY DESCRIPTION 
  5 = ISIN 
  6 = SEDOL 
  7 = TELEKURS 
  8 = CUSIP 
  9 = PICTET SEC. NUMBER 
  10 = MARKET PRICE CURRENCY 
  11 = MARKET PRICE 
  12 = MARKET PRICE CODE 
  13 = MARKET PRICE DATE 
  14 = SECURITY NET COST CCY 
  15 = TOTAL NET COST SEC CCY 
  16 = TOTAL NET COST VAL CCY 
  17 = MARKET VAL WITHOUT ACC. INT. 
  18 = ACCRUED INT. 
  19 = MARKET VAL WITH ACC. INT 
  20 = FX RATE SEC VS. VAL. 
  21 = TYPE CODE 
  22 = FX RATE PRICE VS. VAL. 
  23 = TYPE CODE 
  24 = FINANCIAL INSTR. CODE 
  25 = FIN. INSTR. CODE DESCRIPTION 
  26 = SECC. CURR.RISK CODE 
  27 = COUNTRY RISK CODE 
  28 = GROSS U. COST. SECURITY CCY 
  29 = VALUATION SECURITY CCY 
  30 = VALUATION INT. INCL. SECURITY CCY 
  31 = ACCRUED INT. SECURITY CCY 
  32 = % VAL INT. INCL. 
  33 = UNREALISED IN % SECURITY CCY 
  34 = UNREALISED IN % VALUATION CCY 
  35 = CLASS CODE 
  36 = CLASS CODE TEXT 
  37 = REUTERS KEY CODE 
  38 = BLOOMBERG KEY CODE 
  39 = VALUATION DATE 
  40 = NEXT COUPON PAYMENT DATE 
  41 = LAST COUPON PAYMENT DATE 
  42 = MATURITY DATE 
  43 = MOODY S RATE 
  44 = S&P RATING 
  45 = MODIFIED DURATION 
  46 = NEXT CALL DATE 
  47 = VALUATION INT. INCL. CHF 
  48 = ACRRUED IN. CHF 
  49 = FX RATE SEC.VS CHF 
  50 = TYPE CODE SEC VS CHF 
  51 = CURRENT ACCOUNT KEY 
  52 = FX RATE C/C VS CHF 
  53 = TYPE CO 
*/
include_once("wwwvars.php");
include_once("pictet_reconFuncties.php");


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

if (!validateFile($_GET["file"]))
{
  listError($error);
  exit;
}

$recon = new reconcilatieClass("PIC",$_GET["manualBoekdatum"]);
$batch = "PIC_".date("ymd_His");
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