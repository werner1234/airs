<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.2 $

$Log: RapportPERFD_L5.php,v $
Revision 1.2  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.1  2014/10/29 16:47:20  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportPERFG_L5.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportPERFD_L5
{
	function RapportPERFD_L5($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->rapport=new RapportPERFG_L5($pdf,$portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->rapport->periode='jaar';
	}



	function writeRapport()
	{
	  $this->rapport->writeRapport();
  }
  
}
?>