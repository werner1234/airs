<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIS_L12.php");

class RapportGRAFIEK_L12
{
	function RapportGRAFIEK_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "GRAFIEK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Verdeling totaalvermogen";//Onderverdeling in beleggingscategorie";

    $this->ois=new RapportOIS_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->portefeuille=$this->ois->maakPortefeuille();
    $this->ois=new RapportOIS_L12($pdf,  $this->portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->rapportageDatum=$rapportageDatum;
    $this->portefeuilleOrigineel=$portefeuille;
	}
  
  function writeRapport()
  {
    $this->ois->writeRapport();
    if($this->portefeuille <> $this->portefeuilleOrigineel)
    {
      verwijderTijdelijkeTabel($this->portefeuille, $this->rapportageDatum);
    }
  }
}
?>