<?php

/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/30 06:43:26 $
File Versie					: $Revision: 1.43 $

$Log: ManagementBuilder.php,v $
Revision 1.43  2020/05/30 06:43:26  rvv
*** empty log message ***

Revision 1.42  2019/01/20 12:13:28  rvv
*** empty log message ***

Revision 1.41  2017/03/25 15:59:41  rvv
*** empty log message ***

Revision 1.40  2017/02/06 13:46:22  rvv
*** empty log message ***

Revision 1.39  2017/02/05 16:22:11  rvv
*** empty log message ***

Revision 1.38  2017/01/11 17:12:04  rvv
*** empty log message ***

Revision 1.37  2017/01/07 16:22:18  rvv
*** empty log message ***

Revision 1.36  2016/04/20 15:49:08  rvv
*** empty log message ***

Revision 1.35  2016/03/20 14:37:26  rvv
*** empty log message ***

Revision 1.34  2016/01/09 18:57:51  rvv
*** empty log message ***

Revision 1.33  2015/04/06 19:59:33  rvv
*** empty log message ***

Revision 1.32  2015/04/04 15:14:38  rvv
*** empty log message ***

Revision 1.31  2014/12/21 10:32:26  rvv
*** empty log message ***

Revision 1.30  2014/05/21 15:20:33  rvv
*** empty log message ***

Revision 1.29  2013/05/12 11:18:58  rvv
*** empty log message ***

Revision 1.28  2012/10/28 11:03:22  rvv
*** empty log message ***

Revision 1.27  2012/10/12 07:27:21  cvs
update 12-10-2012

Revision 1.26  2012/10/10 13:36:56  cvs
update 10-10-2012

Revision 1.25  2012/09/05 18:18:18  rvv
*** empty log message ***

Revision 1.24  2012/08/11 13:06:05  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");


