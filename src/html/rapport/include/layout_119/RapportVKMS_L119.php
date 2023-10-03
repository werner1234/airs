<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMS_L119
{
  function RapportVKMS_L119($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->vkm=new RapportVKM($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }


  function writeRapport()
  {
    $this->vkm->skipDetail=true;
    $this->vkm->skipLangeTermijn=false;
    $this->vkm->writeRapport();
    $this->vkm->pdf->templateVars['VKMSPaginas'] = $this->vkm->pdf->page;
  }

}
