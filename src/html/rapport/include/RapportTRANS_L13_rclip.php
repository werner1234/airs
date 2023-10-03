<?	
/* 	
		    AE-ICT source module
		    Author  						: $Author: rvv $
		 		Laatste aanpassing	: $Date: 2008/07/23 10:03:51 $
		 		File Versie					: $Revision: 1.1 $
		 		
		 		$Log: RapportTRANS_L13_rclip.php,v $
		 		Revision 1.1  2008/07/23 10:03:51  rvv
		 		*** empty log message ***
		 		
		 		Revision 1.1  2007/06/29 11:40:15  rvv
		 		*** empty log message ***
		 		
		 		Revision 1.1  2006/10/02 13:35:47  rvv
		 		van .inc naar .php
		 		
		 		Revision 1.1  2006/10/02 12:51:42  rvv
		 		Voor BCS geen Rclip in TRANS rapport
		 		
		 	
*/
			$this->pdf->Cell($this->pdf->widthB[0],4,"");
			$this->pdf->Cell($this->pdf->widthB[1],4,"");
			$this->pdf->Cell($this->pdf->widthB[2],4,"");			  
			$this->pdf->Cell($this->pdf->widthB[3],4,rclip($mutaties[Omschrijving],27));


?>