<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
 		File Versie					: $Revision: 1.9 $
 		
 		$Log: RapportVHO_L15.php,v $
 		Revision 1.9  2018/08/18 12:40:15  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.8  2008/06/06 09:14:17  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2007/12/21 09:16:52  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2007/12/14 14:13:13  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2007/11/16 11:25:30  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2007/10/02 14:16:14  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2007/09/21 14:04:02  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/09/21 13:40:23  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/08/30 12:04:37  rvv
 		*** empty log message ***
 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVHO_L15
{
	function RapportVHO_L15($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO_L15";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Waardebepaling Totale Portefeuille per ";
		
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		
		$this->debug = array();
	}
	
	function formatGetal($waarde, $dec=2)
	{
		return number_format($waarde,$dec,",",".");
	}
	


	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
		
		$DB = new DB();
		$this->pdf->alignA = array('L','C','R','R','R','R');
		$this->pdf->AddPage();
		$this->pdf->SetAligns($this->pdf->alignA);


		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);	
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];
		
   $query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" TijdelijkeRapportage.historischeValutakoers, TijdelijkeRapportage.fondsEenheid, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalRapportageValuta, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, 
			TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro, 
			TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ,Fondsen.GarantiePercentage ".
			" FROM TijdelijkeRapportage, Fondsen WHERE ".
			" TijdelijkeRapportage.Fonds = Fondsen.Fonds AND 
			 TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
		debugSpecial($query,__FILE__,__LINE__);	
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->SetFont('arial','',10);
		while($data = $DB->NextRecord())
		{
			$garantieWaarde = $data['GarantiePercentage'] / 100 * ($data['fondsEenheid'] * $data['totaalAantal'] * 100);
			
			if($data['fondsEenheid'] == 0.01)
			  $koers = $this->formatGetal($data['actueleFonds']).' %';
			else
			  $koers = '€ '.$this->formatGetal($data['actueleFonds']);
			//$this->pdf->rapport_depotbank.'-Bank'
			$this->pdf->row(array($data['fondsOmschrijving'],
			                      '',
			                      '€ '.$this->formatGetal($data['historischeWaardeTotaal']),
			                      $koers,
			                      '€ '.$this->formatGetal($data['actuelePortefeuilleWaardeEuro']),
			                      '€ '.$this->formatGetal($garantieWaarde)  ));

	  	$totaalInleg += $data['historischeWaardeTotaal'];
	   $this->debug['totaalInleg']['historischeWaardeTotaal'][]=array('fonds'=>$data['fondsOmschrijving'],'historischeWaarde'=>$data['historischeWaardeTotaal']);
 	
			$waardeTotaal += $data['actuelePortefeuilleWaardeEuro'];
			$garantieTotaal += $garantieWaarde;
			
//echo "fonds $garantieTotaal += $garantieWaarde; <br>\n";
			// print totaal op hele categorie.
		}
		
		// print rente.
		$query = "
		SELECT TijdelijkeRapportage.fondsOmschrijving,  
		       TijdelijkeRapportage.actueleValuta, 
				   sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro, 
			     TijdelijkeRapportage.type
    FROM 
           TijdelijkeRapportage
    WHERE 
			    TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND  
			    TijdelijkeRapportage.type = 'rente' AND 
			    TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		      .$__appvar['TijdelijkeRapportageMaakUniek']." 
          GROUP BY TijdelijkeRapportage.type ";
		$DB->SQL($query); 
		$DB->Query();
		while($data = $DB->NextRecord())
		{
			$this->pdf->row(array('Opgelopen Rente',
			                      '',
			                      '',
			                      '',
			                      '€ '.$this->formatGetal($data['actuelePortefeuilleWaardeEuro']),
			                      '€ '.$this->formatGetal($data['actuelePortefeuilleWaardeEuro'])  ));

	
			$waardeTotaal += $data['actuelePortefeuilleWaardeEuro'];
			$garantieTotaal += $data['actuelePortefeuilleWaardeEuro'];
//echo "rente $garantieTotaal += $garantieWaarde; <br>\n";			
		}
		

		

		$query = "
		SELECT TijdelijkeRapportage.fondsOmschrijving, 
		       TijdelijkeRapportage.rekening, 
		       TijdelijkeRapportage.actueleValuta, 
				   TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro, 
			     TijdelijkeRapportage.type,
           Rekeningen.Inleg
    FROM 
           TijdelijkeRapportage
           LEFT JOIN Rekeningen on  TijdelijkeRapportage.rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
    WHERE 
			     TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND  
			     TijdelijkeRapportage.type <> 'rente' AND
			     TijdelijkeRapportage.type <> 'fondsen' AND  
			     TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		       .$__appvar['TijdelijkeRapportageMaakUniek']." 
		       ORDER BY Rekeningen.Inleg DESC";
		$DB->SQL($query); //echo $query;exit;
		$DB->Query();
		while($data = $DB->NextRecord())
		{

		if($data['Inleg'] == 0)
		{
		  
		  if($inlegBerekend)
		  {
		    echo "<script> alert('De rekening inleg is voor meer dan 1 rekening niet gevuld.') </script> ";exit;
		  }
		  
		 $stortingen   = getStortingen($this->portefeuille,$this->pdf->PortefeuilleStartdatum,$this->rapportageDatum);//$this->pdf->PortefeuilleStartdatum
		 $onttrekkingen     = getOnttrekkingen($this->portefeuille,$this->pdf->PortefeuilleStartdatum ,$this->rapportageDatum);//  //$this->rapportageDatumVanaf

  $query = "SELECT 
	SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) AS subdebet , 
	SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers) AS subcredit 
	FROM Rekeningmutaties, Rekeningen, Portefeuilles 
	WHERE 
	Rekeningmutaties.Rekening = Rekeningen.Rekening AND 
	Rekeningen.Portefeuille = '".$this->portefeuille."' AND 
	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND 
	Rekeningmutaties.Verwerkt = '1' AND 
	YEAR(Rekeningmutaties.Boekdatum) =  '".date("Y",db2jul($this->pdf->PortefeuilleStartdatum))."' AND 
	Rekeningmutaties.Grootboekrekening = 'VERM' ";
	$DB2 = new DB();
	$DB2->SQL($query); //echo $query;exit;
	$DB2->Query();
	$data2 = $DB2->nextRecord();
	$beginwaarde = $data2['subcredit'] - $data2['subdebet']; 


	//	  $beginwaarde   = getRekeningBeginwaarde($data['rekening'],date("Y",db2jul($this->pdf->PortefeuilleStartdatum)));  
	//	  $stortingen    = getRekeningStortingen($data['rekening'],$this->pdf->PortefeuilleStartdatum,$this->rapportageDatum);
	//	  $onttrekkingen = getRekeningOnttrekkingen($data['rekening'],$this->pdf->PortefeuilleStartdatum,$this->rapportageDatum);
	//  $FondsMutaties = getFondsMutaties2($data['rekening'],$this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,$this->pdf->Valuta,$this->pdf->debug);
		  $rekeningInleg =  ($beginwaarde+$stortingen-$onttrekkingen) - $totaalInleg;// - $FondsMutaties;  
		  $inlegBerekend = true;
		}
		else 
		 $rekeningInleg = $data['Inleg'];  
		
		 
		   $this->debug['totaalInleg']['rekeningInleg'][$data['rekening']]=array('beginwaarde'=>$beginwaarde,'stortingen'=>$stortingen,'onttrekkingen'=>$onttrekkingen,'TotaalInlegAftrek'=>$totaalInleg,'rekeningInleg'=>$rekeningInleg);

		 // $this->debug['totaalInleg']['rekeningInleg'][$data['rekening']]=array('beginwaarde'=>$beginwaarde,'stortingen'=>$stortingen,'onttrekkingen'=>$onttrekkingen,'FondsMutaties'=>$FondsMutaties,'rekeningInleg'=>$rekeningInleg);

		  $rekeningBeginwaarden += $beginwaarde;
	
		  $totaalInleg += $rekeningInleg;
			$garantieWaarde = $data['actuelePortefeuilleWaardeEuro']; //$data['GarantiePercentage'] / 100 * $data['historischeWaardeTotaalValuta'];
			
			$this->pdf->row(array($data['fondsOmschrijving'],
			                      '',
			                      '€'.$this->formatGetal($rekeningInleg),
			                      '',
			                      '€ '.$this->formatGetal($data['actuelePortefeuilleWaardeEuro']),
			                      '€ '.$this->formatGetal($garantieWaarde)  ));
	
			$waardeTotaal += $data['actuelePortefeuilleWaardeEuro'] ;
			
			$garantieTotaal += $garantieWaarde;
		}

		$this->printTotaal($totaalInleg,$waardeTotaal,$garantieTotaal);

		if (round($totaalWaarde,1) != round($waardeTotaal,1))
		{
		  echo "Totaalwaarde komt niet overeen met de portefeuille waarde. ".$totaalWaarde.' <> '.$waardeTotaal;
		}
		
		if($this->pdf->debug)
		{
		  listarray($this->debug);
		  exit;
		}

	}
	
	function printTotaal($totaalInleg,$waardeTotaal,$garantieTotaal)
	{
	  if($this->pdf->getY() > 125)
	    $this->pdf->addPage();
	  
	  $this->pdf->SetFont('arial','B',12);
	  $this->pdf->ln();
	  $this->pdf->SetLineStyle(array('width' => 0.5 , 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
	  $this->pdf->Rect(20, $this->pdf->getY(), 255, 6, 'D');

	  $h =6;
	  $this->pdf->Cell(70,$h,'Totaal',0,0,'L');
	  $this->pdf->Cell(30,$h,'');		
	  $this->pdf->Cell(35,$h,'€ '.$this->formatGetal($totaalInleg),0,0,'R');	
	  $this->pdf->Cell(30,$h,'');	
	  $this->pdf->Cell(35,$h,'€ '.$this->formatGetal($waardeTotaal),0,0,'R');	
	  $this->pdf->Cell(55,$h,'€ '.$this->formatGetal($garantieTotaal),1,0,'R');	  
	  $this->pdf->ln($h);
	  
	  $this->pdf->Cell(200,$h,'');	
	  $this->pdf->Cell(55,$h,$this->formatGetal($garantieTotaal/$totaalInleg *100).'%',1,0,'R');	  
	  $this->pdf->ln($h);
	  
	  $this->pdf->Cell(200,$h,'');	
	  $this->pdf->SetFont('arial','B',10);
	  $this->pdf->Cell(55,$h,'kapitaalgarantie t.o.v. inleg',1,0,'R');
	  $this->pdf->ln(8);
	  
	  $this->pdf->SetFont('arial','',10);
	  $this->pdf->MultiCell(255,4,'Ondanks dat dit overzicht met de meest mogelijke zorgvuldigheid is samengesteld kunnen er geen rechten aan ontleend worden.
De koersinformatie is afkomstig van betrouwbaar geachte bronnen. Bij eventuele vroegtijdige verkoop dient u rekening te houden met
eventuele verkoopkosten en/of aan de bron nog af te schrijven kosten. Op eindvervaldag dient u enkel rekening te houden met eventuele
kosten die door uw bank of broker in rekening worden gebracht. Tussentijds zal de koers afwijken van de onderliggende waarde.
Indien u meent dat er in bovenstaande informatie zaken onjuist zijn weergegeven, verzoeken wij u vriendelijk dit zo spoedig mogelijk aan ons door te
geven, zodat wij een en ander kunnen toelichten en zonodig kunnen aanpassen.
Onder kapitaalgarantie wordt verstaan : tijdens de looptijd uit te keren / uitgekeerde coupons/dividenden plus de gegarandeerde eindwaarde van de producten.
',0,'L');
	  $this->pdf->ln(4);
	  $this->pdf->SetFont('arial','B',12);
	  $this->pdf->Cell(100,$h,'Verwer & Janssen Vermogensmanagement B.V.',0,1,'L');
			 
	}
}
?>