<?php

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_112/RapportFRONT_L112.php");

class RapportFRONTC_L112
{
  function RapportFRONTC_L112($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->front = new RapportFRONT_L112($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }

	function writeRapport()
	{
    $this->front->writeRapport();
	}
}