class ManagementBuilder
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	var $tmp_table;
	var $tmp_table_struct;

	function ManagementBuilder(  $selectData )
	{
	  global $USR;
		$this->selectData = $selectData;
		$this->excelData 	= array();
		$this->datum = mktime();
   
		$this->tmp_table = "tmp_reportbuilder_$USR";
		$this->tmp_table_struct = "CREATE TABLE `".$this->tmp_table."` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Portefeuille` VARCHAR( 24 ) NOT NULL ,
`Vermogensbeheerder` VARCHAR( 10 ) NOT NULL ,
`Client` VARCHAR( 16 ) NOT NULL ,
`Consolidatie` tinyint(4) NOT NULL default '0',
`Naam` VARCHAR( 50 ) NOT NULL ,
`profielOverigeBeperkingen` text NOT NULL,
`Depotbank` VARCHAR( 10 ) NOT NULL ,
`InternDepot` tinyint(4) NOT NULL default '0',
`Startdatum` DATETIME NOT NULL ,
`Einddatum` DATETIME NOT NULL ,
`ClientVermogensbeheerder` VARCHAR( 10 ) NOT NULL ,
`Accountmanager` VARCHAR( 15 ) NOT NULL ,
`Risicoprofiel` VARCHAR( 15 ) NOT NULL ,
`SoortOvereenkomst` VARCHAR( 15 ) NOT NULL ,
`Risicoklasse` VARCHAR( 50 ) NOT NULL ,
`Remisier` VARCHAR( 15 ) NOT NULL ,
`AFMprofiel` VARCHAR( 15 ) NOT NULL ,
`ModelPortefeuille` VARCHAR( 12 ) NOT NULL ,
`totaalvermogen` DOUBLE NOT NULL ,
`totaalbeginvermogen` DOUBLE NOT NULL ,
`inprocenttotaal` DOUBLE NOT NULL ,
`performance` DOUBLE NOT NULL ,
`resultaat` DOUBLE NOT NULL ,
`rendement` DOUBLE NOT NULL ,
`OnttrLicht` DOUBLE NOT NULL ,
`Onttrekkingen` DOUBLE NOT NULL ,
`Lichtingen` DOUBLE NOT NULL ,
`StortDepon` DOUBLE NOT NULL ,
`Stortingen` DOUBLE NOT NULL ,
`Deponeringen` DOUBLE NOT NULL ,
`dividend` DOUBLE NOT NULL ,
`dividendbelasting` DOUBLE NOT NULL ,
`rente` DOUBLE NOT NULL ,
`koersongerealiseerd` DOUBLE NOT NULL ,
`koersgerealiseerd` DOUBLE NOT NULL ,
`stockdividend` DOUBLE NOT NULL ,
`creditrente` DOUBLE NOT NULL ,
`transactiekosten` DOUBLE NOT NULL ,
`kostenBuitenland` DOUBLE NOT NULL ,
`beheerfee` DOUBLE NOT NULL ,
`bewaarloon` DOUBLE NOT NULL ,
`bankkosten` DOUBLE NOT NULL ,
`liquiditeiten` DOUBLE NOT NULL ,
`gemVermogen` DOUBLE NOT NULL ,
`omzet` DOUBLE NOT NULL ,
`omzetsnelheid` DOUBLE NOT NULL ,
`benchmarkRendement` DOUBLE NOT NULL ,
`BANK` DOUBLE NOT NULL ,
`TOB` DOUBLE NOT NULL ,
`BTLBR` DOUBLE NOT NULL ,
`VALK` DOUBLE NOT NULL ,
PRIMARY KEY ( `id` )
) ";

		$this->removeTmpTable();
		$this->createTmpTable();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function removeTmpTable()
	{
		$DB = new DB();
  	$DB->SQL("DROP TABLE IF EXISTS `".$this->tmp_table."` ");
    $DB->Query();
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
    $extraquery='';
		$einddatumJul = $this->datum;
		$einddatum = jul2sql($this->datum);

		$jaar = date("Y",$einddatumJul);




for ($i=1; $i <3; $i++)
{
	$where='where'. $i;
	if(count($this->selectData[$where]) > 0 )
		{
			$where2 = "";
			for($a = 0; $a < count($this->selectData[$where]); $a++)
			{
			$andor = "";
			if((count($this->selectData[$where])) > ($a+1))
			  {
				$andor = $this->selectData[$where][$a]['andor'];
			  }
			if(	$this->selectData[$where][$a]['field'] != "" )
			  {
				if($this->selectData[$where][$a]['operator'] == "LIKE")
				{
				  $waarde = $this->selectData[$where][$a]['search'];
				  if (strstr($waarde,"%"))  // als wildcards aangegeven dan die gebruiken
				  {
				    $where2 .= "Portefeuilles.".$this->selectData[$where][$a]['field']." ".
						  				$this->selectData[$where][$a]['operator']." '".
							  			$this->selectData[$where][$a]['search']."' ".$andor." ";
				  }
				  else                    // als geen wildcards aangegeven dan %zoek%
				  {
					  $where2 .= "Portefeuilles.".$this->selectData[$where][$a]['field']." ".
							  			$this->selectData[$where][$a]['operator']." '%".
						  				$this->selectData[$where][$a]['search']."%' ".$andor." ";
				  }
				}
				else
					$where2 .= "Portefeuilles.".$this->selectData[$where][$a]['field']." ".
										$this->selectData[$where][$a]['operator']." '".
										$this->selectData[$where][$a]['search']."' ".$andor." ";
			  }
			}
			if($where2 <> "")
				$extraquery .= " AND ( ".$where2." ) ";
		  }
		// controle op einddatum portefeuille
}

		// controle op einddatum portefeuille
		if($this->selectData['inactiefOpnemen'] == 1)
	    $extraquery='';
	  else
	    $extraquery  .= " AND Portefeuilles.Einddatum > '".$einddatum."'  ";
    
    if($_SESSION['reportBuilder']['incConsolidaties']==1)
    {
      $consolidatieFilter='AND Portefeuilles.consolidatie<2';
    }
    else
    {
      $consolidatieFilter='AND Portefeuilles.consolidatie=0';
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
	    $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang ) ";
    else
	    $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }

	 $join .= " JOIN Vermogensbeheerders on Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";

		$query = " SELECT ".
					" Portefeuilles.ClientVermogensbeheerder, ".
					" Portefeuilles.Portefeuille, ".
					" Portefeuilles.Depotbank, ".
          " Portefeuilles.InternDepot, ".
					" Portefeuilles.Client, ".
          " Portefeuilles.Consolidatie, ".
					" Portefeuilles.Startdatum, ".
					" Portefeuilles.Einddatum, ".
					" Portefeuilles.Client, ".
					" Portefeuilles.Vermogensbeheerder, ".
					" Portefeuilles.Depotbank, ".
					" Portefeuilles.Accountmanager, ".
					" Portefeuilles.Risicoprofiel, ".
					" Portefeuilles.SoortOvereenkomst, ".
					" Portefeuilles.Risicoklasse, ".
					" Portefeuilles.Remisier, ".
					" Portefeuilles.AFMprofiel, ".
					" Portefeuilles.ModelPortefeuille, ".
			    " Portefeuilles.SpecifiekeIndex, ".
					" Vermogensbeheerders.PerformanceBerekening, ".
					" Clienten.Naam ".
					" FROM (Portefeuilles, Clienten)  $join WHERE Portefeuilles.Client = Clienten.Client ".$extraquery." $consolidatieFilter ".$beperktToegankelijk;


		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$records = $DB->records();

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 50 / $records;
		}

		if ($this->selectData['datumVanaf'] != "")
	   {
	    $begindatum	= $this->selectData['datumVanaf'];
	    $begindatum = form2jul($begindatum);
	    $begindatum = jul2sql($begindatum);
	   }
	   else
     {
	    $begindatumJul = mktime(1,1,1,1,1,$jaar);
	    $begindatum = jul2sql($begindatumJul);
	   }
    $grandtotaalWaarde=0;
		while($pdata = $DB->nextRecord())
		{
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$pdata['Portefeuille']." (Vullen tijdelijke rapportage)");
			}

			$portefeuille = $pdata['Portefeuille'];
			if(db2jul($begindatum) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata['Startdatum']; //31-08-2006 rvv $begindatum vervangen voor startdatum.
			}
			else
			{
				$startdatum = $begindatum;
			}

			$julrapport 		= db2jul($startdatum);
			$rapportMaand 	= date("m",$julrapport);
			$rapportDag 		= date("d",$julrapport);

			if($rapportMaand == 1 && $rapportDag == 1)
				$startjaar = true;
			else
				$startjaar = false;

			$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille,  $startdatum, $startjaar,'',$startdatum);
			$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille,  $einddatum,'','',$startdatum);

			vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$startdatum);
			vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$einddatum);
			//rvv
						// tel totaal op!
			$DB2 = new DB();
			$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($queryTotaal,__FILE__,__LINE__);

			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$totaalWaarde = $DB2->nextRecord();
			$grandtotaalWaarde += $totaalWaarde['totaal'];

		}

				// nog een keer een loop over de portefeuilles!
		$DB = new DB();
    $DB2 = new DB();
		$DB->SQL($query);
		$DB->Query();
		if($DB->records() <= 0)
		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			$this->progressbar->hide();
			exit;
		}
		$tel = 0;
		while($pdata = $DB->nextRecord())
		{
      $crmData=getCrmNaam($pdata['Portefeuille']);
		  if($crmData['CrmClientNaam'] == 1)
		  {
		     $pdata['Naam']=$crmData['naam'];
		     $pdata['Naam1']=$crmData['naam1'];
        $query="SELECT profielOverigeBeperkingen FROM CRM_naw WHERE Portefeuille='".mysql_real_escape_string($pdata['Portefeuille'])."'";
        $DB2->SQL($query);
        $crmData=$DB2->lookupRecord();
		  }

			$tel ++;
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
        logScherm("Portefeuille: ".$pdata['Portefeuille']." (Gegevens ophalen)");
			}
		//	$startdatum = jul2sql($begindatumJul);
			$portefeuille = $pdata['Portefeuille'];
			if(db2jul($begindatum) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata['Startdatum'];
			}
			else
			{
				$startdatum = $begindatum;
			}
			$julrapport 		= db2jul($startdatum);
			$rapportMaand 	= date("m",$julrapport);
			$rapportDag 		= date("d",$julrapport);

			//rvvend
			// doe berekeningen.
			$DB2 = new DB();
			$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' AND ".
							 " type = 'rekening' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($queryTotaal,__FILE__,__LINE__);

			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$totaalWaarde = $DB2->nextRecord();
			$liquiditeiten = $totaalWaarde['totaal'];


			$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($queryTotaal,__FILE__,__LINE__);
			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$totaalWaarde = $DB2->nextRecord();
			$totaalvermogen = $totaalWaarde['totaal'];

			$inprocenttotaal = $totaalvermogen / $grandtotaalWaarde * 100;

			// haal beginwaarde op
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$startdatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);

			$DB2->SQL($query);
			$DB2->Query();
			$totaalWaarde= $DB2->nextRecord();
			$totaalbeginvermogen = $totaalWaarde['totaal'];
			$performance 			  = performanceMeting($portefeuille, $startdatum, $einddatum, $pdata['PerformanceBerekening']);
			$stortingen = getStortingen($portefeuille,$startdatum,$einddatum);
			$onttrekkingen = getOnttrekkingen($portefeuille,$startdatum,$einddatum);
			
			$query = "SELECT 
SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)-(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)) AS waarde , 
Grootboekrekeningen.Onttrekking,
Grootboekrekeningen.Storting,
Rekeningen.Memoriaal
FROM Rekeningmutaties 
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening 
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening
WHERE 
Rekeningen.Portefeuille = '$portefeuille' AND Rekeningmutaties.Verwerkt = '1' AND 
Rekeningmutaties.Boekdatum > '$startdatum' AND Rekeningmutaties.Boekdatum <= '$einddatum' 
AND (Grootboekrekeningen.Onttrekking=1 OR Grootboekrekeningen.Storting=1)
GROUP BY Grootboekrekeningen.Storting,Grootboekrekeningen.Onttrekking,Rekeningen.Memoriaal";
			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();
			$stortontr=array();
			while($data = $DB2->nextRecord())
			{
				if($data['Memoriaal']==1)
				{
					if($data['Storting']==1)
				  	$stortontr['Deponeringen'] += $data['waarde'];
					else
					  $stortontr['Lichtingen'] += $data['waarde'];
				}
				else
				{
					if($data['Storting']==1)
				  	$stortontr['Stortingen'] += $data['waarde'];
					else
					  $stortontr['Onttrekkingen'] += $data['waarde'];
				}
			}

			$waardeMutatie 	   	= $totaalvermogen - $totaalbeginvermogen;
      $gemiddelde	= ($totaalvermogen + $totaalbeginvermogen)/2;
			$resultaat = $waardeMutatie - $stortingen + $onttrekkingen;

			if($pdata['SpecifiekeIndex']<>'')
				$benchmarkRendement=getFondsPerformance($pdata['SpecifiekeIndex'],$startdatum,$einddatum);
			else
				$benchmarkRendement=0;

    //omzet
    $query = "SELECT
