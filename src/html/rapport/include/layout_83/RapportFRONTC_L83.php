<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/20 16:59:05 $
File Versie					: $Revision: 1.2 $

$Log: RapportFRONTC_L83.php,v $
Revision 1.2  2019/04/20 16:59:05  rvv
*** empty log message ***

Revision 1.1  2019/03/02 18:23:01  rvv
*** empty log message ***

Revision 1.1  2018/12/22 16:15:52  rvv
*** empty log message ***

Revision 1.1  2018/10/23 06:20:02  rvv
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
include_once($__appvar["basedir"]."/html/rapport/include/layout_83/RapportFRONT_L83.php");

class RapportFRONTC_L83
{
	function RapportFRONTC_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->rapport = new RapportFRONT_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }

	function writeRapport()
  {
    $this->rapport->writeRapport();
  }
}
?>
