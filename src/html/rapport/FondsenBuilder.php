<?php

/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/27 15:21:08 $
File Versie					: $Revision: 1.37 $

$Log: FondsenBuilder.php,v $
Revision 1.37  2020/05/27 15:21:08  rvv
*** empty log message ***

Revision 1.36  2019/01/20 12:13:28  rvv
*** empty log message ***

Revision 1.35  2018/06/30 17:41:23  rvv
*** empty log message ***

Revision 1.34  2018/06/27 16:13:04  rvv
*** empty log message ***

Revision 1.33  2017/08/12 15:29:40  rvv
*** empty log message ***

Revision 1.32  2017/05/06 17:28:05  rvv
*** empty log message ***

Revision 1.31  2017/04/28 06:20:39  rvv
*** empty log message ***

Revision 1.30  2017/04/27 14:41:07  rvv
*** empty log message ***

Revision 1.29  2017/04/27 06:12:09  rvv
*** empty log message ***

Revision 1.28  2017/04/26 14:37:40  rvv
*** empty log message ***

Revision 1.27  2017/04/24 06:00:27  rvv
*** empty log message ***

Revision 1.26  2017/04/24 05:54:29  rvv
*** empty log message ***

Revision 1.25  2017/04/24 05:47:41  rvv
*** empty log message ***

Revision 1.24  2017/04/15 19:10:32  rvv
*** empty log message ***

Revision 1.23  2017/03/25 15:59:41  rvv
*** empty log message ***

Revision 1.22  2017/03/15 16:35:00  rvv
*** empty log message ***

Revision 1.21  2016/01/17 18:11:29  rvv
*** empty log message ***

Revision 1.20  2016/01/09 18:57:51  rvv
*** empty log message ***

Revision 1.19  2015/11/22 14:30:47  rvv
*** empty log message ***

Revision 1.18  2015/09/06 09:39:26  rvv
*** empty log message ***

Revision 1.17  2015/09/05 16:22:37  rvv
*** empty log message ***

Revision 1.16  2014/05/21 15:20:33  rvv
*** empty log message ***

Revision 1.15  2013/05/12 11:18:58  rvv
*** empty log message ***

Revision 1.14  2012/08/11 13:06:05  rvv
*** empty log message ***

Revision 1.13  2012/07/25 16:00:32  rvv
*** empty log message ***

Revision 1.12  2012/04/04 16:08:04  rvv
*** empty log message ***

Revision 1.11  2012/01/22 13:45:35  rvv
*** empty log message ***

Revision 1.10  2010/08/26 04:08:15  rvv
*** empty log message ***

Revision 1.9  2009/01/20 17:44:08  rvv
*** empty log message ***

Revision 1.8  2008/07/02 07:30:56  rvv
*** empty log message ***

Revision 1.7  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.6  2008/04/23 09:06:06  rvv
*** empty log message ***

Revision 1.5  2006/11/03 11:24:04  rvv
Na user update

Revision 1.4  2006/10/31 11:57:14  rvv
Voor user update

Revision 1.3  2006/09/04 07:13:36  rvv
Naam1 aan selectie toegevoegd.

Revision 1.2  2006/06/28 12:20:30  jwellner
*** empty log message ***

Revision 1.1  2006/04/27 09:03:24  jwellner
*** empty log message ***


*/

include_once("rapportRekenClass.php");

