<?php

/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/31 14:44:58 $
File Versie					: $Revision: 1.46 $

$Log: Fondsen.php,v $
Revision 1.46  2019/07/31 14:44:58  rvv
*** empty log message ***

Revision 1.45  2019/04/20 17:31:48  rvv
*** empty log message ***

Revision 1.44  2017/04/02 10:02:19  rvv
*** empty log message ***

Revision 1.43  2017/03/29 15:56:14  rvv
*** empty log message ***

Revision 1.42  2016/05/08 19:23:09  rvv
*** empty log message ***

Revision 1.41  2016/01/23 17:51:35  rvv
*** empty log message ***

Revision 1.40  2016/01/17 18:11:29  rvv
*** empty log message ***

Revision 1.39  2014/12/21 10:32:26  rvv
*** empty log message ***

Revision 1.38  2014/09/20 17:23:18  rvv
*** empty log message ***

Revision 1.37  2013/08/28 16:02:00  rvv
*** empty log message ***

Revision 1.36  2012/06/20 18:10:12  rvv
*** empty log message ***

Revision 1.35  2012/03/21 19:08:14  rvv
*** empty log message ***

Revision 1.34  2011/12/04 12:56:32  rvv
*** empty log message ***

Revision 1.33  2010/11/24 20:19:15  rvv
*** empty log message ***

Revision 1.32  2010/11/21 13:09:43  rvv
*** empty log message ***

Revision 1.31  2010/11/17 17:16:33  rvv
*** empty log message ***

Revision 1.30  2010/09/18 15:36:33  rvv
*** empty log message ***

Revision 1.29  2010/07/28 17:18:43  rvv
*** empty log message ***

Revision 1.28  2010/06/30 16:19:59  rvv
*** empty log message ***

Revision 1.27  2010/04/07 13:12:31  rvv
*** empty log message ***

Revision 1.26  2010/04/07 12:10:35  rvv
*** empty log message ***

Revision 1.25  2010/02/10 17:57:15  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");

