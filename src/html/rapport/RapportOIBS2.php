<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2009/01/20 17:44:09 $
File Versie					: $Revision: 1.21 $

$Log: RapportOIBS2.php,v $
Revision 1.21  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.20  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.19  2008/05/06 10:22:42  rvv
*** empty log message ***

Revision 1.18  2008/03/27 07:17:07  rvv
*** empty log message ***

Revision 1.17  2008/03/04 10:30:44  rvv
sectoren verwijderd

Revision 1.16  2008/03/03 08:09:45  rvv
*** empty log message ***

Revision 1.15  2008/01/23 07:37:03  rvv
*** empty log message ***

Revision 1.14  2007/11/16 11:22:27  rvv
*** empty log message ***

Revision 1.13  2007/04/20 12:21:16  rvv
*** empty log message ***

Revision 1.12  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.11  2007/01/31 16:20:27  rvv
*** empty log message ***

Revision 1.10  2007/01/17 10:59:25  rvv
Layout 11 OIS met index

Revision 1.9  2006/11/03 11:24:04  rvv
Na user update

Revision 1.8  2006/10/31 12:06:45  rvv
Voor user update

Revision 1.7  2006/10/20 14:55:53  rvv
*** empty log message ***

Revision 1.6  2006/10/02 12:45:56  rvv
teller in berekening percentage ongerealiseerd  maal -1 wanneer waarde in Eur negatief.

Revision 1.5  2006/08/10 10:25:51  cvs
fout weergave totalen over meerdere pagina's

Revision 1.4  2006/08/10 08:39:58  cvs
totaal over meerdere pagina's

Revision 1.3  2006/07/31 14:11:25  cvs
*** empty log message ***

Revision 1.2  2006/06/30 13:23:54  jwellner
*** empty log message ***

Revision 1.1  2006/06/29 14:54:57  jwellner
*** empty log message ***

 
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportOnderverdelingSectorLayout.php");

class RapportOIBS2
{
	function RapportOIBS2($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIBS2";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIS_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIS_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingssector";


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	// type = totaal / subtotaal / tekst
	function printCol($row, $data, $type = "tekst")
	{

		// draw lines
		// calculate positions
		$start = $this->pdf->marge;
		for($tel=0;$tel <$row;$tel++)
		{
			$start += $this->pdf->widthB[$tel];
		}

		$writerow = $this->pdf->widthB[($tel)];
		$end = $start + $writerow;

		// print cell , 1
		$this->pdf->Cell($start-$this->pdf->marge,4,"",0,0,"R");
		$this->pdf->Cell($writerow,4,$data, 0,0, "R");
    $y = $this->pdf->getY();
		if($type == "totaal" || $type == "subtotaal" || $type == "grandtotaal")
		{
			$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
			$this->pdf->ln();
			if($type == "grandtotaal")
			{
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
			else if($type == "totaal")
			{
				$this->pdf->setDash(1,1);
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->setDash();
			}

		}
		$this->pdf->setY($y);
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
  /*
	function printSubTotaal($title, $totaalA, $totaalB)
	{

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(1,$title,"tekst");
			if($totaalA <>0)
				$this->printCol(7,$this->formatGetal($totaalA,$this->pdf->rapport_OIS_decimaal),"subtotaal");
			if($totaalB <>0)
				$this->printCol(8,$this->formatGetal($totaalB,$this->pdf->rapport_OIS_decimaal),"subtotaal");

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();


		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4]+ $this->pdf->widthB[5]+ $this->pdf->widthB[6];

		$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[7],$this->pdf->GetY());


		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_OIS_decimaal);

		$this->pdf->SetX($actueel-$this->pdf->widthB[6]);
		$this->pdf->Cell($this->pdf->widthB[6],4, $title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[7],4,$totaalBtxt, 0,1, "R");
		$this->pdf->ln();
		*/
	//}

	function printTotaal($title, $totaalA, $totaalB, $procent, $grandtotaal = false)
	{

		if($grandtotaal == true)
			$type = "grandtotaal";
		else
			$type = "totaal";

		$this->pdf->ln();

		$this->printCol(5,$title,"tekst");
		if($totaalA <>0)
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(7,$this->formatGetalKoers($totaalA,$this->pdf->rapport_OIS_decimaal),$type);
		}
		if($totaalB <>0)
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(8,$this->formatGetalKoers($totaalB,$this->pdf->rapport_OIS_decimaal),$type);
		}

		if(!empty($procent))
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(10,$procent."%",$type);
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();


		return $totaalA;
	}

	function printKop($title, $procent, $type="default" ,$toonNul=true)
	{
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);

		$procenttxt = $this->formatGetal($procent,0)." %";
		if ($toonNul == false && $procenttxt == '0 %')
		  $procenttxt ='';

		if($this->pdf->rapport_OIS_onderverdelingAandeel)
		{
			if($procent <> 0)
				$procenttxt = $this->formatGetal($procent,0)." %";
			else
				$procenttxt = "";
		}

		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell($this->pdf->widthB[0],4, $procenttxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[1],4, $title, 0,1, "L");
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
		$this->pdf->widthB = array(15,10,80,20,20,20,15,20,25,25,15,15);
		$this->pdf->alignB = array('R','L','L','R','R','R','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(15,30,60,20,20,20,15,20,25,25,15,15);
		$this->pdf->alignA = array('R','L','L','R','R','R','L','R','R','R','R','R');

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

		$query = "SELECT Beleggingscategorien.Omschrijving, ". //Beleggingssectoren.Omschrijving AS secOmschrijving , 
		//" TijdelijkeRapportage.beleggingssector, ".
		" TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) AS subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
		" FROM (TijdelijkeRapportage, Valutas) ".
		" LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
//		" LEFT JOIN Beleggingssectoren on (TijdelijkeRapportage.beleggingssector = Beleggingssectoren.Beleggingssector) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.valuta = Valutas.Valuta AND ".
		" TijdelijkeRapportage.type = 'fondsen' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie  ".
		" ORDER BY Beleggingscategorien.Afdrukvolgorde asc";// Beleggingssectoren.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);	

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			// print totaal op hele categorie.

			if($lastCategorie2 <> $categorien[Omschrijving] && !empty($lastCategorie2) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal);

				$pvtTxt = $this->formatGetal($percentageVanTotaal_totaal,1);
				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalactueel, $resultaatTotaal_totaal ,$pvtTxt);


				$resultaatTotaal_totaal = 0 ;
				$percentageVanTotaal_totaal = 0;
				$totaalbegin = 0;
				$totaalactueel = 0;
			}


