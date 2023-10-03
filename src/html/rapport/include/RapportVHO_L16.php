<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2009/07/12 09:32:42 $
File Versie					: $Revision: 1.4 $

$Log: RapportVHO_L16.php,v $
Revision 1.4  2009/07/12 09:32:42  rvv
*** empty log message ***

Revision 1.3  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.2  2007/10/05 13:29:55  rvv
*** empty log message ***

Revision 1.1  2007/10/04 12:09:12  rvv
*** empty log message ***

Revision 1.25  2007/07/05 12:28:39  rvv
*** empty log message ***

Revision 1.24  2007/06/29 11:38:56  rvv
L14 aanpassingen

Revision 1.23  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.22  2007/03/22 07:35:54  rvv
*** empty log message ***

Revision 1.21  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.20  2007/01/31 16:20:27  rvv
*** empty log message ***

Revision 1.19  2006/11/03 11:24:04  rvv
Na user update

Revision 1.18  2006/10/31 12:11:04  rvv
Voor user update

Revision 1.17  2006/05/09 07:48:11  jwellner
- afronding fondsaantal
- afronding controle bij afdrukken rapporten
- sorteren frontoffice selectie

Revision 1.16  2005/12/08 13:55:21  jwellner
Modelcontrole rapport

Revision 1.15  2005/11/30 08:37:39  jwellner
layout stuff

Revision 1.14  2005/11/11 10:15:31  jwellner
fout in OIV

Revision 1.13  2005/11/07 10:29:17  jwellner
no message

Revision 1.12  2005/11/01 11:20:08  jwellner
diverse aanpassingen

Revision 1.11  2005/10/26 11:47:39  jwellner
no message

Revision 1.10  2005/10/14 16:17:56  jwellner
no message

Revision 1.9  2005/10/07 07:15:15  jwellner
rapportage

Revision 1.8  2005/09/30 09:45:45  jwellner
rapporten aangepast.

Revision 1.7  2005/09/29 15:00:18  jwellner
no message

Revision 1.6  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.5  2005/09/13 14:49:18  jwellner
rapportage toevoegingen

Revision 1.4  2005/09/12 12:04:16  jwellner
bugs en features

Revision 1.3  2005/09/12 09:10:42  jwellner
diverse aanpassingen / bugfixes gemeld in e-mails theo

Revision 1.2  2005/08/01 13:05:25  jwellner
diverse kleine bugfixes :
- beheerfee nooit < 0

Revision 1.1  2005/07/15 11:21:00  jwellner
Layout verwijderd, alles samengevoegd in PDFRapport

