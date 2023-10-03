<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/07/12 15:46:34 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PDFRapport_footers_L77.php,v $

*/

function Footer_basis_L77($object)
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

function FooterFRONT_77($object)
{
   $pdfObject=&$object;


}







?>