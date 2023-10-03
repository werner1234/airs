<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/05/25 14:35:58 $
 		File Versie					: $Revision: 1.4 $

 		$Log: PDFRapport_footers_L32.php,v $
 		Revision 1.4  2017/05/25 14:35:58  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/05/20 18:16:29  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/05/17 15:57:50  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/05/13 16:27:35  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/10/26 12:29:07  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/05/27 08:33:10  rvv
 		*** empty log message ***
 		
 	

*/

function Footer_basis_L32($object)
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
      /*
      $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
      $pdfObject->SetY(-8);
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
      $pdfObject->Cell(255,4,'','0','L');
      $pdfObject->Cell(25,4,$pdfObject->rapport_voettext_rechts,'0','R');
*/
    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}

function FooterFRONT_L32($object)
{
   $pdfObject=&$object;


}







?>