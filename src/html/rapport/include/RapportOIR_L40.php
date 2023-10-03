<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.4 $

$Log: RapportOIR_L40.php,v $
Revision 1.4  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.3  2012/09/16 12:45:46  rvv
*** empty log message ***

Revision 1.2  2012/09/01 14:27:48  rvv
*** empty log message ***

Revision 1.1  2012/07/29 10:24:33  rvv
*** empty log message ***

Revision 1.40  2012/03/14 17:29:35  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIS_L40.php");


class RapportOIR_L40
{
	function RapportOIR_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
	{
    $this->pdf = &$pdf;
    $this->rapport = new rapportOIS_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR');
    $this->rapport->verdeling='regio';
	}


	function writeRapport()
	{
    $this->rapport->writeRapport();
	}
}
?>