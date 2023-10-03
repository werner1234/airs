<?
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/08/16 12:45:00 $
 		File Versie					: $Revision: 1.24 $

 		$Log: HEN_dbDump.php,v $
 		Revision 1.24  2019/08/16 12:45:00  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.22  2011/08/11 10:10:52  cvs
 		*** empty log message ***
 		
 		Revision 1.21  2011/08/02 11:09:39  cvs
 		ISIN vervangen door fomdscode
 		
 		Revision 1.20  2011/05/18 12:58:11  cvs
 		*** empty log message ***
 		
 		Revision 1.19  2011/05/05 07:06:30  cvs
 		verbose output
 		
 		Revision 1.18  2011/04/09 14:30:23  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2011/04/08 07:42:35  cvs
 		*** empty log message ***

 		Revision 1.16  2011/03/22 14:42:18  cvs
 		*** empty log message ***

 		Revision 1.15  2011/03/22 14:12:41  cvs
 		*** empty log message ***

 		Revision 1.14  2011/03/18 10:05:45  cvs
 		versie 1 naar HENS

 		Revision 1.13  2011/03/09 16:07:36  cvs
 		*** empty log message ***

 		Revision 1.12  2011/02/25 09:40:15  cvs
 		*** empty log message ***

 		Revision 1.11  2011/02/24 11:32:41  cvs
 		*** empty log message ***

 		Revision 1.10  2011/02/06 14:38:01  cvs
 		*** empty log message ***

 		Revision 1.9  2011/01/20 13:59:38  cvs
 		*** empty log message ***


 		Revision 1.7  2010/11/30 12:59:14  cvs
 		*** empty log message ***




*/


include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_SQLman.php");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/rapportRekenClass.php");
include_once("rapport/PDFRapport.php");
include_once("rapport/include/RapportVOLK_L26.php");
include_once("rapport/include/RapportPERF_L26.php");
include_once("rapport/include/RapportATT_L26.php");

ob_start();


//error_reporting(E_ALL & ~E_NOTICE);
//@ini_set('display_errors',1);
$silent=true;

$startTijd = date("H:i:s");
$ExtraIncludeMessageArray = array();
function inclLog($txt)
{
  global $ExtraIncludeMessageArray;
  $timestamp = date("d-m-Y H:i:s");
  $ExtraIncludeMessageArray[] = $timestamp." :: ".$txt;
  statusUpdate($txt);
}


$db=new DB(5);
$db1=new DB(5);

$tel=0;

function statusUpdate($txt)
{
  global $tel;
  $tel++;
  echo "<br>".date("H:i:s")." / ".$tel.": ".$txt;
  echo str_pad('',4096)."\n";
  ob_flush();flush();
}

statusUpdate("Start verwerking");

$tablesDefined = $db->QRecords("show tables");
if ($_GET["forceCreateDB"] == "1" OR $tablesDefined == 0)
{
  inclLog("forceCreateDB aangeroepen");
  if ($tablesDefined <> 0)
  {
    $db->executeQuery("show tables");
    while ($rec = $db->nextRecord("num"))
    {
      $db1->executeQuery("DROP TABLE `".$rec[0]."` ");
    }
  }

  $queries = explode("#",file_get_contents("HEN_dumpDB.sql"));

  foreach ($queries as $query)
  {
    echo $db->executeQuery($query);
  }
}




//
//  Clienten ophalen
//

statusUpdate("Clienten ophalen");

$db0 = new DB();

$query = "
SELECT
  Portefeuilles.ClientVermogensbeheerder,
  Portefeuilles.Portefeuille,
  Portefeuilles.Startdatum,
  Portefeuilles.Einddatum,
  Portefeuilles.Vermogensbeheerder,
  Portefeuilles.Accountmanager,
  Clienten.Naam,
  Clienten.Naam1,
  Portefeuilles.Risicoklasse
FROM
  Portefeuilles
Inner Join Clienten ON Portefeuilles.Client = Clienten.Client
WHERE
  Portefeuilles.ClientVermogensbeheerder <> '' AND
  Portefeuilles.Vermogensbeheerder = 'HEN'
";

