<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/09/30 15:53:11 $
 		File Versie					: $Revision: 1.4 $
 		
 		$Log: PDFRapport_footers_L36.php,v $
 		Revision 1.4  2015/09/30 15:53:11  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/04/05 15:33:48  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/03/29 16:22:37  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/03/01 14:01:38  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/12/18 07:14:41  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2008/05/16 08:13:26  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/03/18 12:39:08  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***
 		
 	
*/

function Footer_basis_L36($object)
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

   //   $pdfObject->customPageNo =1;
    }
    else
    {
      //echo "<br>".$pdfObject->page." - ". $pdfObject->customPageNo."<br>\n";
        $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
	      $pdfObject->SetY(-15);
        //$pdfObject->SetY(-7);
        $scenarioFooter="Aan deze opgave kunnen geen rechten worden ontleend. Er wordt onder meer gebruikt gemaakt van veronderstellingen ten aanzien van het verwachte rendement en de standaarddeviatie. De standaarddeviatie is een indicator voor risico en is gebaseerd op historische gegevens. Prognoses zijn geen betrouwbare indicator voor toekomstige resultaten. De waarde van beleggingen kan fluctueren. In het verleden behaalde resultaten bieden geen garantie voor de toekomst.";
	      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
        if(isset($pdfObject->alternativeFooterTxt))
        {
          $pdfObject->SetY(-17);
          $pdfObject->MultiCell(280,3,$pdfObject->alternativeFooterTxt,'0','L');
          unset($pdfObject->alternativeFooterTxt);
        }
        else       
          $pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');
	      $pdfObject->Cell(25,4,$pdfObject->rapport_voettext_rechts,'0','L',1);
        //$pdfObject->ln();
         $pdfObject->SetY(-7);
        $pdfObject->SetTextColor(255,255,255);
        $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
        $pdfObject->MultiCell(285,4," - ".$pdfObject->lastCustomPageNo." - ",0,'C');
        $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);

    }
    
    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }   
}

//function FooterFRONT_L36($object)
//{
//   $pdfObject=&$object;
//}





?>