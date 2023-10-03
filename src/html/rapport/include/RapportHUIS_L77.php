<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/28 15:45:39 $
File Versie					: $Revision: 1.1 $

$Log: RapportHUIS_L77.php,v $
Revision 1.1  2020/03/28 15:45:39  rvv
*** empty log message ***

Revision 1.11  2019/09/18 14:52:23  rvv
*** empty log message ***

Revision 1.10  2019/02/23 18:32:59  rvv
*** empty log message ***

Revision 1.9  2018/11/16 10:18:07  rvv
*** empty log message ***

Revision 1.8  2018/11/07 17:08:06  rvv
*** empty log message ***

Revision 1.7  2018/10/24 16:00:59  rvv
*** empty log message ***

Revision 1.6  2018/10/20 18:05:20  rvv
*** empty log message ***

Revision 1.5  2018/10/13 17:18:13  rvv
*** empty log message ***

Revision 1.4  2018/10/10 15:50:56  rvv
*** empty log message ***

Revision 1.3  2018/10/07 10:19:56  rvv
*** empty log message ***

Revision 1.2  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.1  2018/05/20 10:39:24  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportFRONT_L77.php");

class RapportHUIS_L77
{
	function RapportHUIS_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    $this->front=new RapportFRONT_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->front->naw=false;
  }
  
  
  function writeRapport()
  {
    $this->front->writeRapport();
  }
}
?>
