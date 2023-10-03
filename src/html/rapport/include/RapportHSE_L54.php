<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/10/20 16:48:17 $
File Versie					: $Revision: 1.14 $

$Log: RapportINDEX_L54.php,v $
Revision 1.14  2019/10/20 16:48:17  rvv
*** empty log message ***

Revision 1.13  2019/10/19 16:45:25  rvv
*** empty log message ***

Revision 1.12  2019/10/13 09:30:54  rvv
*** empty log message ***

Revision 1.11  2019/10/11 17:40:07  rvv
*** empty log message ***

Revision 1.10  2019/10/02 15:12:58  rvv
*** empty log message ***

Revision 1.9  2019/09/28 17:20:17  rvv
*** empty log message ***

Revision 1.8  2018/07/21 15:54:40  rvv
*** empty log message ***

Revision 1.7  2016/07/31 10:40:44  rvv
*** empty log message ***

Revision 1.6  2016/07/13 16:06:39  rvv
*** empty log message ***

Revision 1.5  2016/06/02 07:10:25  rvv
*** empty log message ***

Revision 1.4  2016/06/01 19:48:58  rvv
*** empty log message ***

Revision 1.3  2016/05/04 16:01:30  rvv
*** empty log message ***

Revision 1.2  2016/04/23 15:33:07  rvv
*** empty log message ***

Revision 1.1  2016/03/20 14:32:23  rvv
*** empty log message ***

Revision 1.5  2015/03/14 17:25:18  rvv
*** empty log message ***


*/


include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/RapportINDEX_L54.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class RapportHSE_L54
{
	function RapportHSE_L54($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->index=new RapportIndex_L54($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}


	function writeRapport()
  {
    $this->index->grafieken=false;
    $this->index->writeRapport();
  }
}
?>