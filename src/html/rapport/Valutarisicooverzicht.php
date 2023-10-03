<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
File Versie					: $Revision: 1.14 $

$Log: Valutarisicooverzicht.php,v $
Revision 1.14  2019/11/16 17:36:34  rvv
*** empty log message ***

Revision 1.13  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.12  2007/08/02 14:46:01  rvv
*** empty log message ***

Revision 1.11  2007/04/03 13:26:33  rvv
*** empty log message ***

Revision 1.10  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.9  2006/11/27 09:28:20  rvv
Nu ook niet fondsdeel

Revision 1.8  2006/11/03 11:24:04  rvv
Na user update

Revision 1.7  2006/10/31 12:12:15  rvv
Voor user update

Revision 1.6  2006/06/09 13:50:38  jwellner
*** empty log message ***

Revision 1.5  2005/11/09 10:46:12  jwellner
valuta risico rapport aangepast voor Forward valuta koersen

Revision 1.4  2005/11/09 10:21:05  jwellner
no message

Revision 1.3  2005/11/07 10:29:17  jwellner
no message

Revision 1.2  2005/09/12 09:10:42  jwellner
diverse aanpassingen / bugfixes gemeld in e-mails theo

Revision 1.1  2005/09/07 07:33:23  jwellner
no message
 
*/

include_once("rapportRekenClass.php");

class Valutarisicooverzicht
{
	/*
		PDF en CSV 
	*/
	var $selectData;
	var $excelData;
	
	function Valutarisicooverzicht( $selectData ) 
	{
		$this->selectData = $selectData;
		$this->pdf->excelData = array();
				
		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "valutarisicooverzicht";
		$this->pdf->SetAutoPageBreak(true,15); 
		$this->pdf->pagebreak = 190;
	
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->vandatum = $this->selectData[datumVan];
		$this->pdf->tmdatum = $this->selectData[datumTm];
		
		$this->orderby  = " Portefeuilles.Portefeuille ";
		
		$this->pdf->excelData = array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}	

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->__appvar = $this->__appvar;