Revision 1.2  2005/07/12 07:09:50  jwellner
no message

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd
 
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVHO_L16
{
	function RapportVHO_L16($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
	
		if($this->pdf->rapport_VHO_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VHO_titel;
		else 
			$this->pdf->rapport_titel = "Vermogensoverzicht";
		
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}
	
	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
	

	
	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
		
		$DB = new DB();
	
		
		$this->pdf->AddPage();
		
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
		
		$actueleWaardePortefeuille = 0;
		
		$query = "SELECT Beleggingscategorien.Omschrijving, ".
		" TijdelijkeRapportage.beleggingscategorie ".
		" FROM TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'" .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie ".
		" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);	
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			
			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
			$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);		
		
			$this->pdf->Row(array($categorien['Omschrijving']));
		
			// print detail (select from tijdelijkeRapportage)
			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.fonds, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" TijdelijkeRapportage.historischeValutakoers, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, 
			TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro, 
			TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);	
			$DB2 = new DB();
			$DB2->SQL($subquery); //echo $subquery.'<br><br>';exit();
			$DB2->Query();
			
			while($subdata = $DB2->NextRecord())
			{
			  
			  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);		
			  
				$fondsResultaat = ($subdata[actuelePortefeuilleWaardeInValuta] - $subdata[historischeWaardeTotaal]) * $subdata[actueleValuta] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $subdata[historischeWaardeTotaal]) * 100;
				if($subdata[historischeWaardeTotaal] < 0 && $fondsResultaat > 0)
				  $fondsResultaatprocent = -1 * $fondsResultaatprocent;
				$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);
				$valutaResultaat = $subdata[actuelePortefeuilleWaardeEuro] - $subdata[historischeWaardeTotaalValuta] - $fondsResultaat;
				//$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				$procentResultaat = (($subdata[actuelePortefeuilleWaardeEuro] - $subdata[historischeWaardeTotaalValuta]) / ($subdata[historischeWaardeTotaalValuta] /100));
        $gecombeneerdResultaat = $fondsResultaat + $valutaResultaat;
				if($subdata[historischeWaardeTotaalValuta] < 0)
					$procentResultaat = -1 * $procentResultaat;
				// attica ?
				//$procentResultaat = ($valutaResultaat / $subdata[beginPortefeuilleWaardeEuro]) *100;
				
				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VHO_decimaal_proc);
		
				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VHO_decimaal);
					
				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VHO_decimaal,$this->pdf->rapport_VHO_decimaal_proc);
					
				if($this->pdf->rapport_layout == 8 || $this->pdf->rapport_layout == 5 || $this->pdf->rapport_layout == 14)				
				{
					if($fondsResultaatprocent > 1000 || $fondsResultaatprocent < -1000)
						$fondsResultaatprocenttxt = "p.m.";
					else
						$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);
				}
				
				$this->pdf->setX($this->pdf->marge);
				
				$percentageVanTotaal = ($subdata[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
				
				if($this->pdf->rapport_VHO_percentageTotaal == 1)
					$percentageTotaalTekst = $this->formatGetal($percentageVanTotaal,1)."%";
				else 
					$percentageTotaalTekst = "";
					
						$opgelopenRente=array();
			    $subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.rentedatum, ".
					" TijdelijkeRapportage.renteperiode, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.fonds = '".$subdata['fonds']."' AND ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.valuta =  '".$subdata[valuta]."'".
					" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'].
					" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
			     
					 $DB3 = new DB();
		    	 $DB3->SQL($subquery); //echo "$subquery <br>";exit;
			     $DB3->Query();
			     $opgelopenRente=$DB3->NextRecord();

			       
			     if (round($opgelopenRente['actuelePortefeuilleWaardeEuro'],2) == 0.00)
		         $rente = '';
		       else 
		         $rente = $this->formatGetal($opgelopenRente['actuelePortefeuilleWaardeEuro'],2);
			     

		$this->pdf->row(array($subdata[fondsOmschrijving],
													$this->formatGetal($subdata[totaalAantal],0,$this->pdf->rapport_VHO_aantalVierDecimaal),
													$subdata[valuta],
													$this->formatGetal($subdata[actueleFonds],2),
													$this->formatGetal($subdata[historischeWaarde],2),
												  $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),
												  $this->formatGetal($gecombeneerdResultaat,$this->pdf->rapport_VHO_decimaal),
													$procentResultaattxt.'%',
												  $rente,	
												  $percentageTotaalTekst));
												  
				$subtotaal['totaalWaardeEur'] += $subdata['actuelePortefeuilleWaardeEuro'];	
				$subtotaal['totaalGecombeneerdResultaat'] += $gecombeneerdResultaat;		
				$subtotaal['totaalProcentResultaatWaarde']	+=	$totaalWaardeEur * $ProcentResultaat /100; 
				$subtotaal['totaalRente'] += $opgelopenRente['actuelePortefeuilleWaardeEuro'];
				$subtotaal['totaalHistorischeWaarde'] += $subdata[historischeWaardeTotaalValuta];
				$subtotaal['totaalPercentageTotaal'] +=$percentageVanTotaal;
												  
			}
		  if (round($subtotaal['totaalRente'],2) == 0.00)
		    $rente = '';
		  else 
		    $rente = $this->formatGetal($subtotaal['totaalRente'],2);
		  
		  $procentWVTotaal = ($subtotaal['totaalWaardeEur'] - $subtotaal['totaalHistorischeWaarde']) / $subtotaal['totaalHistorischeWaarde'] *100;
			
		  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);		
			$this->pdf->row(array('','','','','',
			                    $this->formatGetal($subtotaal['totaalWaardeEur'],2),
												  $this->formatGetal($subtotaal['totaalGecombeneerdResultaat'],2),
									      	$this->formatGetal($procentWVTotaal,1).'%',
												  $rente,	
												  $this->formatGetal($subtotaal['totaalPercentageTotaal'],2).'%'));
												  
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);		
				
			$totalen['totaalWaardeEur'] += $subtotaal['totaalWaardeEur'];
			$totalen['totaalGecombeneerdResultaat'] += $subtotaal['totaalGecombeneerdResultaat'];		
			$totalen['totaalProcentResultaatWaarde']	+=	$subtotaal['totaalProcentResultaatWaarde']; 
			$totalen['totaalRente'] += $subtotaal['totaalRente'];
			$totalen['totaalHistorischeWaarde'] += $subtotaal['totaalHistorischeWaarde'];
			$totalen['totaalPercentageTotaal'] +=$subtotaal['totaalPercentageTotaal'];
			$subtotaal= array();		

			$this->pdf->ln();
			
		}
		
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);		
		
		$procentWVTotaal = ($totalen['totaalWaardeEur'] - $totalen['totaalHistorischeWaarde']) / $totalen['totaalHistorischeWaarde'] *100;
		$this->pdf->row(array('Subtotaal','','','','',
			                    $this->formatGetal($totalen['totaalWaardeEur'],2),
												  $this->formatGetal($totalen['totaalGecombeneerdResultaat'],2),
									      	$this->formatGetal($procentWVTotaal,1).'%',
												  '',	
												  $this->formatGetal($totalen['totaalPercentageTotaal'],2).'%'));	
		$this->pdf->ln();
		
		if (round($totalen['totaalRente'],2) != 0.00)
		{		
		$percentageVanTotaal = ($totalen['totaalRente']) / ($totaalWaarde/100);
		
		$this->pdf->row(array('Opgelopen Rente','','','','', $this->formatGetal($totalen['totaalRente'],2),'','','',$this->formatGetal($percentageVanTotaal,2).'%'));
		
		$totalen['totaalPercentageTotaal'] += $percentageVanTotaal;
		$totalen['totaalWaardeEur'] += $totalen['totaalRente'];
		}
		
		$this->pdf->ln();
		// Liquiditeiten
		$this->pdf->row(array('Liquiditeiten','','','','','','','','',''));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);		
		
		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro , ".
			" TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);	
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();
		$subtotaal = array();

		if($DB1->records() >0)
		{
		//	$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"bi");
			$totaalLiquiditeitenInValuta = 0;
		 
			while($data = $DB1->NextRecord())
			{
      $percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
				
		  $this->pdf->row(array($data['fondsOmschrijving'],'','','','',
			                    $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],2),
												  '',
									      	'',
												  '',	
												  $this->formatGetal($percentageVanTotaal,2).'%'));	
		   $subtotaal['totaalWaardeEur'] += $data['actuelePortefeuilleWaardeEuro'];	
		   $subtotaal['totaalPercentageTotaal'] += $percentageVanTotaal;	
			}
			$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);		
			$this->pdf->row(array('','','','','',$this->formatGetal($subtotaal['totaalWaardeEur'],2),'','','',''));	
			$totalen['totaalWaardeEur'] += $subtotaal['totaalWaardeEur'];
			$totalen['totaalPercentageTotaal'] +=$subtotaal['totaalPercentageTotaal'];
		}			
		$this->pdf->ln();
	  $this->pdf->row(array('Totaal portefeuille','','','','',$this->formatGetal($totalen['totaalWaardeEur'],2),'','','',$this->formatGetal($totalen['totaalPercentageTotaal'],2).'%'));				
		

		

	
		
		// check op totaalwaarde!
		if(round(($totaalWaarde - $totalen['totaalWaardeEur']),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($totalen['totaalWaardeEur'],2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}
		
		
		$this->pdf->ln();
		
		if($this->pdf->rapport_VHO_valutaoverzicht == 1)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VHO_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}
		
		
		if($this->pdf->rapport_VHO_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}
		
		// index vergelijking afdrukken
		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 && $this->pdf->rapport_VHO_indexUit == 0) 
		{
			$this->pdf->printAEXVergelijking($this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
		
	}
}
?>