<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/18 17:06:34 $
File Versie					: $Revision: 1.28 $

$Log: RapportFRONT_L25.php,v $
Revision 1.28  2020/04/18 17:06:34  rvv
*** empty log message ***

Revision 1.27  2020/02/26 16:12:54  rvv
*** empty log message ***

Revision 1.26  2020/02/22 18:46:19  rvv
*** empty log message ***

Revision 1.25  2020/02/19 15:02:02  rvv
*** empty log message ***

Revision 1.24  2020/02/15 18:29:05  rvv
*** empty log message ***

Revision 1.23  2020/02/02 12:05:21  rvv
*** empty log message ***

Revision 1.22  2020/02/01 18:11:55  rvv
*** empty log message ***

Revision 1.21  2020/01/08 16:35:02  rvv
*** empty log message ***

Revision 1.20  2019/07/05 16:42:29  rvv
*** empty log message ***

Revision 1.19  2019/06/15 20:53:26  rvv
*** empty log message ***

Revision 1.18  2019/05/18 16:29:36  rvv
*** empty log message ***

Revision 1.17  2017/08/30 15:03:56  rvv
*** empty log message ***

Revision 1.16  2017/02/04 19:11:39  rvv
*** empty log message ***

Revision 1.15  2016/07/24 09:50:58  rvv
*** empty log message ***

Revision 1.14  2016/02/04 11:53:49  rvv
*** empty log message ***

Revision 1.13  2016/01/30 16:22:58  rvv
*** empty log message ***

Revision 1.12  2016/01/24 09:52:26  rvv
*** empty log message ***

Revision 1.11  2013/12/21 18:31:53  rvv
*** empty log message ***

Revision 1.10  2013/11/04 08:56:05  rvv
*** empty log message ***

Revision 1.9  2012/12/02 11:05:56  rvv
*** empty log message ***

Revision 1.8  2012/11/28 17:04:42  rvv
*** empty log message ***

Revision 1.7  2012/10/07 14:57:17  rvv
*** empty log message ***

Revision 1.6  2012/09/09 17:35:27  rvv
*** empty log message ***

Revision 1.5  2012/02/09 12:15:46  cvs
adreswijziging

Revision 1.4  2010/06/06 14:11:21  rvv
*** empty log message ***

Revision 1.3  2010/06/02 16:57:23  rvv
*** empty log message ***

Revision 1.2  2010/05/19 16:24:10  rvv
*** empty log message ***

Revision 1.1  2010/05/05 18:37:43  rvv
*** empty log message ***

Revision 1.1  2010/03/31 17:26:12  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportFRONT_L25.php");

class RapportFRONTC_L25
{
	function RapportFRONTC_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->front=new RapportFRONT_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->front->consolidatie=true;

	}

	function writeRapport()
	{
     $this->front->writeRapport();
	}
}
?>