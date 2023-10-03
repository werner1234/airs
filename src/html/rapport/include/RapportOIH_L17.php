<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/01/20 17:44:09 $
 		File Versie					: $Revision: 1.4 $
 		
 		$Log: RapportOIH_L17.php,v $
 		Revision 1.4  2009/01/20 17:44:09  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2008/05/06 10:24:17  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/03/27 08:31:58  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/03/19 12:03:22  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2008/03/18 09:58:08  rvv
 		*** empty log message ***
 		
 	
*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportOIH_L17
{
	function RapportOIH_L17($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIH";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIH_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIH_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingssector";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else 
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }
  


	function writeRapport()
	{
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		global $__appvar;		
		$riscoTotaal = 0;
		// voor data
		$this->pdf->widthB = array(40,50,20,20,20,15,20,25,25,20,20);
		$this->pdf->alignB = array('L','L','R','R','R','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(40,50,20,20,20,15,20,25,25,20,20);
		$this->pdf->alignA = array('L','L','R','R','R','L','R','R','R','R','R');
    $this->pdf->AddPage();
    
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);					  
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];


		$actueleWaardePortefeuille = 0;

	
$query="		

SELECT 
 BeleggingscategoriePerFonds.RisicoPercentageFonds,
TijdelijkeRapportage.Type,
TijdelijkeRapportage.beleggingscategorie as belCategorie, 
TijdelijkeRapportage.fondsOmschrijving, 
TijdelijkeRapportage.actueleValuta,
 TijdelijkeRapportage.beleggingssector,
 Beleggingssectoren.Omschrijving AS secOmschrijving, 
 Beleggingscategorien.Omschrijving as CatOmschrijving,
TijdelijkeRapportage.totaalAantal, 
TijdelijkeRapportage.historischeWaarde, 
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal,
 (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaalValuta, 
TijdelijkeRapportage.beginwaardeLopendeJaar, 
TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, 
TijdelijkeRapportage.beginPortefeuilleWaardeEuro, 
TijdelijkeRapportage.actueleFonds, 
TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, 
TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, 
TijdelijkeRapportage.beleggingscategorie, 
TijdelijkeRapportage.valuta, 
TijdelijkeRapportage.fonds, 
TijdelijkeRapportage.rekening, 
TijdelijkeRapportage.portefeuille
FROM 
TijdelijkeRapportage LEFT JOIN Beleggingssectoren on (TijdelijkeRapportage.beleggingssector = Beleggingssectoren.Beleggingssector) 
LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie) 
LEFT JOIN BeleggingscategoriePerFonds  on (BeleggingscategoriePerFonds.Fonds = TijdelijkeRapportage.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'  )
WHERE TijdelijkeRapportage.portefeuille =  '".$this->portefeuille."'  AND 
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']." 
ORDER BY TijdelijkeRapportage.Type, Beleggingscategorien.Afdrukvolgorde asc, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc
";
  //ORDER BY TijdelijkeRapportage.Type, Beleggingscategorien.Afdrukvolgorde asc, Beleggingssectoren.Afdrukvolgorde asc, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc
          

	$DB = new DB();
	$DB->SQL($query);
	$DB->Query(); 
	$lastCategorie = "xx";
	$secTel = 0;
	while($data = $DB->NextRecord())
	{
	  if($data['Type']=='rente')
	   $data['belCategorie'] = 'rente';
	  
	
	  if($data['belCategorie'] == '')
	  {

	    $data['belCategorie'] = $data['Type'];
	  }
	    
	    
	  $catOmschrijvingingen[$data['belCategorie']] = $data['CatOmschrijving'];
	  
	  
	    	$fondsResultaat = ($data['actuelePortefeuilleWaardeInValuta'] - $data['historischeWaardeTotaal']) * $data['actueleValuta'];
				$valutaResultaat = $data['actuelePortefeuilleWaardeEuro'] - $data['historischeWaardeTotaalValuta'] - $fondsResultaat; 
				
				if ($data['actuelePortefeuilleWaardeEuro'] < 0) 
				  $procentResultaat = ( ( -1 * ($data['actuelePortefeuilleWaardeEuro'] - $data['historischeWaardeTotaalValuta']))/($data['historischeWaardeTotaalValuta'] /100));
				else
				  $procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['historischeWaardeTotaalValuta']) /($data['historischeWaardeTotaalValuta'] /100));

				  $aandeel = $data['actuelePortefeuilleWaardeEuro'] / $totaalWaarde * 100;
				  
$sortedData[$data['Type']][$data['belCategorie']][]=array('omschrijving'=>$data['fondsOmschrijving'],
                                                        'aantal'=>$data['totaalAantal'],
                                                        'kostprijs'=>$data['historischeWaarde'],
                                                        'koers'=>$data['actueleFonds'],
                                                        'valuta'=>$data['valuta'],
                                                        'waardeEur'=>$data['actuelePortefeuilleWaardeEuro'],
                                                        'fondsResultaat'=>$fondsResultaat,
                                                        'valutaResultaat'=>$valutaResultaat,
                                                        'ongerealiseerdResultaat'=>$fondsResultaat+$valutaResultaat,
                                                        'procentResultaat'=>$procentResultaat,
                                                        'aandeel'=>$aandeel,
                                                        'rekening'=>$data['rekening'],
                                                        'RisicoPercentageFonds'=>$data['RisicoPercentageFonds']);
                                                        

$catTotalen[$data['Type']][$data['belCategorie']]['waardeEur'] += $data['actuelePortefeuilleWaardeEuro'];
$catTotalen[$data['Type']][$data['belCategorie']]['ongerealiseerdResultaat'] += $fondsResultaat+$valutaResultaat;    
$catTotalen[$data['Type']][$data['belCategorie']]['aandeel'] += $aandeel;    

$catTotalen[$data['Type']][$data['belCategorie']]['procentResultaatAandeel'] += $procentResultaat * $aandeel;

$totalen['waardeEur'] +=  $data['actuelePortefeuilleWaardeEuro'];	  

if($data['Type'] != 'rekening' && $data['Type'] != 'rente')
  $totalen['ongerealiseerdResultaat'] +=  $fondsResultaat+$valutaResultaat;   
$totalen['aandeel'] +=  $aandeel;	
	 }
	   