$db0->executeQuery($query);
$db1 = new DB(5);

$db1->executeQuery("truncate Clienten");

while ($dRec = $db0->nextRecord())
{
  $insertQuery = "
  INSERT INTO Clienten set
    add_date = NOW()
  , add_user = 'dump'
  , Clientvermogensbeheerder = '".$dRec["ClientVermogensbeheerder"]."'
  , Portefeuille = '".$dRec["Portefeuille"]."'
  , KlantNaam = '".$dRec["Naam"]."'
  , KlantNaam1 = '".$dRec["Naam1"]."'
  , Risicoklasse = '".$dRec["Risicoklasse"]."'
  , VermogensbeheerOrg = '".$dRec["Vermogensbeheerder"]."'
  , AccountManager = '".$dRec["Accountmanager"]."'
  ";

  if ($db1->executeQuery($insertQuery))
  {
    $portefeuilleArray[$dRec["Portefeuille"]] = $dRec;
    $addOk++;
  }
  else
  {
    inclLog("sqlfout :".mysql_error());
    $addFail++;
  }
}


inclLog("Status clienten succes: $addOk, fail: $addFail");



//
//  ISIN ophalen
//

$db0 = new DB();

$query = "
SELECT
  FondsImportCode,
 Omschrijving
FROM
  Fondsen
";

$db0->executeQuery($query);
$db1 = new DB(5);

$db1->executeQuery("truncate isin");
$addOk = 0;
$addFail = 0;

while ($dRec = $db0->nextRecord())
{
  $insertQuery = "
  INSERT INTO isin set
    add_date = NOW()
  , add_user = 'dump'
  , ISIN = '".mysql_escape_string($dRec["FondsImportCode"])."'
  , Omschrijving = '".mysql_escape_string($dRec["Omschrijving"])."'
  ";

  if ($db1->executeQuery($insertQuery))
  {
    $addOk++;
  }
  else
  {
    inclLog("sqlfout :".mysql_error());
    $addFail++;
  }
}

inclLog("Status ISIN succes: $addOk, fail: $addFail");
//
//  portefeuilleOpbouw  (uit VOLK)
//

$index=new indexHerberekening();
$rapportageDatum=substr(getLaatsteValutadatum(),0,10);
$rapportageStart= substr($rapportageDatum,0,4)."-01-01";
//$datum=$index->getKwartalen(db2jul($rapportageDatumVanaf),db2jul($rapportageDatum));
foreach ($portefeuilleArray as $portefeuille=>$pdata)
{
  $rapportageStart= substr($rapportageDatum,0,4)."-01-01";
  statusUpdate("starten met portefeuille $portefeuille verwerken");
  if (db2jul($pdata["Einddatum"]) < db2jul($rapportageDatum))
  {
    $stopDatum=substr($pdata['Einddatum'],0,10);
    $rapportageStart=substr($stopDatum,0,4)."-01-01";
    statusUpdate("$portefeuille heeft Einddatum: $stopDatum");
  }  
  else
    $stopDatum=$rapportageDatum;
    
  if(db2jul($pdata['Startdatum']) > db2jul($rapportageStart)) //Nodig wanneer getKwartalen niet wordt gebruikt.
    $startDatum=substr($pdata['Startdatum'],0,10);
  else
    $startDatum=$rapportageStart;
  
    

  $datum=array();
  $datum[]=array('start'=>$startDatum,'stop'=>$stopDatum);// Op dit moment nog geen kwartaal stapeling.

  foreach ($datum as $periode)
  {
    if(substr($periode['start'],5,5)=='01-01')
      $startjaar=true;
    else
      $startjaar=false;

    $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$periode['start'],$startjaar,$pdata['RapportageValuta'],$periode['start']);
    vulTijdelijkeTabel($fondswaarden ,$portefeuille,$periode['start']);
    $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$periode['stop'],false,$pdata['RapportageValuta'],$periode['start']);
    vulTijdelijkeTabel($fondswaarden ,$portefeuille,$periode['stop']);
  }

  $pdf = new PDFRapport('L','mm');
  $pdf->rapportageValuta = "EUR";
	$pdf->ValutaKoersEind  = 1;
	$pdf->ValutaKoersStart = 1;
	$pdf->ValutaKoersBegin = 1;
  loadLayoutSettings($pdf, $portefeuille);
  $pdf->PortefeuilleStartdatum=$pdata['Startdatum'];
	$rapport = new RapportVOLK_L26($pdf, $portefeuille, $periode['start'], $periode['stop']);
	$rapport->writeRapport();
  statusUpdate("portefeuille $portefeuille VOLK sectie afgerond");
  $portefeuilleOpbouw[$portefeuille]=$rapport->pdf->excelData;

	$rapport = new RapportPERF_L26($pdf, $portefeuille, $periode['start'], $periode['stop']);
  $rapport->grafiekHistorie=true;
	$rapport->writeRapport();
  statusUpdate("portefeuille $portefeuille PERF sectie afgerond");
  $PerformanceMeting[$portefeuille]=$rapport->pdf->excelData;

  foreach ($datum as $periode)
  {
    verwijderTijdelijkeTabel($portefeuille,$periode['start']);
    verwijderTijdelijkeTabel($portefeuille,$periode['stop']);
  }
  verwijderTijdelijkeTabel($portefeuille);
}

