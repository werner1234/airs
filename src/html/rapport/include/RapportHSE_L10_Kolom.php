<?
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/10/02 13:35:47 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: RapportHSE_L10_Kolom.php,v $
 		Revision 1.1  2006/10/02 13:35:47  rvv
 		van .inc naar .php
 		
 		Revision 1.1  2006/10/02 12:50:00  rvv
 		Voor BCS eigen kolom indeling voor HSE rapport
 		
 	
*/

// voor data				0  1  2  3  4  5  6  7  8  9  10
$this->pdf->widthB = array(10,55,49,20,1 ,30,44,20,1 ,30,15);
$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R');

// voor kopjes              0  1  2  3  4  5  6  7  8  9
$this->pdf->widthA = array(65,49,20,1 ,30,44,20,1 ,30,15);
$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R');
?>