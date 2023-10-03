<?
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2009/02/12 11:29:59 $
 		File Versie					: $Revision: 1.2 $

 		$Log: RapportTRANS_L14_Kolom.php,v $
 		Revision 1.2  2009/02/12 11:29:59  cvs
 		decimale aanpassen layout 14 bij aantallen
 		
 		Revision 1.1  2007/06/29 11:40:15  rvv
 		*** empty log message ***

 		Revision 1.1  2006/10/02 13:35:47  rvv
 		van .inc naar .php

 		Revision 1.1  2006/10/02 12:50:55  rvv
 		Voor BCS eigen kolom indeling bij TRANS rapport


*/

		// voor data				0  1  2  3     4  5  6  7 8  9 10 11 12 13
		$this->pdf->widthA = array(15,30,75,18,    25,25,1,25,25,25,7,7,1,1);
		$this->pdf->alignA = array('L','L','L','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = $this->pdf->widthA;
		$this->pdf->alignB = $this->pdf->alignA;
?>