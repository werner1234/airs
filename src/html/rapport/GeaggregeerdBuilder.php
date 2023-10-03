<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/08 16:03:54 $
File Versie					: $Revision: 1.37 $

$Log: GeaggregeerdBuilder.php,v $
Revision 1.37  2019/06/08 16:03:54  rvv
*** empty log message ***

Revision 1.36  2019/01/20 12:13:28  rvv
*** empty log message ***

Revision 1.35  2017/08/26 17:38:48  rvv
*** empty log message ***

Revision 1.34  2017/05/26 16:44:29  rvv
*** empty log message ***

Revision 1.33  2017/03/25 15:59:41  rvv
*** empty log message ***

Revision 1.32  2017/03/15 16:35:00  rvv
*** empty log message ***

Revision 1.31  2016/01/17 18:11:29  rvv
*** empty log message ***

Revision 1.30  2016/01/09 18:57:51  rvv
*** empty log message ***

Revision 1.29  2016/01/06 16:30:36  rvv
*** empty log message ***

Revision 1.28  2015/12/02 16:17:27  rvv
*** empty log message ***

Revision 1.27  2015/11/22 14:30:47  rvv
*** empty log message ***

Revision 1.26  2015/09/05 16:22:37  rvv
*** empty log message ***

Revision 1.25  2015/03/11 16:54:56  rvv
*** empty log message ***

Revision 1.24  2014/11/12 16:41:04  rvv
*** empty log message ***

Revision 1.23  2014/07/02 15:57:25  rvv
*** empty log message ***

Revision 1.22  2014/06/29 15:16:29  rvv
*** empty log message ***

Revision 1.21  2014/05/21 15:20:33  rvv
*** empty log message ***

Revision 1.20  2013/05/12 11:18:58  rvv
*** empty log message ***

Revision 1.19  2013/03/09 16:21:23  rvv
*** empty log message ***

Revision 1.18  2012/08/11 13:06:05  rvv
*** empty log message ***

Revision 1.17  2012/07/25 16:00:32  rvv
*** empty log message ***

Revision 1.16  2012/04/04 16:08:04  rvv
*** empty log message ***

Revision 1.15  2012/01/22 13:45:35  rvv
*** empty log message ***

Revision 1.14  2011/12/11 10:58:18  rvv
*** empty log message ***

Revision 1.13  2011/09/14 18:49:08  rvv
*** empty log message ***

Revision 1.12  2009/10/14 15:46:29  rvv
*** empty log message ***

Revision 1.11  2009/05/10 08:59:47  rvv
*** empty log message ***

Revision 1.10  2009/03/14 11:42:57  rvv
*** empty log message ***

Revision 1.9  2009/01/20 17:44:08  rvv
*** empty log message ***

Revision 1.8  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.7  2008/04/23 09:06:06  rvv
*** empty log message ***

Revision 1.6  2006/11/27 09:35:12  rvv
filter selectie

Revision 1.5  2006/11/10 11:55:08  rvv
Aanpassing filter selectie

Revision 1.4  2006/09/07 06:14:12  rvv
Liquiditeiten meenemen

Revision 1.3  2006/09/04 07:16:39  rvv
Toevoeging voor het bepalen van het aandeel van een fonds aan de Totaalwaarde.

Revision 1.2  2006/06/28 12:20:30  jwellner
*** empty log message ***

Revision 1.1  2006/04/27 09:03:24  jwellner
*** empty log message ***


*/

include_once("rapportRekenClass.php");

