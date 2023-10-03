<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/01/06 16:20:40 $
File Versie					: $Revision: 1.3 $

$Log: RapportFRONTC_L53.php,v $
Revision 1.3  2016/01/06 16:20:40  rvv
*** empty log message ***

Revision 1.2  2014/06/20 09:26:34  rvv
*** empty log message ***

Revision 1.1  2014/06/18 15:48:59  rvv
*** empty log message ***

Revision 1.3  2014/06/04 16:13:28  rvv
*** empty log message ***

Revision 1.2  2014/04/30 16:03:17  rvv
*** empty log message ***

Revision 1.1  2014/04/26 16:43:08  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFrontC_L53
{
	function RapportFrontC_L53($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

	}

	
	function writeRapport()
	{
		global $__appvar;
  	$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');

		if(is_file($this->pdf->rapport_logo))
		{
      $logoWidth=40;
			$this->pdf->Image($this->pdf->rapport_logo, 20, 20, $logoWidth);
		}

   	$this->pdf->widthA = array(12,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize

    $this->pdf->SetFont($this->pdf->rapport_font,'B',16);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetTextColor(127);
    $this->pdf->SetY(80);
    $this->pdf->row(array('','Vermogensrapportage '.$this->pdf->portefeuilledata['Naam']));
    $this->pdf->ln(10);
    $this->pdf->SetFont($this->pdf->rapport_font,'',14);
    
    $start=date("j",$this->rapportageDatumVanafJul)." ".
		      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		      date("Y",$this->rapportageDatumVanafJul);
    $stop=date("j",$this->rapportageDatumJul)." ".
		      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		      date("Y",$this->rapportageDatumJul);
    $this->pdf->row(array('','Periode: '.$start.' / '.$stop));
    $this->pdf->ln(10);
    $this->pdf->SetWidths(array(12,40,30));
    $this->pdf->row(array('','Portefeuille(s):'));
    $this->pdf->setY($this->pdf->GetY()-4);
    foreach($this->pdf->portefeuilles as $portefeuille)
    {
      $this->pdf->row(array('','',$portefeuille));
      $this->pdf->Ln(2);
    }
   
 		$this->pdf->SetFillColor($this->pdf->rapport_balkKleur[0],$this->pdf->rapport_balkKleur[1],$this->pdf->rapport_balkKleur[2]);
		$this->pdf->Rect(0, 210-5.7, 297, 5.7 , 'F');

	
	  $this->pdf->SetTextColor(127);
	  $this->pdf->frontPage = true;
  // 	  $this->pdf->rapport_type = "OIB";
	  $this->pdf->rapport_titel = "";//Inhoudsopgave
	  $this->pdf->addPage('L');
    $this->pdf->Rect(0, 210-5.7, 297, 5.7 , 'F');
		if(is_file($this->pdf->rapport_logo))
		{
      $logoWidth=40;
			$this->pdf->Image($this->pdf->rapport_logo, 20, 20, $logoWidth);
		}
    $this->pdf->SetFont($this->pdf->rapport_font,'',9);
    $disclaimer='Disclaimer
    
De gegevens uit deze vermogensrapportage zijn niet bedoeld voor fiscale doeleinden. Hiertoe dient het fiscale jaaroverzicht dat door de (depotbank van de) vermogensbeheerder wordt verstrekt.
De rapportage is met de nodige zorg samengesteld en beoogt een feitelijke weergave te geven van uw beleggingsportefeuille(s). Bij constatering van een onjuistheid of onvolledigheid in deze rapportage verzoeken wij u contact op te nemen met Capital Counsel.
De posities van de portefeuille worden gewaardeerd tegen de laatste bekende koersen op de datum van opmaak van de rapportage. Beleggingen zijn omgeven met risico. In het verleden behaalde rendementen vormen derhalve geen garantie voor toekomstige resultaten. U kunt geen rechten ontlenen aan de inhoud van deze rapportage.

De koersen van posities in de portefeuille kunnen afwijken van de koersen in de rapportages van de vermogensbeheerders doordat informatie van verschillende bronnen kan worden gebruikt. Het rendement van de portefeuilles kan hierdoor tevens afwijken.';
$this->pdf->SetXY(160,70);

$this->pdf->MultiCell(100,$this->pdf->rowHeight,$disclaimer,0,'L');
$this->pdf->SetTextColor(0);
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	}
}
?>
