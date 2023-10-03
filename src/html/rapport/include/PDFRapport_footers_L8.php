<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/12/18 07:14:41 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: PDFRapport_footers_L8.php,v $
 		Revision 1.1  2008/12/18 07:14:41  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2008/05/16 08:13:26  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/03/18 12:39:08  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***
 		
 	
*/

function Footer_basis_L8($object)
{
    $pdfObject=&$object;
    
    if ($pdfObject->geenBasisFooter == true)
    {
       $pdfObject->geenBasisFooter = false;     
    }
    elseif ($pdfObject->nextFactuur == true )
    {
      $pdfObject->FooterFACTUUR();
      $pdfObject->nextFactuur = false;
    }
    elseif ($pdfObject->frontPage == true)
    {
      $pdfObject->frontPage = false;   
      $pdfObject->SetXY(30+$pdfObject->marge,-8);
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
      $pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');  
    }
    else
    {
    //  $this->AliasNbPages();
     $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
      $pdfObject->SetY(-8);
      $pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');

      if($pdfObject->rapport_type == "FRONT" && $pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
        $pdfObject->customPageNo =1;
        

    }
    
    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }   
}

function FooterFRONT_L8($object)
{
   $pdfObject=&$object;
 }





?>