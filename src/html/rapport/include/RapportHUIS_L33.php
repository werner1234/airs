<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/02/13 14:50:15 $
File Versie					: $Revision: 1.3 $

$Log: RapportHUIS_L33.php,v $
Revision 1.3  2019/02/13 14:50:15  rvv
*** empty log message ***

Revision 1.2  2019/02/10 14:26:19  rvv
*** empty log message ***

Revision 1.1  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.4  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.3  2013/03/23 16:19:36  rvv
*** empty log message ***

Revision 1.2  2013/02/27 17:04:41  rvv
*** empty log message ***

Revision 1.1  2013/01/30 16:58:23  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIS_L33.php");
include_once("rapport/rapportATTberekening.php");

class RapportHUIS_L33
{
	function RapportHUIS_L33($pdf,$portefeuille, $rapportageDatumVanaf, $rapportageDatum )
	{
		$this->pdf = &$pdf;
    $this->ois=new RapportOIS_L33($this->pdf,$portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->ois->verdeling='regio';
    $this->ois->pdf->rapport_titel = "Overzicht beleggingen per regio (met beginkoers)";//"Vermogensoverzicht regio (met beginkoers)";
    $this->ois->vastWhere='';
    $this->ois->vastWhereAtt="";
    $this->ois->metHoofdcategorie=false;
    $this->pdf->rapport_type = "HUIS";

    
    
	}

	function formatGetal($waarde, $dec)
	{
	  if($waarde==0)
	    return '';
	  else
  		return number_format($waarde,$dec,",",".");
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function writeRapport()
	{
    $this->ois->rapport();
	}

}
?>