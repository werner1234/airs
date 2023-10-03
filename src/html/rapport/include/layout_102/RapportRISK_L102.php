<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/24 06:30:58 $
File Versie					: $Revision: 1.8 $

$Log: RapportRISK_L80.php,v $
Revision 1.8  2020/05/24 06:30:58  rvv
*** empty log message ***

Revision 1.7  2020/05/23 16:39:00  rvv
*** empty log message ***

Revision 1.6  2019/12/01 08:15:05  rvv
*** empty log message ***

Revision 1.5  2019/12/01 07:51:04  rvv
*** empty log message ***

Revision 1.4  2019/07/06 15:43:47  rvv
*** empty log message ***

Revision 1.3  2019/01/30 16:47:26  rvv
*** empty log message ***

Revision 1.2  2019/01/12 17:08:31  rvv
*** empty log message ***

Revision 1.1  2018/10/03 15:42:01  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_102/RapportPERFD_L102.php");

class RapportRISK_L102
{

	function RapportRISK_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->perfd = new RapportPERFD_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "RISK";
    $this->pdf->rapport_titel = "Risicoverdeling - lange termijn";
		$this->portefeuille = $portefeuille;
	}

	function writeRapport()
  {
    global $__appvar;
    $this->perfd->extraVerdeling='AttributieCategorie';
    $this->perfd->writeRapport();
  }
  
}
?>