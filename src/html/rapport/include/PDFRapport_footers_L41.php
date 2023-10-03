<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/12/02 11:05:56 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PDFRapport_footers_L41.php,v $
 		Revision 1.1  2012/12/02 11:05:56  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/10/17 15:55:14  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/10/07 14:57:17  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/09/16 12:45:46  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/05/27 08:33:10  rvv
 		*** empty log message ***
 		
 	

*/

function Footer_basis_L41($object)
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
// geen footer

    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function FooterFRONT_L40($object)
{
   $pdfObject=&$object;
}











?>