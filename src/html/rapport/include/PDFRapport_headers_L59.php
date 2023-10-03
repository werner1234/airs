<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/01/31 20:03:27 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: PDFRapport_headers_L59.php,v $
 		Revision 1.2  2015/01/31 20:03:27  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/01/28 17:04:14  rvv
 		*** empty log message ***
 		
 	
 	
*/
function Header_basis_L59($object)
{
    $pdfObject = &$object;
  
    if(is_file($pdfObject->rapport_logo))
    {
	    $factor=0.06;
		  $x=990*$factor;//$x=885*$factor;
		  $y=332*$factor;//$y=849*$factor;

      if($pdfObject->CurOrientation=='P')
     	  $pdfObject->Image($pdfObject->rapport_logo, 140, 5, $x, $y);
      else
        $pdfObject->Image($pdfObject->rapport_logo, 230, 5, $x, $y);  
		}
}

function HeaderParticipatie_L59($object)
{
   $pdfObject = &$object;
   $widthBackup=$pdfObject->widths;
   $alignBackup=$pdfObject->aligns;
   
   if($pdfObject->nawPrinted==false)
   {
     $pdfObject->SetWidths(array(10,100));
     $pdfObject->SetAligns(array('L','L'));
     $pdfObject->SetFont($pdfObject->rapport_font, 'B', $pdfObject->rapport_fontsize);
     $pdfObject->SetXY($pdfObject->marge,40);
     $pdfObject->Row(array('',$pdfObject->portefeuilledata['Naam']));
     if($pdfObject->portefeuilledata['Naam1'] <> '')
       $pdfObject->Row(array('',$pdfObject->portefeuilledata['Naam1']));
     $pdfObject->Row(array('',$pdfObject->portefeuilledata['Adres'])); 
     $pdfObject->Row(array('',$pdfObject->portefeuilledata['Woonplaats']));
     $pdfObject->Row(array('',$pdfObject->portefeuilledata['Land']));
     $pdfObject->SetY(70);         
   }
   else
     $pdfObject->SetY(25);
   $pdfObject->nawPrinted=true;
   $pdfObject->widths=$widthBackup;
   $pdfObject->aligns=$alignBackup;
}


function HeaderCourse_L59($object)
{
  $pdfObject = &$object;
  $pdfObject->SetY(25);
  


}
	  
  

?>