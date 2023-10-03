<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_121/RapportVKM_L121.php");

class RapportVKMS_L121
{
  function RapportVKMS_L121($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->vkm=new RapportVKM_L121($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }


  function writeRapport()
  {
    $this->vkm->skipDetail=true;
    $this->vkm->skipLangeTermijn=false;
    $this->vkm->changeGrootboekOmschrijving = array('BEH' => 'Adviesvergoeding');
    $this->vkm->writeRapport();
    $this->vkm->pdf->templateVars['VKMSPaginas'] = $this->vkm->pdf->page;
  }

}