class Fondsen
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Fondsen( $selectData )
	{
		$this->selectData = $selectData;
		$this->pdf->excelData 	= array();
		global $USR;

		if($this->selectData['portraitVersie'])
		{
  		$this->pdf = new PDFOverzicht('P','mm');
  		$this->pdf->pagebreak = 280;
		}
  	else
  	{
		  $this->pdf = new PDFOverzicht('L','mm');
		  $this->pdf->pagebreak = 190;
  	}
		$this->pdf->rapport_type = "fondsen";
		$this->pdf->SetAutoPageBreak(true,15);

	//	$this->pdf->forceOneRow = true;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData[datumTm];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;

	//	$this->orderby = " Portefeuilles.ClientVermogensbeheerder ";
		$this->orderby = " Clienten.Client ";

		$this->dbTable="CREATE TABLE `reportbuilder_$USR` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Rapport` VARCHAR( 20 ) NOT NULL ,
`Portefeuille` VARCHAR( 24 ) NOT NULL ,
`Vermogensbeheerder` VARCHAR( 10 ) NOT NULL ,
`Client` VARCHAR( 16 ) NOT NULL ,
`Naam` VARCHAR( 50 ) NOT NULL ,
`Naam1` VARCHAR( 50 ) NOT NULL ,
`Fonds` VARCHAR( 25 ) NOT NULL ,
`FondsOmschrijving` VARCHAR( 50 ) NOT NULL ,
`Kostprijs` DOUBLE NOT NULL ,
`HistorischeWaarde` DOUBLE NOT NULL ,
`AandeelBeleggingscategorie` DOUBLE NOT NULL ,
`AandeelTotaalvermogen` DOUBLE NOT NULL ,
`AandeelTotaalBelegdvermogen` DOUBLE NOT NULL ,
`AantalInPortefeuille` DOUBLE NOT NULL ,
`FondsKoers` DOUBLE NOT NULL ,
`FondsEenheid` DOUBLE NOT NULL ,
`ValutaKoers` DOUBLE NOT NULL ,
`FondsWaarde` DOUBLE NOT NULL ,
`add_date` datetime ,
PRIMARY KEY ( `id` ),
KEY `Portefeuille` (`Portefeuille`),
KEY `Fonds` (`Fonds`)
) ";
	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatAantal($waarde, $dec, $zesDecimalenZonderNullen=false)
	{
	  if ($zesDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '000000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  }
    return number_format($waarde,$dec,",",".");
	}

	function printKop($title)
	{
		$this->pdf->SetFont("Times", "bi", 10);
		$this->pdf->Cell(100 , 4 , $title , 0, 1, "L");
		$this->pdf->SetFont("Times", "", 10);
	}

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->__appvar = $this->__appvar;

		$einddatum = jul2sql($this->selectData['datumTm']);

		// selecteer koers van fonds op datum uit fonds tabel.
		$query = "SELECT Valutakoersen.Koers FROM Valutakoersen, Fondsen WHERE ".
						 " Fondsen.Fonds  			= '".$this->selectData['fonds']."' AND ".
						 " Valutakoersen.Valuta = Fondsen.Valuta AND ".
						 " Valutakoersen.Datum <= '".$einddatum."' ORDER BY Valutakoersen.Datum DESC LIMIT 1 ";

		$DB2 = new DB();
		$DB2->SQL($query);
		$DB2->Query();
		$kdata 	= $DB2->nextRecord();
		$valutakoers = $kdata['Koers'];

		// selecteer koers van fonds op datum uit fonds tabel.
		$query = "SELECT Fondsen.Fondseenheid, Fondsen.Omschrijving, Fondsen.ISINCode, Fondsen.Huisfonds,Fondsen.Portefeuille as huisfondsPortefeuille FROM Fondsen WHERE Fondsen.Fonds = '".$this->selectData['fonds']."' LIMIT 1 ";

		$DB2 = new DB();
		$DB2->SQL($query);
		$DB2->Query();

		$fdata 	= $DB2->nextRecord();
		$this->pdf->fonds = $fdata['Omschrijving'];
    $this->pdf->fondsISIN = $fdata['ISINCode'];
		$fondseenheid = $fdata['Fondseenheid'];


		$query = "SELECT Fondskoersen.Koers FROM Fondskoersen WHERE Fondskoersen.Fonds = '".$this->selectData['fonds']."' AND Fondskoersen.Datum <='".$einddatum."' ORDER BY Fondskoersen.Datum DESC LIMIT 1";
		$DB2->SQL($query);
		$DB2->Query();
		$koersData 	= $DB2->nextRecord();
		if($koersData['Koers']=='' && $fdata['Huisfonds']==1 &&  $fdata['huisfondsPortefeuille']<>'')
			$koersData=bepaalHuisfondsKoers($this->selectData['fonds'],$fdata['huisfondsPortefeuille'],$einddatum);
		$koersWaarde 	= $koersData['Koers'];

		$jaar = date("Y",$this->selectData['datumTm']);

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    $portefeuilleList=array_keys($portefeuilles);
		$extraquery=" AND Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') ";
//echo "|".count($portefeuilleList);exit;
		// selectie Fonds
		if(!empty($this->selectData['depotbank']))
			$extraquery .= " AND Portefeuilles.Depotbank = '".$this->selectData['depotbank']."' ";
   
		
    if(isset($this->selectData['allePortefeuillesOpnemen']) && $this->selectData['allePortefeuillesOpnemen']==1)
      $allePortefeuilles=true;
    else
      $allePortefeuilles=false;

		if($allePortefeuilles==true)
		  $allePortefeuilleJoin='LEFT';
		
		// selecteer alleen portefeuilles waar het fonds voorkomt!
		$q = "SELECT ".
				" Portefeuilles.ClientVermogensbeheerder, ".
				" Portefeuilles.Vermogensbeheerder, ".
				" Portefeuilles.Portefeuille, ".
				" Portefeuilles.Depotbank, ".
				" Portefeuilles.Accountmanager, ".
				" Clienten.Client, ".
				" Clienten.Naam, ".
				" Clienten.Naam1,
          Portefeuilles.Risicoklasse,
          Portefeuilles.SoortOvereenkomst,
				 CRM_naw.profielOverigeBeperkingen ".
				" FROM Portefeuilles
				  JOIN Clienten ON Portefeuilles.Client = Clienten.Client
				  JOIN Rekeningen ON Portefeuilles.Portefeuille=Rekeningen.Portefeuille
				  $allePortefeuilleJoin JOIN Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
			  	AND Rekeningmutaties.Fonds = '".$this->selectData['fonds']."' AND ".
      " Rekeningmutaties.Grootboekrekening = 'FONDS' AND ".
      " YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
      " Rekeningmutaties.Verwerkt = '1' AND ".
      " Rekeningmutaties.Boekdatum <= '".$einddatum."' AND ".
      " Rekeningmutaties.Fonds IS NOT NULL
        LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille ".
				" WHERE 1 ".
				"  ".$extraquery."   ".
				" GROUP BY Portefeuilles.Portefeuille ".
				" ORDER BY ".$this->orderby;

		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		$records = $DB->records();

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}

		if($this->selectData['portraitVersie'])
		{
  		$this->pdf->AddPage('P');
		}
		else
		{
		  $this->pdf->AddPage();
		}
		$this->pdf->SetFont("Times","",10);

		// Maak header voor CSV bestand
  $this->pdf->excelData[] = array($this->pdf->fonds .' ('.$this->pdf->fondsISIN.')');
	if($this->selectData['portraitVersie'] == 1)
		$this->pdf->excelData[] = array("nr","Portefeuille","Naam","","Aantal");
	else
	{
		if($this->selectData['fondsenOpBewaarder']==1)
			$depot='Bewaarder';
		else
			$depot='Depotbank';
		$this->pdf->excelData[] = array("Nr", "Client", "Portefeuille", $depot, "Acc.mgr.", 'SoortOvereenkomst', 'Risicoprofiel', "Naam", "", "Kostprijs", "Aandeel " . $this->selectData['berekeningswijze'], "Aantal", 'Overige beperkingen','Fondskoers','Fondseenheid','Valutakoers','Waarde');
	}
	  $lineNr =1;
		while($portefeuille = $DB->NextRecord())
		{
      $crmNaam=getCrmNaam($portefeuille['Portefeuille']);
      if($crmNaam)
      {
        $portefeuille['Naam'] = $crmNaam['naam'];
        $portefeuille['Naam1'] = $crmNaam['naam1'];
      }

		  $DB2 = new DB();
			if($this->selectData['fondsenOpBewaarder']==1)
				$portefeuilleData = berekenPortefeuilleWaardeBewaarders($portefeuille['Portefeuille'], $einddatum);
			else
	  		$portefeuilleData = berekenPortefeuilleWaardeQuick($portefeuille['Portefeuille'], $einddatum);
			vulTijdelijkeTabel($portefeuilleData,$portefeuille['Portefeuille'],$einddatum);

			// selecteer fondswaarde portefeuille
			$query = "SELECT totaalAantal, actuelePortefeuilleWaardeEuro,bewaarder  ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " type = 'fondsen' AND ".
							 " Fonds = '".$this->selectData['fonds']."' AND ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille['Portefeuille']."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB2->SQL($query);
			$DB2->Query();
			$bewaarders=array();
			while($fdata = $DB2->nextRecord())
			{
				$bewaarders[]=$fdata;
			}
			
			if(count($bewaarders)==0)
			  $bewaarders[]=$portefeuille['Depotbank'];


			foreach($bewaarders as $fdata)
			{
			$fondsWaarde = $fdata['actuelePortefeuilleWaardeEuro'];
			$fondsAantal = $fdata['totaalAantal'];
				$bewaarder = $fdata['bewaarder'];

			$query = 	"SELECT SUM(Rekeningmutaties.Aantal) as optieAantal
	 			 				FROM (Rekeningmutaties, Rekeningen, Portefeuilles)
					JOIN Fondsen on Fondsen.Fonds =  Rekeningmutaties.Fonds
					WHERE
	 				Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	 				Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Portefeuilles.Portefeuille = '".$portefeuille['Portefeuille']."' AND
	 				YEAR(Rekeningmutaties.Boekdatum) = '$jaar' AND
	 				Rekeningmutaties.Verwerkt = '1' AND
	 				Rekeningmutaties.Boekdatum <= '$einddatum' AND
	 				Fondsen.OptieBovenliggendFonds =  '".$this->selectData['fonds']."' AND
	 				Rekeningmutaties.Grootboekrekening = 'FONDS' ";
			$DB2->SQL($query);
			$DB2->Query();
			$optieAantal = $DB2->nextRecord();

			if($fondsAantal <> 0  || ($optieAantal['optieAantal'] <> 0 && $this->selectData['optiesWeergeven'] == 1) || $allePortefeuilles==true )
			{
				$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
					"FROM TijdelijkeRapportage WHERE " .
					" rapportageDatum ='" . $einddatum . "' AND " .
					" portefeuille = '" . $portefeuille['Portefeuille'] . "' "
					. $__appvar['TijdelijkeRapportageMaakUniek'];
				debugSpecial($query, __FILE__, __LINE__);
				$DB2 = new DB();
				$DB2->SQL($query);
				$DB2->Query();
				$tdata = $DB2->nextRecord();
				$totaalWaarde = $tdata['totaal'];

				// selecteer fondswaarde portefeuille
				$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
					"FROM TijdelijkeRapportage WHERE " .
					" type = 'rekening' AND " .
					" rapportageDatum ='" . $einddatum . "' AND " .
					" portefeuille = '" . $portefeuille[Portefeuille] . "' "
					. $__appvar['TijdelijkeRapportageMaakUniek'];
				debugSpecial($query, __FILE__, __LINE__);
				$DB2 = new DB();
				$DB2->SQL($query);
				$DB2->Query();
				$liqWaarde = $DB2->nextRecord();
				$liqWaarde = $liqWaarde['totaal'];

				// selecteer belegingscategorie
				$query = " SELECT BeleggingscategoriePerFonds.Beleggingscategorie " .
					" FROM BeleggingscategoriePerFonds, Portefeuilles WHERE " .
					" Portefeuilles.Portefeuille = '" . $portefeuille['Portefeuille'] . "' AND " .
					" Portefeuilles.Vermogensbeheerder =  BeleggingscategoriePerFonds.Vermogensbeheerder AND " .
					" BeleggingscategoriePerFonds.Fonds = '" . $this->selectData['fonds'] . "' ";

				$DB2 = new DB();
				$DB2->SQL($query);
				$DB2->Query();
				$cdata = $DB2->nextRecord();
				$categorie = $cdata['Beleggingscategorie'];

				// selecteer totaal in categorie portefeuille
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
				$categorieWaarde = $cdata[totaalWaarde];


				switch ($this->pdf->selectData['berekeningswijze'])
				{
					case "Totaal vermogen" :
						$totaalRekenwaarde = $totaalWaarde;
						break;
					case "Totaal belegd vermogen" :
						$totaalRekenwaarde = $totaalWaarde - $liqWaarde;
						break;
					case "Belegd vermogen per beleggingscategorie" :
						$totaalRekenwaarde = $categorieWaarde;
						break;
				}

				$percentage = $fondsWaarde / ($totaalRekenwaarde / 100);

				$aandeelop = array();
				$aandeelop['totaalvermogen'] = $fondsWaarde / ($totaalWaarde / 100);
				$aandeelop['vermogenbelegd'] = $fondsWaarde / (($totaalWaarde - $liqWaarde) / 100);
				$aandeelop['beleggingscat'] = $fondsWaarde / (($categorieWaarde) / 100);

				// bereken historische waarde
				$hist = fondsWaardeOpdatum($portefeuille['Portefeuille'], $this->selectData['fonds'], $einddatum);
        $fondsWaarde=round($fondsAantal * $koersWaarde * $fondseenheid * $valutakoers,2);

				$this->dbWaarden[] = array(
					'Rapport'                     => 'Fondsen',
					'Portefeuille'                => $portefeuille['Portefeuille'],
					'Vermogensbeheerder'          => $portefeuille['Vermogensbeheerder'],
					'Client'                      => $portefeuille['Client'],
					'Naam'                        => $portefeuille['Naam'],
					'Naam1'                       => $portefeuille['Naam1'],
					'Fonds'                       => $this->selectData['fonds'],
					'HistorischeWaarde'           => round($hist['historischeWaarde'], 2),
					'AandeelTotaalvermogen'       => round($aandeelop['totaalvermogen'], 2),
					'AandeelBeleggingscategorie'  => round($aandeelop['beleggingscat'], 2),
					'AandeelTotaalBelegdvermogen' => round($aandeelop['vermogenbelegd'], 2),
          'AantalInPortefeuille'        => round($fondsAantal, 6),
          'FondsKoers'                  => round($koersWaarde, 6),
          'FondsEenheid'                => round($fondseenheid, 6),
          'ValutaKoers'                 => round($valutakoers, 6),
          'FondsWaarde'                 => round($fondsWaarde, 2));

				if ($this->selectData['optiesWeergeven'] == 1)
				{//$portefeuille[Client]
					if ($this->selectData['portraitVersie'])
					{
						$data = array($lineNr, $portefeuille['Portefeuille'], substr($portefeuille['Naam'] . " " . $portefeuille['Naam1'], 0, 45), $this->pdf->fonds, $this->formatAantal($fondsAantal, 0, true));
					}
					else
					{
						if($this->selectData['fondsenOpBewaarder']==1)
						{
							if($bewaarder<>'')
							  $depot = $bewaarder;
							else
						    $depot=$portefeuille['Depotbank'];
						}
						else
						{
							$depot=$portefeuille['Depotbank'];
						}

						$data = array($lineNr . ' ' . $portefeuille['Client'], $portefeuille['Portefeuille'], $depot, $portefeuille['Accountmanager'],
							substr($portefeuille['Naam'] . " " . $portefeuille['Naam1'], 0, 60),
							$this->pdf->fonds, $this->formatGetal($hist['historischeWaarde'], 2), $this->formatGetal($percentage, 2) . " %", $this->formatAantal($fondsAantal, 0, true));
					}
					$this->pdf->Row($data);

					if ($this->selectData['portraitVersie'] == 1)
					{
						$this->pdf->excelData[] = array($lineNr, $portefeuille['Portefeuille'], substr($portefeuille['Naam'] . " " . $portefeuille['Naam1'], 0, 140), $this->pdf->fonds, round($fondsAantal, 4));
					}
					else
					{
						$this->pdf->excelData[] = array($lineNr, $portefeuille['Client'], $portefeuille['Portefeuille'], $depot, $portefeuille['Accountmanager'], $portefeuille['SoortOvereenkomst'], $portefeuille['Risicoklasse'],
							substr($portefeuille['Naam'] . " " . $portefeuille['Naam1'], 0, 140), $this->pdf->fonds, round($hist['historischeWaarde'], 2), round($percentage, 2), round($fondsAantal, 4),
							$portefeuille['profielOverigeBeperkingen']);
					}


					$query = "SELECT
	 				Rekeningmutaties.Fonds,
	 				Fondsen.OptieBovenliggendFonds,
	 				Fondsen.Valuta
	 				FROM (Rekeningmutaties, Rekeningen, Portefeuilles)
					JOIN Fondsen on Fondsen.Fonds =  Rekeningmutaties.Fonds
					WHERE
	 				Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	 				Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Portefeuilles.Portefeuille = '" . $portefeuille['Portefeuille'] . "' AND
	 				YEAR(Rekeningmutaties.Boekdatum) = '$jaar' AND
	 				Rekeningmutaties.Verwerkt = '1' AND
	 				Rekeningmutaties.Boekdatum <= '$einddatum' AND
	 				Fondsen.OptieBovenliggendFonds =  '" . $this->selectData['fonds'] . "' AND
	 				Rekeningmutaties.Grootboekrekening = 'FONDS'
	 				GROUP BY Rekeningmutaties.Fonds ";

					$DB2 = new DB();
					$DB2->SQL($query);
					$DB2->Query();

					$records = $DB2->records();

					$opties = array();
					while ($optie = $DB2->NextRecord())
					{
						$optieData = optieAantalOpdatum($portefeuille['Portefeuille'], $optie['Fonds'], $einddatum);
						$hist = fondsWaardeOpdatum($portefeuille['Portefeuille'], $optie['Fonds'], $einddatum);
						$optieWaarde = ($optieData['totaalAantal'] * $optieData['fondsEenheid'] * $hist['actueleFonds']);
						$percentage = $optieWaarde / ($totaalRekenwaarde / 100);
						if (round($optieData['totaalAantal'], 0) <> 0)
						{
							if ($this->selectData['portraitVersie'])
							{
								$data = array('', $portefeuille['Portefeuille'], substr($portefeuille['Naam'] . " " . $portefeuille['Naam1'], 0, 45), $optieData['fondsOmschrijving'], $this->formatAantal($optieData['totaalAantal'], 0, true));
							}
							else
							{
								$data = array('', '', '', '', '', $optieData['fondsOmschrijving'], $this->formatGetal($hist['historischeWaarde'], 2), $this->formatGetal($percentage, 2) . " %", $this->formatAantal($optieData['totaalAantal'], 0, true));
							}

							$this->pdf->Row($data);
							if ($this->selectData['portraitVersie'])
							{
								$this->pdf->excelData[] = array('', $portefeuille['Portefeuille'], substr($portefeuille['Naam'] . " " . $portefeuille['Naam1'], 0, 140), $optieData['fondsOmschrijving'], round($optieData['totaalAantal'], 4));
							}
							else
							{
								$this->pdf->excelData[] = array('', $portefeuille['Client'], $portefeuille['Portefeuille'], $portefeuille['Depotbank'], $portefeuille['Accountmanager'], $portefeuille['SoortOvereenkomst'], $portefeuille['Risicoklasse'], $portefeuille['Naam'] . " " . $portefeuille['Naam1'],
									$optieData['fondsOmschrijving'], round($hist['historischeWaarde'], 2), round($percentage, 2), round($optieData['totaalAantal'], 4));
							}

							$aandeelop = array();
							$aandeelop['totaalvermogen'] = $optieWaarde / ($totaalWaarde / 100);
							$aandeelop['vermogenbelegd'] = $optieWaarde / (($totaalWaarde - $liqWaarde) / 100);
							$aandeelop['beleggingscat'] = $optieWaarde / (($categorieWaarde) / 100);

							$this->dbWaarden[] = array(
								'Rapport'                     => 'Fondsen',
								'Portefeuille'                => $portefeuille['Portefeuille'],
								'Vermogensbeheerder'          => $portefeuille['Vermogensbeheerder'],
								'Client'                      => $portefeuille['Client'],
								'Naam'                        => $portefeuille['Naam'],
								'Naam1'                       => $portefeuille['Naam1'],
								'Fonds'                       => $optie['Fonds'],
								'HistorischeWaarde'           => round($hist['historischeWaarde'], 2),
								'AandeelTotaalvermogen'       => round($aandeelop['totaalvermogen'], 2),
								'AandeelBeleggingscategorie'  => round($aandeelop['beleggingscat'], 2),
								'AandeelTotaalBelegdvermogen' => round($aandeelop['vermogenbelegd'], 2),
                'AantalInPortefeuille'        => round($optieData['totaalAantal'], 6));
						}
					}
				}
				else
				{

					if($this->selectData['fondsenOpBewaarder']==1)
					{
						if($bewaarder<>'')
							$depot = $bewaarder;
						else
							$depot=$portefeuille['Depotbank'];
					}
					else
					{
						$depot=$portefeuille['Depotbank'];
					}

					if ($this->selectData['portraitVersie'])
					{
						$data = array($lineNr, $portefeuille['Portefeuille'], substr($portefeuille['Naam'], 0, 40), $portefeuille['Naam1'], $this->formatAantal($fondsAantal, 0, true));
					}
					else
					{
						$data = array($portefeuille['Client'], $portefeuille['Portefeuille'], $depot, $portefeuille['Accountmanager'], substr($portefeuille['Naam'], 0, 40),
							$portefeuille['Naam1'], $this->formatGetal($hist['historischeWaarde'], 2), $this->formatGetal($percentage, 2) . " %", $this->formatAantal($fondsAantal, 0, true), $this->formatAantal($fondsWaarde, 0));
					}
				
					$this->pdf->Row($data);

					if ($this->selectData['portraitVersie'])
					{
						$this->pdf->excelData[] = array($lineNr, $portefeuille['Portefeuille'], substr($portefeuille['Naam'], 0, 40), $portefeuille['Naam1'], round($fondsAantal, 6));
					}
					else
					{
						$this->pdf->excelData[] = array($lineNr, $portefeuille['Client'], $portefeuille['Portefeuille'], $depot, $portefeuille['Accountmanager'], $portefeuille['SoortOvereenkomst'], $portefeuille['Risicoklasse'], $portefeuille['Naam'],
							$portefeuille['Naam1'], round($hist['historischeWaarde'], 2), round($percentage, 2), $fondsAantal, $portefeuille['profielOverigeBeperkingen'], $koersWaarde , $fondseenheid , $valutakoers,$fondsWaarde);
					}
				}
				$lineNr++;
				$totaalAantal += $fondsAantal;
			}
			}
				// verwijder Data van tijdelijke tabel
			verwijderTijdelijkeTabel($portefeuille['Portefeuille'],$einddatum);
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
		}

		if($this->selectData['portraitVersie'])
		{
		  if($this->pdf->GetY() > 230)
		 		$this->pdf->AddPage();
			$extraRuimte = 120;
	  }
	  else
	  {

		  if($this->pdf->GetY() > 140)
		    $this->pdf->AddPage();
			$extraRuimte = 191;
	  }

		// druk totaal af.
		$this->pdf->ln();
		$this->pdf->Cell($extraRuimte, 4 , "", 0, 0, "L");
		$this->pdf->SetFont("Times","b",10);
		$this->pdf->Cell(25 , 4 , "Totaal aantal:" , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
		$this->pdf->SetFont("Times","",10);
		$this->pdf->Cell(20 , 4 , $this->formatAantal($totaalAantal,0,true) , 0, 1, "R");

		$this->pdf->ln();

		$this->pdf->Cell($extraRuimte , 4 , "" , 0, 0, "L");
		$this->pdf->Cell(25 , 4 , "Koers:" , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , $this->formatGetal($koersWaarde,4) , 0, 1, "R");


		$this->pdf->Cell($extraRuimte , 4 , "" , 0, 0, "L");
		$this->pdf->Cell(25 , 4 , "Valutakoers:" , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , $this->formatGetal($valutakoers,4) , 0, 1, "R");
		//$valutakoers
		$this->pdf->Cell($extraRuimte , 4 , "" , 0, 0, "L");
		$this->pdf->Cell(25 , 4 , "Factor:" , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , $this->formatGetal($fondseenheid,2) , 0, 1, "R");

		$this->pdf->ln();
		$this->pdf->Cell($extraRuimte , 4 , "" , 0, 0, "L");
		$this->pdf->SetFont("Times","b",10);
		$this->pdf->Cell(25 , 4 , "Totale positie:" , 0, 0, "R");
		$this->pdf->Cell(25 , 4 , "" , 0, 0, "R");
		$this->pdf->SetFont("Times","",10);
		$this->pdf->Cell(20 , 4 , $this->formatGetal((($totaalAantal * ($koersWaarde * $fondseenheid) * $valutakoers)),2) , 0, 1, "R");


		if($this->progressbar)
			$this->progressbar->hide();
	}

	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$exceldata = generateCSV($this->pdf->excelData);
			fwrite($fp,$exceldata);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}
	}


	function OutputDatabase()
	{
	  global $USR;
	  $db=new DB();
	  $table="reportbuilder_$USR";
	  $query="SHOW TABLES like '$table'";
	  if($db->QRecords($query) > 0)
	  {
	    $db->SQL("DROP table $table");
	    $db->Query();
	  }
    if($this->dbTable)
    {
      $db->SQL($this->dbTable);
	    $db->Query();
	    $query="show variables like 'character_set_database'";
      $db->SQL($query);
      $db->Query();
      $charset=$db->lookupRecord();
      $charset=$charset['Value'];
      $query="ALTER TABLE `$table` CONVERT TO CHARACTER SET $charset";
      $db->SQL($query);
      $db->Query();
    }
    if(is_array($this->dbWaarden))
    {
      foreach ($this->dbWaarden as $rege=>$waarden)
      {
        $query="INSERT INTO $table SET add_date=now() ";
        //listarray($waarden);
        foreach ($waarden as $key=>$value)
        {
          $query.=",$key='".addslashes($value)."' ";
        }
        $db->SQL($query);
	      $db->Query();
      }
    }
	//    listarray($this->dbTable);


	}
}
?>