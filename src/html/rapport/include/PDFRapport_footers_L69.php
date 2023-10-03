<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/09/11 08:30:02 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PDFRapport_footers_L69.php,v $
 		Revision 1.1  2016/09/11 08:30:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/06/15 15:58:41  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/03/06 14:37:11  rvv
 		*** empty log message ***
 		
 
*/

function Footer_basis_L69($object)
{
    $pdfObject=&$object;

    if ($pdfObject->nextFactuur == true )
    {
      $pdfObject->FooterFACTUUR();
      $pdfObject->nextFactuur = false;
    }
  else
  {
    if ($pdfObject->rapport_type != "FACTUUR")
    {
      $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
      $pdfObject->SetY(-10);
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
      $pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');
      $pdfObject->Cell(25,4,$pdfObject->rapport_voettext_rechts,'0','L');
    }
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