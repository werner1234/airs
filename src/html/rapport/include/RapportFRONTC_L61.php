<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/10 15:50:36 $
File Versie					: $Revision: 1.1 $

$Log: RapportFRONTC_L61.php,v $
Revision 1.1  2019/04/10 15:50:36  rvv
*** empty log message ***

Revision 1.15  2017/05/10 14:44:58  rvv
*** empty log message ***

Revision 1.14  2017/01/21 17:48:04  rvv
*** empty log message ***

Revision 1.13  2016/11/23 16:55:20  rvv
*** empty log message ***

Revision 1.12  2016/10/30 13:02:59  rvv
*** empty log message ***

Revision 1.11  2016/10/29 15:41:46  rvv
*** empty log message ***

Revision 1.10  2016/10/23 11:32:33  rvv
*** empty log message ***

Revision 1.9  2016/08/21 08:52:52  rvv
*** empty log message ***

Revision 1.8  2016/02/13 14:02:39  rvv
*** empty log message ***

Revision 1.7  2016/01/28 15:27:48  rvv
*** empty log message ***

Revision 1.6  2016/01/28 15:00:18  rvv
*** empty log message ***

Revision 1.5  2016/01/17 18:10:27  rvv
*** empty log message ***

Revision 1.4  2015/11/11 17:28:10  rvv
*** empty log message ***

Revision 1.3  2015/11/08 16:35:01  rvv
*** empty log message ***

Revision 1.2  2015/10/04 11:52:21  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:04  rvv
*** empty log message ***

Revision 1.4  2015/05/31 10:15:24  rvv
*** empty log message ***

Revision 1.3  2015/03/01 14:08:16  rvv
*** empty log message ***

Revision 1.2  2015/02/18 17:09:13  rvv
*** empty log message ***

Revision 1.1  2015/02/15 10:26:57  rvv
*** empty log message ***

Revision 1.9  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.8  2014/01/18 17:27:23  rvv
*** empty log message ***

Revision 1.7  2013/07/10 16:01:24  rvv
*** empty log message ***

Revision 1.6  2013/06/09 18:01:53  rvv
*** empty log message ***

Revision 1.5  2012/10/24 15:45:39  rvv
*** empty log message ***

Revision 1.4  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.3  2012/10/07 14:57:17  rvv
*** empty log message ***

Revision 1.2  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.1  2012/06/17 13:04:11  rvv
*** empty log message ***

Revision 1.2  2012/06/09 13:43:40  rvv
*** empty log message ***

Revision 1.1  2012/05/27 08:33:11  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportFRONT_L61.php");

class RapportFRONTC_L61
{
	function RapportFRONTC_L61($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->front=new RapportFRONT_L61($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->front->consolidatie=true;

	}


	function writeRapport()
	{
		global $__appvar;
    $this->front->writeRapport();
  
	}
}
?>
