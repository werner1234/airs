<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/06/22 16:15:05 $
File Versie					: $Revision: 1.1 $

$Log: RapportFRONT_L19.php,v $
Revision 1.1  2016/06/22 16:15:05  rvv
*** empty log message ***

Revision 1.3  2016/01/27 17:08:53  rvv
*** empty log message ***

Revision 1.2  2016/01/21 06:43:39  rvv
*** empty log message ***

Revision 1.1  2015/11/29 13:13:22  rvv
*** empty log message ***

Revision 1.2  2015/09/13 11:32:29  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:04  rvv
*** empty log message ***




*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L19
{
	function RapportFront_L19($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
        $factor=0.15;
		    $xSize=380*$factor;
		    $ySize=109*$factor;
        $logopos=297-$xSize-$this->pdf->marge;//(297/2)-($xSize/2);
	      $this->pdf->Image($this->pdf->rapport_logo, $logopos, $this->pdf->marge, $xSize, $ySize);
		}

   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');
		$this->pdf->SetFillColor(5,154,37);
		$this->pdf->Rect(70,70,140, 50, 'F');
		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+10);
		$this->pdf->SetWidths(array(70-$this->pdf->marge,140));
		$this->pdf->SetAligns(array('L','C','R','R','R'));
		$this->pdf->setY(85);
		$this->pdf->SetTextColor(255,255,255);
		$this->pdf->row(array('',vertaalTekst('Portefeuille rapportage',$this->pdf->rapport_taal)));
		$this->pdf->ln(6);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+8);
		$this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetAligns(array('L','R','R','R','R'));
		$this->pdf->setY(113);
		$this->pdf->SetWidths(array(70-$this->pdf->marge-2,140));
		$this->pdf->row(array(' ',date("d",$this->rapportageDatumJul)." ".vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".date("Y",$this->rapportageDatumJul)));
		$this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;


	}
}
?>
