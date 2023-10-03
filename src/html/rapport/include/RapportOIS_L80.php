<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/12/08 18:28:30 $
File Versie					: $Revision: 1.1 $

$Log: RapportOIS_L80.php,v $
Revision 1.1  2018/12/08 18:28:30  rvv
*** empty log message ***

Revision 1.35  2011/06/25 16:51:45  rvv
*** empty log message ***

Revision 1.34  2010/06/30 16:10:10  rvv
*** empty log message ***

Revision 1.33  2010/06/02 09:13:01  rvv
*** empty log message ***

Revision 1.32  2009/11/20 09:37:51  rvv
*** empty log message ***

Revision 1.31  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.30  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.29  2007/10/04 11:57:04  rvv
*** empty log message ***

Revision 1.28  2007/06/29 12:16:31  rvv
*** empty log message ***

Revision 1.27  2007/06/29 11:38:56  rvv
L14 aanpassingen

Revision 1.26  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.25  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.24  2007/01/31 16:20:27  rvv
*** empty log message ***

Revision 1.23  2006/11/27 13:33:02  rvv
Sortering werkt nu ook met eigen kleuren.

Revision 1.22  2006/11/27 09:27:15  rvv
grafiekkleuren uit vermogensbeheerder check

Revision 1.21  2006/11/10 11:56:12  rvv
Eigen kleuren aanpassing/toevoeging

Revision 1.20  2006/11/03 11:24:04  rvv
Na user update

Revision 1.19  2006/10/31 12:06:45  rvv
Voor user update

Revision 1.18  2006/10/20 14:55:53  rvv
*** empty log message ***

Revision 1.17  2006/05/09 07:48:11  jwellner
- afronding fondsaantal
- afronding controle bij afdrukken rapporten
- sorteren frontoffice selectie

Revision 1.16  2006/04/12 07:54:47  jwellner
*** empty log message ***

Revision 1.15  2005/12/19 13:23:27  jwellner
no message

Revision 1.14  2005/11/30 08:37:39  jwellner
layout stuff

Revision 1.13  2005/11/25 09:30:08  jwellner
- verdiept overzicht
- layout

Revision 1.12  2005/11/18 15:15:01  jwellner
no message

Revision 1.11  2005/11/17 07:25:02  jwellner
no message

Revision 1.10  2005/10/07 07:15:15  jwellner
rapportage

Revision 1.9  2005/09/30 09:45:45  jwellner
rapporten aangepast.

Revision 1.8  2005/09/29 15:00:18  jwellner
no message

Revision 1.7  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.6  2005/09/13 14:49:18  jwellner
rapportage toevoegingen

Revision 1.5  2005/09/12 12:04:16  jwellner
bugs en features

Revision 1.4  2005/09/09 11:31:46  jwellner
diverse aanpassingen zie e-mails Theo

Revision 1.3  2005/08/05 12:08:04  jwellner
no message

Revision 1.2  2005/08/01 13:05:25  jwellner
diverse kleine bugfixes :
- beheerfee nooit < 0

Revision 1.1  2005/07/15 11:21:00  jwellner
Layout verwijderd, alles samengevoegd in PDFRapport

Revision 1.3  2005/07/12 07:09:50  jwellner
no message

Revision 1.2  2005/07/08 13:52:01  jwellner
no message

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIV_L80.php");

class RapportOIS_L80
{
	function RapportOIS_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    $this->oiv=new RapportOIV_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Onderverdeling naar sector";

   
    $this->oiv->verdeling1='beleggingssector';
    $this->oiv->verdeling2='valuta';
	}



	function writeRapport()
	{
    $this->oiv->writeRapport();
    
	}
}
?>