SUM(abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Debet)+abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Credit)) as omzet
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE 
Rekeningen.Portefeuille='$portefeuille' AND Rekeningmutaties.Boekdatum > '$startdatum' AND Rekeningmutaties.Boekdatum <= '$einddatum'
AND Rekeningmutaties.Grootboekrekening='FONDS' AND
Rekeningmutaties.Transactietype IN('A','A/O','A/S','V','V/O','V/S') ";
			  $DB2->SQL($query);
			  $DB2->Query();
			  $totaal = $DB2->nextRecord();
  			$omzet = $totaal['omzet'];
        $omzetsnelheid = $omzet/$gemiddelde*100;
			// rendement == performance

			// ophalen van rente totaal A en rentetotaal B
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' AND ".
							 " type = 'rente' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);

			$DB2->SQL($query);
			$DB2->Query();
			$totaalA = $DB2->nextRecord();

			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$startdatum."' AND ".
							 " portefeuille = '".$portefeuille."' AND ".
							 " type = 'rente' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB2->SQL($query);
			$DB2->Query();
			$totaalB = $DB2->nextRecord();

			$rente = $totaalA['totaal'] - $totaalB['totaal'];
			//$totaalOpbrengst += $opgelopenRente;

			// koersresultaat
	 		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaalB, ".
	 						 "SUM(beginPortefeuilleWaardeEuro) AS totaalA ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$startdatum."' AND ".
							 " portefeuille = '".$portefeuille."' AND ".
							 " type = 'fondsen' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB2->SQL($query);
			$DB2->Query();
			$totaal = $DB2->nextRecord();

			$koersongerealiseerd = $totaal['totaalB'] - $totaal['totaalA'];
			//$totaalOpbrengst += $ongerealiseerdeKoersResultaat;

			$koersgerealiseerd = gerealiseerdKoersresultaat($portefeuille, $startdatum, $einddatum);
			//$totaalOpbrengst += $gerealiseerdeKoersResultaat;

			// loopje over grootboekrekeningen
			$query = "SELECT Rekeningmutaties.Grootboekrekening, ".
			"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) AS totaalcredit, ".
			"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaaldebet ".
			"FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
			"WHERE ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$startdatum."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$einddatum."'  ".
			"GROUP BY Rekeningmutaties.Grootboekrekening ";