class GeaggregeerdBuilder
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;


	var $tmp_table;
	var $tmp_table_struct;

	function GeaggregeerdBuilder( $selectData )
	{
	  global $USR;
		$this->selectData = $selectData;
		$this->excelData 	= array();
		$this->datum = mktime();
		$this->tmp_table = "tmp_reportbuilder_$USR";
		$this->tmp_table_struct = "CREATE TABLE `".$this->tmp_table."` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Portefeuille` VARCHAR( 25 ) NOT NULL ,
`Consolidatie` TINYINT NOT NULL ,
`Fonds` VARCHAR( 25 ) NOT NULL ,
`standaardSector` VARCHAR( 15 ) NOT NULL ,
`Omschrijving` VARCHAR( 50 ) NOT NULL ,
`FondsImportCode` VARCHAR( 16 ) NOT NULL ,
`Valuta` VARCHAR( 4 ) NOT NULL ,
`Fondseenheid` DOUBLE NOT NULL ,
`Rentedatum` DATETIME NOT NULL ,
`Renteperiode` BIGINT NOT NULL ,
`ISINCode` VARCHAR( 26 ) NOT NULL ,
`rating` VARCHAR( 26 ) NOT NULL ,
`internDepot` TINYINT NOT NULL ,
`TGBCode` VARCHAR( 25 ) NOT NULL ,
`stroeveCode` VARCHAR( 25 ) NOT NULL ,
`AABCode` VARCHAR( 26 ) NOT NULL ,
`Beleggingscategorie` VARCHAR( 15 ) NOT NULL ,
`Regio` VARCHAR( 15 ) NOT NULL ,
`AttributieCategorie` VARCHAR( 15 ) NOT NULL ,
`afmCategorie` VARCHAR( 15 ) NOT NULL ,				
`Duurzaamheid` VARCHAR( 15 ) NOT NULL ,
`RisicoPercentageFonds` DOUBLE NOT NULL ,
`Beleggingssector` VARCHAR ( 15 ) NOT NULL ,
`Zorgplicht` VARCHAR ( 50 ) NOT NULL ,
`Aantal` DECIMAL (14,6) NOT NULL ,
`Fondskoers` DOUBLE NOT NULL ,
`Fondstotaal` DOUBLE NOT NULL ,
`FondstotaalEUR` DOUBLE NOT NULL ,
`PercentageTotaal` DOUBLE NOT NULL ,
`KoersDatum` date NOT NULL default '0000-00-00',
`AantalWaarnemingen` DECIMAL (12,4) NOT NULL ,
`opgelopenrente` DECIMAL (12,4) NOT NULL ,
`opgelopenrenteFondsvaluta` DECIMAL (12,4) NOT NULL ,
`FondsYtd` DOUBLE NOT NULL ,
`VKM` tinyint(3) NOT NULL,
`passiefFonds` tinyint(3) NOT NULL,
PRIMARY KEY (`id`)
)";

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
		$select = "SHOW TABLES LIKE '".$this->tmp_table."'";
    logScherm("Is tabel aanwezig? : ".$this->tmp_table);
	  $DB->SQL($select);
	  if ($DB->lookupRecord())
	  {
			$DB->SQL("DROP TABLE `".$this->tmp_table."` ");
      logScherm("Verwijderen : ".$this->tmp_table);
			return $DB->Query();
	  }
	}

	function createTmpTable()
	{
		$DB = new DB();
		$DB->SQL($this->tmp_table_struct);
    logScherm("Aanmaken: ".$this->tmp_table);
		return $DB->Query();
	}

	function berekenWaarde()
	{
		$this->tijdelijkePortefeuille = mktime();


		return $fondswaardenClean;
	}

	function printKop($title)
	{
		$this->pdf->SetFont("Times", "bi", 10);
		$this->pdf->Cell(100 , 4 , $title , 0, 1, "L");
		$this->pdf->SetFont("Times", "", 10);

		$this->csvData[] = array($title);
	}

	function writeRapport()
	{
	  global $USR;

		$einddatumJul = $this->datum;
		$einddatum = jul2sql($this->datum);

		$fondswaardenClean = array();
		$fondswaardenRente = array();
		$rekeningwaarden 	 = array();

		$jaar = date("Y",$einddatumJul);


		// build Fondsselection.

for ($i=1; $i <3; $i++)
{
	$where='where'. $i;
		if(count($this->selectData[$where]) > 0)
		{
			for($a = 0; $a < count($this->selectData[$where]); $a++)
			{
				$andor = "";
				if((count($this->selectData[$where])) > ($a+1))
				{
					$andor = $this->selectData[$where][$a]['andor'];
				}

				if(	$this->selectData[$where][$a]['field'] == "Beleggingscategorie" ||
						$this->selectData[$where][$a]['field'] == "RisicoPercentageFonds" )
				{
					$table = "BeleggingscategoriePerFonds";
				}
				else if($this->selectData[$where][$a]['field'] == "Beleggingssector")
				{
					$table = "BeleggingssectorPerFonds";
				}
				else if($this->selectData[$where][$a]['field'] == "Zorgplicht")
				{
					$table = "ZorgplichtPerFonds";
				}
				else if($this->selectData[$where][$a]['field'] == "Portefeuille")
				{
					$table = "Portefeuilles";
				}
				else if($this->selectData[$where][$a]['field'] == "Client")
				{
					$table = "Portefeuilles";
				}
        else if($this->selectData[$where][$a]['field'] == "Consolidatie")
        {
          $table = "Portefeuilles";
        }
				else
				{
					$table = "Fondsen";
				}

				if($this->selectData[$where][$a]['operator'] == "LIKE")
				{
					$whereF .= $table.".".$this->selectData[$where][$a]['field']." ".
										$this->selectData[$where][$a]['operator']." '%".
										$this->selectData[$where][$a]['search']."%' ".$andor." ";
				}
				else
				{
					$whereF .= $table.".".$this->selectData[$where][$a]['field']." ".
										$this->selectData[$where][$a]['operator']." '".
										$this->selectData[$where][$a]['search']."' ".$andor." ";
				}
			}
			if($whereF <> "")
			{
				$whereQuery .= " AND ( ".$whereF." )";
				$whereF = "";
			}
		}

}


			// controle op einddatum portefeuille
		if($this->selectData['inactiefOpnemen'] == 1)
	    $extraquery='';
	  else
	    $extraquery  .= " Portefeuilles.Einddatum > '".$einddatum."' AND ";

	if(checkAccess($type))
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
    
    
    if($_SESSION['reportBuilder']['incConsolidaties']==1)
    {
      $consolidatieFilter='AND Portefeuilles.consolidatie<2';
    }
    else
    {
      $consolidatieFilter='AND Portefeuilles.consolidatie=0';
    }

		$q = "SELECT ".
		" Portefeuilles.Portefeuille,". //rvv
    " Portefeuilles.Consolidatie,".
		" Rekeningmutaties.Fonds, ".
		" Fondsen.Omschrijving, ".
    " Fondsen.standaardSector, ".
		" Fondsen.FondsImportCode, ".
		" Fondsen.Valuta, ".
		" Fondsen.Fondseenheid, ".
    " Fondsen.EersteRentedatum, ".
		" Fondsen.Rentedatum, ".
		" Fondsen.Renteperiode, ".
		" Fondsen.ISINCode, ".
		" Fondsen.rating, ".
		" Fondsen.TGBCode, ".
		" Fondsen.stroeveCode, ".
		" Fondsen.AABCode, ".
		" Fondsen.VKM, ".
		" Fondsen.passiefFonds, ".
  	" SUM(Rekeningmutaties.Aantal) as aantal,
    (SELECT count(Datum) as aantal FROM Rentepercentages WHERE Fonds = Rekeningmutaties.Fonds AND Datum <= '".$einddatum."' LIMIT 1) as renteBerekenen,
		(SELECT  Beleggingssector FROM  BeleggingssectorPerFonds WHERE BeleggingssectorPerFonds.Fonds = Rekeningmutaties.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <= '".$einddatum."' ORDER BY Vanaf DESC LIMIT 1) as Beleggingssector,
	  (SELECT  AttributieCategorie FROM  BeleggingssectorPerFonds WHERE BeleggingssectorPerFonds.Fonds = Rekeningmutaties.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <= '".$einddatum."' ORDER BY Vanaf DESC LIMIT 1) as AttributieCategorie,
  	(SELECT  Regio FROM  BeleggingssectorPerFonds WHERE BeleggingssectorPerFonds.Fonds = Rekeningmutaties.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <= '".$einddatum."' ORDER BY Vanaf DESC LIMIT 1) as Regio,
    (SELECT  Beleggingscategorie FROM  BeleggingscategoriePerFonds WHERE BeleggingscategoriePerFonds.Fonds = Rekeningmutaties.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <= '".$einddatum."' ORDER BY Vanaf  DESC LIMIT 1) as Beleggingscategorie,
    (SELECT  afmCategorie FROM  BeleggingscategoriePerFonds WHERE BeleggingscategoriePerFonds.Fonds = Rekeningmutaties.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <= '".$einddatum."' ORDER BY Vanaf  DESC LIMIT 1) as afmCategorie,
    (SELECT  Duurzaamheid FROM  BeleggingscategoriePerFonds WHERE BeleggingscategoriePerFonds.Fonds = Rekeningmutaties.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <= '".$einddatum."' ORDER BY Vanaf  DESC LIMIT 1) as Duurzaamheid,
    (SELECT  RisicoPercentageFonds FROM  BeleggingscategoriePerFonds WHERE BeleggingscategoriePerFonds.Fonds = Rekeningmutaties.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <= '".$einddatum."'  ORDER BY Vanaf  DESC LIMIT 1) as RisicoPercentageFonds,
		".
		" ZorgplichtPerFonds.Zorgplicht ".
		" FROM (Rekeningmutaties, Rekeningen, Portefeuilles, Fondsen) ".
		" LEFT JOIN ZorgplichtPerFonds ON ZorgplichtPerFonds.Fonds = Rekeningmutaties.Fonds AND  ".
		" ZorgplichtPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
		  $join".
		" WHERE ".
		" Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".$extraquery.
		" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
		" Rekeningmutaties.Verwerkt = '1' AND ".
		" Rekeningmutaties.Boekdatum <= '".$einddatum."' AND ".
		" Rekeningmutaties.Fonds IS NOT NULL AND ".
		" Rekeningmutaties.Grootboekrekening = 'FONDS' AND ".
		" Rekeningmutaties.Fonds = Fondsen.Fonds ".
		$whereQuery ." $consolidatieFilter ".$beperktToegankelijk.
		" GROUP BY Rekeningmutaties.Fonds ,Portefeuilles.Portefeuille ".
		" ORDER BY Rekeningmutaties.Fonds ,Portefeuilles.Portefeuille ";

		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		$records = $DB->records();

    $selectiePort = array();
    $aantalWaarnemingen=array();
		while($fonds = $DB->NextRecord())
		{
			$fondsen[$fonds['Fonds']] = $fonds;
      if(round($fonds['aantal'],2)<>0)
          $aantalWaarnemingen[$fonds['Fonds']]+=1;
      $selectiePort[$fonds['Portefeuille']] = $fonds['Portefeuille'];
		}

    
    $fondsen=array_values($fondsen);
		// build portefeuille array
		$pselectie = array();
		if ($this->selectData['where2'][0]['field'] == "Portefeuille")
		  $pselectie['portefeuilleVan'] = $this->selectData['where2'][0]['search'];
		if ($this->selectData['where2'][1]['field'] == "Portefeuille")
		  $pselectie['portefeuilleTm'] = $this->selectData['where2'][1]['search'];
		if ($this->selectData['where2'][2]['field'] == "Client")
		  $pselectie['clientVan'] = $this->selectData['where2'][2]['search'];
		if ($this->selectData['where2'][3]['field'] == "Client")
		  $pselectie['clientTm'] = $this->selectData['where2'][3]['search'];



		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}

		for($a=0; $a < count($fondsen); $a++)
		{
			// berekening van Fonds Waarden in een aparte functie gezet
			$fondswaarden[$fondsen[$a]['Fonds']] = fondsAantalOpdatum($pselectie, $fondsen[$a]['Fonds'], $einddatum);
      $fondswaarden[$fondsen[$a]['Fonds']]['waarnemingen'] = $aantalWaarnemingen[$fondsen[$a]['Fonds']];
      
	  	$q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '".$fondsen[$a]['Fonds']."' AND Datum <= '".substr($einddatum,0,4).'-01-01'."' ORDER BY Datum DESC LIMIT 1";
			$DB2 = new DB();
			$DB2->SQL($q);
			$DB2->Query();
			$fondsJanKoers = $DB2->NextRecord();
      $fondswaarden[$fondsen[$a]['Fonds']]['fondsJanKoers']=$fondsJanKoers['Koers'];
      $fondswaarden[$fondsen[$a]['Fonds']]['fondsYtD'] = ($fondswaarden[$fondsen[$a]['Fonds']]['actueleFonds'] - $fondsJanKoers['Koers']) / ($fondsJanKoers['Koers']/100 );  
      
      $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '".$fondsen[$a]['Fonds']."' AND Datum <= '".$einddatum."' ORDER BY Datum DESC LIMIT 1";
			$DB2 = new DB();
			$DB2->SQL($q);
			$DB2->Query();
			$actuelefonds = $DB2->NextRecord();
      $fondswaarden[$fondsen[$a]['Fonds']]['koersDatum']  	= $actuelefonds['Datum'];
   
   
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Fonds: ".$fondsen[$a]['Fonds']);
			}
		}

    $t=count($fondsen);
		for($a=0; $a < count($fondsen); $a++) //rvv extra loop om totaal te bepalen. Nodig voor PercentageTotaal
		{
			$fonds 	= $fondsen[$a];
			$data 	= $fondswaarden[$fonds['Fonds']];
			$TotaalAlles = $TotaalAlles + ($data['fondsEenheid'] * $data['totaalAantal']) * $data['actueleFonds'] * $data['actueleValuta'] ;
      
 	  	if($fondsen[$a]['renteBerekenen'] > 0)
			{
				 $rente=getRenteParameters($fondsen[$a]['Fonds'], $einddatum);
				 $renteBerekenen=$rente['rentemethodiek'];

			  $fondstmp=array();
			  foreach($fonds as $key=>$value)
          $fondstmp[strtolower($key)]=$value;
        $fondstmp['totaalAantal']=$data['totaalAantal'];
        $fondstmp['eersteRentedatum']=$fondstmp['eersterentedatum'];
        
 				$rentebedrag = renteOverPeriode($fondstmp, $einddatum,false,$renteBerekenen);
        $fondsen[$a]['opgelopenrenteFondsvaluta']=$rentebedrag;
//        logScherm("Fonds: ".$fondstmp['fonds']." rente:$rentebedrag |".$renteBerekenen."|");exit;
        $fondsen[$a]['opgelopenrente']=$data['actueleValuta'] * $rentebedrag;
        $TotaalAlles += $fondsen[$a]['opgelopenrente'];
        /*
				$fondswaardenRente[$t] = $fonds;
				$fondswaardenRente[$t]['type'] = "rente";
        $fondswaardenRente[$t]['Omschrijving']="Rente ".$fonds['Omschrijving'];
				$fondswaardenRente[$t]['actuelePortefeuilleWaardeInValuta'] = $rentebedrag;
				$fondswaardenRente[$t]['actuelePortefeuilleWaardeEuro'] = $data['actueleValuta'] * $rentebedrag;
        
        $TotaalAlles += $fondswaardenRente[$t]['actuelePortefeuilleWaardeEuro'];
				$t++;
        */
      }
		}
