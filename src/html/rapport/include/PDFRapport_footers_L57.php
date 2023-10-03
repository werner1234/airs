<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/01/07 17:25:26 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PDFRapport_footers_L57.php,v $
 		Revision 1.1  2015/01/07 17:25:26  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/12/20 16:32:36  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/11/08 18:37:31  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:06:36  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/05/27 08:33:10  rvv
 		*** empty log message ***
 		
 	

*/

function Footer_basis_L57($object)
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
      if($pdfObject->last_rapport_type <> 'VHO')
      {
        $pdfObject->SetXY(8,202); 
        $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
        
        $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	      $pdfObject->Cell(25,4,$pdfObject->rapport_voettext,'0','L');
      }
      */
    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}

function FooterFRONT_L57($object)
{
   $pdfObject=&$object;
}

/*
function FooterVHO_L57($object)
{
   $pdfObject=&$object;
   $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
   $pdfObject->SetXY(8,202);
   $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
   $rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend. Koersen met een * gemarkeerd zijn minstens 1 dag oud.",$pdf->rapport_taal);
   $pdfObject->Cell(25,4,$rapport_voettext,'0','L');
}
*/





?>