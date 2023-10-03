<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportGRAFIEK_L102
{
	function RapportGRAFIEK_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    $this->pdf->rapport_type='GRAFIEK';
    $this->pdf->rapport_titel='Toelichting';
    $this->portefeuille=$portefeuille;
	}

	function writeRapport()
	{
    $this->pdf->AddPage();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    
    
    $velden=array();
    $checkVelden=array('Resultaten','Beheer','Voorstellen','Bewaken','Markten','Scenario');
    $query = "desc CRM_naw";
    $this->DB = new DB();
    $this->DB->SQL($query);
    $this->DB->query();
    while($data=$this->DB->nextRecord('num'))
      $velden[]=$data[0];
    $extraVeld='';
    foreach($checkVelden as $check)
      if(in_array($check,$velden))
        $extraVeld.=','.$check;
    
    $query = "SELECT verzendAanhef $extraVeld FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
    $this->DB->SQL($query);
    $crmData = $this->DB->lookupRecord();

    $this->pdf->SetWidths(array(280));
    $this->pdf->SetAligns(array('L','L'));
    
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Ln();
  

    $crmObject=new NAW();
    foreach($checkVelden as $veld)
    {
      //echo $i." ".$checkVelden[$i].' ';
      if($crmData[$veld] <> '')
      {
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->row(array(vertaalTekst($crmObject->data['fields'][$veld]['description'],$this->pdf->rapport_taal)));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->row(array(vertaalTekst($crmData[$veld],$this->pdf->rapport_taal)));
        $this->pdf->Ln();
      }
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

  }
}
?>