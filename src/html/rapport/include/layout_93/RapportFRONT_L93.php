<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/04 14:09:21 $
File Versie					: $Revision: 1.1 $

$Log: RapportFRONT_L93.php,v $


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L93
{
	function RapportFront_L93($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIS_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Titel pagina";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();
		$this->beeld=base64_decode('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KI4j763PmjdtR8cXoxHHyqRe67GjA+IYxG2ngXZ0EEdxHJuLW4++GI04iuPYe+uDItrRQRzFcYS1np0vRiOO4jim3rWerR0dxFEcv1HjWo8dHcRRHKfcemxrulxs7OggjuI4VRVrPXZ0EEdxfGFGkW892tFBHMXx9ffWMdd67OggjuL4rmBrPXZ0EEdxTCXIWo8vRiOO4phY8Ws9dnQQR3GcR7FrPVu/vABxFMdZlbfW09rRQRzFcZGxF3Pr0Y4O4iiOi763LmCt5+OL0S4XEUdxXFjOaz12dEAcV3Td5vihix0dEMe15bXW8/HFaJeLII5ZuLR5XC7a0QFxzMzKaz2+GA3imOt765XWeuzogDhmbn9fYUfH2EEc87fYWs/Jjg6IY1FmX+uxowPiWKQZ13rs6IA4lmyGtZ6dHR0QxwBSrvXY0QFxDPTeOsVaz8cXo10ugjjG8t5ajx0dEMewXlvr8csLQBzDe26tx44OiGMtpq71nPzyAhDHuvy01mNHB8SxUrfWjg6IIyO6u19eAOLIiP/9Fq6tHR0QR/6waVo7OiCOAOIIII7iCIijOALiKI6AOIojII7iCIijOAKII4A4AogjgDgCiCOAOAKII4A4AoijOALiKI6AOIojII7iCIijOAKII4A4AogjgDgCiCOAOAKII4A4AoijWQPiKI6AOIojII7iCIijOALiKI4A4gggjgDiCCCOAOIIII4A4gggjgDiKI6AOIojII7iCIijOALiKI6AOIojgDgCiCOAOAKII4A4AogjgDgCiCOAOIojII7iCIijOALiKI6AOIojgDgCiCOAOAKII4A4AogjgDgCiCOAOIojII7iCIijOALiKI6AOIojII7iCCCOAOIIII4A4gggjgDiCCCOAOIIII7iCIijOALiKI6AOIojII7iCCCOAOIIII4A4gggjgDiCCCOAOIIII7iCIijOALiKI6AOIojII7iCIijOAKII4A4AogjgDgCiCOAOAKII4A4AoijOALiKI6AOIojII7iCIijOALiKI4A4gggjgDiCCCOAOIIII4A4gggjgDiKI6AOIojII7iCIijOALiKI4A4gggjgDiCCCOAOIIII4A4gggjgDiKI6AOIojII7iCIijOALiKI6AOIojgDgCiCOAOAKII4A4AogjgDgCiCOAOIojII7iCIijOALiKI6AOIojgDgCiCOAOAKII4A4AogjgDgCiCOAOIojII7iCIijOALiKI6AOIojII7iCCCOAOIIII4A4gggjgDiCCCOAOIIII7iCIijOALiKI6AOIojII7iCCCOAOIIII4A4gggjgDiCCCOAOIIII7iCIijOALiKI6AOIojII7iCIijOAKII4A4AogjgDgCiCOAOAKII4A4AoijOALiKI6AOIojII7iCIijOALiKI4A4gggjgDiCCCOAOIIII4A4gggjgDiKI6AOIojII7iCIijOALiKI4A4gggjgDiCCCOAOIIII4A4gggjgDiKI6AOIojII7iCIijOALiKI6AOIojgDgCiCOAOAKII4A4AogjgDgCiCOAOIojII7iCIijOALiKI6AOIojgDgCiCOAOAKII4A4AogjgDgCiCOAOIojII7iCIijOALiKI6AOIojII7iCCCOAOIIII4A4gggjgDiCCCOAOIIII7iCIijOALiKI6AOIojII7iCCCOAOIIII4A4gggjgDiCCCOAOIIII5mDYijOALiKI6AOIojII7iCIijOAKII4A4AogjgDgCiCOAOAKII4A4AoijOALiKI6AOIojII7iCIijOALiKI4A4gggjgDiCCCOAOIIII4A4gggjgDiKI6AOIojII7iCIijOALiKI4A4gggjgDiCCCOAOIIII4A4gggjgDiKI6AOIojII7iCIijOALiKI6AOIojgDgCiCOAOAKII4A4AogjgDgCiCOAOIojII7iCIijOALiKI6AOIojgDgCiCOAOAKII4A4AogjgDgCiCOAOIojII7iCIijOALiKI6AOKaN48WwAXF81Bk2II7iCIijOALi+Gocj4YNiOOjxrABcRRHQBwnxfFs2IA4PmoNGxBHcQTEcVIct4YNVBfHe+/L1YA4PmgnxHFv2oA4+ooMII6T4ng1baC2OG57W+CAOD6Y0Ea7PIA4jtmZNlBZHC9T4miXB6gtjt2kOPq4GqgsjrdJcfRxNVBZHJtJcTwYN1BXHM+9T2QAcXzQTopjvzFvoKo47qbF8WbeQFVxnNZGDwMH6opjNzGObjoCVcXxODGOnloGVBXH89Q42nQEaopjOzWOnj0B1BTHfjLLPEA9ceymx9H7aqCeODbT43gycaCaOLbT49hfjByoJI6bJ9ro4RNANXE8PhPHrY9kgErieHgmjn7NFlBJHJ96V+3SEagljsfn4ujSEagjjqfepSMgjp91/bN8YA1UEMf26Tj6LYRA/Dg+f+HY9ztvrIHocXzhwtEbayB8HI/9S44GD0SO42b7Why3vmINRI7jqe/VERDHTw79y04+lAGixvGNNv6qo1+2BYSM4+atNnpnDcSM4/7Uv2l7M34gWhxv2/59nkEBxIrj/t4n4cYjECiOm2bbp+LiEYgSx+OuT2jnMRRAhDimTeOHVh6BwuO4T/iGWh6BIHHsDv1cTh5FAZQZx32z6+e0PfvkGigtjpvjqZ/f6aqPQDlx3F/v/VL0ESgjjpfm1C9rd/a1QiDrOG6Oh22/irbpPNIMyDGOm9v51K/qdLja8AFyiuN+9TD+UcimcxcSWD+O3fW+63Nzaptr5/GPwDpxvBzPbZ+1tj3/qqRMAovGsS9F668NEEdxBMRRHAFxFEdAHMUREEdxBMRRHAFxFEdAHMUREEdxBBBHAHEEEEcAcQQQR3EExFEcAXEUR0AcxREQR3EECo9jU4qjvzZgwTgCII4A4gggjgDiCCCOAOIIII4A4gggjgDiCCCOAOIIUJP/CDAAm30ohoHlyfwAAAAASUVORK5CYII=');

	}

	


	function writeRapport()
	{
		global $__appvar;


		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');

    $this->pdf->memImage($this->beeld,10,80,150);
    //$tmp=base64_encode( file_get_contents( $__appvar["basedir"]."/html/rapport/logo/beaufort-beeld.png"));
    //echo $tmp;exit;

		if(is_file($this->pdf->rapport_logo))
		{
			$logoXsize=40;
		  $logopos=($this->pdf->w - $this->pdf->marge)-($logoXsize);
	    $this->pdf->Image($this->pdf->rapport_logo, $logopos, $this->pdf->marge, $logoXsize);
		}

   	$this->pdf->widthA = array(170,130);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize


    $this->pdf->SetWidths($this->pdf->widthA);

    $this->pdf->SetY(60);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    $this->pdf->row(array(' ',vertaalTekst('PERSOONLIJK EN VERTROUWELIJK',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
    $this->pdf->ln(1);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Adres']));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Woonplaats']));
    
    $this->pdf->SetY(120);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',20);
    $this->pdf->row(array('','RAPPORTAGE'));
		$this->pdf->SetY(165);

		$rapportagePeriode = date("j",$this->rapportageDatumVanafJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumVanafJul).
		                     ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                     date("j",$this->rapportageDatumJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumJul);

    $this->pdf->SetWidths(array(170,140,5,120));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    $this->pdf->row(array('',date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    $this->pdf->ln(2);
    $this->pdf->row(array('',$rapportagePeriode));
		$this->pdf->ln(2);
		$this->pdf->row(array(' ',vertaalTekst($this->pdf->portefeuilledata['Risicoklasse'],$this->pdf->rapport_taal)));
    $this->pdf->ln(2);
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 

		$this->pdf->SetY(133);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		
		$this->pdf->ln(2);
		$this->pdf->row(array('',''));


	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;
   
    $this->pdf->rapport_type = "INHOUD";
	  $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	}
}
?>
