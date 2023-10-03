<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/02 10:03:42 $
File Versie					: $Revision: 1.4 $

$Log: RapportFRONT_L10.php,v $
Revision 1.4  2019/06/02 10:03:42  rvv
*** empty log message ***

Revision 1.3  2019/05/18 16:29:36  rvv
*** empty log message ***

Revision 1.2  2018/12/02 12:35:31  rvv
*** empty log message ***

Revision 1.1  2018/10/07 08:29:03  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportFRONT_L10.php");

class RapportFRONTC_L10
{
  function RapportFRONTC_L10($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->front=new RapportFRONT_L10($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }
  
  function writeRapport()
  {
    $this->front->writeRapport();
  }
}
?>
