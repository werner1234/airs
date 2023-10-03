<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/10/26 12:29:07 $
 		File Versie					: $Revision: 1.2 $

 		$Log: PDFRapport_footers_L38.php,v $
 		Revision 1.2  2016/10/26 12:29:07  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/05/27 08:33:10  rvv
 		*** empty log message ***
 		
 	

*/

function Footer_basis_L38($object)
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


    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}

function FooterFRONT_L38($object)
{
   $pdfObject=&$object;


}







?>