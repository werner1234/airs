<?php
/*
    AE-ICT sourcemodule created 19 mrt. 2021
    Author              : Chris van Santen
    Filename            : api_TRANS.php


*/

$error = array();
$portefeuille = $__ses["data"]["portefeuille"];
if ($__ses["data"]["rapportDatum"])
{
  $data["datum_tot"] = $__ses["data"]["rapportDatum"];
}
if ($__ses["data"]["startDatum"])
{
  $data["datum_van"] = $__ses["data"]["startDatum"];
}

$USR = "api_".rand(111111,999999); // param portaal
$sessionId = rand(15,100);   // AIRS gebruikers hebben 0-10  // param portaal
$__appvar['TijdelijkeRapportageMaakUniek'] = " AND TijdelijkeRapportage.add_user = '".$USR."' AND TijdelijkeRapportage.sessionId = '".$sessionId."' ";

include_once($__appvar["basedir"]."/html/rapport/rapportRekenClass.php");
include_once($__appvar["basedir"]."/classes/AE_cls_htmlColomns.php");
include_once($__appvar["basedir"]."/classes/htmlReports/htmlTRANS.php");

$db = new DB();
$portRec = $db->lookupRecordByQuery("SELECT * FROM `Portefeuilles` WHERE Portefeuille = '$portefeuille'");
$rapportageValuta = ($portRec["RapportageValuta"] <> "")?$portRec["RapportageValuta"]:"EUR";

if ($data["datum_van"] == "portStart")
{
  $data["datum_van"] = $portRec["Startdatum"];
}

if(trim($data["datum_tot"]) == "")
{
  $d = explode("-",substr(getLaatsteValutadatum(),0,10));

  $data["datum_tot"] = substr(getLaatsteValutadatum(),0,10);
}
if (trim($data["datum_van"]) == "" AND trim($data["datum_tot"]) !== "" )
{
  $d = explode("-",substr($data["datum_tot"],0,10));
  $data["datum_van"] = "01-01-".$d[2];
}

$rapportStart = $data["datum_van"];
$rapportDatum = $data["datum_tot"];

$trns = new htmlTRANS($portefeuille);
$trns->initModule();
$trns->clearTable();
$transactieTypes = array();
$query = "SELECT * FROM Transactietypes ORDER BY Transactietype";
$db->executeQuery($query);
while ($tt = $db->nextRecord())
{
  $transactieTypes[$tt["Transactietype"]] = $tt["Omschrijving"];
}

$query = "
  SELECT 
    Fondsen.Omschrijving,
    Fondsen.Fondseenheid, 
    Fondsen.Fonds,
    Rekeningmutaties.Boekdatum, 
    Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		Rekeningmutaties.Afschriftnummer,
    Rekeningmutaties.omschrijving as rekeningOmschrijving,
		Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  
    Rekeningmutaties.Fondskoers, 
    Rekeningmutaties.Debet as Debet, 
    Rekeningmutaties.Credit as Credit, 
    Rekeningmutaties.Valutakoers
    
  FROM 
    Rekeningmutaties, 
    Fondsen, 
    Rekeningen, 
    Portefeuilles, 
    Grootboekrekeningen
  WHERE
    Rekeningmutaties.Rekening = Rekeningen.Rekening AND 
    Rekeningmutaties.Fonds = Fondsen.Fonds AND 
    Rekeningen.Portefeuille = '".$portefeuille."' AND 
    Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND 
    Rekeningmutaties.Verwerkt = '1' AND 
    Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND 
    Rekeningmutaties.Transactietype <> 'B' AND 
    Grootboekrekeningen.FondsAanVerkoop = '1' AND 
    Rekeningmutaties.Boekdatum > '".$rapportStart."' AND 
    Rekeningmutaties.Boekdatum <= '".$rapportDatum."' 
  ORDER BY 
    Rekeningmutaties.Boekdatum, 
    Rekeningmutaties.Fonds, 
    Rekeningmutaties.id
  ";