$fondsen = array_merge($fondsen, $fondswaardenRente);
//rvv liquiditeiten ook nog ophalen.

$WherePort = "AND Rekeningen.Portefeuille IN('".implode("','",$selectiePort)."')";

$query = 	"SELECT Rekeningen.Valuta, SUM(Rekeningmutaties.Bedrag) as totaal, max(boekdatum) as datumMax  ".
			" FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
			" WHERE ".
			" Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			" Rekeningen.Memoriaal < 1 AND ".
			" Rekeningmutaties.boekdatum >= '".$jaar."' AND ".
			" Rekeningmutaties.boekdatum <= '".$einddatum."' ". $WherePort .// AND ".$extraquery.
            " GROUP BY Rekeningen.Valuta ".
            " ORDER BY Rekeningen.Valuta";

	$DB1 = new DB();
	$DB1->SQL($query);
	$DB1->Query();
	$t=0;
	$a=count($fondsen);

	while($data = $DB1->NextRecord())
	{
			$q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$data['Valuta']."' AND Datum <= '".$einddatum."' ORDER BY Datum DESC LIMIT 1";
			$DB2 = new DB();
			$DB2->SQL($q);
			$DB2->Query();
			$actuelevaluta = $DB2->NextRecord();

			$fondsen[$a]['Omschrijving'] = $data['Valuta'];
			$fondsen[$a]['Fonds'] = $data['Valuta'];
			$fondsen[$a]['Portefeuille'] = "";//$pselectie[portefeuilleVan];
			$fondsen[$a]['Beleggingscategorie'] = "Liquiditeiten";

			$fondswaarden[$data['Valuta']]['totaalAantal'] = $data['totaal'];
			$fondswaarden[$data['Valuta']]['actueleFonds'] = 1;
			$fondswaarden[$data['Valuta']]['fondsEenheid'] = 1;
			$fondswaarden[$data['Valuta']]['actueleValuta'] = $actuelevaluta['Koers'];
      
      if(db2jul($data['datumMax']) > db2jul($einddatum))
        $maxDatum=$einddatum;
      else
        $maxDatum=$data['datumMax']; 
      $fondswaarden[$data['Valuta']]['koersDatum'] = $maxDatum;

			$liquiditeitenEuroTotaal += $data['totaal'] * $actuelevaluta['Koers'];
			$a++;
	}
	$TotaalAlles += $liquiditeitenEuroTotaal; //Liquiditeiten totaal optellen bij fondstotaal.
