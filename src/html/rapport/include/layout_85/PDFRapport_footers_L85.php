<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/10/23 13:34:56 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PDFRapport_footers_L85.php,v $
 		Revision 1.1  2019/10/23 13:34:56  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/07/05 16:47:00  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:06:36  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/05/27 08:33:10  rvv
 		*** empty log message ***
 		
 	

*/

function Footer_basis_L85($object)
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
    //  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
	  //  $pdfObject->Cell(25,4,$pdfObject->rapport_voettext_rechts,'0','L');
    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}

function FooterFRONT_L85($object)
{
   $pdfObject=&$object;
}








?>