$db->executeQuery($query);
while ($mutaties = $db->nextRecord())
{

  $historie = berekenHistorischKostprijs($portefeuille, $mutaties["Fonds"], $mutaties["Boekdatum"], $rapportageValuta,$rapportStart);
//    debug($mutaties);
//    debug($historie);
  $v = array();

  $v["datum"] = $mutaties["Boekdatum"];
  $v['transactietype'] = $mutaties["Transactietype"];
  $v['aantal'] = abs($mutaties["Aantal"]);
  $v['fonds'] = $mutaties["Fonds"];
  $v['fondsOmschrijving'] = $mutaties["Omschrijving"];
  $v['portefeuille'] = $portefeuille;

  $aankoop_koers            = "";
  $aankoop_waardeinValuta   = "";
  $aankoop_waarde           = "";
  $verkoop_koers            = "";
  $verkoop_waardeinValuta   = "";
  $verkoop_waarde           = "";
  $historisch_kostprijs     = "";
  $resultaat_voorgaande     = "";
  $resultaat_lopendeProcent = "";
  $resultaatlopende         = 0 ;

  $t_aankoop_koers          = 0;
  $t_aankoop_waardeinValuta = 0;
  $t_aankoop_waarde         = 0;
  $t_verkoop_koers          = 0;
  $t_verkoop_waardeinValuta = 0;
  $t_verkoop_waarde         = 0;

  $historischekostprijs = $v['aantal']  * $historie["historischeWaarde"]       * $historie["historischeValutakoers"]        * $mutaties["Fondseenheid"];
  $beginditjaar         = $v['aantal']  * $historie["beginwaardeLopendeJaar"]  * $historie["beginwaardeValutaLopendeJaar"]  * $mutaties["Fondseenheid"];

  switch($mutaties["Transactietype"])
  {
    case "A/S" :
      $historischekostprijs = (-1 * $v['aantal'])  * $historie["historischeWaarde"]       * $historie["historischeValutakoers"]        * $mutaties["Fondseenheid"];
      $beginditjaar         = (-1 * $v['aantal'])  * $historie["beginwaardeLopendeJaar"]  * $historie["beginwaardeValutaLopendeJaar"]  * $mutaties["Fondseenheid"];
    // geen break nodig hier!!
    case "A" :
    case "A/O" :

      $t_aankoop_waarde = abs($mutaties["Debet"]) * $mutaties["Valutakoers"] * $mutaties['Rapportagekoers'];
      $t_aankoop_waardeinValuta = abs($mutaties["Debet"]);
      $t_aankoop_koers = $mutaties["Fondskoers"];

      if ($t_aankoop_waarde > 0)
      {
        $aankoop_koers = $t_aankoop_koers;
      }

      if ($t_aankoop_waardeinValuta > 0)
      {
        $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
      }

      if ($t_aankoop_koers > 0)
      {
        $aankoop_waarde = $t_aankoop_waarde;
      }

      break;
    case "B" :
      // Beginstorting
      break;
    case "D" :
    case "S" :
      // Deponering
      $t_aankoop_waarde = abs($mutaties["Debet"]) * $mutaties["Valutakoers"] * $mutaties['Rapportagekoers'];
      $t_aankoop_waardeinValuta = abs($mutaties["Debet"]);
      $t_aankoop_koers = $mutaties["Fondskoers"];

      if ($t_aankoop_waarde > 0)
      {
        $aankoop_koers = $t_aankoop_koers;
      }

      if ($t_aankoop_waardeinValuta > 0)
      {
        $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
      }

      if ($t_aankoop_waarde > 0)
      {
        $aankoop_waarde	= $t_aankoop_waarde;
      }

      break;
    case "L" :
      // Lichting
      $t_verkoop_waarde 				= abs($mutaties["Credit"]) * $mutaties["Valutakoers"] * $mutaties['Rapportagekoers'];
      $t_verkoop_waardeinValuta = abs($mutaties["Credit"]);
      $t_verkoop_koers					= $mutaties["Fondskoers"];

      if ($t_verkoop_koers > 0)
      {
        $verkoop_koers 					= $t_verkoop_koers;
      }

      if ($t_verkoop_waardeinValuta > 0)
      {
        $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
      }

      if ($t_verkoop_waarde > 0)
      {
        $verkoop_waarde = $t_verkoop_waarde;
      }

      break;
    case "V" :
    case "V/O" :
    case "V/S" :
      $t_verkoop_waarde 				= abs($mutaties["Credit"]) * $mutaties["Valutakoers"] * $mutaties['Rapportagekoers'];
      $t_verkoop_waardeinValuta = abs($mutaties["Credit"]);
      $t_verkoop_koers					= $mutaties["Fondskoers"];

      if ($t_verkoop_koers > 0)
      {
        $verkoop_koers = $t_verkoop_koers;
      }

      if ($t_verkoop_waardeinValuta > 0)
      {
        $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
      }

      if ($t_verkoop_waarde > 0)
      {
        $verkoop_waarde	= $t_verkoop_waarde;
      }
      break;
    default :
      $_error = "Fout ongeldig tranactietype!!";
      break;
  }

  if($historie["voorgaandejarenActief"] == 0)
  {
    $resultaatvoorgaande = 0;
    $resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
    if($mutaties['Transactietype'] == "A/S")
    {
      $resultaatvoorgaande = 0;
      $resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
    }
  }
  else
  {
    $resultaatvoorgaande = $beginditjaar - $historischekostprijs;
    $resultaatlopende = $t_verkoop_waarde - $beginditjaar;
  }

  if ($t_aankoop_waardeinValuta <> 0)
  {
    $v['aankoopKoersValuta'] = $t_aankoop_koers;
    $v['aankoopWaardeValuta'] = $t_aankoop_waardeinValuta;
    $v['aankoopWaardeEur'] = $t_aankoop_waardeinValuta * $mutaties["Valutakoers"];
  }
  else
  {

    $v['verkoopKoersValuta'] = $verkoop_koers;
    $v['verkoopWaardeValuta'] = $verkoop_waardeinValuta;
    $v['verkoopWaardeEur'] = $verkoop_waardeinValuta * $mutaties["Valutakoers"];
    $v['historischeKostprijsEur'] = $historischekostprijs;
    $v['resultaatvoorgaandEur'] = $resultaatvoorgaande;
    $v['resultaatgedurendEur'] = ($resultaatlopende + ($verkoop_waardeinValuta * $mutaties["Valutakoers"]));

    //$percentageTotaal = ABS(($v['resultaatgedurendEur']/ ($v['resultaatvoorgaandEur'] + $v['historischeKostprijsEur'])) *100);
    $percentageTotaal = ($v["resultaatgedurendEur"]/($v["resultaatvoorgaandEur"] + $v["historischeKostprijsEur"]))*100;
    $v['resultaatPercent'] = $percentageTotaal;
  }

  $trns->addRecord($v);
}


$query = "
  SELECT 
    * 
  FROM 
    `_htmlRapport_TRANS` 
  WHERE 
    portefeuille='$portefeuille' AND 
    add_user='$USR' 
  ORDER BY 
    datum";

$db->executeQuery($query);

$dataSet = array();
$notEncodeArray = array(
  "change_user",
  "change_date",
  "add_user",
  "add_date",
  "id",
);

while($rec = $db->nextRecord())
{
  $data = array();
  foreach($rec as $k=>$v)
  {
    if (in_array($k, $notEncodeArray))
    {
      $data[$k] = $v;
    }
    else
    {
      $data[$k] = utf8_encode($v);
    }

  }

  $dataSet[] = $data;


}
//ksort($dataSet);


$query = "DELETE FROM `_htmlRapport_TRANS` WHERE portefeuille='$portefeuille' AND add_user='$USR'";
$db->executeQuery($query);

$output = array();
$output["statics"] = array(
  "portefeuille"    => $portefeuille,
  "client"          => $portRec["Client"],
  "start"           => $rapportStart,
  "stop"            => $rapportDatum,
  "rapportType"     => "TRANS",
  "transactieTypes" => $transactieTypes
);

$output["data"] = $dataSet;