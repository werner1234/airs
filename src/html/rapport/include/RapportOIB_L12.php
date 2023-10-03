<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/12/04 15:56:35 $
File Versie					: $Revision: 1.8 $

$Log: RapportOIB_L12.php,v $
Revision 1.8  2019/12/04 15:56:35  rvv
*** empty log message ***

Revision 1.7  2012/06/30 14:42:50  rvv
*** empty log message ***

Revision 1.6  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.5  2008/06/30 07:59:39  rvv
*** empty log message ***

Revision 1.4  2008/05/14 08:05:37  rvv
*** empty log message ***

Revision 1.3  2007/10/04 12:09:12  rvv
*** empty log message ***

Revision 1.2  2007/03/27 14:59:13  rvv
VreemdeValutaRapportage

Revision 1.1  2007/02/21 11:06:15  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIS_L12.php");

class RapportOIB_L12
{
	function RapportOIB_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    $this->ois=new RapportOIS_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    
    $this->ois->pdf->rapport_type = "OIB";
	
	}


	function writeRapport()
	{
		
    $this->ois->writeRapport();

	}
}


?>