class FondsenBuilder
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	var $tmp_table;
	var $tmp_table_struct;

	function FondsenBuilder( $selectData )
	{
	  global $USR;
		$this->selectData = $selectData;
		$this->excelData 	= array();
		$this->datum = mktime();
		$this->tmp_table = "tmp_reportbuilder_$USR";//rvv add naam
		$this->tmp_table_struct = "CREATE TABLE `".$this->tmp_table."` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Portefeuille` VARCHAR( 24 ) NOT NULL ,
`Einddatum` date DEFAULT '0000-00-00',
`Vermogensbeheerder` VARCHAR( 10 ) NOT NULL ,
`Client` VARCHAR( 16 ) NOT NULL ,
`Naam` VARCHAR( 50 ) NOT NULL ,
`Naam1` VARCHAR( 50 ) NOT NULL ,
`profielOverigeBeperkingen` text NOT NULL,
`Depotbank` VARCHAR( 10 ) NOT NULL ,
`Bewaarder` VARCHAR( 10 ) NOT NULL ,
`Accountmanager` VARCHAR( 15 ) NOT NULL ,
`Risicoprofiel` VARCHAR( 5 ) NOT NULL ,
`SoortOvereenkomst` VARCHAR( 15 ) NOT NULL ,
`Risicoklasse` VARCHAR( 50 ) NOT NULL ,
`Remisier` VARCHAR( 15 ) NOT NULL ,
`AFMprofiel` VARCHAR( 15 ) NOT NULL ,
`InternDepot` TINYINT( 1 ) NOT NULL ,
`Consolidatie` TINYINT( 1 ) NOT NULL ,
`Fonds` VARCHAR( 25 ) NOT NULL ,
`standaardSector` VARCHAR( 15 ) NOT NULL ,
`KoersDatum` date NOT NULL default '0000-00-00',
`LaatsteKoers` DOUBLE NOT NULL ,
`Kostprijs` DOUBLE NOT NULL ,
`Beginwaardelopendjaar` DOUBLE NOT NULL ,
`AandeelBeleggingscategorie` DOUBLE NOT NULL ,
`AandeelTotaalvermogen` DOUBLE NOT NULL ,
`AandeelTotaalBelegdvermogen` DOUBLE NOT NULL ,
`AantalInPortefeuille` DECIMAL (20,6) NOT NULL ,
  `hoofdcategorie` varchar(15) NOT NULL default '',
  `hoofdsector` varchar(15) NOT NULL default '',
  `beleggingssector` varchar(15) NOT NULL default '',
  `beleggingscategorie` varchar(15) NOT NULL default '',
  `regio` varchar(15) NOT NULL default '',
  `attributieCategorie` varchar(15) NOT NULL default '',
  `afmCategorie` varchar(15) NOT NULL default '',
  `ISINCode` varchar(26) NOT NULL default '',
  `fondsValuta` varchar(4) NOT NULL default '',
  `renteWaarde` DOUBLE NOT NULL ,
  `fondsWaarde` DOUBLE NOT NULL ,
  `VKM` tinyint(3) NOT NULL,
  `passiefFonds` tinyint(3) NOT NULL,
  `fondssoort` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ";



		$this->removeTmpTable();

		$this->createTmpTable();
    sleep(1);
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function removeTmpTable()
	{
		$DB = new DB();
		$select = "SHOW TABLES LIKE '".$this->tmp_table."'";
	  $DB->SQL($select);
	  if ($DB->lookupRecord())
	  {
			$DB->SQL("DROP TABLE `".$this->tmp_table."` ");
			return $DB->Query();
	  }

	}

	function createTmpTable()
	{
		$DB = new DB();
		$DB->SQL($this->tmp_table_struct);
		return $DB->Query();
	}


	function writeRapport()
	{
		global $__appvar,$USR;

		$einddatumJul = $this->datum;
		$einddatum = jul2sql($this->datum);

		$whereF='';
		$whereP='';
		// build Fondsselection.
		if(count($this->selectData['where1']) > 0)
		{
			for($a = 0; $a < count($this->selectData['where1']); $a++)
			{
				$andor = "";
				if((count($this->selectData['where1'])) > ($a+1))
				{
					$andor = $this->selectData['where1'][$a]['andor'];
				}

				if($this->selectData['where1'][$a]['operator'] == "LIKE")
				{
					$whereF .= "Fondsen.".$this->selectData['where1'][$a]['field']." ".
										$this->selectData['where1'][$a]['operator']." '%".
										$this->selectData['where1'][$a]['search']."%' ".$andor." ";
					$whereP .= "Rekeningmutaties.".$this->selectData['where1'][$a]['field']." ".
										$this->selectData['where1'][$a]['operator']." '%".
										$this->selectData['where1'][$a]['search']."%' ".$andor." ";
				}
				else
				{
					$whereF .= "Fondsen.".$this->selectData['where1'][$a]['field']." ".
										$this->selectData['where1'][$a]['operator']." '".
										$this->selectData['where1'][$a]['search']."' ".$andor." ";
					$whereP .= "Rekeningmutaties.".$this->selectData['where1'][$a]['field']." ".
										$this->selectData['where1'][$a]['operator']." '".
										$this->selectData['where1'][$a]['search']."' ".$andor." ";
				}
			}
			if($whereF <> "")
				$whereF = " AND ( ".$whereF." )";

			if($whereP <> "")
				$whereP = " AND ( ".$whereP." )";
		}
		if($_SESSION['reportBuilder']['incLiquiditeiten']==1)
			$whereP='1';


		$pVelden = array("Portefeuille","Einddatum",
			"Vermogensbeheerder",
			"Client",
			"Naam",
			"Naam1",
			"Depotbank",
			"Accountmanager",
			"Risicoprofiel",
			"SoortOvereenkomst",
			"Risicoklasse",
			"Remisier",
			"AFMprofiel",
			"InternDepot");
		$extraquery='';
		if(count($this->selectData['where2']) > 0)
		{
			$where2 = "";
			for($a = 0; $a < count($this->selectData['where2']); $a++)
			{
				if(in_array($this->selectData['where2'][$a]['field'],$pVelden))
				{
					$andor = "";
					if((count($this->selectData['where2'])) > ($a+1))
					{
						$andor = $this->selectData['where2'][$a]['andor'];
					}

					if($this->selectData['where2'][$a]['operator'] == "LIKE")
						$where2 .= "Portefeuilles.".$this->selectData['where2'][$a]['field']." ".
							$this->selectData['where2'][$a]['operator']." '%".
							$this->selectData['where2'][$a]['search']."%' ".$andor." ";
					else
						$where2 .= "Portefeuilles.".$this->selectData['where2'][$a]['field']." ".
							$this->selectData['where2'][$a]['operator']." '".
							$this->selectData['where2'][$a]['search']."' ".$andor." ";
				}
			}

			if($where2 <> "")
				$extraquery = " AND ( ".$where2." ) ";
		}



$groep=" AND Rekeningmutaties.Boekdatum>='$einddatum' GROUP BY Fondsen.Fonds";
		/*
		 * JOIN Rekeningmutaties ON Fondsen.Fonds = Rekeningmutaties.Fonds
JOIN Rekeningen On Rekeningmutaties.Rekening=Rekeningen.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille
		. $extraquery.$groep
		 */

		// begin met loop over Fondsen.
		// selecteer koers van fonds op datum uit fonds tabel.
	 $fondsQuery = "SELECT Fondsen.Fondseenheid, Fondsen.Omschrijving, Fondsen.Fonds, Fondsen.ISINCode, Fondsen.VKM, Fondsen.standaardSector,
Fondsen.passiefFonds, Fondsen.fondssoort, Fondsen.Lossingsdatum , Fondsen.Lossingsdatum FROM Fondsen  WHERE 1".$whereF ;
// Fondskoersen.Koers , Valutakoersen.Koers AS valutaKoers, 
//,Fondskoersen, Valutakoersen 						 			"
// Fondsen.Fonds = Fondskoersen.Fonds AND ".
//						 			" Fondsen.Valuta = Valutakoersen.Valuta AND ".



		$DBf = new DB();
		$DBf->SQL($fondsQuery);
		$DBf->Query();


		while($fondsData = $DBf->nextRecord())
		{
			$fondsArray[] = $fondsData;
		}

			$jaar = date("Y",$einddatumJul);



		  if(in_array("Bewaarder",$this->selectData['fields']))
		  {
			  $splitsOpBewaarder=true;
		  }
		else
		{
			$splitsOpBewaarder=false;
		}




	if(checkAccess())
  {
	  $join = "";
	  $beperktToegankelijk = '';
  }
  else
  {
  	$join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND  VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	  				JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
    if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
        $internDepotToegang="OR Portefeuilles.interndepot=1";

	  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	    $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
    else
	    $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }


			// controle op einddatum portefeuille
		if($this->selectData['inactiefOpnemen'] == 1)
	    $extraquery='';
	  else
	    $extraquery  .= " AND Portefeuilles.Einddatum > '".$einddatum."' ";

			// selecteer alleen portefeuilles waar het fonds voorkomt!
		if($_SESSION['reportBuilder']['incConsolidaties']==1)
		{
			$consolidatieFilter='AND Portefeuilles.consolidatie<2';
    }
    else
		{
      $consolidatieFilter='AND Portefeuilles.consolidatie=0';
		}
		
		if($_SESSION['reportBuilder']['incLiquiditeiten']==1)
		{
			$q = "SELECT ".
				" Portefeuilles.ClientVermogensbeheerder, ".
				" Portefeuilles.Portefeuille, ".
				" Portefeuilles.Depotbank, ".
        " Portefeuilles.Consolidatie, ".
				" Portefeuilles.Vermogensbeheerder, ".
				" Portefeuilles.Depotbank, ".
				" Portefeuilles.Accountmanager, ".
				" Portefeuilles.Risicoprofiel, ".
				" Portefeuilles.SoortOvereenkomst, ".
				" Portefeuilles.Risicoklasse, ".
				" Portefeuilles.Remisier, ".
				" Portefeuilles.AFMprofiel, ".
				" Portefeuilles.InternDepot, ".
				" Portefeuilles.Einddatum, ".
				" Clienten.Client, ".
				" Clienten.Naam, ".
				" Clienten.Naam1 ".
				" FROM (Portefeuilles, Clienten)  ".$join.
				" WHERE  ".
				" Portefeuilles.Client = Clienten.Client ".$extraquery."  $beperktToegankelijk $consolidatieFilter ".
				" GROUP BY Portefeuilles.Portefeuille ";
		}
		else
		{
			$q = "SELECT " .
				" Portefeuilles.ClientVermogensbeheerder, " .
				" Portefeuilles.Portefeuille, " .
				" Portefeuilles.Depotbank, " .
        " Portefeuilles.Consolidatie, ".
				" Portefeuilles.Vermogensbeheerder, " .
				" Portefeuilles.Depotbank, " .
				" Portefeuilles.Accountmanager, " .
				" Portefeuilles.Risicoprofiel, " .
				" Portefeuilles.SoortOvereenkomst, " .
				" Portefeuilles.Risicoklasse, " .
				" Portefeuilles.Remisier, " .
				" Portefeuilles.AFMprofiel, " .
				" Portefeuilles.InternDepot, " .
				" Portefeuilles.Einddatum, ".
				" Clienten.Client, " .
				" Clienten.Naam, " .
				" Clienten.Naam1 ".
				" FROM (Rekeningmutaties, Rekeningen, Portefeuilles, Clienten)  " . $join .
				" WHERE  " .
				" Portefeuilles.Client = Clienten.Client AND" .
				" Rekeningmutaties.Rekening = Rekeningen.Rekening AND  " .
				" Rekeningen.Portefeuille = Portefeuilles.Portefeuille " . $extraquery . " AND  " .
				" YEAR(Rekeningmutaties.Boekdatum) = '" . $jaar . "' AND " .
				" Rekeningmutaties.Verwerkt = '1' AND " .
				" Rekeningmutaties.Boekdatum <= '" . $einddatum . "' AND " .
				" Rekeningmutaties.Fonds IS NOT NULL AND " .
				" Rekeningmutaties.Grootboekrekening = 'FONDS' $beperktToegankelijk $consolidatieFilter " .
				$whereP .
				" GROUP BY Portefeuilles.Portefeuille ";
		}
			$DB = new DB();
      $DB2 = new DB();
			$DB->SQL($q);
			$DB->Query();

			$records = $DB->records();

			if($this->progressbar)
			{
				$this->progressbar->moveStep(0);
				$pro_step = 0;
				$pro_multiplier = 100 / $records;
			}
			while($portefeuille = $DB->NextRecord())
			{
			  $crmData=getCrmNaam($portefeuille['Portefeuille']);
			  if($crmData['CrmClientNaam'] == 1)
			  {
			     $portefeuille['Naam']=$crmData['naam'];
			     $portefeuille['Naam1']=$crmData['naam1'];
          $query="SELECT profielOverigeBeperkingen FROM CRM_naw WHERE Portefeuille='".mysql_real_escape_string($portefeuille['Portefeuille'])."'";
          $DB2->SQL($query);
          $crmData=$DB2->lookupRecord();

			  }

        
				if($this->progressbar)
				{
					$pro_step += $pro_multiplier;
					$this->progressbar->moveStep($pro_step);
					logScherm("Portefeuille: ".$portefeuille['Portefeuille']);
				}

				$beginDatum=substr($einddatum,0,4)."-01-01";
				if(substr($einddatum,5,5)=="01-01")
					$startJaar=true;
				else
					$startJaar=false;

				if($splitsOpBewaarder==true)
				{
					$portefeuilleData = berekenPortefeuilleWaardeBewaarders($portefeuille['Portefeuille'], $einddatum, $startJaar, 'EUR', $beginDatum);

					$extraGroup=",Bewaarder";
				}
				else
				{
					$portefeuilleData = berekenPortefeuilleWaarde($portefeuille['Portefeuille'], $einddatum, $startJaar, 'EUR', $beginDatum);
					$extraGroup='';
				}
				vulTijdelijkeTabel($portefeuilleData,$portefeuille['Portefeuille'],$einddatum);

				$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
								 "FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum ='".$einddatum."' AND ".
								 " portefeuille = '".$portefeuille['Portefeuille']."' "
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
				debugSpecial($query,__FILE__,__LINE__);
				$DB2 = new DB();
				$DB2->SQL($query);
				$DB2->Query();
				$tdata = $DB2->nextRecord();
				$totaalWaarde = $tdata[totaal];

				// selecteer fondswaarde portefeuille
				$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
								 "FROM TijdelijkeRapportage WHERE ".
								 " type = 'rekening' AND ".
								 " rapportageDatum ='".$einddatum."' AND ".
								 " portefeuille = '".$portefeuille['Portefeuille']."' "
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
				debugSpecial($query,__FILE__,__LINE__);
				$DB2 = new DB();
				$DB2->SQL($query);
				$DB2->Query();
				$liqWaarde = $DB2->nextRecord();
				$liqWaarde = $liqWaarde[totaal];

				for($a=0; $a < count($fondsArray); $a++)
				{
					$fondsData = $fondsArray[$a];
					$fondseenheid = $fondsData['Fondseenheid'];
					$koersWaarde 	= $fondsData['Koers'];
					$valutakoers 	= $fondsData['valutaKoers'];

					// selecteer belegingscategorie
					$query  = " SELECT valuta as fondsValuta, totaalAantal, Bewaarder,
                      sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro, 
                      sum(if(type='rente',actuelePortefeuilleWaardeEuro,0)) as renteWaarde, 
                      sum(if(type='fondsen',actuelePortefeuilleWaardeEuro,0)) as fondsWaarde, 
                      beleggingscategorie,koersDatum ,actueleFonds,hoofdcategorie,hoofdsector,beleggingssector,
                      beleggingscategorie,regio,attributieCategorie,afmCategorie  ".
										" FROM TijdelijkeRapportage WHERE ".
										" TijdelijkeRapportage.Portefeuille = '".$portefeuille['Portefeuille']."' AND 
                      TijdelijkeRapportage.Fonds = '".$fondsData['Fonds']."' AND 
                      rapportageDatum ='".$einddatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY Fonds $extraGroup";

					$DB2 = new DB();
					$DB2->SQL($query);
					$DB2->Query();

					$dbrecords=array();
					while($fdata = $DB2->nextRecord())
					{
						$dbrecords[]=$fdata;
					}
					foreach($dbrecords as $fdata)
					{
					  $categorie = $fdata['beleggingscategorie'];
            $koersDatum = $fdata['koersDatum'];
            $laatsteKoers = $fdata['actueleFonds'];
            $fondsWaarde = $fdata['actuelePortefeuilleWaardeEuro'];
					  $fondsAantal = $fdata['totaalAantal'];

				  	// selecteer totaal in categorie portefeuille
				  	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaalWaarde ".
									 " FROM TijdelijkeRapportage WHERE ".
									 " type = 'fondsen' AND ".
									 " beleggingscategorie = '".$categorie."' AND ".
									 " rapportageDatum ='".$einddatum."' AND ".
									 " portefeuille = '".$portefeuille['Portefeuille']."' "
									 .$__appvar['TijdelijkeRapportageMaakUniek'];
				  	debugSpecial($query,__FILE__,__LINE__);
					  $DB2 = new DB();
				  	$DB2->SQL($query);
				  	$DB2->Query();
					  $cdata= $DB2->nextRecord();
					  $categorieWaarde = $cdata['totaalWaarde'];

					  if($fondsAantal <> 0)
					  {

						$aandeelop = array();

						$aandeelop['totaalvermogen'] =  $fondsWaarde / ($totaalWaarde/100);
						$aandeelop['vermogenbelegd'] =  $fondsWaarde / (($totaalWaarde - $liqWaarde)/100);
						$aandeelop['beleggingscat'] =  $fondsWaarde / (($categorieWaarde)/100);
						//$percentage = $fondsWaarde / ($categorieWaarde/100);

						// bereken historische waarde
							if($splitsOpBewaarder==true)
							{
								$hist =  fondsWaardeOpdatum($portefeuille['Portefeuille'], $fondsData['Fonds'], $einddatum, "EUR",$beginDatum, '',$fdata['Bewaarder']);
							}
							else
							{
								$hist = fondsWaardeOpdatum($portefeuille['Portefeuille'], $fondsData['Fonds'], $einddatum);
							}

//$fondsData['ISINCode'] $fdata['fondsValuta'] $fdata['renteWaarde'] $fdata['fondsWaarde']


						// build query ..
						$insert = "INSERT INTO `".$this->tmp_table."` SET ".
											" Portefeuille = '".$portefeuille['Portefeuille']."' ".
							        ",Einddatum = '".$portefeuille['Einddatum']."' ".
											",Vermogensbeheerder = '".$portefeuille['Vermogensbeheerder']."' ".
											",Client = '".$portefeuille['Client']."' ".
                      ",Consolidatie = '".$portefeuille['Consolidatie']."' ".
											",Naam = '".mysql_escape_string($portefeuille['Naam'])."' ". //rvv add naam
											",Naam1 = '".mysql_escape_string($portefeuille['Naam1'])."' ".
                      ",profielOverigeBeperkingen = '".mysql_escape_string($crmData['profielOverigeBeperkingen'])."' ".
              				",Depotbank = '".$portefeuille['Depotbank']."' ".
							        ",Bewaarder = '" . $fdata['Bewaarder'] . "' " .
											",Accountmanager = '".$portefeuille['Accountmanager']."' ".
											",Risicoprofiel = '".$portefeuille['Risicoprofiel']."' ".
											",SoortOvereenkomst = '".$portefeuille['SoortOvereenkomst']."' ".
											",Risicoklasse = '".$portefeuille['Risicoklasse']."' ".
											",Remisier = '".$portefeuille['Remisier']."' ".
											",AFMprofiel = '".$portefeuille['AFMprofiel']."' ".
											",InternDepot = '".$portefeuille['InternDepot']."' ".
											",Fonds = '".$fondsData['Fonds']."' ".
                      ",standaardSector = '".$fondsData['standaardSector']."' ".
											",Kostprijs = '".$hist['historischeWaarde']."' ".
											",Beginwaardelopendjaar = '".$hist['beginwaardeLopendeJaar']."' ".
											",AandeelBeleggingscategorie = '".$aandeelop['beleggingscat']."' ".
                      ",hoofdcategorie = '".$fdata['hoofdcategorie']."' ".
                      ",hoofdsector = '".$fdata['hoofdsector']."' ".
                      ",beleggingssector = '".$fdata['beleggingssector']."' ".
                      ",beleggingscategorie = '".$fdata['beleggingscategorie']."' ".
                      ",regio = '".$fdata['regio']."' ".
                      ",attributieCategorie = '".$fdata['attributieCategorie']."' ".
                      ",afmCategorie = '".$fdata['afmCategorie']."' ".
                      ",KoersDatum = '".$koersDatum."' ".
                      ",LaatsteKoers = '".$laatsteKoers."' ".
											",AandeelTotaalvermogen = '".$aandeelop['totaalvermogen']."' ".
                      ",ISINCode = '".$fondsData['ISINCode']."' ".
						         	",VKM = '".$fondsData['VKM']."' ".
							        ",passiefFonds = '".$fondsData['passiefFonds']."' ".
							        ",fondssoort = '".$fondsData['fondssoort']."' ".
                      ",fondsValuta = '".$fdata['fondsValuta']."' ".
                      ",renteWaarde = '".$fdata['renteWaarde']."' ".
                      ",fondsWaarde = '".$fdata['fondsWaarde']."' ".
											",AandeelTotaalBelegdvermogen = '".$aandeelop['vermogenbelegd']."' ".
											",AantalInPortefeuille = '".$fondsAantal."' ";
                      
						/*
						*/
						// gooi in nieuwe tmp Table....

						$DBt = new DB();
						$DBt->SQL($insert);
						$DBt->Query();
					  }
					}

				}


				if($_SESSION['reportBuilder']['incLiquiditeiten']==1)
				{
					$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaalWaarde " .
						" FROM TijdelijkeRapportage WHERE " .
						" type = 'fondsen' AND " .
						" beleggingscategorie = '" . $categorie . "' AND " .
						" rapportageDatum ='" . $einddatum . "' AND " .
						" portefeuille = '" . $portefeuille['Portefeuille'] . "' "
						. $__appvar['TijdelijkeRapportageMaakUniek'];
					debugSpecial($query, __FILE__, __LINE__);
					$DB2 = new DB();
					$DB2->SQL($query);
					$DB2->Query();
					$cdata = $DB2->nextRecord();
					$categorieWaarde = $cdata['totaalWaarde'];

					// selecteer belegingscategorie
					$query = " SELECT valuta as fondsValuta, totaalAantal, Bewaarder,
                      sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro, 
                      sum(if(type='rente',actuelePortefeuilleWaardeEuro,0)) as renteWaarde, 
                      sum(if(type='fondsen',actuelePortefeuilleWaardeEuro,0)) as fondsWaarde, 
                      beleggingscategorie,koersDatum ,actueleFonds,hoofdcategorie,hoofdsector,beleggingssector,
                      regio,attributieCategorie,afmCategorie  " .
						" FROM TijdelijkeRapportage WHERE " .
						" TijdelijkeRapportage.Portefeuille = '" . $portefeuille['Portefeuille'] . "' AND TijdelijkeRapportage.type='rekening' AND
                      rapportageDatum ='" . $einddatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . " GROUP BY Valuta ORDER BY TijdelijkeRapportage.valutaVolgorde";

					$DB2 = new DB();
					$DB2->SQL($query);
					$DB2->Query();
					while($fdata = $DB2->nextRecord())
					{ 
						$categorie = $fdata['beleggingscategorie'];
						$koersDatum = $fdata['koersDatum'];
						$laatsteKoers = $fdata['actueleFonds'];
						$fondsWaarde = $fdata['actuelePortefeuilleWaardeEuro'];
						$fondsAantal = $fdata['totaalAantal'];
						// selecteer totaal in categorie portefeuille


						$aandeelop = array();
						$aandeelop['totaalvermogen'] = $fondsWaarde / ($totaalWaarde / 100);
						$aandeelop['vermogenbelegd'] = $fondsWaarde / (($totaalWaarde - $liqWaarde) / 100);
						$aandeelop['beleggingscat'] = $fondsWaarde / (($categorieWaarde) / 100);
						$hist = array();

						$fondsData = array('Fonds' => $fdata['fondsValuta'], 'fondssoort' => 'LIQ');
						// build query ..
						$insert = "INSERT INTO `" . $this->tmp_table . "` SET " .
							" Portefeuille = '" . $portefeuille['Portefeuille'] . "' " .
							",Vermogensbeheerder = '" . $portefeuille['Vermogensbeheerder'] . "' " .
							",Client = '" . $portefeuille['Client'] . "' " .
							",Naam = '" . mysql_escape_string($portefeuille['Naam']) . "' " . //rvv add naam
							",Naam1 = '" . mysql_escape_string($portefeuille['Naam1']) . "' " .
              ",profielOverigeBeperkingen = '".mysql_escape_string($crmData['profielOverigeBeperkingen'])."' ".
							",Depotbank = '" . $portefeuille['Depotbank'] . "' " .
							",Bewaarder = '" . $fdata['Bewaarder'] . "' " .
							",Accountmanager = '" . $portefeuille['Accountmanager'] . "' " .
							",Risicoprofiel = '" . $portefeuille['Risicoprofiel'] . "' " .
							",SoortOvereenkomst = '" . $portefeuille['SoortOvereenkomst'] . "' " .
							",Risicoklasse = '" . $portefeuille['Risicoklasse'] . "' " .
							",Remisier = '" . $portefeuille['Remisier'] . "' " .
							",AFMprofiel = '" . $portefeuille['AFMprofiel'] . "' " .
							",InternDepot = '" . $portefeuille['InternDepot'] . "' " .
							",Fonds = '" . $fondsData['Fonds'] . "' " .
							",Kostprijs = '" . $hist['historischeWaarde'] . "' " .
							",Beginwaardelopendjaar = '" . $hist['beginwaardeLopendeJaar'] . "' " .
							",AandeelBeleggingscategorie = '" . $aandeelop['beleggingscat'] . "' " .
							",hoofdcategorie = '" . $fdata['hoofdcategorie'] . "' " .
							",hoofdsector = '" . $fdata['hoofdsector'] . "' " .
							",beleggingssector = '" . $fdata['beleggingssector'] . "' " .
							",beleggingscategorie = '" . $fdata['beleggingscategorie'] . "' " .
							",regio = '" . $fdata['regio'] . "' " .
							",attributieCategorie = '" . $fdata['attributieCategorie'] . "' " .
							",afmCategorie = '" . $fdata['afmCategorie'] . "' " .
							",KoersDatum = '" . $koersDatum . "' " .
							",LaatsteKoers = '" . $laatsteKoers . "' " .
							",AandeelTotaalvermogen = '" . $aandeelop['totaalvermogen'] . "' " .
							",ISINCode = '" . $fondsData['ISINCode'] . "' " .
							",VKM = '" . $fondsData['VKM'] . "' " .
							",passiefFonds = '" . $fondsData['passiefFonds'] . "' " .
							",fondssoort = '" . $fondsData['fondssoort'] . "' " .
							",fondsValuta = '" . $fdata['fondsValuta'] . "' " .
							",renteWaarde = '" . $fdata['renteWaarde'] . "' " .
							",fondsWaarde = '" . $fondsWaarde . "' " .
							",AandeelTotaalBelegdvermogen = '" . $aandeelop['vermogenbelegd'] . "' " .
							",AantalInPortefeuille = '" . $fondsAantal . "' ";

						/*
            */
						// gooi in nieuwe tmp Table....

						$DBt = new DB();
						$DBt->SQL($insert);
						$DBt->Query();

					}
				}
					// verwijder Data van tijdelijke tabel
				verwijderTijdelijkeTabel($portefeuille['Portefeuille'],$einddatum);

		}
		$this->buildCSV();

		if($this->progressbar)
			$this->progressbar->hide();
		$this->removeTmpTable();
	}

	function buildCSV()
	{
		// rebuild query on temp table.
		if(count($this->selectData['where3']) > 0)
		{
			$where3 = "";
			for($a = 0; $a < count($this->selectData['where3']); $a++)
			{
				$andor = "";
				if((count($this->selectData['where3'])) > ($a+1))
				{
					$andor = $this->selectData['where3'][$a]['andor'];
				}

				if($this->selectData['where3'][$a]['operator'] == "LIKE")
					$where3 .= $this->selectData['where3'][$a]['field']." ".
										$this->selectData['where3'][$a]['operator']." '%".
										$this->selectData['where3'][$a]['search']."%' ".$andor." ";
				else
					$where3 .= $this->selectData['where3'][$a]['field']." ".
										$this->selectData['where3'][$a]['operator']." '".
										$this->selectData['where3'][$a]['search']."' ".$andor." ";
			}

			if($where3 <> "")
				$extraquery = " AND ( ".$where3." ) ";
		}

		// maak veld selectie
		// maak CSV header
		for($a=0; $a < count($this->selectData['fields']); $a++)
		{
			$used=false;
			for($i=0;$i<=count($this->selectData['groupby']);$i++)
			{
				if ($this->selectData['fields'][$a] == $this->selectData['groupby'][$i]['actionField'])
				{
					$selectThis[] = $this->selectData['groupby'][$i]['actionType'] . "(" . $this->selectData['groupby'][$i]['actionField'] . ") AS " .
						$this->selectData['groupby'][$i]['actionType'] . "_" . $this->selectData['fields'][$a];
					$header[] = $this->selectData['groupby'][$i]['actionType'] . "_" . $this->selectData['fields'][$a];
					$used=true;
				}
			}
			if($used==false)
			{
				$selectThis[] = $this->selectData['fields'][$a];
				$header[] = $this->selectData['fields'][$a];
			}
		}

		$fields = implode(", ",$selectThis);

		$this->excelData[] = $header;
		if(count($this->selectData['groupby']) > 0)
		{
			$groupby = " GROUP BY ";
			foreach($this->selectData['groupby'] as $index=>$gb)
			{
				if($index==0)
					$groupby .= $gb['field'];
				else
					$groupby .= ', '.$gb['field'];
			}
		}

		if(count($this->selectData['orderby']) > 0)
		{
			for($a = 0; $a < count($this->selectData['orderby']); $a++)
			{
				$sort[] = $this->selectData['orderby'][$a]['field'];
				if(!empty($this->selectData['orderby'][$a]['order']))
					$order[] = $this->selectData['orderby'][$a]['order'];
			}
		}

		// order by
		for($a=0; $a <= count($sort); $a++)
		{
			if(!empty($sort[$a]))
				$orderbyarray[] = $sort[$a]." ".$order[$a];
		}
		$orderby='';
		if($orderbyarray)
			$orderby .= " ORDER BY ".implode(", ",$orderbyarray);

		$query = "SELECT $fields FROM `".$this->tmp_table."` WHERE 1 ".$extraquery." ".$groupby." ".$orderby;
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($cdata = $DB->nextRecord("num"))
		{
			$this->excelData[] = $cdata;
		}
	}


	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$exceldata = generateCSV($this->excelData);
			fwrite($fp,$exceldata);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}
	}
	function OutputXls($filename,$type="S")
	{
		global $__appvar;
	  include_once('../classes/excel/Writer.php');

		$workbook = new Spreadsheet_Excel_Writer($filename);
    $worksheet =& $workbook->addWorksheet();
    $this->excelOpmaak['date']=array('setNumFormat'=>'DD-MM-YYYY');
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }
    }

    if ($this->nullenOnderdrukken == 1)
	  {
	   $this->excelData =  $this->verwijderNulwaarden($this->excelData);
	  }

	   for($regel = 0; $regel < count($this->excelData); $regel++ )
	   {
		   for($col = 0; $col < count($this->excelData[$regel]); $col++)
		   {
		     if (is_array($this->excelData[$regel][$col]))
		     {
		       //$opmaak[$opmaakSleutel]
		       $celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col][0],$opmaak[$celOpmaak]);	//0=waarde
		     }
		     else
		     {
		       $waarde=$this->excelData[$regel][$col];
		       $worksheet->write($regel, $col, $waarde);
		     }
		   }
	   }

	   $workbook->close();
	}

	 	function fillXlsSheet($worksheet)
	{
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }

    }

	   for($regel = 0; $regel < count($this->excelData); $regel++ )
	   {
		   for($col = 0; $col < count($this->excelData[$regel]); $col++)
		   {
		     if (is_array($this->excelData[$regel][$col]))
		     {
		       //$opmaak[$opmaakSleutel]
		       $celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col][0],$opmaak[$celOpmaak]);	//0=waarde
		     }
		     else
		     {
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col]);
		     }
		   }
	   }
	}
}
?>
