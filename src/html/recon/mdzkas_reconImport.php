<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/04/08 07:22:33 $
 		File Versie					: $Revision: 1.2 $

 		$Log: mdzkas_reconImport.php,v $
 		Revision 1.2  2020/04/08 07:22:33  cvs
 		call 7925
 		
 		Revision 1.1  2020/04/06 08:57:12  cvs
 		call 7925
 		
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
include_once("mdzkas_reconFuncties.php");
//debug($_REQUEST);

session_start();
$_SESSION["NAV"] = "";
//listarray($__appvar);
$error    = array();
$reconArray =array();
$content  = array("title"=>$PHP_SELF);
$db = new DB();
$filetype = "";
$bankPositie = array();
$airsPositie = array();

define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once ("../rapport/PDFOverzicht.php");
include_once("../../classes/portefeuilleSelectieClass.php");
include_once ("../rapport/rapportRekenClass.php");

include_once ("../rapport/Geaggregeerdoverzicht.php");
$selectData = array(
  'posted' => 'true',
  'save' => '0',
  'rapport_types' => '',
  'filetype' => 'xls',
  'portefeuilleIntern' => '10',
  'metConsolidatie' => '0',
  'extra' => '',
  'soort' => 'Geaggregeerd Portefeuille Overzicht',
  'datumVan' => mktime(0,0,0,1,1,date("Y")),
  'datumTm' => mktime(),
  'VermogensbeheerderVan' => 'VRY',
  'VermogensbeheerderTm' => 'VRY',


);
$rapport = new Geaggregeerdoverzicht( $selectData );
$rapport->writeRapport();


foreach ($rapport->dbWaarden as $item)
{
  $query = "SELECT * FROM `Fondsen` WHERE `Fonds` = '".$item["Fonds"]."'";
  $fnds = $db->lookupRecordByQuery($query);
//  debug($item);
//  debug($fnds);

  if ($item["Fonds"] == "")
  {
    $item["Fonds"] = "EUR";
    $item["totaalAantal"] = $item["actuelePortefeuilleWaardeEuro"];
  }
  $airsPositie[] = array(
    "isin" => $fnds["ISINCode"],
    "valuta" => $fnds["Valuta"],
    "fonds" => $item["Fonds"],
    "fondsValuta" => $fnds["Valuta"],
    "fondsOmschrijving" => $item["FondsOmschrijving"],
    "aantal" => $item["totaalAantal"]

  );
}
//debug($airsPositie);

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
$batch = "MDZKAS_".date("ymd_His");
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

foreach($bankPositie as $row)
{
//debug($row);
  $idx = ($row["rekening"] != '')?$row["rekening"]:$idx = $row["ISIN"];

  $reconArray[$idx]["AirsAantal"] = 0;
  $row["match"] = "alleen bank";
  $row["bankAantal"] += $reconArray[$idx]["bankAantal"];

  $reconArray[$idx] = $row;
  $reconArray[$idx]["verschil"]  = $row["bankAantal"];
}


foreach ($airsPositie as $item)
{
  $idx = ($item["isin"] == '')?"EUR":$item["isin"];
//  debug($item, $idx);
  if (key_exists($idx, $reconArray))
  {
    $b = $reconArray[$idx];
    $reconArray[$idx]["AirsAantal"] = $item["aantal"];
    $reconArray[$idx]["verschil"]   = $b["bankAantal"] - $item["aantal"];
    $reconArray[$idx]["match"]      = "bank/airs";
//    debug($reconArray[$idx]);
  }
  else
  {
    if ($idx == "EUR")
    {
      $reconArray[$idx]["rekening"] = "0000-0000-0000-000";
      $reconArray[$idx]["valuta"] = "EUR";
      $reconArray[$idx]["type"] = "cash";
    }
    else
    {
      $reconArray[$idx]["ISIN"] = $item["isin"];
      $reconArray[$idx]["fonds"] = $item["fonds"];
      $reconArray[$idx]["valuta"] = $item["valuta"];
      $reconArray[$idx]["type"] = "sec";

    }
    $reconArray[$idx]["depot"]      = "kasbank";
    $reconArray[$idx]["batch"]      = $batch;
    $reconArray[$idx]["AirsAantal"] = $item["aantal"];
    $reconArray[$idx]["verschil"]   = -1 * $item["aantal"];
    $reconArray[$idx]["match"]      = "alleen airs";
  }

  //$idx = ($row["rekening"] != '')?$row["rekening"]:$row["ISIN"];

}
foreach ($reconArray as $rec)
{
  if ($rec["type"] == "cash")
  {
    $cash = 1;
    $rekening = $rec["rekening"].$rec["valuta"];
  }
  else
  {
    $cash = 0;
    $rekening = $rec["rekening"];
  }


  $query = "INSERT INTO tijdelijkeRecon SET 
       `add_user` = '{$USR}'  
      ,`add_date` = NOW()
      ,`change_user` = '{$USR}'
      ,`change_date` = NOW() 
      ,`vermogensbeheerder` = 'VRY'
      ,`depotbank`=  'KAS'
      ,`portefeuille`=  ''
      ,`rekeningnummer` = '{$rekening}'
      ,`client`=  ''
      ,`Einddatum`=  '{$rec["datum"]}'
      ,`reconDatum`=  '".$testDate."'   
      ,`Accountmanager`=  ''
      ,`cashPositie`=  {$cash}
      ,`fonds`=  '{$rec["fonds"]}'
      ,`importCode`= '' 
      ,`fondsImportcode`= '' 
      ,`depotbankFondsCode`=  ''
      ,`fileBankCode`=  ''
      ,`isinCode`= '{$rec["ISIN"]}'
      ,`koers` = ''
      ,`koersDatum` = ''
      ,`valuta`=  '{$rec["valuta"]}'
      ,`positieBank`= '{$rec["bankAantal"]}'
      ,`positieAirs`= '{$rec["AirsAantal"]}'
      ,`verschil`=  '{$rec["verschil"]}'
      ,`fondsCodeMatch`= '{$rec["match"]}' 
      ,`batch`=  '{$rec["batch"]}'";

  $db->executeQuery($query);

}

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