//rvv

		for($a=0; $a <count($fondsen); $a++)
		{
			$fonds 	= $fondsen[$a];
			$data 	= $fondswaarden[$fonds['Fonds']];
			if(($data['totaalAantal'] <> 0) || $fonds['Beleggingscategorie'] == "Liquiditeiten" || $fonds['type'] == "rente") //liquiditeiten ook tonen
			{
				// bereken totalen met actuele koers
        if($fonds['type'] == "rente")
        {
          $actuelePortefeuilleWaardeInValuta=$fonds['actuelePortefeuilleWaardeInValuta'];
          $actuelePortefeuilleWaardeEuro=$fonds['actuelePortefeuilleWaardeEuro'];
        }
        else
        {
				  $actuelePortefeuilleWaardeInValuta 	= ($data['fondsEenheid']  * $data['totaalAantal']) * $data['actueleFonds'];
			  	$actuelePortefeuilleWaardeEuro 		=  $data['actueleValuta'] * $actuelePortefeuilleWaardeInValuta;
				}
        $PercentageTotaal					=  $actuelePortefeuilleWaardeEuro / $TotaalAlles * 100;

				// maak nieuwe schone array
				$clean = $data;
				$clean['beginPortefeuilleWaardeInValuta'] 	= $beginPortefeuilleWaardeInValuta;
				$clean['beginPortefeuilleWaardeEuro'] 		= $beginPortefeuilleWaardeEuro;
				$clean['actuelePortefeuilleWaardeInValuta'] 	= $actuelePortefeuilleWaardeInValuta;
				$clean['actuelePortefeuilleWaardeEuro'] 		= $actuelePortefeuilleWaardeEuro;
				$clean['fonds'] 								= $fonds['Fonds'];
				$clean['beleggingssector'] 					= $fonds['Beleggingssector'];
				$clean['beleggingscategorie'] 				= $fonds['Beleggingscategorie'];


				$fondswaardenClean[] = $clean;
				// insert into temp table!
				// build query ..
				$insert = "INSERT INTO `".$this->tmp_table."` SET ".
									" Fonds = '".mysql_escape_string($fonds['Fonds'])."' ".
									",Portefeuille = '".mysql_escape_string($fonds['Portefeuille'])."' ".
                  ",Consolidatie = '".mysql_escape_string($fonds['Consolidatie'])."' ".
									",Omschrijving = '".mysql_escape_string($fonds['Omschrijving'])."' ".
                  ",standaardSector = '".mysql_escape_string($fonds['standaardSector'])."' ".
									",FondsImportCode = '".mysql_escape_string($fonds['FondsImportCode'])."' ".
									",Valuta = '".mysql_escape_string($fonds['Valuta'])."' ".
									",Fondseenheid = '".mysql_escape_string($fonds['Fondseenheid'])."' ".
									",Rentedatum = '".mysql_escape_string($fonds['Rentedatum'])."' ".
									",Renteperiode = '".mysql_escape_string($fonds['Renteperiode'])."' ".
									",ISINCode = '".mysql_escape_string($fonds['ISINCode'])."' ".
									",rating = '".mysql_escape_string($fonds['rating'])."' ".
									",TGBCode = '".mysql_escape_string($fonds['TGBCode'])."' ".
									",stroeveCode = '".mysql_escape_string($fonds['stroeveCode'])."' ".
									",AABCode = '".mysql_escape_string($fonds['AABCode'])."' ".
				        	",VKM = '".$fonds['VKM']."' ".
					        ",passiefFonds = '".$fonds['passiefFonds']."' ".
									",Beleggingscategorie = '".mysql_escape_string($fonds['Beleggingscategorie'])."' ".
                  ",Regio = '".mysql_escape_string($fonds['Regio'])."' ".
         					",AttributieCategorie = '".mysql_escape_string($fonds['AttributieCategorie'])."' ".
				        	",afmCategorie = '".mysql_escape_string($fonds['afmCategorie'])."' ".
                  ",Duurzaamheid = '".mysql_escape_string($fonds['Duurzaamheid'])."' ".
									",RisicoPercentageFonds = '".mysql_escape_string($fonds['RisicoPercentageFonds'])."' ".
									",Beleggingssector = '".mysql_escape_string($fonds['Beleggingssector'])."' ".
									",Zorgplicht = '".mysql_escape_string($fonds['Zorgplicht'])."' ".
									",Aantal = '".mysql_escape_string($data['totaalAantal'])."' ".
									",Fondskoers = '".mysql_escape_string($data['actueleFonds'])."' ".
									",FondsTotaal = '".mysql_escape_string(round($actuelePortefeuilleWaardeInValuta,2))."' ".
									",FondsTotaalEUR = '".mysql_escape_string(round($actuelePortefeuilleWaardeEuro,2))."' ".
									",PercentageTotaal = '".mysql_escape_string($PercentageTotaal)."' ".
                  ",FondsYtD = '".mysql_escape_string($clean['fondsYtD'])."' ".
                  ",KoersDatum = '".$clean['koersDatum']."' ".
									",AantalWaarnemingen = '".mysql_escape_string($clean['waarnemingen'])."' ".
                  ",opgelopenrenteFondsvaluta = '".mysql_escape_string($fonds['opgelopenrenteFondsvaluta'])."'".
                  ",opgelopenrente = '".mysql_escape_string($fonds['opgelopenrente'])."'";

				// gooi in nieuwe tmp Table....
				$DBt = new DB();
				$DBt->SQL($insert);
				$DBt->Query();
			}
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
			$where2 = "";
			for($a = 0; $a < count($this->selectData['where3']); $a++)
			{
				$andor = "";
				if((count($this->selectData['where3'])) > ($a+1))
				{
					$andor = $this->selectData['where3'][$a]['andor'];
				}

				if($this->selectData['where3'][$a]['operator'] == "LIKE")
					$where2 .= $this->selectData['where3'][$a]['field']." ".
										$this->selectData['where3'][$a]['operator']." '%".
										$this->selectData['where3'][$a]['search']."%' ".$andor." ";
				else
					$where2 .= $this->selectData['where3'][$a]['field']." ".
										$this->selectData['where3'][$a]['operator']." '".
										$this->selectData['where3'][$a]['search']."' ".$andor." ";
			}

			if($where2 <> "")
				$extraquery = " AND ( ".$where2." ) ";
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
        if($this->selectData['groupby'][0]['field']<>'Portefeuille')
        {
          $removeFields=array('Portefeuille');
          $selectThis = array_values(array_diff($selectThis, $removeFields));
          $header = array_values(array_diff($header, $removeFields));
        }
		}
    else
    {
      $removeFields=array('Portefeuille');
      $selectThis = array_values(array_diff($selectThis, $removeFields));
      $header = array_values(array_diff($header, $removeFields));
    }

    $fields = implode(", ",$selectThis);
    $this->excelData[] = $header;

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
