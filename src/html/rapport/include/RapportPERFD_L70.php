<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.3 $

$Log: RapportPERFD_L70.php,v $
Revision 1.3  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.2  2016/06/08 15:40:53  rvv
*** empty log message ***

Revision 1.1  2016/05/22 18:49:26  rvv
*** empty log message ***

Revision 1.1  2014/10/29 16:47:20  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportPERFG_L70.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportPERFD_L70
{
	function RapportPERFD_L70($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->rapport=new RapportPERFG_L70($pdf,$portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->rapport->pdf->rapport_titel = "Beleggingsresultaat jaren";
    $this->rapport->periode='jaar';
	}



	function writeRapport()
	{
	  $this->rapport->writeRapport();
  }
  
}
?>