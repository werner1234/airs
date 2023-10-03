<?	
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/10/02 13:35:47 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: RapportTRANS_L10_Kolom.php,v $
 		Revision 1.1  2006/10/02 13:35:47  rvv
 		van .inc naar .php
 		
 		Revision 1.1  2006/10/02 12:50:55  rvv
 		Voor BCS eigen kolom indeling bij TRANS rapport
 		
 	
*/

		// voor data				0  1  2  3     4  5  6  7 8  9 10 11 12 13
		$this->pdf->widthA = array(15,10,15,78,    25,3,25,25,3,25,7,7,25,15);
		$this->pdf->alignA = array('L','L','R','L','R','R','R','R','R','R','R','R','R','R');
		
		// voor kopjes
		$this->pdf->widthB = $this->pdf->widthA;
		$this->pdf->alignB = $this->pdf->alignA;
?>