<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"] . "/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"] . "/html/rapport/include/layout_87/RapportOIS_L87.php");

class RapportTRANSFEE_L87
{
  function RapportTRANSFEE_L87($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->ois=new RapportOIS_L87($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }
  
  function writeRapport()
  {
    //$this->ois->verdeling='Regio';
    $this->ois->beleggingscategorieFilter='OBL';
    $this->ois->pdf->rapport_titel = 'Rendement obligaties vergeleken met benchmarks segmenten';
    $this->ois->pdf->rapport_type = "TRANSFEE";
    $this->ois->writeRapport();
  }
}

?>
