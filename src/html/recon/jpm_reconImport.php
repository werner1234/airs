<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/01/15 13:42:23 $
 		File Versie					: $Revision: 1.3 $

 		$Log: jpm_reconImport.php,v $


*/

include_once("wwwvars.php");
include_once("jpm_reconFuncties.php");


session_start();
$_SESSION["NAV"] = "";
//listarray($__appvar);
$error    = array();
$content  = array("title"=>$PHP_SELF);

$filetype = "";

//debug($_GET);

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
    $error = array();
    validateFile($_GET["file"]);

    if (count($error) > 0)
    {
      listError($error);
    }

    //?><!--  -->
    <!--  <script>  $("#running").hide(600); </script>-->
    <?//
    //  exit;
    //}
    $recon = new reconcilatieClass("JPM",$_GET["manualBoekdatum"]);
    $batch = "JPM".date("ymd_His");
    $recon->batch = $batch;



    ob_flush();flush();

    echo " <li> depotbank ".$recon->depotbank."</li>";
    ob_flush();flush();


    $sRecords = recon_readBank($_GET["file"],true);
    echo " <li><b> Positiebestand bevatte ".$sRecords." dataregels</b></li>";
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

