<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/04/26 16:43:08 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: PDFRapport_footers_L53.php,v $
 		Revision 1.1  2014/04/26 16:43:08  rvv
 		*** empty log message ***
 		
 
*/

function Footer_basis_L53($object)
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

function FooterFRONT_L53($object)
{
   $pdfObject=&$object;
 
  
}

function FooterATT_L53($object)
{
   $pdfObject=&$object;
  
}





?>