$query="SELECT Rekeningmutaties.Grootboekrekening, if(Rekeningmutaties.Fonds='','','gekoppeld') as fondsgekoppeld,
			SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) AS totaalcredit, 
			SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaaldebet 
			FROM 
     Rekeningmutaties 
    JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening  
    JOIN  Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
			WHERE 
			Rekeningen.Portefeuille = '".mysql_real_escape_string($portefeuille)."' AND 
			Rekeningmutaties.Verwerkt = '1' AND 
			Rekeningmutaties.Boekdatum > '".$startdatum."' AND Rekeningmutaties.Boekdatum <= '".$einddatum."'  
			GROUP BY Rekeningmutaties.Grootboekrekening ,fondsgekoppeld";
			$DB2->SQL($query);
			$DB2->Query();
      $grootboekWaarden=array();
			while($grootboek = $DB2->nextRecord())
			{
			  $grootboekWaarden[$grootboek['Grootboekrekening']]+=($grootboek['totaalcredit']-$grootboek['totaaldebet']);
			}
   
   
			// Insert into tmp table.
			$insert = "INSERT INTO `".$this->tmp_table."` SET ".
								" Portefeuille = '".mysql_escape_string($pdata['Portefeuille'])."' ".
								",Vermogensbeheerder = '".mysql_escape_string($pdata['Vermogensbeheerder'])."' ".
								",Client = '".mysql_escape_string($pdata['Client'])."' ".
                ",Consolidatie = '".mysql_escape_string($pdata['Consolidatie'])."' ".
								",Naam = '".mysql_escape_string($pdata['Naam'])."' ".
                ",profielOverigeBeperkingen = '".mysql_escape_string($crmData['profielOverigeBeperkingen'])."' ".
								",Depotbank = '".mysql_escape_string($pdata['Depotbank'])."' ".
                ",InternDepot = '".mysql_escape_string($pdata['InternDepot'])."' ".
								",Startdatum = '".mysql_escape_string($pdata['Startdatum'])."' ".
								",Einddatum = '".mysql_escape_string($pdata['Einddatum'])."' ".
								",ClientVermogensbeheerder = '".mysql_escape_string($pdata['ClientVermogensbeheerder'])."' ".
								",Accountmanager = '".mysql_escape_string($pdata['Accountmanager'])."' ".
								",Risicoklasse = '".mysql_escape_string($pdata['Risicoklasse'])."' ".
								",Remisier = '".mysql_escape_string($pdata['Remisier'])."' ".
								",AFMprofiel = '".mysql_escape_string($pdata['AFMprofiel'])."' ".
								",ModelPortefeuille = '".mysql_escape_string($pdata['ModelPortefeuille'])."' ".
								",totaalvermogen = '".mysql_escape_string(round($totaalvermogen,2))."' ".
				        ",totaalbeginvermogen = '".mysql_escape_string(round($totaalbeginvermogen,2))."' ".
				        ",inprocenttotaal = '".mysql_escape_string(round($inprocenttotaal,2))."' ".
								",performance = '".mysql_escape_string(round($performance,2))."' ".
								",resultaat = '".mysql_escape_string(round($resultaat,2))."' ".
								",rendement = '".mysql_escape_string(round($performance,2))."' ".
								",OnttrLicht = '".mysql_escape_string(round($onttrekkingen,2))."' ".
			         	",Onttrekkingen = '".mysql_escape_string(round($stortontr['Onttrekkingen'],2))."' ".
				        ",Lichtingen = '".mysql_escape_string(round($stortontr['Lichtingen'],2))."' ".
			        	",Stortingen = '".mysql_escape_string(round($stortontr['Stortingen'],2))."' ".
			        	",Deponeringen = '".mysql_escape_string(round($stortontr['Deponeringen'],2))."' ".
								",StortDepon = '".mysql_escape_string(round($stortingen,2))."' ".
								",dividend = '".mysql_escape_string(round($grootboekWaarden['DIV'],2))."' ".
								",dividendbelasting = '".mysql_escape_string(round($grootboekWaarden['DIVBE'],2))."' ".
								",rente = '".mysql_escape_string(round($rente,2))."' ".
								",koersongerealiseerd = '".mysql_escape_string(round($koersongerealiseerd,2))."' ".
								",koersgerealiseerd = '".mysql_escape_string(round($koersgerealiseerd,2))."' ".
								",stockdividend = '".mysql_escape_string(round($grootboekWaarden['VKSTO'],2))."' ".
								",creditrente = '".mysql_escape_string(round($grootboekWaarden['RENTE'],2))."' ".
								",transactiekosten = '".mysql_escape_string(round($grootboekWaarden['KOST'],2))."' ".
								",kostenbuitenland = '".mysql_escape_string(round($grootboekWaarden['KOBU'],2))."' ".
								",beheerfee = '".mysql_escape_string(round($grootboekWaarden['BEH'],2))."' ".
								",bewaarloon = '".mysql_escape_string(round($grootboekWaarden['BEW'],2))."' ".
								",bankkosten = '".mysql_escape_string(round($grootboekWaarden['KNBA'],2))."' ".
								",liquiditeiten = '".mysql_escape_string(round($liquiditeiten,2))."' ".
                ",omzet = '".mysql_escape_string(round($omzet,2))."' ".
                ",omzetsnelheid = '".mysql_escape_string(round($omzetsnelheid,2))."' ".
                ",TOB = '".mysql_escape_string(round($grootboekWaarden['TOB'],2))."' ".
                ",BTLBR = '".mysql_escape_string(round($grootboekWaarden['BTLBR'],2))."' ".
                ",BANK = '".mysql_escape_string(round($grootboekWaarden['BANK'],2))."' ".
                ",VALK = '".mysql_escape_string(round($grootboekWaarden['VALK'],2))."' ".
                ",gemVermogen = '".mysql_escape_string(round($gemiddelde,2))."' ".
			        	",benchmarkRendement = '".mysql_escape_string(round($benchmarkRendement,2))."' ".
								",SoortOvereenkomst = '".mysql_escape_string($pdata['SoortOvereenkomst'])."' ";


			$totaalvermogen = '';$inprocenttotaal='';$performance='';$resultaat='';$onttrekkingen='';$stortingen='';$dividend='';
