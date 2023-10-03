<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/07/12 15:46:34 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PDFRapport_footers_L12.php,v $
 		Revision 1.1  2017/07/12 15:46:34  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/03/06 14:37:11  rvv
 		*** empty log message ***
 		
 
*/

function Footer_basis_L12($object)
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

function FooterFRONT_12($object)
{
   $pdfObject=&$object;


}







?>