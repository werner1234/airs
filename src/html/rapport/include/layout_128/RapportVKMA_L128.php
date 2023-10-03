<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKMA.php");

class RapportVKMA_L128
{
  function RapportVKMA_L128($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->vkma=new RapportVKMA($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }


  function writeRapport()
  {
    $this->vkma->skipDetail=true;
    $this->vkma->skipLangeTermijn=false;
    $this->vkma->waardeZonderKostenKleur= array(186,192,11);
    $this->vkma->waardeNaKostenKleur= array(0,171,167);
    $this->vkma->cumulatieveKostenKleur= array(0,88,110);
  
    $this->vkma->writeRapport();
    $this->vkma->pdf->templateVars['VKMAPaginas'] = $this->vkma->pdf->page;
  }

}
