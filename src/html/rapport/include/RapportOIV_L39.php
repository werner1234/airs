<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.4 $

$Log: RapportOIV_L39.php,v $
Revision 1.4  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.3  2013/04/10 15:58:01  rvv
*** empty log message ***

Revision 1.2  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.1  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.3  2012/09/19 16:53:18  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIB_L39.php");

class RapportOIV_L39
{
	function RapportOIV_L39($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->rapport = new rapportOIB_L39($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR');
    
    $this->rapport->eerste='Regio';
    $this->rapport->tweede='Valuta';
    $this->rapport->eersteWhere='';
    $this->rapport->tweedeWhere='';
    $this->rapport->tweedeTitel='Valuta';
    $this->rapport->tweedeWhereATT="";
    $this->rapport->tweedeWhere="";
    $this->rapport->pdf->rapport_titel = "Onderverdeling in regio en valuta";
	}


	function writeRapport()
	{
	  $this->rapport->writeRapport();
	}
}
?>