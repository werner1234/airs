<?php

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONT_L123
{
	function RapportFRONT_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_titel = "Titel pagina";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);

    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();

	}


	function writeRapport()
	{
		global $__appvar;

		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');
    
    if(is_file($this->pdf->rapport_front_achtergrond))
    {
      $this->pdf->Image($this->pdf->rapport_front_achtergrond, 0, 0, $this->pdf->w,$this->pdf->h);
    }
		if(is_file($this->pdf->rapport_logo))
		{
	    $this->pdf->Image($this->pdf->rapport_logo, 14, 14, 86.1);
    }
	    //function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null)

      $kleurVerloop=array(array('kleurStart'=>$this->pdf->triodosGreen,'kleurStop'=>$this->pdf->triodosBlue,'xStart'=>0,'xWidth'=>10,'yStart'=>0,'yHeight'=>$this->pdf->h));
      foreach($kleurVerloop as $verloop)
			{
				$stappen=80;
				$yStap=$verloop['yHeight']/$stappen;
				for ($i=0;$i<$stappen;$i++)
				{
					$aandeel=$i/$stappen;
				  $kleur=array($verloop['kleurStart'][0]*(1-$aandeel)+$verloop['kleurStop'][0]*($aandeel),$verloop['kleurStart'][1]*(1-$aandeel)+$verloop['kleurStop'][1]*($aandeel),$verloop['kleurStart'][2]*(1-$aandeel)+$verloop['kleurStop'][2]*($aandeel));
          $this->pdf->rect($verloop['xStart'],$verloop['yStart']+$i*$yStap,$verloop['xStart']+$verloop['xWidth'],$verloop['yHeight']+$i*$yStap,'F',null,$kleur);
				}
			}

   	$this->pdf->widthA = array(6,230);
		$this->pdf->alignA = array('L','L','L');

    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->SetWidths($this->pdf->widthA);

    $this->pdf->SetY(58);
    $this->pdf->SetFont($this->pdf->rapport_font,'',20);
    $this->pdf->row(array(' ',vertaalTekst('Persoonlijk en vertrouwelijk',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',48);
    $this->pdf->SetY(88);
    $this->pdf->row(array('',vertaalTekst('Vermogensrapportage', $this->pdf->rapport_taal)));
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->SetY(150);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+6);
    $this->pdf->SetWidths(array(6,250));
    $this->pdf->row(array('',trim($this->pdf->portefeuilledata['Naam'])));
    $this->pdf->ln(4);
    $this->pdf->row(array('',trim($this->pdf->portefeuilledata['Naam1'])));
    $this->pdf->row(array('',));

		$this->pdf->SetY(170);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
    $beginDatum=date("d",$this->rapportageDatumVanafJul)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
      date("Y",$this->rapportageDatumVanafJul);
    $eindDatum=date("d",$this->rapportageDatumJul)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
      date("Y",$this->rapportageDatumJul);
		$rapportagePeriode = $beginDatum.' '.vertaalTekst(' - ',$this->pdf->rapport_taal).' '.$eindDatum;	                     ;

    $this->pdf->SetWidths(array(6,50,120));
    $this->pdf->row(array('',vertaalTekst('Rapportagedatum',$this->pdf->rapport_taal).':',date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    $this->pdf->ln();
    $this->pdf->row(array('',vertaalTekst('Waarderingsdatum',$this->pdf->rapport_taal).":",$eindDatum));
    $this->pdf->ln();
    $this->pdf->row(array('',vertaalTekst('Rapportageperiode',$this->pdf->rapport_taal).":",$rapportagePeriode));



	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

    $this->pdf->rapport_type = "INHOUD";
	  $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

//    $poly=array(145,30,
//			          268,30,
//			          268,95,
//                263,100,
//			          145,100);
//    $this->pdf->Polygon($poly,'F',null,$this->pdf->rapport_lichtgrijs);
    $this->pdf->SetFillColor($this->pdf->rapport_lichtgrijs[0],$this->pdf->rapport_lichtgrijs[1],$this->pdf->rapport_lichtgrijs[2]);
    $this->pdf->Rect(145, 30, 125, 70 , 'F');
    $this->pdf->SetWidths(array(140,115));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->sety(31);

    if( $this->pdf->rapport_taal == 2 ) {

      $this->pdf->Row(array('',"Dear client,

We are pleased to send you this investment report for your portfolio ".$this->portefeuille.".

The reporting currency of this portfolio is the ".$this->pdf->portefeuilledata['RapportageValuta'].".

In this report you will find information about:

• The size and asset allocation of your assets;
• The investment results that have been achieved.

If you have questions about this report, please contact your private banker.

Kind regards,

".$this->pdf->portefeuilledata['VermogensbeheerderNaam']));
    } elseif( $this->pdf->rapport_taal == 3 ) {

    $this->pdf->Row(array('',"Chère Madame, cher Monsieur,

Nous avond le plaisir de vous adresser pa la présente l'évaluation de votre portefeuille portant le numéro ".$this->portefeuille.".

La devise de référence de ce portefeuille est ".$this->pdf->portefeuilledata['RapportageValuta'].".

Dans ce rapport, vouw trouverez:

• Le volume et l'allocations de vos actifs;
• Le résultat des placements réalisés.

Nous vous remercions de votre confiance et vous prions d'agréer, chère Madame, cher Monsieur, l'expression de notre respectueuse considération.

".$this->pdf->portefeuilledata['VermogensbeheerderNaam']));
  } else {
      $this->pdf->Rect(145, 30, 125, 110 , 'F');
    $this->pdf->Row(array('',"Geachte relatie,

Als klant van Triodos Bank Private Banking heeft u gekozen voor de combinatie van marktconform financieel rendement en het bevorderen van duurzame ontwikkeling.

We zijn ervan overtuigd dat financieel en maatschappelijk rendement elkaar niet uitsluitenen, maar juist versterken.

Om inzicht te houden in de waardeontwikkleing van uw vermogen ontvangt u deze vermogensrapportage. Naast de financiële rendementen van de afgelopen periode vindt u een toelichting op het beleggingsklimaat. Daarbij vindt u een overzicht van de mutaties in uw portefeuille plus de inkomsten en kosten. Bij de berekening is ervan uitgegaan dat uw vermogen de volledige periode bij Triodos Bank Private Banking in beheer is geweest.

Heeft u na het lezen van deze rapportage nog vragen? Neemt u dan contact op met uw relatiemanager. Dat kan telefonisch of per mail. Natuurlijk kunt u ook altijd bellen met 030 693 6505 of mailen naar private.banking@triodos.nl. We helpen u graag.

Met vriendelijke groet,
Triodos Bank NV
"));

      $handtekening=base64_decode('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');
      $this->pdf->MemImage($handtekening, 147, 113, 40, 23);
    }
	}
}
?>
