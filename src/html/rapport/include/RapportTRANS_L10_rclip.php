<?	
/* 	
		    AE-ICT source module
		    Author  						: $Author: rvv $
		 		Laatste aanpassing	: $Date: 2007/06/29 11:41:45 $
		 		File Versie					: $Revision: 1.2 $
		 		
		 		$Log: RapportTRANS_L10_rclip.php,v $
		 		Revision 1.2  2007/06/29 11:41:45  rvv
		 		L14 aanpassing
		 		
		 		Revision 1.1  2006/10/02 13:35:47  rvv
		 		van .inc naar .php
		 		
		 		Revision 1.1  2006/10/02 12:51:42  rvv
		 		Voor BCS geen Rclip in TRANS rapport
		 		
		 	
*/
$this->pdf->Cell($this->pdf->widthB[0],4,"");
$this->pdf->Cell($this->pdf->widthB[1],4,"");
$this->pdf->Cell($this->pdf->widthB[2],4,"");	
$this->pdf->Cell($this->pdf->widthB[3],4,$mutaties[Omschrijving]); 	
?>