<?
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/01/20 17:45:20 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: RapportHSE_L8_Kolom.php,v $
 		Revision 1.1  2009/01/20 17:45:20  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/10/02 13:35:47  rvv
 		van .inc naar .php
 		
 		Revision 1.1  2006/10/02 12:50:00  rvv
 		Voor BCS eigen kolom indeling voor HSE rapport
 		
 	
*/

		$this->pdf->widthB = array(10,61,20,20,30,30,15,20,30,30,15);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(65,26,20,30,30,15,20,30,30,15);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R');
?>