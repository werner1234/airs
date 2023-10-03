<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/08 15:26:28 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: PDFRapport_footers_L91.php,v $
 		Revision 1.2  2020/07/08 15:26:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2020/07/01 16:22:28  rvv
 		*** empty log message ***
 		
 	
*/

function Footer_basis_L91($object)
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
      $pdfObject->MemImage($pdfObject->boomLogo,$pdfObject->w-11.5,$pdfObject->h-25,5);
      $pdfObject->SetLineWidth(0.1);
      $pdfObject->setDrawColor($pdfObject->rapportLineColor[0],$pdfObject->rapportLineColor[1],$pdfObject->rapportLineColor[2]);
      $pdfObject->line($pdfObject->w-11.5,$pdfObject->h-10,$pdfObject->w-6.5,$pdfObject->h-10);
   
      $pdfObject->SetTextColor($pdfObject->rapportLineColor[0],$pdfObject->rapportLineColor[1],$pdfObject->rapportLineColor[2]);
	    $pdfObject->SetY(-9);
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	    $pdfObject->MultiCell(282,4,$pdfObject->SeqPageNo,'0','R');
      $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
      /*
   $pdfObject->SetY(-15);
   $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
   $pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');
   $pdfObject->Cell(25,4,$pdfObject->rapport_voettext_rechts,'0','L');
   */
    }
    
    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }    
}

function FooterFRONT_L91($object)
{
   $pdfObject=&$object;
 
  
}


function FooterFACTUUR_L91($object)
{
   $pdfObject=&$object;
}






?>