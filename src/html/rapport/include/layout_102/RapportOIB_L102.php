<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportOIB.php");

class RapportOIB_L102
{
    function RapportOIB_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
    {
      $this->pdf = &$pdf;
      $this->oib = new RapportOIB($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
      $this->pdf->rapport_type='OIB';
      $this->pdf->subtitel = "in deviezen";
    }

    function writeRapport()
    {
      $this->oib->verdeling='hoofdcategorie';
      $this->oib->writeRapport();
      $this->pdf->subtitel='';
    }
}
?>
