<?php

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONT_L117
{
	function RapportFRONT_L117($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

		if(is_file($this->pdf->rapport_logo))
		{
	    $this->pdf->Image($this->pdf->rapport_logo, 10, 10, 80);
    }
	    //function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null)
	    $this->pdf->rect(0,55,180,10,'F',null,$this->pdf->rapport_donker);
      $this->pdf->rect(0,65,180,35,'F',null,$this->pdf->rapport_donkergroen);
      $this->pdf->rect(0,100,180,35,'F',null,$this->pdf->rapport_groen);
      $kleurVerloop=array(array('kleurStart'=>$this->pdf->rapport_donker,'kleurStop'=>array(59,109,112),'xStart'=>180,'xWidth'=>74,'yStart'=>55,'yHeight'=>10),
        array('kleurStart'=>$this->pdf->rapport_donkergroen,'kleurStop'=>array(63,111,117),'xStart'=>180,'xStop'=>74,'xWidth'=>74,'yStart'=>65,'yHeight'=>35),
        array('kleurStart'=>$this->pdf->rapport_groen,'kleurStop'=>array(128,172,173),'xStart'=>180,'xStop'=>74,'xWidth'=>74,'yStart'=>100,'yHeight'=>35));
      foreach($kleurVerloop as $verloop)
			{
				$stappen=40;
				$xStap=$verloop['xWidth']/$stappen;
				for ($i=0;$i<$stappen;$i++)
				{
					$aandeel=$i/$stappen;
				  $kleur=array($verloop['kleurStart'][0]*(1-$aandeel)+$verloop['kleurStop'][0]*($aandeel),$verloop['kleurStart'][1]*(1-$aandeel)+$verloop['kleurStop'][1]*($aandeel),$verloop['kleurStart'][2]*(1-$aandeel)+$verloop['kleurStop'][2]*($aandeel));
          $this->pdf->rect($verloop['xStart']+$i*$xStap,$verloop['yStart'],$xStap,$verloop['yHeight'],'F',null,$kleur);
				}
			}
      $poly=array(0,22,
				9,22,
				14,27,
				19,22,
				297,22,

				297,100,
				254,100,
				254,65,
				254-10,55,
				0,55
			  );
      $this->pdf->Polygon($poly,'F',null,array(121,131,139));


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
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetY(150);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+6);
    $this->pdf->SetWidths(array(6,250));
    $this->pdf->row(array('',trim($this->pdf->portefeuilledata['Naam'])));
    $this->pdf->ln(4);
    $this->pdf->row(array('',trim($this->pdf->portefeuilledata['Naam1'])));
    $this->pdf->row(array('',));

    $this->pdf->SetTextColor($this->pdf->rapport_groen[0],$this->pdf->rapport_groen[1],$this->pdf->rapport_groen[2]);
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

    $poly=array(145,30,
			          268,30,
			          268,95,
                263,100,
			          145,100);
    $this->pdf->Polygon($poly,'F',null,$this->pdf->rapport_lichtgrijs);
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

ABN AMRO Private Banking"));
    } elseif( $this->pdf->rapport_taal == 3 ) {

    $this->pdf->Row(array('',"Chère Madame, cher Monsieur,

Nous avons le plaisir de vous adresser par la présente l'évaluation de votre portefeuille portant le numéro ".$this->portefeuille.".

La devise de référence de ce portefeuille est ".$this->pdf->portefeuilledata['RapportageValuta'].".

Dans ce rapport, vous trouverez:

• Le volume et l'allocation de vos actifs;
• Le résultat des placements réalisés.

Nous vous remercions de votre confiance et vous prions d'agréer, chère Madame, cher Monsieur, l'expression de notre respectueuse considération.

ABN AMRO Private Banking"));
  } else {

    $this->pdf->Row(array('',"Geachte cliënt,
    
Hierbij bezorgen wij u de beleggingsrapportage van uw portefeuille ".$this->portefeuille.".

De rapportagevaluta van uw portefeuille is EUR.

In deze rapportage treft u informatie aan over:

• de omvang en verdeling van uw vermogen;
• het behaalde beleggingsresultaat.

Indien u vragen heeft, aarzel niet contact op te nemen. Wij zullen deze graag met u bespreken.

Met vriendelijke groeten,

ABN AMRO Private Banking"));
    }
	}
}
?>