$db1 = new DB(5);
$db1->executeQuery("truncate portefeuilleOpbouw");

$veldenPortefeuilleOpbouw=array('Clientvermogensbeheerder',
                                'Portefeuille',
                                'Soort',
                                'Categorie',
                                'Regio',
                                'Aantal',
                                'Instrument',
                                'ISIN',
                                'Omschrijving',
                                'Valuta',
                                'Begin_koers',
                                'Begin_waarde',
                                'Actueel_koers',
                                'Actueel_waarde',
                                'Koersresultaat',
                                'Valutaresultaat',
                                'Totaalresultaat',
                                'ResultaatPercent',
                                'Actueel_aandeel');
$addOk=0;
$addFail=0;

foreach ($portefeuilleOpbouw as $portefeuille=>$excelData)
{
  foreach ($excelData as $regel=>$waarden)
  {
    $query="INSERT INTO portefeuilleOpbouw SET change_user='$USR', change_date=NOW(),add_user='$USR',add_date=NOW()";
    foreach ($waarden as $index=>$waarde)
      $query.=",".$veldenPortefeuilleOpbouw[$index]."='".addslashes($waarde)."'";

    if ($db1->executeQuery($query))
    {
      $addOk++;
    }
    else
    {
      inclLog("sqlfout :".mysql_error());
      $addFail++;
    }
  }
}

inclLog("Status portefeuilleOpbouw succes: $addOk, fail: $addFail");

//
//  vermogensverloop, performanceMeting, portefeuillePerformance  (uit PERF)
//
$addOk=0;
$addFail=0;

$db1->executeQuery("truncate vermogensverloop");
$db1->executeQuery("truncate performanceMeting");
$db1->executeQuery("truncate portefeuillePerformance");

$veldenvermogensverloop=array('Clientvermogensbeheerder',
                              'Portefeuille',
                              'Startdatum',
                              'Einddatum',
                              'Risicodragend_startdatum',
                              'Risicodragend_einddatum',
                              'Risicodragend_onttrekkingen_stortingen',
                              'Risicodragend_gemiddeld_vermogen',
                              'Risicodragend_bruto_resultaat',
                              'Risicodragend_bruto_rendement',
                              'Risicodragend_weging',
                              'Risicodragend_bijdrage_rendement',
                              'Risicomijdend_startdatum',
                              'Risicomijdend_einddatum',
                              'Risicomijdend_onttrekkingen_stortingen',
                              'Risicomijdend_gemiddeld_vermogen',
                              'Risicomijdend_bruto_resultaat',
                              'Risicomijdend_bruto_rendement',
                              'Risicomijdend_weging',
                              'Risicomijdend_bijdrage_rendement',
                              'Liquiditeiten_startdatum',
                              'Liquiditeiten_einddatum',
                              'Liquiditeiten_onttrekkingen_stortingen',
                              'Liquiditeiten_gemiddeld_vermogen',
                              'Liquiditeiten_bruto_resultaat',
                              'Liquiditeiten_bruto_rendement',
                              'Liquiditeiten_weging',
                              'Liquiditeiten_bijdrage_rendement',
                              'Totaal_startdatum',
                              'Totaal_einddatum',
                              'Onttrekkingen_stortingen_totaal',
                              'Gemiddeld_vermogen_totaal',
                              'Bruto_resultaat_totaal',
                              'Totaal_bruto_rendement',
                              'Totaal_bijdrage_rendement',
                              'Niet_toe_te_rekenen_kosten',
                              'Netto_resultaat_totaal',
                              'Rendement_totaal',
                              'Rendement_benchmark');



