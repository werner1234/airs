<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_102/RapportGRAFIEK_L102.php");

class RapportOIS_L102
{
    function RapportOIS_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
    {
      $this->pdf = &$pdf;
      $this->pdf->rapport_type='OIS';
      $this->pdf->rapport_titel='Asset allocation in the large';
    }

    function writeRapport()
    {
      $this->pdf->AddPage();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    }
}
?>