			if($lastCategorie2 <> $categorien[Omschrijving])
			{
				// 123 .

				if($this->pdf->rapport_OIS_onderverdelingAandeel)
				{
					if(strtolower($categorien[Omschrijving]) == "aandelen")
						$percentageVanTotaal = 100;
					else
						$percentageVanTotaal = 0;
				}
				else
				{
					$percentageVanTotaal = $categorien[subtotaalactueel]/ ($totaalWaarde/100);
				}
				$this->printKop(vertaalTekst($categorien[Omschrijving],$this->pdf->rapport_taal),$percentageVanTotaal, "bi");
				$secTel =0;
			}


			// subkop (valuta)

			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
	//		" TijdelijkeRapportage.beleggingssector, ".
	//		" Beleggingssectoren.Omschrijving AS secOmschrijving, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaalValuta, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.actueleFonds, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.fonds, ".
			" TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage ".
//			" LEFT JOIN Beleggingssectoren on (TijdelijkeRapportage.beleggingssector = Beleggingssectoren.Beleggingssector) ".
			" WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien[beleggingscategorie]."' AND ".
			//" TijdelijkeRapportage.beleggingssector =  '".$categorien[beleggingssector]."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
		//	" ORDER BY Beleggingssectoren.Afdrukvolgorde asc, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
			" ORDER BY  TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);	
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();

