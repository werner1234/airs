<?php

include_once("wwwvars.php");
include_once("rapport/rapportRekenClass.php");
$data = array_merge($_POST,$_GET);

$rapportArray = array('ATT','VOLK','TRANS','MUT',"MODEL");
$rapportValues =array();
$portefeuille = $data['Portefeuille'];

$aeMessage = new AE_Message();

$redirect = ($data["redirect"] != "no");
$redirect = true;
/** Tot datum omzetten naar form datum wanneer deze een db datum is */
$dateTotcheck = explode('-', $data['datum_tot']);
if ( strlen($dateTotcheck[0]) === 4 ) {
	$data['datum_tot'] = dbdate2form($data['datum_tot']);
}

/** wanneer er een eind datum maar geen start datum aanwezig is moet de start datum op 1-1 van het eind jaar terecht komen */
if (trim($data["datum_van"]) == "" && trim($data["datum_tot"]) !== "" ) {
	$d = explode("-",substr($data["datum_tot"],0,10));
	$data["datum_van"] = "01-01-".$d[2];
}

/* Wanneer er geen eind datum is deze invullen */
if(trim($data["datum_tot"]) == "")
{
	$d = explode("-",substr(getLaatsteValutadatum(),0,10));
	$data["datum_tot"] = $d[2]."-".$d[1]."-".$d[0];
}

/* start datum omzetten van een db datum naar een form datum */
$dateTotcheck = explode('-', $data['datum_van']);
if ( strlen($dateTotcheck[0]) === 4 ) {
	$data['datum_van'] = dbdate2form($data['datum_van']);
}

/* wanneer de start datum leeg is deze vullen */
if (trim($data["datum_van"]) == "")
{
	$d = explode("-",substr($data["datum_tot"],0,10));
	$data["datum_van"] = "01-01-".$d[2];
//	$data["datum_van"] = "01-01-".date("Y");
}


$rapportDatum = formdate2db($data['datum_tot']);
$rapportStart = formdate2db($data["datum_van"]);

if($data['consolidatie']==1)
{
  $consolidatie=consolidatieAanmaken($data,$rapportStart,$rapportDatum);
  $portefeuille=$consolidatie['portefeuille'];
  $rapportStart=$consolidatie['rapportageStart'];
  $rapportDatum=$consolidatie['rapportageEind'];
  $portefeuilles=$consolidatie['portefeuilles'];
}


$exit = false;
$rap = (substr($data["rapport_types"],0,1) == "|")?substr($data["rapport_types"],1):$data["rapport_types"];
$rap = explode("|",$rap);
if(empty($portefeuille))
{
//	echo "<li>Fout: geen portefeuille opgegeven </li>";
  $aeMessage->setFlash('Fout: geen portefeuille opgegeven', 'info');
  header('Location: ' . $_SERVER['HTTP_REFERER']);
  $exit = true;
}
if (count($rap) <> 1)
{
//	echo "<li>U kunt maar één rapportsoort selecteren</li>";
  $aeMessage->setFlash('Fout: U kunt maar één rapportsoort selecteren', 'info');
  header('Location: ' . $_SERVER['HTTP_REFERER']);
	$exit = true;
}

