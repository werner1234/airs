<?	
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/07/23 10:03:51 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: RapportTRANS_L13_Kolom.php,v $
 		Revision 1.1  2008/07/23 10:03:51  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/06/29 11:40:15  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/10/02 13:35:47  rvv
 		van .inc naar .php
 		
 		Revision 1.1  2006/10/02 12:50:55  rvv
 		Voor BCS eigen kolom indeling bij TRANS rapport
 		
 	
*/
/*
		$this->pdf->widthA = array(15,10,15,38,16,22,22,16,22,22,22,22,22,15);
		$this->pdf->alignA = array('L','L','R','L','R','R','R','R','R','R','R','R','R','R');
		
		// voor kopjes
		$this->pdf->widthB = array(15,10,15,38,16,22,22,16,22,22,22,22,22,15);
		$this->pdf->alignB = array('L','L','R','L','R','R','R','R','R','R','R','R','R','R');
*/

		$this->pdf->widthA = array(15,10,15,50,20,20,20,19,19,19,19,19,19,15);
		$this->pdf->alignA = array('L','L','R','L','R','R','R','R','R','R','R','R','R','R');
		
		// voor kopjes
		$this->pdf->widthB = $this->pdf->widthA;
		$this->pdf->alignB = $this->pdf->alignA;
?>