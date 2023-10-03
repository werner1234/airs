<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMS_L128
{
  function RapportVKMS_L128($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->vkm=new RapportVKM($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }


  function writeRapport()
  {
    $this->vkm->skipDetail=true;
    $this->vkm->skipLangeTermijn=false;
    $this->vkm->waardeZonderKostenKleur= array(186,192,11);
    $this->vkm->waardeNaKostenKleur= array(0,171,167);
    $this->vkm->cumulatieveKostenKleur= array(0,88,110);
  
    $this->vkm->writeRapport();
    $this->vkm->pdf->templateVars['VKMSPaginas'] = $this->vkm->pdf->page;
  }

}
