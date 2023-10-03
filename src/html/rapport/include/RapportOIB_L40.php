<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.4 $

$Log: RapportOIB_L40.php,v $
Revision 1.4  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.3  2013/07/15 17:06:38  rvv
*** empty log message ***

Revision 1.2  2012/09/02 08:56:33  rvv
*** empty log message ***

Revision 1.1  2012/09/01 14:27:48  rvv
*** empty log message ***

Revision 1.5  2010/06/09 16:39:37  rvv
*** empty log message ***

Revision 1.4  2010/05/30 12:46:25  rvv
*** empty log message ***

Revision 1.3  2010/05/19 16:49:42  rvv
*** empty log message ***

Revision 1.2  2010/05/19 16:33:07  rvv
*** empty log message ***

Revision 1.1  2010/05/19 16:24:10  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L40
{
	function RapportOIB_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
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


	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	function printTotaal($title, $totaalA, $procent, $grandtotaal)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2];

		if(!empty($totaalA))
		{
			if($this->pdf->rapport_OIB_specificatie == 1)
				$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetalKoers($totaalA,$this->pdf->rapport_OIB_decimaal);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,1);

		$this->pdf->SetX($actueel);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[3],4,$title, 0,0, "R");
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalprtxt, 0,1, "R");

		if($grandtotaal)
		{
		  if($this->pdf->rapport_layout == 14)
		  {
      $actueel  = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1];
		  }

			$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[3],$this->pdf->GetY()+1);
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln(2);

		return $totaalA;
	}

	function printKop($title, $type="default")
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

		if(($this->pdf->GetY() + 12) >= $this->pdf->pagebreak) {
			$this->pdf->AddPage();
			$this->pdf->ln();
		}
		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$y = $this->pdf->getY();


		if($this->pdf->rapport_layout == 14)
		{
		  $this->pdf->MultiCell($this->pdf->widthB[0]+$this->pdf->widthB[1],4, $title, 0, "L");
		}
		else
		{
		  $this->pdf->MultiCell($this->pdf->widthB[0],4, $title, 0, "L");
		}

	  $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
		$this->pdf->SetY($y);
	}

	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		// voor data
		$this->pdf->widthB = array(40,35,25,25,25,15,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(40,35,25,25,25,15,115);
		$this->pdf->alignA = array('L','L','R','R','R','R','R');

		if($this->pdf->rapport_layout == 8)
		{
		  $this->pdf->widthA = array(40,35,25,25,25,15,116);
		  $this->pdf->widthB = array(40,35,25,25,25,15,116);
		}

		$this->pdf->AddPage();

		$query="UPDATE TijdelijkeRapportage SET 	Beleggingscategorie='Rente' WHERE TijdelijkeRapportage.type = 'rente' AND rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
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

		$query = "SELECT 
BeleggingscategorieOmschrijving as Omschrijving,
ValutaOmschrijving, 
valuta, 
TijdelijkeRapportage.actueleValuta,
beleggingscategorie,
SUM(actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, 
SUM(actuelePortefeuilleWaardeEuro) AS subtotaalactueel, 
Hoofdcategorie, 
HoofdcategorieOmschrijving 
FROM TijdelijkeRapportage
WHERE  portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']." 
GROUP BY beleggingscategorie, valuta ORDER BY HoofdcategorieVolgorde, BeleggingscategorieVolgorde asc, ValutaVolgorde asc";
   //TijdelijkeRapportage.type <> 'rente' AND 
 		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
    $DB2 = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
		  /*
       $query="SELECT SUM(actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, 
       SUM(actuelePortefeuilleWaardeEuro) AS subtotaalactueel 
       FROM TijdelijkeRapportage
			 WHERE
       TijdelijkeRapportage.type = 'rente' AND
       beleggingscategorie = '".$categorien['beleggingscategorie']."' AND
       valuta = '".$categorien['valuta']."' AND
   	   TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
			 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
		   $__appvar['TijdelijkeRapportageMaakUniek'];
       $DB2->SQL($query);
       $rente=$DB2->lookupRecord(); 
       $categorien['subtotaalactueel']=$categorien['subtotaalactueel']+$rente['subtotaalactueel'];
       $categorien['subtotaalactueelvaluta']=$categorien['subtotaalactueelvaluta']+$rente['subtotaalactueelvaluta'];
*/
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien[Omschrijving] && !empty($lastCategorie) )
			{
				$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
				$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $percentageVanTotaal);
				$totaalbegin = 0;
				$totaalactueel = 0;
				// voor Pie
				$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
				$grafiekCategorien[$lastCat]=array($percentageVanTotaal,$lastCategorie); //toevoeging voor kleuren.
			}


			if($lastHoofdCategorie <> $categorien['HoofdcategorieOmschrijving'] && !empty($lastHoofdCategorie) )
      {
        $hoofdPercentageVanTotaal = $hoofdTotalen[$lastHoofdCategorie] / ($totaalWaarde/100);
	      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      	$this->pdf->Line(140,$this->pdf->getY(),173,$this->pdf->getY());
	      $this->pdf->Cell($this->pdf->widths[0],4,$lastHoofdCategorie);
	      $this->pdf->setX($this->pdf->marge);
	      $this->pdf->Row(array('','','','',$this->formatGetal($hoofdTotalen[$lastHoofdCategorie],0),$this->formatGetal($hoofdPercentageVanTotaal,1)));
        $this->pdf->Ln();
      }
      
      if($lastHoofdCategorie != $categorien['HoofdcategorieOmschrijving'])
			{ 
				$categorieTekst = $categorien['HoofdcategorieOmschrijving'];
				$this->printKop(vertaalTekst($categorieTekst,$this->pdf->rapport_taal), "b");
        $this->pdf->Ln();
        $lastHoofdCategorie = $categorien['HoofdcategorieOmschrijving'];
			}

			if($lastCategorie != $categorien[Omschrijving])
			{
				$categorieTekst = $categorien[Omschrijving];
				$this->printKop(vertaalTekst($categorieTekst,$this->pdf->rapport_taal), $this->pdf->rapport_kop2_fontstyle);
			}


			$percentageVanTotaal = $categorien[subtotaalactueel] / ($totaalWaarde/100);

			// print valutaomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
			$this->pdf->setX($this->pdf->marge);
 	    $this->pdf->Cell($this->pdf->widthB[0],4,"");
			$this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst($categorien[ValutaOmschrijving],$this->pdf->rapport_taal));
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			// print categorie footers

			if($this->pdf->rapport_OIB_specificatie == 1)
			{
					$this->pdf->row(array("",
											"",
											$this->formatGetal($categorien[subtotaalactueelvaluta],$this->pdf->rapport_OIB_decimaal),
											$this->formatGetalKoers($categorien[subtotaalactueel],$this->pdf->rapport_OIB_decimaal),
											"",
											$this->formatGetal($percentageVanTotaal,1).""));
			}
			// totaal op categorie tellen
			$totaalinvaluta += $categorien[subtotaalactueelvaluta];
			$totaalactueel += $categorien[subtotaalactueel];
			$hoofdTotalen[$categorien['HoofdcategorieOmschrijving']] += $categorien[subtotaalactueel];

			$lastCategorie = $categorien['Omschrijving'];
			$lastCat       = $categorien['beleggingscategorie'];
			
		}

		// totaal voor de laatste categorie


		$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
		$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $percentageVanTotaal);
		// voor Pie
		$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
		$grafiekCategorien[$lastCat]=array($percentageVanTotaal,$lastCategorie);; //toevoeging voor kleuren.


    $hoofdPercentageVanTotaal = $hoofdTotalen[$lastHoofdCategorie] / ($totaalWaarde/100);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
   	$this->pdf->Line(140,$this->pdf->getY(),173,$this->pdf->getY());
    $this->pdf->Cell($this->pdf->widths[0],4,$lastHoofdCategorie);
    $this->pdf->setX($this->pdf->marge);
    $this->pdf->Row(array('','','','',$this->formatGetal($hoofdTotalen[$lastHoofdCategorie],0),$this->formatGetal($hoofdPercentageVanTotaal,1)));
    $this->pdf->Ln();

		// print grandtotaal
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3];
		$proc = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$extra =0;
		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[4],$this->pdf->GetY());
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY(),$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY());
		$this->pdf->setX($this->pdf->marge);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[0],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[2],4,"", 0,0, "R");
	  $this->pdf->Cell($this->pdf->widthB[3],4,"", 0,0, "L");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);


		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}
		$this->pdf->Cell($this->pdf->widthB[4],4,$this->formatGetalKoers($actueleWaardePortefeuille,$this->pdf->rapport_OIB_decimaal), 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$this->formatGetal(100,1), 0,1, "R");

		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[4],$this->pdf->GetY());
		$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[4],$this->pdf->GetY()+1);
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY(),$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY());
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY()+1,$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY()+1);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		if($this->pdf->rapport_OIB_rendement == 1)
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf, $this->pdf->rapport_OIB_rendementKort);
      
    getTypeGrafiekData($this,'Beleggingscategorie');
    foreach ($this->pdf->grafiekData['Beleggingscategorie']['grafiek'] as $omschrijving=>$waarde)
    {
      $grafiekData['OIB']['Omschrijving'][]=$omschrijving." (".$this->formatGetal($waarde,1)."%)";
      $grafiekData['OIB']['Percentage'][]=$waarde;
    }
    $diameter = 34;
$hoek = 30;
$dikte = 10;
$Xas= 75;
$yas= 70;
$onderY=80;
$xRechts=160;
    $this->pdf->set3dLabels($grafiekData['OIB']['Omschrijving'],$Xas+$xRechts,$yas,$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
    $this->pdf->Pie3D($grafiekData['OIB']['Percentage'],$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'],$Xas+$xRechts,$yas,$diameter,$hoek,$dikte,"Beleggingscategorie");



	}
}
?>