//	  listarray($sortedData);
$catOmschrijvingingen['rekening'] = 'Liquiditeiten';
$catOmschrijvingingen['rente'] = 'Opgelopen Rente';

if(isset($sortedData['rekening']))
{
$tmpRekening = $sortedData['rekening'];
unset($sortedData['rekening']);
$sortedData['rekening']= $tmpRekening;
}

	  foreach ($sortedData as $type=>$typeData)
	  {
	    
	    
	    foreach ($typeData as $cat=>$catData)
	    {

	        $catOmschrijving = $this->formatGetal($catTotalen[$type][$cat]['waardeEur']/$totaalWaarde * 100,0) ."% ".$catOmschrijvingingen[$cat];
	        $x=$this->pdf->getX();
	        $this->pdf->switchFont('2'); 	
          $this->pdf->fillCell = array(1,0);	
          $this->pdf->CellBorders = array('','U','U','U','U','U','U','U','U','U','U'); 
          $this->pdf->Cell($this->pdf->widthB[0],4,$catOmschrijving,0,0,"L",1);
          $this->pdf->switchFont('fonds'); 
          $this->pdf->fillCell = array();	
          $this->pdf->setX($x);
	     

	      foreach ($catData as $data)
	      {
	        $data['omschrijving'] = $data['omschrijving'];
	        $data['aantal'] = $this->formatGetal($data['aantal'],0);
	        $data['kostprijs'] = $this->formatGetal($data['kostprijs'],2);
	        $data['koers'] = $this->formatGetal($data['koers'],2);
	        $data['waardeEur']=$this->formatGetalKoers($data['waardeEur'],2);
	        $data['ongerealiseerdResultaat'] = $this->formatGetalKoers($data['ongerealiseerdResultaat'],2);
	        $data['procentResultaat'] = $this->formatGetal($data['procentResultaat'],1)."%";
	        $data['aandeel'] = $this->formatGetal($data['aandeel'],1).'%';
	        $data['risico'] = $this->formatGetal($data['RisicoPercentageFonds'],0)."%";

	        
	        if($type=='rekening')
	        {
	          
	          $tmpdata['omschrijving'] = $data['omschrijving'].' '.$data['rekening'];
	          $tmpdata['waardeEur']=$data['waardeEur'];
	          $tmpdata['aandeel'] = $data['aandeel'];
	          $data = $tmpdata;
	        }
	        
	        if($type == 'rente' && $this->pdf->rapport_OIH_geenrentespec == 1)
	        {
	          //Toon geen regels	          
	        }
	        else
	        {	        
	      	$this->pdf->row(array('',$data['omschrijving'],$data['aantal'],$data['kostprijs'],$data['koers'],$data['valuta'],$data['waardeEur'],
	      	$data['ongerealiseerdResultaat'],$data['procentResultaat'],$data['aandeel'],$data['risico']));
	        }
												
												
	      }
	      
      	$catTotalen[$type][$cat]['procentResultaat'] = $this->formatGetalKoers($catTotalen[$type][$cat]['procentResultaatAandeel']/$catTotalen[$type][$cat]['aandeel'],1)."%";
	      $catTotalen[$type][$cat]['waardeEur']=$this->formatGetalKoers($catTotalen[$type][$cat]['waardeEur'],2);
	      $catTotalen[$type][$cat]['ongerealiseerdResultaat']=$this->formatGetalKoers($catTotalen[$type][$cat]['ongerealiseerdResultaat'],2);
				$catTotalen[$type][$cat]['aandeel']=$this->formatGetalKoers($catTotalen[$type][$cat]['aandeel'],1).'%';
									
				if($type == 'rekening' || $type == 'rente')
				{
				  $catTotalen[$type][$cat]['ongerealiseerdResultaat'] ='';
				  $catTotalen[$type][$cat]['procentResultaat'] ='';
				}
				
	      
	
	      $this->pdf->switchFont('totaal');  	
        $this->pdf->fillCell = array(0,1,1,1,1,1,1,1,1,1,1);	
        $this->pdf->CellBorders = array();			
				$this->pdf->row(array('','Subtotaal','','','','',
												$catTotalen[$type][$cat]['waardeEur'],
												$catTotalen[$type][$cat]['ongerealiseerdResultaat'],
												$catTotalen[$type][$cat]['procentResultaat'],
												$catTotalen[$type][$cat]['aandeel'],
												''));
	      $this->pdf->fillCell = array();											
        $this->pdf->switchFont('fonds'); 
        $this->pdf->ln(2);
	      
	    }
	    

	    
	  }

		// check op totaalwaarde!
		if(round(($totalen['waardeEur'] - $totaalWaarde),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}
	
		// print grandtotaal
			      $this->pdf->switchFont('rapportKop'); 
			      $this->pdf->fillCell = array(0,1,1,1,1,1,1,1,1,1,1);	 
			      
			      				$this->pdf->row(array('','Totale actuele waarde portefeuille','','','','',
												$this->formatGetal($totalen['waardeEur'],2),
												$this->formatGetal($totalen['ongerealiseerdResultaat'],2),
												'',
												$this->formatGetal($totalen['aandeel'],1)." %",
												''));	
						$this->pdf->fillCell = array();							

		$totaalTxt = $this->formatGetal(100,1);
		$this->pdf->ln();

		$this->pdf->switchFont('fonds');  
		
		//
		/*
		$query = "SELECT  
		          OrderRegels.Aantal,   
		          Fondsen.Valuta,     
		          Orders.fonds as omschrijving,
		          Fondsen.Fonds,
		          Fondsen.Fondseenheid,
		          Orders.transactieSoort, 
		          (SELECT Fondskoersen.koers FROM Fondskoersen, Fondsen WHERE Fondskoersen.Fonds = Fondsen.Fonds AND Fondsen.Omschrijving = Orders.Fonds AND Datum <= '".$this->rapportageDatum."' ORDER BY Datum DESC LIMIT 1 ) as koers
		          FROM OrderRegels, Orders, Fondsen
		          WHERE
		          Fondsen.Omschrijving = Orders.Fonds AND
		          OrderRegels.orderid = Orders.orderid AND
			        OrderRegels.portefeuille = '".$this->portefeuille."' AND 
			        OrderRegels.Status < 4 
			        ORDER BY fonds"; 
		$DB1 = new DB();
		$DB1->SQL($query); 
		$DB1->Query();

//		$totaalLiquiditeitenInValuta = 0;

		if($DB1->records() > 0)
		{
			$this->printKop(vertaalTekst("Lopende orders",$this->pdf->rapport_taal),$percentageVanTotaal,"b",false);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	
	//		$this->printTotaal(,$this->pdf->rapport_taal), 100, 0, '',false);
			


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->printCol(5,vertaalTekst("Huidige stand liquiditeiten"),"tekst");
		$this->printCol(7,$this->formatGetal($totaalLiquiditeitenEuro,2),"tekst");
		$this->pdf->ln();

			
			while($data = $DB1->NextRecord())
			{
			  if ($data['transactieSoort'] == 'V' || $data['transactieSoort'] == 'VO' || $data['transactieSoort'] == 'VS')
			    $data['Aantal'] = $data['Aantal'] * -1;
			  

			    
			 	$dbr = new DB();
				$select = 	" SELECT BeleggingscategoriePerFonds.RisicoPercentageFonds ".
									" FROM Portefeuilles,BeleggingscategoriePerFonds ".
									" WHERE Portefeuilles.Portefeuille = '".$this->portefeuille."' AND ".
									" Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder AND ".
									" BeleggingscategoriePerFonds.Fonds = '".$data['Fonds']."' LIMIT 1 ";
				$dbr->SQL($select);
				$dbr->Query();
				$risico = $dbr->nextRecord();

				$percentage = $risico[RisicoPercentageFonds];   
				$waardeEuro = $data['Aantal'] * $data['koers'] * $data['Fondseenheid'] * getValutaKoers($data['Valuta'],$this->rapportageDatum) ;
				
				if ($data['koers'] != '')
			    $data['koers'] = $this->formatGetal($data['koers'],2);
			  
			  $this->pdf->Row(array(
			                        '',
			                        $data['omschrijving'],
			                        $this->formatGetal($data['Aantal'],0),
			                        '',
			                        $data['koers'],
			                        $data['Valuta'],
			                        $this->formatGetal(-1*$waardeEuro,2),
			                        '',
			                        '',
			                        '',
			                        $percentage.'%'
			                        ));
			  $geschatteLiquiditeitenEuro +=  $waardeEuro;                    
			}
		$this->pdf->ln();		
		$this->printCol(5,vertaalTekst("Geschatte liquiditeiten na lopende orders"),"tekst");
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->printCol(7,$this->formatGetal($totaalLiquiditeitenEuro-$geschatteLiquiditeitenEuro,2),"totaal");
		$this->pdf->ln();	
		}
*/
    //
    
		if($this->pdf->rapport_OIH_valutaoverzicht == 1)
		{
			// selecteer distinct valuta.
		$q = "SELECT DISTINCT(TijdelijkeRapportage.valuta) AS val, Valutas.Omschrijving AS ValutaOmschrijving, 1/TijdelijkeRapportage.actueleValuta as actueleValuta".
		" FROM TijdelijkeRapportage, Valutas ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND ".
		" TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."' AND ".
		" TijdelijkeRapportage.valuta = Valutas.Valuta "
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($q,__FILE__,__LINE__);

		$DB->SQL($q); 
		$DB->Query();

		if($DB->records() > 0)
		{
			$t=0;
			while ($valuta = $DB->NextRecord())
			{
				$valutas[$t] = $valuta; 
				$t++;
			}

			if(count($valutas) > 4)
			{
				$regels = ceil((count($valutas) / 2));
			}

			$hoogte = ($regels * 4.5) + 10;
			if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
			{
				$this->pdf->AddPage();
				$this->pdf->ln();
			}

			$kop = "Valuta koersen";

      $this->pdf->switchFont('4');  
      $this->pdf->SetFont($this->pdf->rapport_font,'',12);
			$this->pdf->Row(array('', vertaalTekst($kop,$this->rapport_taal)));
			$this->pdf->ln();

			$plusmarge = 0;
			$y = $this->pdf->getY();
			$start = false;
			//while ($valuta = $DB->NextRecord())
			for($a=0; $a < count($valutas); $a++)
			{
				if(count($valutas) > 4)
				{
					if($a >= $regels && $start == false)
					{
						$y2 = $this->pdf->getY();
						$this->pdf->setY($y);
						$plusmarge = 60;
						$start = true;
					}
				}
		   $this->pdf->switchFont('fonds');  
  		 if($this->pdf->ValutaKoersEind > 0)
				 $valutas[$a][actueleValuta] = $valutas[$a][actueleValuta] / $this->pdf->ValutaKoersEind ;
				
			//	$this->pdf->Row(array('', vertaalTekst($valutas[$a][ValutaOmschrijving],$this->pdf->rapport_taal),$this->formatGetal($valutas[$a][actueleValuta],4)));
//
//

	      $this->pdf->SetX(48+$plusmarge);
				$this->pdf->Cell(35,4, vertaalTekst($valutas[$a][ValutaOmschrijving],$this->rapport_taal), 0,0, "L");
				$this->pdf->Cell(20,4, $this->formatGetal($valutas[$a][actueleValuta],4), 0,1, "R");
				//
			
			
			
			}
		}
		}
		elseif($this->pdf->rapport_OIH_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}
		



		if($this->pdf->rapport_OIH_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}
		
		if($this->pdf->rapport_OIH_risico == 1)
	  	$this->pdf->printRisico($this->portefeuille, $risicoTotaal, $actueleWaardePortefeuille);


		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 && $this->pdf->rapport_layout == 11)
		{
			$this->pdf->printAEXVergelijking($this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
		
		
	}
}
?>