<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/01/19 13:36:46 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIS_L87.php,v $

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"] . "/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"] . "/html/rapport/include/layout_87/RapportOIS_L87.php");

class RapportOIH_L87
{
  function RapportOIH_L87($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->ois=new RapportOIS_L87($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }
  
  function writeRapport()
  {
    $this->ois->verdeling='Regio';
    $this->ois->benchmarkTotalen=true;
    $this->ois->pdf->rapport_type = "OIH";
    $this->ois->pdf->rapport_titel = 'Rendement aandelen vergeleken met benchmarks regio\'s (totaal)';
    $data=$this->ois->writeRapport();
    $this->ois->addBenchmarkTabel($data);
    
  }
}

?>
