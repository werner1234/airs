<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportDOORKIJKVR.php");

class RapportDOORKIJKVR_L102
{
    function RapportDOORKIJKVR_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
    {
      $this->pdf = &$pdf;
      $this->doorkijkvr = new RapportDOORKIJKVR($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
      $this->pdf->rapport_type='DOORKIJKVR';
      $this->pdf->rapport_titel='Rating en looptijden';
    }

    function writeRapport()
    {
      $this->doorkijkvr->writeRapport();
    }
}
?>
