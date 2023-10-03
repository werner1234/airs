<?php

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONT_L125
{
	function RapportFRONT_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->DB = new DB();

	}


	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT
		            Rekeningen.IBANnr
		          FROM
		            Rekeningen
		          WHERE
		            Rekeningen.Portefeuille = '".$this->portefeuille."' AND Valuta='EUR' AND Inactief=0 AND Memoriaal=0 ORDER BY Rekeningen.id desc limit 1";
		$this->DB->SQL($query);
		$this->DB->Query();
		$rekening = $this->DB->nextRecord();

   //background
    $this->pdf->AddPage();

    $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
    
    if(is_file($this->pdf->rapport_logo))
    {
      $xSize=75;
      //$logopos=($this->pdf->w/2)-($xSize/2);
      //$logopos=($this->pdf->w)-($xSize)-$this->pdf->marge;
      $this->pdf->Image($this->pdf->rapport_logo, 12, 12, $xSize);
    }
    $this->pdf->rect(0,75,$this->pdf->w,20,'F', null, array($this->pdf->kopGrijs[0],$this->pdf->kopGrijs[1],$this->pdf->kopGrijs[2]));
    $this->pdf->SetY(82.5);
    //w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link=''
    $this->pdf->SetFont($this->pdf->rapport_font,'',24);
    $this->pdf->Cell($this->pdf->w,5,vertaalTekst('Uw vermogensrapportage',$this->pdf->rapport_taal),0,0,'C');
    $this->pdf->SetY(100);
    $rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
      date("Y",$this->rapportageDatumVanafJul).
      ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
      date("d",$this->rapportageDatumJul)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
      date("Y",$this->rapportageDatumJul);
    $this->pdf->SetFont($this->pdf->rapport_font,'',10);
    $this->pdf->setTextColor($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2]);
    $this->pdf->Cell($this->pdf->w,5,vertaalTekst('Verslagperiode',$this->pdf->rapport_taal) .' '. $rapportagePeriode,0,0,'C');
    $this->pdf->setTextColor(0);
    
    $this->pdf->SetY(100);
   	$this->pdf->widthA = array(10,40,10,150);
		$this->pdf->alignA = array('L','L','L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
    $this->pdf->SetTextColor(0,0,0);

    $width=array(30,140,30,40);
		$this->pdf->SetY(130);
    $this->pdf->setTextColor($this->pdf->textBlauw[0],$this->pdf->textBlauw[1],$this->pdf->textBlauw[2]);
    $this->pdf->ln();
    $this->pdf->Cell($width[0],5,'',0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
    $this->pdf->Cell($width[1],5,($this->pdf->portefeuilledata['Naam']<>''?$this->pdf->portefeuilledata['Naam']:$this->pdf->portefeuilledata['Portefeuille']),0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setTextColor($this->pdf->textGroen[0],$this->pdf->textGroen[1],$this->pdf->textGroen[2]);
    $this->pdf->Cell($width[2]-6,5,vertaalTekst('Relatienummer:',$this->pdf->rapport_taal),0,0,'L');
    $this->pdf->setTextColor(0);
    $this->pdf->Cell($width[3],5,$this->pdf->portefeuilledata['Portefeuille'],0,0,'L');
    $this->pdf->ln(6);
    $this->pdf->Cell($width[0],5,'',0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
    $this->pdf->Cell($width[1],5,($this->pdf->portefeuilledata['Naam1']<>''?$this->pdf->portefeuilledata['Naam1']:''),0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setTextColor($this->pdf->textGroen[0],$this->pdf->textGroen[1],$this->pdf->textGroen[2]);
    $this->pdf->Cell($width[2]-20,5,vertaalTekst('IBAN:',$this->pdf->rapport_taal),0,0,'L');
    $this->pdf->setTextColor(0);
    $this->pdf->Cell($width[3],5,$rekening['IBANnr'],0,0,'L');
  //  listarray($this->pdf->portefeuilledata['Naam']);
		
		//$this->pdf->row(array('',vertaalTekst('Client',$this->pdf->rapport_taal),':',$portefeuilledata['Portefeuille']));
    $this->pdf->ln(6);

		//$this->pdf->row(array('',vertaalTekst('Datum rapport',$this->pdf->rapport_taal),':',  $rapDatum=date("j")." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(6);
	  $this->pdf->frontPage = true;


	}
}
?>