	  $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    
		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			$this->progressbar->hide();
			exit;
		}
		
		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}
		
		
		$einddatum = jul2sql($this->selectData[datumTm]);
		
		// vul eerst de tijdelijketabel
		foreach($portefeuilles as $pdata)
		{	
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);	
			}
			
			$fondswaarden =  berekenPortefeuilleWaardeQuick($pdata[Portefeuille],  $einddatum);
			vulTijdelijkeTabel($fondswaarden ,$pdata[Portefeuille],$einddatum);
			// tel totaal op!
		}
		$this->pdf->AddPage();
	
		$this->pdf->SetFont("Times","b",10);
			
		$excelData = array("Portefeuille",
											"Client",
											"Valuta",
											"Totaal stukken",
											"Totaal Cash",
											"Totale waarde",
											"Valuta hedge",
											"Hedge-verschil",
											"Hedge-ratio (%)");
		$this->pdf->excelData[] = $excelData;
				
		$this->pdf->SetFont("Times","",10);
		
		// nog een keer een loop over de portefeuilles!
		

		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			exit;
		}
		$tel = 0;
		foreach($portefeuilles as $pdata)
		{
			$tel ++;
			$portefeuille = $pdata[Portefeuille];
			
			// select valuta per portefeuille.
/* Query vervangen zodat alle Valuta van de portefeuille worden opgehaald. RvV 17-11-2006 */
/*
			$query = " SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS totaal, TijdelijkeRapportage.valuta ".
										" FROM TijdelijkeRapportage, Fondsen WHERE ".
										" TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
										" TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND ".
										" TijdelijkeRapportage.type = 'fondsen' AND ".
										" TijdelijkeRapportage.fonds = Fondsen.Fonds AND ".
										" Fondsen.FondsOverslaanInValutaRisico = '0' AND ".
										" TijdelijkeRapportage.valuta <> 'EUR' "
										.$__appvar['TijdelijkeRapportageMaakUniek'].
										" GROUP BY TijdelijkeRapportage.valuta";
*/

			$query = "SELECT Valuta as valuta FROM TijdelijkeRapportage 
					WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND 
 					TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND 
					TijdelijkeRapportage.valuta <> 'EUR' "
					.$__appvar['TijdelijkeRapportageMaakUniek']. 
					" GROUP BY valuta";
			debugSpecial($query,__FILE__,__LINE__);
									
			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();
			while ($vdata = $DB2->nextRecord())
			{
				$_beginJaar = substr($einddatum,0,4)."-01-01";
  			
				//stukken deel toegevoegd, niet langer in Valuta selectie query. RvV 17-11-2006
				$query = " SELECT 
				              SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS TotaalOld ,
				              SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta * (Fondsen.valutaRisicoPercentage/100)) AS totaal ".
										" FROM TijdelijkeRapportage, Fondsen WHERE ".
										" TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
										" TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND ".
										" TijdelijkeRapportage.type = 'fondsen' AND ".
										" TijdelijkeRapportage.fonds = Fondsen.Fonds AND ".
										" Fondsen.FondsOverslaanInValutaRisico = '0' AND ".
										" TijdelijkeRapportage.Valuta = '".$vdata[valuta]."' "
										.$__appvar['TijdelijkeRapportageMaakUniek'].
										" GROUP BY TijdelijkeRapportage.Valuta";
				debugSpecial($query,__FILE__,__LINE__);
				$DB3 = new DB();
				$DB3->SQL($query);
				$DB3->Query();
				$rdata = $DB3->nextRecord();
				$stukkenTotaal = $rdata[totaal];
									
				// valuta hedge
  				$query = "SELECT SUM(Rekeningmutaties.Bedrag) as totaal ".
						" FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
					  " WHERE ".
						" Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
						" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
						" Portefeuilles.Portefeuille = '".$portefeuille."' AND ".
						" Rekeningmutaties.boekdatum >= '".$_beginJaar."' AND ".
						" Rekeningmutaties.boekdatum <= '".$einddatum."' AND ".
						" SUBSTRING(Rekeningen.Valuta,1,3) = '".$vdata[valuta]."' AND ".
						" Rekeningen.Termijnrekening <> '0' ";
											
				$DB3 = new DB();
				$DB3->SQL($query);
				$DB3->Query();
				$rdata = $DB3->nextRecord();
				$hedge = $rdata[totaal];
			
				// totaal Cash
  				$query = "SELECT SUM(Rekeningmutaties.Bedrag) as totaal ".
						" FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
					  " WHERE ".
						" Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
						" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
						" Portefeuilles.Portefeuille = '".$portefeuille."' AND ".
						" Rekeningmutaties.boekdatum >= '".$_beginJaar."' AND ".
						" Rekeningmutaties.boekdatum <= '".$einddatum."' AND ".
						" SUBSTRING(Rekeningen.Valuta,1,3) = '".$vdata[valuta]."' AND ".
						" Rekeningen.Termijnrekening = '0' ";
											
				$DB3 = new DB();
				$DB3->SQL($query);
				$DB3->Query();
				$rdata = $DB3->nextRecord();
				$cash =  $rdata[totaal];
				
//				$totaalWaarde = $vdata[totaal] + $cash;
				$totaalWaarde = $stukkenTotaal + $cash;
				$hedgeVerschil = $hedge + $totaalWaarde;
				$hedgeRatio = (ABS($hedge) / $totaalWaarde) * 100;


				if ($totaalWaarde != 0 || $hedge != 0)//Als totaalwaarde 0 of hedge 0 => Geen reden om toe te voegen. RvV 16-11-2006
				{			
				// schrijf data !
				$this->pdf->Cell(25 , 4 , $portefeuille , 0, 0, "R");
				$this->pdf->Cell(70 , 4 , substr($pdata[Naam],0,40) , 0, 0, "L");
				$this->pdf->Cell(20 , 4 , $vdata[valuta] , 0, 0, "L");
				$this->pdf->Cell(25 , 4 , $this->formatGetal($stukkenTotaal,2) , 0, 0, "R");
//				$this->pdf->Cell(25 , 4 , $this->formatGetal($vdata[totaal],2) , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , $this->formatGetal($rdata[totaal],2) , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , $this->formatGetal($totaalWaarde,2) , 0, 0, "R");
				$this->pdf->Cell(25 , 4 , $this->formatGetal($hedge,2), 0, 0, "R");
				$this->pdf->Cell(25 , 4 , $this->formatGetal($hedgeVerschil,2), 0, 0, "R");
				$this->pdf->Cell(25 , 4 , $this->formatGetal($hedgeRatio,2)." %", 0, 1, "R");
				
				$excelData = array($portefeuille,
													$pdata[Naam],
													$vdata[valuta],
													round($vdata[totaal],2),
													round($rdata[totaal],2),
													round($totaalWaarde,2),
													round($hedge,2),
													round($hedgeVerschil,2),
													round($hedgeRatio,2));
													
				$this->pdf->excelData[] = $excelData;
				}
			}
			verwijderTijdelijkeTabel($portefeuille,$einddatum);
			$vdata=array();
			$rdata=array();
		}		
		
	
		if($this->progressbar)
			$this->progressbar->hide();	
	}
	

}
?>