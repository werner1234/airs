<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVKMA_L25.php");

class RapportVKMA_L57
{
	function RapportVKMA_L57($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkma=new RapportVKMA_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}
  
  
  function writeRapport()
  {
    $this->vkma->layout='L';
    $this->vkma->writeRapport();
  }
  
}
