<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/03/14 13:24:27 $
 		File Versie					: $Revision: 1.9 $
 		
 		$Log: FactuurBerekening.php,v $
 		Revision 1.9  2009/03/14 13:24:27  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2007/07/03 11:50:23  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2007/04/20 12:21:43  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2007/04/03 13:26:33  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2007/03/22 07:35:54  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2007/01/24 09:37:13  rvv
 		1 factuur per jaar datum naar 1-1
 		
 		Revision 1.3  2007/01/22 13:57:10  rvv
 		Gemiddeld vermogen weer als default
 		
 		Revision 1.2  2007/01/12 16:06:46  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/05 12:15:35  rvv
 		Toevoeging layout5 factuur
 		
 	
*/
/*

		// ***************************** ophalen data voor afdruk ************************ //
		global $__appvar;

		$DB = new DB();

		$query = "SELECT Clienten.* ".
						 " FROM Portefeuilles, Clienten ".
						 " WHERE ".
						 " Portefeuilles.Client = Clienten.Client AND ".
						 " Portefeuilles.Portefeuille = '".$this->portefeuille."'";

		$DB->SQL($query);
		$DB->Query();
		$clientdata = $DB->nextRecord();

		$query = "SELECT * FROM Portefeuilles WHERE Portefeuille = '".$this->portefeuille."' ";
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->NextRecord();


		$julrapport 		= db2jul($this->vandatum);    
		$rapportMaand 	= date("m",$julrapport);
		$rapportDag 		= date("d",$julrapport);
		$rapportJaar 		= date("Y",$julrapport);
		
		if($rapportMaand == 1 && $rapportDag == 1)
		{
			$startjaar = true;
			$extrastart = false;
		}
		else 
		{
			$startjaar = false;
			$extrastart = mktime(0,0,0,1,1,$rapportJaar);
			if($extrastart < 	db2jul($pdata[Startdatum]))
			{
				$extrastart = $pdata[Startdatum];
			}
			else 
			{
				$extrastart = jul2db($extrastart);
			}
		}
	
		if($portefeuilledata['BeheerfeeBasisberekening'] == 4) //3MaandsUltimo
		{
		  for ($i=1; $i<4; $i++)
		  {
		    if($rapportMaand+$i == 1 && $rapportDag == 1)
			      $startjaar = true;
		    else 
		        $startjaar = false;
    
      $berekenDatum = mktime(0,0,0,($i+$rapportMaand),0,$rapportJaar);	
      $berekenDatum = jul2sql($berekenDatum);
		  $fondswaarden['a'] =  berekenPortefeuilleWaarde($this->portefeuille, $berekenDatum,$startjaar);
		  vulTijdelijkeTabel($fondswaarden['a'] ,$this->portefeuille,$berekenDatum);
		  }
		  if($berekenDatum != $this->tmdatum)
		  {
	    echo 'De 3 maands ultimo periode voor portefeuille '.$this->portefeuille.' loopt niet over 3 maanden. <br>';
		  }
		}		
		
		
		
		// check of facturen perjaar == 1 dan moet de begindatum = 0101 en de einddatum 3112 zijn!
		
		if($portefeuilledata[BeheerfeeAantalFacturen] == 1)
		{
			$julvan = db2jul($this->vandatum);
			$jultm  = db2jul($this->tmdatum);
			if(date("d-m",$jultm) == "31-12")
			{
				$this->vandatum =  date('Y').'-01-01' ; 
			}
			else
			{
				return true;
			}
		}

		// haal totaalwaarde op startdatum
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum = '".$this->vandatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaardeVanaf = $DB->nextRecord();

		// haal totaalwaarde op einddatum
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum = '".$this->tmdatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();

		$gemiddeldeVermogen = ($totaalWaardeVanaf[totaal] + $totaalWaarde[totaal]) / 2;
		
		if($portefeuilledata['BeheerfeeBasisberekening'] == 4)
		{	
		 	$julrapport 		= db2jul($this->vandatum);
		  $rapportMaand 		= date("m",$julrapport);
		  $rapportJaar 		= date("Y",$julrapport);
		  for ($i=1; $i<4; $i++)
		  {
      $berekenDatum = mktime(0,0,0,($i+$rapportMaand),0,$rapportJaar);	
      $berekenDatum = jul2sql($berekenDatum);
   
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					     "FROM TijdelijkeRapportage WHERE ".
						   " rapportageDatum = '".$berekenDatum."' AND ".
						   " portefeuille = '".$this->portefeuille."' ";
		  $DB->SQL($query);
		  $DB->Query();
		  $tmp = $DB->nextRecord();
      $drieMaandsTotaal += $tmp['totaal'];

      if ($portefeuilledata['valutaUitsluiten'] == 1) //uitsluiten valuta
		    {
		    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				    		 "FROM TijdelijkeRapportage WHERE ".
						     " rapportageDatum = '".$berekenDatum."' AND ".
						     " portefeuille = '".$this->portefeuille."' AND ".
						     " type <> 'fondsen' "
						     .$__appvar['TijdelijkeRapportageMaakUniek'];
		    debugSpecial($query,__FILE__,__LINE__);
		    $DB->SQL($query);
		    $DB->Query();
		    $waardeLiquiditeiten = $DB->nextRecord();
		    $waardeLiquiditeitendrieMaanden += $waardeLiquiditeiten['totaal'];
		    }
      

      }
      $drieMaandsGemiddelde = $drieMaandsTotaal/3;
	    $drieMaandsLiquiditeiteinGemiddelde = $waardeLiquiditeitendrieMaanden/3;
		}

		$query = "SELECT ".
		"SUM(((TO_DAYS('".$this->tmdatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
		"  / (TO_DAYS('".$this->tmdatum."') - TO_DAYS('".$this->vandatum."')) ".
		"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ))) AS totaal1, ".
		"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers))  AS totaal2 ".
		"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$this->vandatum."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->tmdatum."' AND ".
		"( Rekeningmutaties.Grootboekrekening = 'ONTTR' OR Rekeningmutaties.Grootboekrekening = 'STORT')";

		$DB->SQL($query);
		$DB->Query();
		$weging = $DB->NextRecord();

		// select trasactiekosten uit mutatie table.o
  	$query = "SELECT
  							((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)) as kosten,
  							Rekeningmutaties.Omschrijving
							FROM
								Rekeningmutaties, Rekeningen
							WHERE
  							Rekeningmutaties.Rekening = Rekeningen.Rekening AND
  							Rekeningmutaties.Boekdatum > '".$this->vandatum."' AND
	  						Rekeningmutaties.Boekdatum <= '".$this->tmdatum."' AND
  							Rekeningmutaties.Grootboekrekening = 'KOST' AND
  							Rekeningen.Portefeuille = '".$this->portefeuille."' ";
		$DB->SQL($query);
		$DB->Query();
		$_totaalTransactie = 0;
		$subDB = new DB();
		$totaalTransactie[aantal] = $DB->records();
		while ($data = $DB->nextRecord())
		{
			$_match = strtolower(substr($data[Omschrijving],0,7));
			if ($_match == "aankoop" OR $_match == "verkoop")
			{
				$_fonds = trim(substr($data[Omschrijving],8));
				$_subQuery = "SELECT * FROM Fondsen WHERE Fonds = '".mysql_escape_string($_fonds)."'";
				$subDB->SQL($_subQuery);

				if ($subRecord = $subDB->lookupRecord())
				{
					if($subRecord[Huisfonds] == 1 && $portefeuilledata[Depotbank] == "STR")
					{
						$totaalTransactie[aantal]--;
					}
					else
					{
						$_totaalTransactie += $data[kosten];
					}
				}
				else
				{
					$_totaalTransactie += $data[kosten];
				}
			}
		}
		$totaalTransactie[totaal] = $_totaalTransactie * -1;

		//selecteer korting per depotbank.
		$query = "SELECT Korting FROM KortingenPerDepotbank WHERE ".
						 " Vermogensbeheerder = '".$portefeuilledata[Vermogensbeheerder]."' AND ".
						 " Depotbank = '".$portefeuilledata[Depotbank]."' AND Grootboekrekening = 'KOST'";
		$DB->SQL($query);
		$DB->Query();
		if ($DB->records() > 0)
		{
			$korting = $DB->nextRecord();
			//echo $korting[Korting];
			$totaalTransactie[totaal] = $totaalTransactie[totaal] - ($totaalTransactie[aantal] * $korting[Korting]);
		}

		if($portefeuilledata[BeheerfeeRemisiervergoedingsPercentage])
		{
			$remisierBedrag = ($totaalTransactie[totaal]/100) * $portefeuilledata[BeheerfeeRemisiervergoedingsPercentage];
		}

		// korting op huisfonds.
		if($portefeuilledata[BeheerfeeTeruggaveHuisfondsenPercentage])
		{
			$query = "SELECT TijdelijkeRapportage.actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage, Fondsen ".
							 " WHERE TijdelijkeRapportage.Fonds = Fondsen.Fonds AND ".
							 " Fondsen.Huisfonds <> '0' AND TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
							 " rapportageDatum  = '".$this->vandatum."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);				 
			$DB->SQL($query);
			$DB->Query();
			$huisfondsKorting = 0;
			$huisfondsKortingPercentage = $portefeuilledata[BeheerfeeTeruggaveHuisfondsenPercentage];

			while($huisfonds = $DB->nextRecord())
			{
				$huisfondsenWaarden_start += $huisfonds[actuelePortefeuilleWaardeEuro];
			}

			$query = "SELECT TijdelijkeRapportage.actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage, Fondsen ".
							 " WHERE TijdelijkeRapportage.Fonds = Fondsen.Fonds AND ".
							 " Fondsen.Huisfonds <> '0' AND ".
							 " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' ".
							 " AND rapportageDatum  = '".$this->tmdatum."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);					 
			$DB->SQL($query);
			$DB->Query();

			while($huisfonds = $DB->nextRecord())
			{
				$huisfondsenWaarden_stop += $huisfonds[actuelePortefeuilleWaardeEuro];
			}

      $huisfondsenWaarden = ($huisfondsenWaarden_start + $huisfondsenWaarden_stop)/2;

      $huisfondsKorting = ($huisfondsenWaarden/100) * $huisfondsKortingPercentage;
			$huisfondsKorting = $huisfondsKorting / $portefeuilledata[BeheerfeeAantalFacturen];

		}
		// $this->tmdatum
		// - dag doen.
		$nieuwvan = $this->vandatum;

		$stortingen 			 			= getStortingen($this->portefeuille,$nieuwvan,$this->tmdatum);
		$onttrekkingen 		 			= getOnttrekkingen($this->portefeuille,$nieuwvan,$this->tmdatum);
		$stortingenOntrekkingen = $stortingen - $onttrekkingen;

		$resultaat = $totaalWaarde[totaal] - $totaalWaardeVanaf[totaal] - $stortingen + $onttrekkingen;
		
		$valutaUitsluiten = true;
		
		if ($portefeuilledata['valutaUitsluiten'] == 1) //uitsluiten valuta
		{
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum = '".$this->vandatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						  " type <> 'fondsen' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$waardeLiquiditeitenVanaf = $DB->nextRecord();

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum = '".$this->tmdatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND".
						 " type <> 'fondsen' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$waardeLiquiditeitenEind = $DB->nextRecord();
		
		$waardeLiquiditeitenVanaf = $waardeLiquiditeitenVanaf['totaal'];
		$waardeLiquiditeitenEind = $waardeLiquiditeitenEind['totaal'];
		}
		else 
		{
		$waardeLiquiditeitenVanaf = 0;  
		$waardeLiquiditeitenEind = 0;
		}
		
		

//	 	"0"=>"Gemiddeld vermogen",
//		"1"=>"Beginvermogen",
//		"2"=>"Eindvermogen",
//		"3"=>"Gecorrigeerd beginvermogen"

//		Kijk welk Basis rekenvermogen gebruikt moet worden


		$totaalStortingen = $stortingenOntrekkingen;
		
		switch($portefeuilledata["BeheerfeeBasisberekening"])
		{
			case 1 :
				$rekenvermogen = $totaalWaardeVanaf[totaal] - $waardeLiquiditeitenVanaf;
			break;
			case 2 :
				$rekenvermogen = $totaalWaarde[totaal] - $waardeLiquiditeitenEind; 
			break;
			case 3 :
				$rekenvermogen = $totaalWaardeVanaf[totaal] + $resultaat + $weging[totaal1] - $waardeLiquiditeitenVanaf;
				$totaalStortingen = $weging[totaal1];
			break;
			case 4 :
			  $rekenvermogen = $drieMaandsGemiddelde - $drieMaandsLiquiditeiteinGemiddelde;
			  $gemiddeldeVermogen = $drieMaandsGemiddelde;
			break;
			default :
				$rekenvermogen = $gemiddeldeVermogen ;  //$totaalWaarde[totaal];//
			break;
		}
		

		
		$jaar = date("Y",db2jul($this->tmdatum));
		$jaarstart = mktime(1,1,1,1,1,$jaar);

		$performancePeriode 		= performanceMeting($this->portefeuille, $this->vandatum, $this->tmdatum);
		if($this->extrastart)
			$performanceJaar 				= performanceMeting($this->portefeuille, $this->extrastart, $this->tmdatum);
		else
			$performanceJaar = $performancePeriode;

		// administratiekosten
		if($portefeuilledata[BeheerfeeAdministratieVergoeding])
		{
			// BeheerfeeAdministratieVergoeding
			$administratieBedrag = $portefeuilledata[BeheerfeeAdministratieVergoeding] / $portefeuilledata[BeheerfeeAantalFacturen];
		}

		if($portefeuilledata[BeheerfeeMethode] == 0)
		{
			$beheerfeeOpJaarbasis = 0;
			$beheerfeePerPeriode =  $administratieBedrag;
		}
		else  if($portefeuilledata[BeheerfeeMethode] == 1 || $portefeuilledata[BeheerfeeMethode] == 2 )
		{
//		  listarray($portefeuilledata);
			$restwaarde = $rekenvermogen;


//  start Chris methode

	  $_bs1 = $portefeuilledata["BeheerfeeStaffel1"];
      $_bs2 = $portefeuilledata["BeheerfeeStaffel2"];
      $_bs3 = $portefeuilledata["BeheerfeeStaffel3"];
      $_bs4 = $portefeuilledata["BeheerfeeStaffel4"];
      $_bs5 = $portefeuilledata["BeheerfeeStaffel5"];

      if (($restwaarde > $_bs1) AND $_bs1 > 0 )     $maxStaf = 1;
      if (($restwaarde > $_bs2) AND $_bs2 > $_bs1)  $maxStaf = 2;
      if (($restwaarde > $_bs3) AND $_bs3 > $_bs2)  $maxStaf = 3;
      if (($restwaarde > $_bs4) AND $_bs4 > $_bs3)  $maxStaf = 4;
      if (($restwaarde > $_bs5) AND $_bs5 > $_bs4)  $maxStaf = 5;

      $_vastStaf[1] =  $_bs1          * ($portefeuilledata["BeheerfeeStaffelPercentage1"]/100);
      $_vastStaf[2] = ($_bs2 - $_bs1) * ($portefeuilledata["BeheerfeeStaffelPercentage2"]/100);
      $_vastStaf[3] = ($_bs3 - $_bs2) * ($portefeuilledata["BeheerfeeStaffelPercentage3"]/100);
      $_vastStaf[4] = ($_bs4 - $_bs3) * ($portefeuilledata["BeheerfeeStaffelPercentage4"]/100);
      $_vastStaf[5] = ($_bs5 - $_bs4) * ($portefeuilledata["BeheerfeeStaffelPercentage5"]/100);

      for ($x=1; $x <= $maxStaf; $x++)
      {
      	$_fee += $_vastStaf[$x];
      }
      $_ss = "_bs".($maxStaf);
      $_rest = ($restwaarde - $$_ss);
      $_feerest = ($_rest * ($portefeuilledata["BeheerfeeStaffelPercentage".($maxStaf+1)]/100));
      $percentagetotaal = $_fee + $_feerest;

//  einde Chris methode

			if ($portefeuilledata[BeheerfeeMethode] == 2)
			{
				$percentagetotaal = $percentagetotaal - (($percentagetotaal/100) * $portefeuilledata["BeheerfeeKortingspercentage"]);
			}
			$beheerfeeOpJaarbasis = $percentagetotaal;
			$beheerfeePerPeriode = ($beheerfeeOpJaarbasis / $portefeuilledata[BeheerfeeAantalFacturen]) + $administratieBedrag;
		}
		else  if($portefeuilledata[BeheerfeeMethode] == 3)
		{
			$beheerfeeOpJaarbasis = (($rekenvermogen/100) *$portefeuilledata[BeheerfeePercentageVermogen]);
			$beheerfeePerPeriode = ($beheerfeeOpJaarbasis / $portefeuilledata[BeheerfeeAantalFacturen]) + $administratieBedrag;
		}
		else  if($portefeuilledata[BeheerfeeMethode] == 4)
		{
			$beheerfeeOpJaarbasis = $portefeuilledata[BeheerfeeBedrag];
			$beheerfeePerPeriode = ($beheerfeeOpJaarbasis / $portefeuilledata[BeheerfeeAantalFacturen]) + $administratieBedrag;
		}
		else	if($portefeuilledata[BeheerfeeMethode] == 5)
		{
			$beheerfeeOpJaarbasis = ($resultaat/100) *$portefeuilledata[BeheerfeePerformancePercentage];
			$beheerfeePerPeriode = ($beheerfeeOpJaarbasis / $portefeuilledata[BeheerfeeAantalFacturen]) + $administratieBedrag;
		}



		
		// Werkelijk aantal dagen
		if($portefeuilledata['WerkelijkeDagen'] == 1)
		{
		  $aantalDagen = round((((db2jul($this->tmdatum) - db2jul($this->vandatum))/86400)+1),0);
      $aantalDagenInJaar = (mktime(0,0,0,12,31,$rapportageJaar) - mktime(0,0,0,1,1,$rapportageJaar))/86400;
      $periodeDeelVanJaar = $aantalDagen / $aantalDagenInJaar;	
			$portefeuilledata['BeheerfeeMinPeriodeBedrag'] = $portefeuilledata['BeheerfeeMinJaarBedrag'] * $periodeDeelVanJaar;
			$beheerfeePerPeriode = $beheerfeePerPeriode * $portefeuilledata['BeheerfeeAantalFacturen'] * $periodeDeelVanJaar;
		}
    else 
    {
    	$portefeuilledata['BeheerfeeMinPeriodeBedrag'] = $portefeuilledata['BeheerfeeMinJaarBedrag'] / $portefeuilledata['BeheerfeeAantalFacturen'];
    }		

    $beheerfeeBetalen = $beheerfeePerPeriode - $remisierBedrag - $huisfondsKorting;

		if($beheerfeeBetalen < 0)
			$beheerfeeBetalen = 0;

	  $btwTarief = $portefeuilledata[BeheerfeeBTW];
		if($btwTarief > 0)
		{
			$btw = round(($beheerfeeBetalen/100) * $btwTarief,2);
		}
		$beheerfeeBetalen = round($beheerfeeBetalen,2);
		$beheerfeeBetalenIncl = $beheerfeeBetalen + $btw;

*/
?>