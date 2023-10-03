<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/03/27 17:34:16 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: PDFRapport_footers_L22.php,v $
 		Revision 1.3  2016/03/27 17:34:16  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/04/12 16:28:12  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/09/27 12:54:02  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2008/09/15 08:04:05  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2008/05/16 08:13:26  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/03/18 12:39:08  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***
 		
 	
*/

function Footer_basis_L22($object)
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
	    $pdfObject->SetY(-10);
	    $pdfObject->MultiCell(282,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->SeqPageNo,'0','R');
	    $pdfObject->SetY(-15);
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
	    $pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');
	    $pdfObject->Cell(25,4,$pdfObject->rapport_voettext_rechts,'0','L');
    }
    
    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }    
}

function FooterFRONT_L22($object)
{
   $pdfObject=&$object;
 
  
}


function FooterFACTUUR_L22($object)
{
   $pdfObject=&$object;
}






?>