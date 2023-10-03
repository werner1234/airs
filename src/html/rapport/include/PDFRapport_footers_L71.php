<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/06/15 15:58:41 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PDFRapport_footers_L71.php,v $
 		Revision 1.1  2016/06/15 15:58:41  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/03/06 14:37:11  rvv
 		*** empty log message ***
 		
 
*/

function Footer_basis_L71($object)
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

function FooterFRONT_L71($object)
{
   $pdfObject=&$object;


}







?>