			$lastCategorie = "xx";
			$secTel = 0;
			while($subdata = $DB2->NextRecord())
			{
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
/*
				if($lastCategorie <> $subdata[secOmschrijving])
				{
					// selecteer sum van deze sector... en dan :

					$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS sectortotaal FROM TijdelijkeRapportage ".
							 " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
						   " TijdelijkeRapportage.beleggingscategorie =  '".$subdata[beleggingscategorie]."' AND ".
						   " TijdelijkeRapportage.beleggingssector =  '".$subdata[beleggingssector]."' AND ".
							 " TijdelijkeRapportage.type =  'fondsen' AND ".
							 " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
					debugSpecial($q,__FILE__,__LINE__);	
					$DB3 = new DB();
					$DB3->SQL($q);
					$DB3->Query();
					$subtotaal = $DB3->nextRecord();
					$subtotaal = $subtotaal['sectortotaal'];


					if($this->pdf->rapport_OIS_onderverdelingAandeel)
					{
						if(strtolower($categorien[Omschrijving]) == "aandelen")
						{
							
							$percentageVanTotaal = $subtotaal/ ($categorien[subtotaalactueel]/100);
							$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,0)." %";
						}
						else
						{
							$percentageVanTotaaltxt = "";
						}
					}
					else
					{
						$percentageVanTotaal 			= $subtotaal/ ($categorien[subtotaalactueel]/100);
						$percentageVanTotaaltxt 	= $this->formatGetal($percentageVanTotaal,0)." %";
					}

					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
//sec					$this->pdf->Cell($this->pdf->widthB[0],4, $percentageVanTotaaltxt, 0,0, "R");
//sec					$this->pdf->Cell($this->pdf->widthB[1],4, $subdata[secOmschrijving], 0,0, "L");
					$this->pdf->SetX($this->pdf->marge);
				}
*/
				// bereken resultaat
				$fondsResultaat = ($subdata[actuelePortefeuilleWaardeInValuta] - $subdata[historischeWaardeTotaal]) * $subdata[actueleValuta];
				//$fondsResultaatprocent = ($fondsResultaat / $subdata[historischeWaardeTotaal]) * 100;
				$valutaResultaat = $subdata[actuelePortefeuilleWaardeEuro] - $subdata[historischeWaardeTotaalValuta] - $fondsResultaat;
				//$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));

				//$resultaatTotaal = $fondsResultaat + $valutaResultaat;
				$resultaatTotaal = ($subdata[actuelePortefeuilleWaardeEuro] - $subdata[historischeWaardeTotaalValuta]);

				if ($subdata[actuelePortefeuilleWaardeEuro] < 0) 
				  $procentResultaat = ( ( -1 * ($subdata[actuelePortefeuilleWaardeEuro] - $subdata[historischeWaardeTotaalValuta])) /
														 ($subdata[historischeWaardeTotaalValuta] /100));
				else
				  $procentResultaat = (($subdata[actuelePortefeuilleWaardeEuro] - $subdata[historischeWaardeTotaalValuta]) /
														 ($subdata[historischeWaardeTotaalValuta] /100));


				// selecteer risico % bij fonds
				$dbr = new DB();
				$select = 	" SELECT BeleggingscategoriePerFonds.RisicoPercentageFonds ".
									" FROM Portefeuilles,BeleggingscategoriePerFonds ".
									" WHERE Portefeuilles.Portefeuille = '".$this->portefeuille."' AND ".
									" Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder AND ".
									" BeleggingscategoriePerFonds.Fonds = '".$subdata[fonds]."' LIMIT 1 ";
				$dbr->SQL($select);
				$dbr->Query();
				$risico = $dbr->nextRecord();

				$percentage = $risico[RisicoPercentageFonds];

				$risicoBedrag = (ABS($subdata[actuelePortefeuilleWaardeEuro]) / 100) * $percentage;

				$percentageVanTotaal = $subdata[actuelePortefeuilleWaardeEuro] / ($totaalWaarde/100);
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1)."%";

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("",
												"",
												$subdata[fondsOmschrijving],
												$this->formatGetal($subdata[totaalAantal],0),
												$this->formatGetal($subdata[historischeWaarde],2),
												$this->formatGetal($subdata[actueleFonds],2),
												$subdata[valuta],
												$this->formatGetalKoers($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_OIS_decimaal),
												$this->formatGetalKoers($resultaatTotaal,2),
												$this->formatGetal($procentResultaat,1)."%",
												$percentageVanTotaaltxt,
												$this->formatGetal($risico['RisicoPercentageFonds'],0)."%"));

				$percentageVanTotaal_totaal += $percentageVanTotaal;

				$resultaatTotaal_totaal += $resultaatTotaal;
				$resultaatTotaal_grand_totaal += $resultaatTotaal;

				$risicoTotaal += $risicoBedrag;

				$valutaWaarden[$categorien[valuta]] = $subdata[actueleValuta];
				$lastCategorie = $subdata[secOmschrijving];
			}

			// print categorie footers
			//$this->printSubTotaal("Subtotaal:", $categorien[subtotaalbegin], $categorien[subtotaalactueel]);

			// totaal op categorie tellen
			$totaalbegin += $categorien[subtotaalbegin];
			$totaalactueel += $categorien[subtotaalactueel];
			$lastCategorie2 = $categorien[Omschrijving];
		}

		// totaal voor de laatste categorie
		//$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$percentageVanTotaal_totaal);

		$pvtTxt = $this->formatGetal($percentageVanTotaal_totaal,1);
		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalactueel, $resultaatTotaal_totaal, $pvtTxt);


		$resultaatTotaal_totaal = 0 ;
		$percentageVanTotaal_totaal = 0;

		$percentageVanTotaal_totaal = 0;

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.RenteBerekenen ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);	
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{

			$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS rentetotaal FROM TijdelijkeRapportage ".
					" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($q,__FILE__,__LINE__);	
			$DB3 = new DB();
			$DB3->SQL($q);
			$DB3->Query();
			$subtotaal = $DB3->nextRecord();
			$subtotaal = $subtotaal['rentetotaal'];

			if($this->pdf->rapport_OIS_onderverdelingAandeel)
			{
				$percentageVanTotaal = 0;
				$percentageVanTotaaltxt = "";
			}
			else
			{
				$percentageVanTotaal = $subtotaal/ ($totaalWaarde/100);
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,0)." %";
			}

			//$percentageVanTotaal = $categorien[subtotaalactueel]/ ($totaalWaarde/100);
			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$percentageVanTotaal ,"bi");

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_OIS_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'].
					" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery,__FILE__,__LINE__);	
					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();
					while($subdata = $DB2->NextRecord())
					{
						$percentageVanTotaal = $subdata[actuelePortefeuilleWaardeEuro] / ($totaalWaarde/100);
						$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1);

						$subtotaalRenteInValuta += $subdata[actuelePortefeuilleWaardeEuro];
						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


						$this->pdf->row(array("","",$subdata[fondsOmschrijving],"","","","",
														$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_OIS_decimaal),
														"","",$percentageVanTotaaltxt));

						$percentageTotaal += $percentageVanTotaal;
					}

					// print subtotaal
					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien[subtotaalactueel];
				}
			}

			// totaal op rente
			$pTxt = $this->formatGetal($percentageTotaal,1);
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalRenteInValuta, "",$pTxt);
			$percentageTotaal	= 0;
		}

		// Liquiditeiten
		$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS liqtotaal FROM TijdelijkeRapportage ".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type = 'rekening'  AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($q,__FILE__,__LINE__);	
		$DB3 = new DB();
		$DB3->SQL($q);
		$DB3->Query();
		$subtotaal = $DB3->nextRecord();
		$subtotaal = $subtotaal['liqtotaal'];

		if($this->pdf->rapport_OIS_onderverdelingAandeel)
		{
			$percentageVanTotaal = 0;
			$percentageVanTotaaltxt = "";
		}
		else
		{
			$percentageVanTotaal = $subtotaal/ ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,0)." %";
		}

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
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