$rente='';$koersongerealiseerd='';$stockdividend='';$creditrente='';$transactiekosten='';$beheerfee='';$bewaarloon='';
$bankkosten='';$liquiditeiten='';$kostenbuitenland='';$dividendbelasting='';$gemiddelde='';$omzet='';$omzetsnelheid='';
$totaalbeginvermogen='';
//echo $insert;
			$DBt = new DB();
			$DBt->SQL($insert);
			$DBt->Query();

			//verwijderTijdelijkeTabel($portefeuille,$einddatum);
			verwijderTijdelijkeTabel($portefeuille,$startdatum);

			// en weer verwijderen ?
		}
		// maak nu maar de 2e selectie en de CSV
		$this->buildCSV();

		if($this->progressbar)
			$this->progressbar->hide();
		$this->removeTmpTable();
	}

	function buildCSV()
	{
		// rebuild query on temp table.
    $extraquery='';
for ($i=3; $i <4; $i++)
{
	$where='where'. $i;
	if(count($this->selectData[$where]) > 0 )
		{
			$where2 = "";
			for($a = 0; $a < count($this->selectData[$where]); $a++)
			{
			$andor = "";
			if((count($this->selectData[$where])) > ($a+1))
			  {
				$andor = $this->selectData[$where][$a]['andor'];
			  }
			if(	$this->selectData[$where][$a]['field'] != "" )
			  {
				if($this->selectData[$where][$a]['operator'] == "LIKE")
				{
				  $waarde = $this->selectData[$where][$a]['search'];
				  if (strstr($waarde,"%"))  // als wildcards aangegeven dan die gebruiken
				  {
				    $where2 .= 			$this->selectData[$where][$a]['field']." ".
						  				$this->selectData[$where][$a]['operator']." '".
							  			$this->selectData[$where][$a]['search']."' ".$andor." ";
				  }
				  else                    // als geen wildcards aangegeven dan %zoek%
				  {
					  $where2 .= 		$this->selectData[$where][$a]['field']." ".
							  			$this->selectData[$where][$a]['operator']." '%".
						  				$this->selectData[$where][$a]['search']."%' ".$andor." ";
				  }
				}
				else
					$where2 .= 			$this->selectData[$where][$a]['field']." ".
										$this->selectData[$where][$a]['operator']." '".
										$this->selectData[$where][$a]['search']."' ".$andor." ";
			  }
			}
			if($where2 <> "")
				$extraquery .= " AND ( ".$where2." ) ";
		  }
		// controle op einddatum portefeuille

		//$extraquery  .= " AND Einddatum > '".$einddatum."' ";
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
		$orderbyarray=array();
		for($a=0; $a <= count($sort); $a++)
		{
			if(!empty($sort[$a]))
				$orderbyarray[] = $sort[$a]." ".$order[$a];
		}
		if(count($orderbyarray)>0)
			$orderby = " ORDER BY ".implode(", ",$orderbyarray);

		$query = "SELECT $fields FROM `".$this->tmp_table."` WHERE 1 ".$extraquery." ".$groupby." ".$orderby;
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($cdata = $DB->nextRecord("num"))
		{
			$this->excelData[] = $cdata;
		}

		$this->removeTmpTable();
	}


	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$csvdata = generateCSV($this->excelData);
			fwrite($fp,$csvdata);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}

	}

	function OutputXls($filename,$type="S")
	{
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


	   for($regel = 0; $regel < count($this->excelData); $regel++ )
	   {
		   for($col = 0; $col < count($this->excelData[$regel]); $col++)
		   {
		     if (is_array($this->excelData[$regel][$col]))
		     {
		       //$opmaak[$opmaakSleutel]
		       //$celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col][0]);	//0=waarde
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
