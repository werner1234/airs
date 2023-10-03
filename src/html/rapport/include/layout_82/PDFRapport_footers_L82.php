<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/01/12 17:10:16 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PDFRapport_footers_L82.php,v $
 		Revision 1.1  2019/01/12 17:10:16  rvv
 		*** empty log message ***
 		


*/

function Footer_basis_L82($object)
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
      $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
	    $pdfObject->SetY(-15);
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
	    if($pdfObject->rapport_type <> 'FACTUUR')
	      $pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');
	    $pdfObject->Cell(25,4,$pdfObject->rapport_voettext_rechts,'0','L');
    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}

function FooterFRONT_L82($object)
{
   $pdfObject=&$object;


}







?>