if (!in_array($rap[0], $rapportArray))
{
//	echo "HTML rapportages alleen voor <br/><li>".implode("<li>", $rapportArray);
  $aeMessage->setFlash("HTML rapportages alleen voor <br/><li>".implode("<li>", $rapportArray));
  header('Location: ' . $_SERVER['HTTP_REFERER']);
	$exit = true;
}
if ($exit)
{
	exit();
}
if ($rap[0] == "MODEL")
{
  $rapportDatum = getLaatsteValutadatum();
  $rapportDatum = date('Y-m-d', strtotime($rapportDatum));
  $einddatum = $rapportDatum;

  $_SESSION["htmlRapportVars"] = array();
  unset($_SESSION["htmlVOLK"]);
  include_once("../classes/AE_cls_htmlColomns.php");
  include_once("../classes/htmlReports/htmlMODEL.php");
  include_once("rapport/rapportRekenClass.php");
  include_once("rapport/RapportMODEL.php");

  $db = new DB();

  $mdl = new htmlMODEL($portefeuille);
  $mdl->initModule();
  $mdl->clearTable();

  $q = "
   SELECT 
    Portefeuilles.Portefeuille, 
    Portefeuilles.Startdatum,
    Portefeuilles.Client, 
    Portefeuilles.Depotbank, 
    Portefeuilles.Risicoklasse, 
    Portefeuilles.Vermogensbeheerder, 
    Portefeuilles.ModelPortefeuille, 
    Clienten.Naam  
    FROM 
      (Portefeuilles, Clienten) 
    WHERE 
      Portefeuilles.Client = Clienten.Client AND 
      Portefeuilles.Portefeuille = '".$portefeuille."'";

  $stamgegevens = array(
    "datum"        => $einddatum,
    "portefeuille" => $portefeuille,

  );
  $db->executeQuery($q);
  $records = $db->records();
  while($portefeuille = $db->NextRecord())
  {
    $mPortefeuille=$portefeuille['ModelPortefeuille'];
    $stamgegevens["modelPortefeuille"] = $mPortefeuille;
    $dblk = new DB();
    $query="SELECT Fixed, Beleggingscategorie FROM ModelPortefeuilles WHERE Portefeuille='".$mPortefeuille."'";
    $modelType = $dblk->lookupRecordByQuery($query);

    if($modelType['Fixed']==1)
    {
      $portefeuilleData = berekenFixedModelPortefeuille($mPortefeuille,$einddatum); 
    }
    elseif($modelType['Fixed']==3)
    {
      $portefeuilleData = berekenMeervoudigeModelPortefeuille($portefeuille["Portefeuille"],$einddatum,$mPortefeuille);
    }
    else
    {
      $portefeuilleData = berekenPortefeuilleWaarde($mPortefeuille, $einddatum);
    }

    vulTijdelijkeTabel($portefeuilleData,"m".$mPortefeuille,$einddatum);
    $portefeuilleData = berekenPortefeuilleWaarde($portefeuille["Portefeuille"], $einddatum);
    vulTijdelijkeTabel($portefeuilleData,$portefeuille["Portefeuille"],$einddatum);
    $uitsluitingen=bepaalModelUitsluitingen($portefeuille['Portefeuille'],$einddatum);

    if($modelType['Beleggingscategorie'] <> '')
    {
      $extraCategorieFilter=" AND TijdelijkeRapportage.Beleggingscategorie='".$modelType['Beleggingscategorie']."' ";
    }
    // bereken totaal waarde model
    $query = "
      SELECT 
        SUM(actuelePortefeuilleWaardeEuro) AS totaal 
      FROM 
        TijdelijkeRapportage 
      WHERE 
        rapportageDatum ='".$einddatum."' AND 
        portefeuille = '"."m".$portefeuille['ModelPortefeuille']."' AND 
        `type` <> 'rente' 
        $extraCategorieFilter ".$__appvar['TijdelijkeRapportageMaakUniek'];

    $modelwaarde = $dblk->lookupRecordByQuery($query);
//    debug($modelwaarde,$query);
    $modelTotaal = $modelwaarde['totaal'];
    $stamgegevens["modelTotaal"] = $modelTotaal;

    if($mPortefeuille=='' || $modelTotaal==0)
    {
      $aeMessage = new AE_Message();
      $aeMessage->setFlash("Modelvergelijking voor portefeuille ".$portefeuille["Portefeuille"]." gestopt, geen waarden voor modelportefeuille '".$mPortefeuille."' gevonden.", 'info');
      header('Location: ' . $_SERVER['HTTP_REFERER']);

      logScherm("Modelvergelijking voor portefeuille ".$portefeuille["Portefeuille"]." gestopt, geen waarden voor modelportefeuille '".$mPortefeuille."' gevonden.",true);
      logScherm("",true);
      return 1;
    }

    $query = "
      SELECT 
        norm 
      FROM 
        NormPerRisicoprofiel 
      WHERE 
        Risicoklasse='".$portefeuille['Risicoklasse']."'  AND 
        Vermogensbeheerder='".$portefeuille['Vermogensbeheerder']."' AND 
        Beleggingscategorie='".$modelType['Beleggingscategorie']."'";
    $norm = $dblk->lookupRecordByQuery($query);

    $naamOmschrijving = $portefeuille['Naam'];
    $clientOmschrijving = $portefeuille['Client']." / ".$portefeuille['Portefeuille']." / ".$portefeuille['Depotbank'];

    $query = "
    SELECT 
      SUM(actuelePortefeuilleWaardeEuro) AS totaal 
    FROM 
      TijdelijkeRapportage 
    WHERE 
      rapportageDatum = '".$einddatum."' AND 
      portefeuille = '".$portefeuille['Portefeuille']."' AND 
      `type` <> 'rente' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];

    $dblk = new DB();

    $portefwaarde = $dblk->lookupRecordByQuery($query);

    if($norm['norm'] <> '')
    {
      $portefwaarde['totaal']=$portefwaarde['totaal']*($norm['norm']/100);
    }

    $portefTotaal = $portefwaarde['totaal'];

    $query = "
    SELECT
			SUM(IF(TijdelijkeRapportage.portefeuille ='m".$mPortefeuille."' ,model.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$mPortefeuille."' ,model.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 AS percentageModel,
  		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
	  	SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100 AS percentagePortefeuille,
			(
			  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$mPortefeuille."' ,model.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 -
		  	SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100
			) AS afwijking,
      TijdelijkeRapportage.fondsOmschrijving as RegelOmschrijving,
			TijdelijkeRapportage.fonds,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid,
			TijdelijkeRapportage.hoofdcategorie,
			TijdelijkeRapportage.hoofdsector,
			TijdelijkeRapportage.Regio,
			TijdelijkeRapportage.beleggingscategorie,
			TijdelijkeRapportage.beleggingssector,
			TijdelijkeRapportage.hoofdcategorieVolgorde,
			TijdelijkeRapportage.hoofdcategorieOmschrijving,
			TijdelijkeRapportage.hoofdsectorVolgorde,
			TijdelijkeRapportage.hoofdsectorOmschrijving,
			TijdelijkeRapportage.valutaVolgorde,
			TijdelijkeRapportage.valutaOmschrijving,
			TijdelijkeRapportage.regioVolgorde,
			TijdelijkeRapportage.regioOmschrijving,
			TijdelijkeRapportage.beleggingscategorieVolgorde,
			TijdelijkeRapportage.beleggingscategorieOmschrijving,
			TijdelijkeRapportage.beleggingssectorVolgorde,
			TijdelijkeRapportage.beleggingssectorOmschrijving,
			Fondsen.ISINCode,
			TijdelijkeRapportage.valuta,
			 TijdelijkeRapportage.totaalAantal
			FROM TijdelijkeRapportage
			JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
			LEFT JOIN TijdelijkeRapportage AS model ON model.fonds = TijdelijkeRapportage.fonds AND model.portefeuille = \""."m".$mPortefeuille."\" 
           AND model.type = 'fondsen'  AND model.rapportageDatum = '".$einddatum."'"
      .str_replace("TijdelijkeRapportage",'model',$__appvar['TijdelijkeRapportageMaakUniek'])."
			LEFT JOIN TijdelijkeRapportage AS portef ON portef.fonds = TijdelijkeRapportage.fonds AND portef.portefeuille = \"".$portefeuille['Portefeuille']."\" 
           AND portef.type = 'fondsen'  AND portef.rapportageDatum = '".$einddatum."'"
      .str_replace("TijdelijkeRapportage",'portef',$__appvar['TijdelijkeRapportageMaakUniek'])."
			WHERE
			TijdelijkeRapportage.type = 'fondsen' AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$portefeuille['Portefeuille']."\" OR TijdelijkeRapportage.portefeuille = \""."m".$mPortefeuille."\")  "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY TijdelijkeRapportage.fondsOmschrijving
			ORDER BY afwijking DESC ";

    $db2 = new DB();
    $db2->executeQuery($query);

    while($fdata = $db2->nextRecord())
    {
      $aankoopStuks=0;
      $verkoopStuks=0;

      $aankoopWaarde 	= ((($portefTotaal) / 100) * $fdata['percentageModel']) - $fdata['portefeuilleWaarde'];
      $aankoopStuks 	= round(($aankoopWaarde / ($fdata['actueleFonds'] * $fdata['actueleValuta']))  / $fdata['fondsEenheid'],4);
      if($fdata['fondsEenheid'] == '0.01')
      {
        $aankoopStuks = ($aankoopStuks > 0)?$aankoopStuks=floor($aankoopStuks/100)*100:$aankoopStuks=ceil($aankoopStuks/100)*100;
      }

      $waardeVolgensModel = (($portefTotaal) / 100) * $fdata['percentageModel'];

      $aankoopStuks=round($aankoopStuks,0);
      if($aankoopStuks < 0)
      {
        $verkoopStuks = (round($fdata['percentageModel'],1) == 0)?$verkoopStuks=$fdata['totaalAantal']:$verkoopStuks = $aankoopStuks * -1;
        $aankoopStuks = 0;
      }

      $geschatOrderbedrag 	= (($verkoopStuks-$aankoopStuks) * ($fdata['actueleFonds'] * $fdata['actueleValuta'])) * $fdata['fondsEenheid'];

      $row[] = array(
        "type"                            => "fonds",
        "fonds"                           => $fdata['fonds'],
        "fondsOmschrijving"               => $fdata['fondsOmschrijving'],
        "ISINCode"                        => $fdata['ISINCode'],
        "valuta"                          => $fdata['valuta'],
        "modelPercentage"                 => $fdata['percentageModel'],
        "werkelijkPercentage"             => $fdata['percentagePortefeuille'],
        "afwijkingPercentage"             => $fdata['afwijking'],
        "afwijkingEur"                    => $aankoopWaarde,
        "kopen"                           => $aankoopStuks,
        "verkopen"                        => $verkoopStuks,
        "waardeModel"                     => $waardeVolgensModel,
        "koersLokaal"                     => $fdata['actueleFonds'],
        "huidigeWaarde"                   => $fdata['portefeuilleWaarde'],
        "geschatOrderbedrag"              => $geschatOrderbedrag,
        "portefeuille"                    => $portefeuille['Portefeuille'],
        "hoofdcategorie"                  => $fdata['hoofdcategorie'],
        "hoofdsector"                     => $fdata['hoofdsector'],
        "Regio"                           => $fdata['Regio'],
        "beleggingscategorie"             => $fdata['beleggingscategorie'],
        "beleggingssector"                => $fdata['beleggingssector'],
        "hoofdcategorieVolgorde"          => $fdata['hoofdcategorieVolgorde'],
        "hoofdcategorieOmschrijving"      => $fdata['hoofdcategorieOmschrijving'],
        "hoofdsectorVolgorde"             => $fdata['hoofdsectorVolgorde'],
        "hoofdsectorOmschrijving"         => $fdata['hoofdsectorOmschrijving'],
        "valutaVolgorde"                  => $fdata['valutaVolgorde'],
        "valutaOmschrijving"              => $fdata['valutaOmschrijving'],
        "regioVolgorde"                   => $fdata['regioVolgorde'],
        "regioOmschrijving"               => $fdata['regioOmschrijving'],
        "beleggingscategorieVolgorde"     => $fdata['beleggingscategorieVolgorde'],
        "beleggingscategorieOmschrijving" => $fdata['beleggingscategorieOmschrijving'],
        "beleggingssectorVolgorde"        => $fdata['beleggingssectorVolgorde'],
        "beleggingssectorOmschrijving"    => $fdata['beleggingssectorOmschrijving'],
        "stamgegevens"                    => serialize($stamgegevens)
      );


    }

    $query = "SELECT
			SUM(IF(TijdelijkeRapportage.portefeuille ='m".$mPortefeuille."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$mPortefeuille."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 AS percentageModel,
   		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100 AS percentagePortefeuille,
			(
			 SUM(IF(TijdelijkeRapportage.portefeuille ='m".$mPortefeuille."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 -
			 SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100
			) AS afwijking,
			TijdelijkeRapportage.fonds,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid,
			TijdelijkeRapportage.valuta
			FROM TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.type = 'rekening'  AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$portefeuille['Portefeuille']."\" OR TijdelijkeRapportage.portefeuille = \""."m".$mPortefeuille."\")  "
      .$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY TijdelijkeRapportage.type
			ORDER BY afwijking DESC ";


    $db2 = new DB();
    $db2->executeQuery($query);


    while($fdata = $db2->nextRecord())
    {
      $aankoopWaarde 	= ((($portefTotaal) / 100) * $fdata['percentageModel']) - $fdata['portefeuilleWaarde'];
      $aankoopStuks 	= ($aankoopWaarde / ($fdata['actueleFonds'] * $fdata['actueleValuta']))  / $fdata['fondsEenheid'];
      $verkoopStuks = 0;
      $waardeVolgensModel = (($portefTotaal) / 100) * $fdata['percentageModel'];

      if ($fdata['portefeuilleWaarde'] != 0)
      {
        $row[] = array(
          "type"                => "geld",
          "fonds"               => "",
          "fondsOmschrijving"   => "Effectenrekening ".$fdata['fondsOmschrijving'],
          "ISINCode"            => "",
          "valuta"              => "",
          "modelPercentage"     => $fdata['percentageModel'],
          "werkelijkPercentage" => $fdata['percentagePortefeuille'],
          "afwijkingPercentage" => $fdata['afwijking'],
          "afwijkingEur"        => $aankoopWaarde,
          "kopen"               => $aankoopStuks,
          "verkopen"            => $verkoopStuks,
          "waardeModel"         => $waardeVolgensModel,
          "koersLokaal"         => $fdata['actueleValuta'],
          "huidigeWaarde"       => $fdata['portefeuilleWaarde'],
          "geschatOrderbedrag"  => 0,
          "portefeuille"        => $portefeuille['Portefeuille'],
          "stamgegevens"        => serialize($stamgegevens)
        );

      }
    }


  }

  foreach ($row as $item)
  {
    $mdl->addRecord($item);
    $portefeuille = $item["portefeuille"];
  }

  $_SESSION["htmlRapportVars"] = array(
    "portefeuille" => $portefeuille,
    "client" => $portRec["Client"],
    "start" => $rapportStart,
    "stop" => $rapportDatum,
  );

  if($data['consolidatie']==1)
  {
    verwijderConsolidatie($portefeuille);
  }
  header("location: HTMLrapport/modelRapport.php?portefeuille=".$portefeuille.''.(isset($data['type'])? '&type='.$data['type']:''));
  exit;
}
if ($rap[0] == "VOLK")
{

	include_once("rapport/rapportRekenClass.php");
	// selecteer rapportage volgorde

	$min1dag = false;
	$d = explode("-",substr($data["datum_tot"],0,10));
	if ( (int) $d[0] === 1 && (int) $d[1] === 1 )
	{
		$min1dag = true;
	}

	if(checkAccess($type))
		$join = "";
	else
	{
		$join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND ".
			" VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
							JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
		$beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";

	}
	// check begin datum rapportage!
	$query = "SELECT Portefeuilles.Startdatum, ".
		"Portefeuilles.Einddatum,		".
		"Portefeuilles.consolidatie,		".
		"Portefeuilles.RapportageValuta, ".
		"Vermogensbeheerders.layout, ".
		"Vermogensbeheerders.Vermogensbeheerder	".
		" FROM (Portefeuilles, Vermogensbeheerders) ".$join." WHERE Portefeuilles.Portefeuille = '".$portefeuille."'".
		" AND Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder $beperktToegankelijk";

	verwijderTijdelijkeTabel($portefeuille);
	// asort
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$pdata = $DB->nextRecord();

	$rapportageDatum['a'] = jul2sql(form2jul($data['datum_van']));
	$rapJul=form2jul($data['datum_tot']);
	$valutaDatum = getLaatsteValutadatum();
	$valutaJul = db2jul($valutaDatum);

	$rapportValues["rapportageDatum"]["a"] = $rapportageDatum['a'];
	$rapportValues["rapJul"] = $rapJul;
	$rapportValues["valutaDatum"] = $valutaDatum;
	$rapportValues["valutaJul"] = $valutaJul;

	if($rapJul > $valutaJul + 86400)
	{
		echo "<b>Fout: kan niet in de toekomst rapporteren.</b>";
		exit;
	}
	$rapportageDatum['b'] = jul2sql($rapJul);

	if(db2jul($rapportageDatum['b']) < db2jul($pdata['Startdatum']))
	{
//		$rapportageDatum['a'] = $pdata['Startdatum'];
		$rapportageDatum['b'] = $pdata['Startdatum'];

		$rapportDatum = ($pdata['Startdatum']);
//		$rapportStart = ($pdata['Startdatum']);
	}

	if(db2jul($rapportageDatum['b']) > db2jul($pdata['Einddatum']))
	{
		echo "<b>Fout: Deze portefeille heeft een einddatum  (".date("d-m-Y",db2jul($pdata['Einddatum'])).")</b>";
		exit;
	}


	// controlleer of datum a niet groter is dan datum b!
	if(db2jul($rapportageDatum['a']) > db2jul($rapportageDatum['b']))
	{
		echo "<b>Fout: Van datum kan niet groter zijn dan  T/m datum! </b>";
		exit;
	}
	$julrapport = db2jul($rapportageDatum['a']);
	$rapportMaand = date("m",$julrapport);
	$rapportDag = date("d",$julrapport);
	$rapportJaar = date("Y",$julrapport);

	if($rapportMaand == 1 && $rapportDag == 1)
	{
		$startjaar = true;
		$extrastart = false;
	}
	else
	{
		$startjaar = false;
		// 1 dag eraf is de startdatum!
		$julrapport = db2jul($rapportageDatum['a']);
		$rapportageDatum['a'] = jul2sql($julrapport);

		$extrastart = mktime(0,0,0,1,1,$rapportJaar);
		if($extrastart < 	db2jul($pdata['Startdatum']))
			$extrastart = $pdata['Startdatum'];
		else
			$extrastart = date("Y-m-d",$extrastart);
	}
	$rapportValues["rapportageDatum"]["a"] = $rapportageDatum['a'];
	$rapportValues["rapportageDatum"]["b"] = $rapportageDatum['b'];
	$rapportValues["julrapport"] = $julrapport;
	$rapportValues["rapportMaand"] = $rapportMaand;
	$rapportValues["rapportDag"] = $rapportDag;
	$rapportValues["rapportJaar"] = $rapportJaar;
	$rapportValues["startjaar"] = $startjaar;
	$rapportValues["extrastart"] = $extrastart;

	$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['b'],$min1dag,$pdata['RapportageValuta'],$rapportageDatum['a']);

	// eerste loop bepaald totale portefeuillwaarde in EUR
	$porteuilleWaarde = 0;
	for ($x=0; $x < count($fondswaarden['b']); $x++)
	{
		$rec = $fondswaarden['b'][$x];
		$porteuilleWaarde += $rec["actuelePortefeuilleWaardeEuro"];
	}

	include_once("../classes/AE_cls_htmlColomns.php");
	include_once($__appvar["basedir"]."/classes/htmlReports/htmlMODEL.php");
	include_once("../classes/htmlReports/htmlVOLK.php");
	$volk = new htmlVOLK($portefeuille);

//	if ( ! isset($data['type']) || $data['type'] !== 'csv' ) {
		$volk->clearTable();
//	}
	// berekende velden bepalen
	for ($x=0; $x < count($fondswaarden['b']); $x++)
	{
//	  debug($fondswaarden['b'][$x]);
		$rec = $fondswaarden['b'][$x];
		// aandeel op totalewaarde
		$rec["aandeelOpTotaleWaarde"] = round($rec["actuelePortefeuilleWaardeEuro"]/$porteuilleWaarde * 100,2);
		$rec["consolidatie"] = $pdata['consolidatie'];
		$rec["portefeuille"] = $portefeuille;
		$rec["rapportDatum"] = $rapportDatum;
		if ($rec["type"] == "fondsen")
		{
			// fondsResultaat
			$fondsResultaatReken = ($rec["actuelePortefeuilleWaardeInValuta"] - $rec["beginPortefeuilleWaardeInValuta"]) * $rec["actueleValuta"];
			$rec["fondsResultaat"] = round($fondsResultaatReken, 0);
			//  valutaResultaat
			$rec["valutaResultaat"] = round($rec["actuelePortefeuilleWaardeEuro"] - $rec["beginPortefeuilleWaardeEuro"] - $fondsResultaatReken, 2);
			// resultaatInProcent
			if ( (int)$rec["beginPortefeuilleWaardeEuro"] === 0 )
			{
				$rec["resultaatInProcent"] = 0;
			}
			else
			{

				$rec["resultaatInProcent"] = round ( ( ($rec["fondsResultaat"]+$rec["valutaResultaat"])/abs($rec["beginPortefeuilleWaardeEuro"])) *100,2);
			}
			// hier komt de directe opbrengst berekening


		}

//		if (($rec["fondsResultaat"]  + $rec["valutaResultaat"]) > 0 AND $rec["resultaatInProcent"] < 0)
//    {
//      $rec["resultaatInProcent"] *= -1;
//    }
//    debug($rec);

   // $resultaatProcent=($data['fondsResultaat'] + $data['valutaResultaat'] + $data['dividendCorrected'])/abs($data['beginPortefeuilleWaardeEuro'])*100;

//    if (($rec["fondsResultaat"]  + $rec["valutaResultaat"]) > 0 AND $rec["resultaatInProcent"] < 0)
//    {
//      $rec["resultaatInProcent"] *= -1;
//    }

		// opgelopen rente uitsplitsen
		if ($rec["fonds"] <> "" AND $rec["type"] == "rente")
		{
			$rec["beleggingscategorie"] == "RENTE";
			$rec["beleggingscategorieOmschrijving"] = "Opgelopen Rente";
			$rec["beleggingscategorieVolgorde"] = "87";
		}
		$dbl = new DB();
    $query = "SELECT * FROM Fondsen WHERE Fonds = '".$rec["fonds"]."'";
    $fondsRec = $dbl->lookupRecordByQuery($query);
		$rec["ISINCode"] = $fondsRec["ISINCode"];
		$rec["rating"] = $fondsRec["rating"];
		$rec["orderinlegInBedrag"] = $fondsRec["orderinlegInBedrag"];
		$volk->addRecord($rec);
	}
//
//	$d = explode("-",substr($data["datum_van"],0,10));
//	if ( (int) $d[0] === 1 && (int) $d[1] === 1 ) {
//		$rapportDatum = (date('Y', form2jul($rapportDatum)) -1) . '-12-31';
//	}


	$_SESSION["htmlRapportVars"] = array(
		"portefeuille" => $portefeuille,
		"client" => $portRec["Client"],
		"specifiekeIndex" => $portRec["SpecifiekeIndex"],
		"start" => $rapportStart,
		"stop" => $rapportDatum,
		"altFonds" => $_POST["altFonds"],
    "srtAlt"  => $_POST["srtAlt"]

	);

  if($data['consolidatie']==1)
  {
    verwijderConsolidatie($portefeuille);
  }

	header("location: HTMLrapport/volkRapport.php?portefeuille=".$portefeuille .''.(isset($data['type'])? '&type='.$data['type']:''));
	exit();

}

if ($rap[0] == "ATT")
{

  unset($_SESSION["htmlATT"]);
  include_once("rapportRekenClass.php");
  include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
  include_once($__appvar["basedir"]."/html/indexBerekening.php");
  $db = new DB();
  $portRec = $db->lookupRecordByQuery("SELECT * FROM `Portefeuilles` WHERE Portefeuille = '$portefeuille'");

  $query = "SELECT Vermogensbeheerders.PerformanceBerekening,Vermogensbeheerders.Vermogensbeheerder,Vermogensbeheerders.Layout	".
    " FROM Portefeuilles JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder WHERE Portefeuilles.Portefeuille = '".$portefeuille."'";
  $db->SQL($query);
  $db->Query();
  $vdata = $db->nextRecord();

  verwijderTijdelijkeTabel($portefeuille);
  progress("matrix leegmaken");
  $index = new indexHerberekening();
  $julstart = db2jul($rapportStart);
  $julstop  = db2jul($rapportDatum);
  $kwartalen = _mkDatums($index->getKwartalen($julstart,$julstop));
  $jaren     = _mkDatums($index->getJaren($julstart,$julstop));

  $indexData = $index->getWaarden( $rapportStart,$rapportDatum ,$portefeuille,$portRec["SpecifiekeIndex"]);
  $cumPerfArray = array();
  $kwartCum = array();    // array met tussenwaarden voor de kwartaalberekening
  $jaarCum = array();     // array met tussenwaarden voor de jaarberekening
  $qPerfArray = array();
  $yPerfArray = array();

  include_once($__appvar["basedir"]."/classes/AE_cls_htmlColomns.php");
  include_once($__appvar["basedir"]."/classes/htmlReports/htmlATT.php");
  $att = new htmlATT($portefeuille);
  $att->initModule();
  $att->clearTable();
  progress("maanden ophalen");
  $specifiekeIndexVorige = 0;
  $started = false;
  $kTel = 0;
  $vorigeMaand=100;
  
  $maandenCumulatief=array();
  if($vdata['PerformanceBerekening']==2 || $vdata['PerformanceBerekening']==3|| $vdata['PerformanceBerekening']==4)
  {
    $class='ATTberekening_L'.$vdata['Layout'];
    if(file_exists($__appvar["basedir"].'/html/rapport/include/'.$class.'.php'))
    {
      include_once($__appvar["basedir"].'/html/rapport/include/' . $class . '.php');
    }
    elseif(file_exists($__appvar["basedir"].'/html/rapport/include/layout_'.$vdata['Layout'].'/'.$class.'.php'))
    {
      include_once($__appvar["basedir"].'/html/rapport/include/layout_'.$vdata['Layout'].'/' . $class . '.php');
    }
    if(class_exists($class) && method_exists($class,'getPerfArray'))
    {
      $attBerekening=new $class();
      $maandenCumulatief = $attBerekening->getPerfArray($portefeuille,$rapportStart,$rapportDatum, $valuta);
    }
  }
  
  foreach($indexData as $row)
  {
//    debug($row);
    $row["soort"] = "maand";
    $row["portefeuille"] = $portefeuille;
    /* Te gebruiken voor 5902  */
    if(isset($maandenCumulatief[$row['datum']]))
    {
      //echo "old perf:".($row["performance"])." new:". $maandenCumulatief[$row['datum']]['performance']." <br>\n";
      //echo "old cumu:".($row["perfCumulatief"])." new:". $maandenCumulatief[$row['datum']]['index']." <br>\n";
      $row["performance"]=$maandenCumulatief[$row['datum']]['performance'];
      $row["perfCumulatief"]=$maandenCumulatief[$row['datum']]['index'];
    }
    elseif($vdata['PerformanceBerekening']==7 || $vdata['PerformanceBerekening']==2 || $vdata['PerformanceBerekening']==3)
    {
      $row["perfCumulatief"] = $index->periodePerformance($portefeuille, $rapportStart, $row['datum'], substr($portRec["Startdatum"], 0, 10));
      $row["index"]=$row["perfCumulatief"]+100;
      $maandPerf=(($row["index"]/100)/($vorigeMaand/100)-1)*100;
      $vorigeMaand=$row["index"];
      //echo "old:".($row["performance"])." new:". $maandPerf." ->cumu->".$row["perfCumulatief"] ." <br>\n";
      $row["performance"] =$maandPerf;
     // echo "old:".($row["index"] - 100)." new:". $row["perfCumulatief"]." ($portefeuille,$rapportStart,".$row['datum'].",".substr($portRec["Startdatum"],0,10).")<br>\n";
    }
    else
    {
      $row["perfCumulatief"] = $row["index"] - 100;
    }
  
    if ($row["perfCumulatief"] <> 0 OR $started)
    {
      $started = true;
      $row["specifiekeIndexVorige"] = $row["specifiekeIndexPerformance"]; //- $specifiekeIndexVorige;
      $specifiekeIndexVorige = $row["specifiekeIndexPerformance"];
    }
    else
    {
      $row["specifiekeIndexVorige"] = 0;
    }

    $cumPerfArray[$row["datum"]] = $row["perfCumulatief"];

    if (in_array($row["datum"], $kwartalen))   // leg kwartaal perf en Cum perf vast
    {
      $kwartCum[] = $row["performance"];
      $cum = 1;
      foreach($kwartCum as $item)
      {
        $cum *= (1 + ($item/100));
      }
      $cum = ($cum - 1) * 100;
//      debug($kwartCum, $cum);
      $kwartCum = array();
      $qPerfArray[$row["datum"]] = $cum;
    }
    else
    {
      $kwartCum[] = $row["performance"];
    }

    if (in_array($row["datum"], $jaren))   // leg jaar perf en Cum perf vast
    {
      $jaarCum[] = $row["performance"];
      $cum = 1;
      foreach($jaarCum as $item)
      {
        $cum *= (1 + ($item/100));
      }
      $cum = ($cum - 1) * 100;
      $jaarCum = array();
      $yPerfArray[$row["datum"]] = $cum;
    }
    else
    {
      $jaarCum[] = $row["performance"];
    }



    $cumPerfArray[$row["datum"]] = $row["perfCumulatief"];
    $att->addRecord($row);
  }

//debug($cumPerfArray);
  progress("kwartalen ophalen");
  $specifiekeIndexVorige = 0;
  $started = false;
  $indexData = $index->getWaarden( $rapportStart,$rapportDatum ,$portefeuille,$portRec["SpecifiekeIndex"], "kwartaal");

  foreach($indexData as $row)
  {
    $row["soort"] = "kwartaal";
    $row["portefeuille"] = $portefeuille;
    $row["perfCumulatief"] = $row["index"] - 100;


    if ($row["perfCumulatief"] <> 0 OR $started)
    {
      $started = true;
      $row["specifiekeIndexVorige"] = $row["specifiekeIndexPerformance"]; //- $specifiekeIndexVorige;
      $specifiekeIndexVorige = $row["specifiekeIndexPerformance"];
    }
    else
    {
      $row["specifiekeIndexVorige"] = 0;
    }
    $row["performance"]    = $qPerfArray[$row["datum"]];
    $row["perfCumulatief"] = $cumPerfArray[$row["datum"]];
    $att->addRecord($row);
  }

  progress("jaren ophalen");
  $indexData = $index->getWaarden( $rapportStart,$rapportDatum ,$portefeuille,$portRec["SpecifiekeIndex"], "jaar");

  $specifiekeIndexVorige = 0;
  $started = false;
  foreach($indexData as $row)
  {
    $row["soort"] = "jaar";

    $row["portefeuille"] = $portefeuille;
    $row["perfCumulatief"] = $row["index"] - 100;
    if ($row["perfCumulatief"] <> 0 OR $started)
    {
      $started = true;
      $row["specifiekeIndexVorige"] = $row["specifiekeIndexPerformance"]; //- $specifiekeIndexVorige;
      $specifiekeIndexVorige = $row["specifiekeIndexPerformance"];
    }
    else
    {
      $row["specifiekeIndexVorige"] = 0;
    }
    $row["performance"]    = $yPerfArray[$row["datum"]];
    $row["perfCumulatief"] = $cumPerfArray[$row["datum"]];
    $att->addRecord($row);
  }
  progress("klaar");
  $_SESSION["htmlRapportVars"] = array(
    "portefeuille" => $portefeuille,
    "client" => $portRec["Client"],
    "specifiekeIndex" => $portRec["SpecifiekeIndex"],
    "start" => $rapportStart,
    "stop" => $rapportDatum,
    "altFonds" => $_POST["altFonds"],
    "srtAlt"  => $_POST["srtAlt"]
  );

  if($data['consolidatie']==1)
  {
    verwijderConsolidatie($portefeuille);
  }
  if (!$data["APIcall"])
  {
    header("location: HTMLrapport/attRapport.php?portefeuille=" . $portefeuille . '' . (isset($data['type'])?'&type=' . $data['type']:''));
    exit;
  }

}


if ($rap[0] == "TRANS")
{
  unset($_SESSION["htmlTRANS"]);
  include_once("rapport/rapportRekenClass.php");
  $db = new DB();
  $portRec = $db->lookupRecordByQuery("SELECT * FROM `Portefeuilles` WHERE Portefeuille = '$portefeuille'");
  $rapportageValuta = ($portRec["RapportageValuta"] <> "")?$portRec["RapportageValuta"]:"EUR";

  progress("matrix leegmaken");
  include_once("../classes/AE_cls_htmlColomns.php");
  include_once("../classes/htmlReports/htmlTRANS.php");
  $trns = new htmlTRANS($portefeuille);
  $trns->initModule();
  $trns->clearTable();
  $query = "
  SELECT 
    Fondsen.Omschrijving,
    Fondsen.Fondseenheid, 
    Fondsen.Fonds,
    Rekeningmutaties.id,
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
    if(in_array($mutaties['Transactietype'],array("L","V","V/S","A/S")))
    {
      $historie = berekenHistorischKostprijs($portefeuille, $mutaties["Fonds"], $mutaties["Boekdatum"], $rapportageValuta, $rapportStart, $mutaties['id']);
    }
    else
    {
      $historie=array();
    }
    //echo $mutaties["Fonds"];
  //  debug($mutaties);
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
  $_SESSION["htmlRapportVars"] = array(
    "portefeuille" => $portefeuille,
    "client" => $portRec["Client"],
    "start" => $rapportStart,
    "stop" => $rapportDatum,
  );

  if($data['consolidatie']==1)
  {
    verwijderConsolidatie($portefeuille);
  }
  header("location: HTMLrapport/transRapport.php?portefeuille=".$portefeuille.''.(isset($data['type'])? '&type='.$data['type']:''));
  exit;
}

if ($rap[0] == "MUT")
{
  unset($_SESSION["htmlMUT"]);
  $db = new DB();


  include_once("../classes/AE_cls_htmlColomns.php");
  include_once("../classes/htmlReports/htmlMUT.php");
  $mut = new htmlMUT($portefeuille);
  $mut->initModule();
  $mut->clearTable();

  $portRec = $db->lookupRecordByQuery("SELECT * FROM `Portefeuilles` WHERE Portefeuille = '$portefeuille'");
  progress("matrix leegmaken");

  $query = "
  SELECT 
    Rekeningmutaties.Boekdatum, 
    Rekeningmutaties.Omschrijving ,
    ABS(Rekeningmutaties.Aantal) AS Aantal, 
    Rekeningmutaties.Debet ".$koersQuery." as Debet, 
    Rekeningmutaties.Credit ".$koersQuery." as Credit, 
    Rekeningmutaties.Valutakoers, 
    Rekeningmutaties.Rekening, 
    Rekeningmutaties.Grootboekrekening, 
    Rekeningmutaties.Afschriftnummer, 
    Rekeningmutaties.Fonds,
    Rekeningmutaties.Bedrag,
    Rekeningmutaties.Valuta,
    Fondsen.Omschrijving as FondsOms,
    Grootboekrekeningen.Omschrijving AS gbOmschrijving, 
    Grootboekrekeningen.Opbrengst, 
    Grootboekrekeningen.Kosten, 
    Grootboekrekeningen.Afdrukvolgorde,
    Rekeningen.Valuta as rekValuta
  FROM 
    (Rekeningmutaties, Rekeningen,  Grootboekrekeningen)
	LEFT JOIN Fondsen ON
		Fondsen.Fonds = Rekeningmutaties.Fonds
  WHERE
    Rekeningmutaties.Rekening = Rekeningen.Rekening      AND 
    Rekeningen.Portefeuille = '".$portefeuille."'    AND 
    Rekeningmutaties.Verwerkt = '1'                      AND 
    Rekeningmutaties.Boekdatum > '".$rapportStart."'  AND 
    Rekeningmutaties.Boekdatum <= '".$rapportDatum."' ".$extraquery."  AND 
    Grootboekrekeningen.Afdrukvolgorde IS NOT NULL AND 
    Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND 
    ( 
       Grootboekrekeningen.Kosten = '1'       OR 
       Grootboekrekeningen.Opbrengst = '1'    OR 
       Grootboekrekeningen.Onttrekking = '1'  OR 
       Grootboekrekeningen.Storting = '1'     OR 
       Grootboekrekeningen.Kruispost = '1'
    ) 
    ORDER BY Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.Boekdatum;
  
  ";

  $db->executeQuery($query);
  while ($mutaties = $db->nextRecord())
  {
    $v = array();
    $v['portefeuille']        = $portefeuille;
    $v["Boekdatum"]           = $mutaties["Boekdatum"];
    $v['Omschrijving']        = $mutaties["Omschrijving"];
    $v['Aantal']              = $mutaties["Aantal"];
    $v['Debet']               = $mutaties["Debet"];
    $v['Credit']              = $mutaties["Credit"];
    $v['Valutakoers']         = $mutaties["Valutakoers"];
    $v['Rekening']            = $mutaties["Rekening"];
    $v['Grootboekrekening']   = $mutaties["Grootboekrekening"];
    $v['Afschriftnummer']     = $mutaties["Afschriftnummer"];
    $v['gbOmschrijving']      = $mutaties["gbOmschrijving"];
    $v['Opbrengst']           = $mutaties["Opbrengst"];
    $v['Kosten']              = $mutaties["Kosten"];
    $v['Afdrukvolgorde']      = $mutaties["Afdrukvolgorde"];
    $v['fonds']               = $mutaties["Fonds"];
    $v['fondsOmschrijving']   = $mutaties["FondsOms"];
    $v['Bedrag']              = $mutaties["Bedrag"];
    $v['Valuta']              = $mutaties["Valuta"];
    $v['rekValuta']           = $mutaties["rekValuta"];
    $v['bedragVV']            = $mutaties["Credit"]-$mutaties["Debet"];
    $v['bedragEUR']           = ($mutaties["Credit"]-$mutaties["Debet"]) * $mutaties["Valutakoers"];

    $mut->addRecord($v);
  }

  $_SESSION["htmlRapportVars"] = array(
    "portefeuille" => $portefeuille,
    "client" => $portRec["Client"],
    "start" => $rapportStart,
    "stop" => $rapportDatum,
  );

  if($data['consolidatie']==1)
  {
    verwijderConsolidatie($portefeuille);
  }
  header("location: HTMLrapport/mutRapport.php?portefeuille=".$portefeuille.''.(isset($data['type'])? '&type='.$data['type']:''));
  exit;

}


function _mkDatums($in)
{
  foreach($in as $item)
  {
    $out[] = $item["stop"];
  }
  return $out;
}

function progress($text)
{
	return true;
	echo "<li>".$text."</li>";
	flush();flush();flush();
}




?>