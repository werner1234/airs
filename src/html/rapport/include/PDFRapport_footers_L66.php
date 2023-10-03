<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/03/06 14:37:11 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PDFRapport_footers_L66.php,v $
 		Revision 1.1  2016/03/06 14:37:11  rvv
 		*** empty log message ***
 		
 
*/

function Footer_basis_L66($object)
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

function FooterFRONT_L66($object)
{
   $pdfObject=&$object;


}







?>