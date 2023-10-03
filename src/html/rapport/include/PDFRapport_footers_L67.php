<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/03/06 18:17:00 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PDFRapport_footers_L67.php,v $
 		Revision 1.1  2016/03/06 18:17:00  rvv
 		*** empty log message ***
 		
 
*/

function Footer_basis_L67($object)
{
    $pdfObject=&$object;

    if ($pdfObject->nextFactuur == true )
    {
      $pdfObject->FooterFACTUUR();
      $pdfObject->nextFactuur = false;
    }
  

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}

function FooterFRONT_L67($object)
{
   $pdfObject=&$object;


}







?>