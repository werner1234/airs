<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/04/16 15:51:22 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: PDFRapport_footers_L51.php,v $
 		Revision 1.1  2014/04/16 15:51:22  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/04/12 16:28:12  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/09/27 12:54:02  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2008/09/15 08:04:05  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2008/05/16 08:13:26  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/03/18 12:39:08  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***
 		
 	
*/

function Footer_basis_L51($object)
{
    $pdfObject=&$object;
    if ($pdfObject->nextFactuur == true )
    {
      $pdfObject->FooterFACTUUR();
      $pdfObject->nextFactuur = false;
    }
    elseif ($pdfObject->frontPage == true)
    {
       $pdfObject->frontPage = false;     
    }
    else 
    {  
 //
    }
    
    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }    
}

function FooterFRONT_L51($object)
{
   $pdfObject=&$object;
 
  
}







?>