$veldenPerformanceMeting=array('Clientvermogensbeheerder',
                               'Portefeuille',
                               'KoersresultaatVerkopen',
                               'ValutaresultaatVerkopen',
                               'Uitkeringen',
                               'Belastingen',
                               'Rente',
                               'TotaalGerealiseerd',
                               'KoersresultaatPositie',
                               'ValutaresultaatPositie',
                               'TotaalOngerealiseerd',
                               'Valutaresultaat',
                               'TotaalOverig',
                               'TransactieKosten',
                               'Beheerloon',
                               'Bewaarloon',
                               'Performancefee',
                               'Bankkosten',
                               'RestitutieRetrocessie',
                               'TotaalKosten',
                               'TotaalResultaatInEuro');

$veldenPortefeuillePerformance=array('Clientvermogensbeheerder',
                                     'Portefeuille',
                                     'Datum',
                                     'PortefeuilleIndex',
                                     'IndexKoers');

foreach ($PerformanceMeting as $portefeuille=>$excelData)
{
  foreach ($excelData as $regel=>$waarden)
  {
    if($regel==0)
      $table='vermogensverloop';
    elseif($regel==1)
      $table='performanceMeting';
    else
      $table='portefeuillePerformance';

    $query="INSERT INTO $table SET change_user='$USR', change_date=NOW(),add_user='$USR',add_date=NOW()";


    $tmp =array();
    $rekenVelden = array('Onttrekkingen_stortingen_totaal',
                         'Niet_toe_te_rekenen_kosten',
                         'Gemiddeld_vermogen_totaal',
                         'Bruto_resultaat_totaal');

    foreach ($waarden as $index=>$waarde)
    {
      if($regel==0)
      {
        $query.=",".$veldenvermogensverloop[$index]."='".addslashes($waarde)."'\n";
        if ( in_array($veldenvermogensverloop[$index], $rekenVelden) )
           $tmp[$veldenvermogensverloop[$index]] = $waarde;
      }
      elseif($regel==1)
        $query.=",".$veldenPerformanceMeting[$index]."='".addslashes($waarde)."'\n";
      else
        $query.=",".$veldenPortefeuillePerformance[$index]."='".addslashes($waarde)."'\n";
    }

    if ($regel==0)  // berekende velden
    {
      $nettoStortingenEnOntrekkingen = $tmp['Onttrekkingen_stortingen_totaal'] + ($tmp['Niet_toe_te_rekenen_kosten'] * -1);
      $query.=",Netto_stortingenEnOntrekkingen = '".$nettoStortingenEnOntrekkingen."'\n";

      $nettoResultaat = $tmp['Bruto_resultaat_totaal'] + $tmp['Niet_toe_te_rekenen_kosten'];
      $query.=",Netto_resultaat = '".$nettoResultaat."'\n";

      $netto_netto_rendement = ($nettoResultaat/$tmp['Gemiddeld_vermogen_totaal']) * 100;
      $query.=",Netto_netto_rendement = '".$netto_netto_rendement."'\n";
    }

    if ($db1->executeQuery($query))
    {
      $addOk++;
    }
    else
    {
      inclLog("sqlfout($regel) :".mysql_error());
      $addFail++;
    }
   // echo "\n<br> $regel \n $query <br>\n";
  }
}
inclLog("Status succes: $addOk, fail: $addFail");

inclLog("Einde script. Gestart om ".$startTijd);




listarray($ExtraIncludeMessageArray);

?>