//		$totaalLiquiditeitenInValuta = 0;

		if($DB1->records() > 0)
		{
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),$percentageVanTotaal,"bi");

			while($data = $DB1->NextRecord())
			{
				$percentageVanTotaal = $data[actuelePortefeuilleWaardeEuro] / ($totaalWaarde/100);
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1);

				if($this->pdf->rapport_OIS_liquiditeiten_omschr)
					$this->pdf->rapport_liquiditeiten_omschr = $this->pdf->rapport_OIS_liquiditeiten_omschr;

				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data[rekening],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data[fondsOmschrijving],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data[valuta],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("",
												"",
												$omschrijving,
												"",
												"",
												"",
												"",
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_OIS_decimaal),
												"","",$percentageVanTotaaltxt."%"));

				$percentageTotaal += $percentageVanTotaal;
			}
			// totaal liquiditeiten
			$pTxt = $this->formatGetal($percentageTotaal,1);

	  	$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalLiquiditeitenEuro,"",$pTxt);


			$percentageTotaal =0;
		}

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}
		$actueleWaardePortefeuille = $totaalWaarde;
		// print grandtotaal

		$totaalTxt = $this->formatGetal(100,1);

		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), $actueleWaardePortefeuille, $resultaatTotaal_grand_totaal,$totaalTxt, true);

		$this->pdf->ln();

		
		//
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
			$this->printKop(vertaalTekst("Lopende orders",$this->pdf->rapport_taal),'',"bi",false);
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
			    
			  
			  $this->pdf->Row(array('',
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

    //
    
		if($this->pdf->rapport_OIS_valutaoverzicht == 1)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->rapport_valutaoverzicht_rev = true;   // koersen in eurowaarde
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_OIS_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}
		



		if($this->pdf->rapport_OIS_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}
		
		if($this->pdf->rapport_OIS_risico == 1)
	  	$this->pdf->printRisico($this->portefeuille, $risicoTotaal, $actueleWaardePortefeuille);


		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 && ($this->pdf->rapport_layout == 11||$this->pdf->rapport_layout == 17))
		{
		  if(!$this->pdf->rapport_OIS_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
		
		
	}
}
?>