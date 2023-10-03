<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/01/20 12:28:53 $
 		File Versie					: $Revision: 1.5 $

 		$Log: PDFRapport_footers_L58.php,v $
 		Revision 1.5  2015/01/20 12:28:53  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/01/20 12:22:52  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/01/14 20:18:36  rvv
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

function Footer_basis_L58($object)
{
    $pdfObject=&$object;

    //if ($pdfObject->nextFactuur == true || ($pdfObject->rapport_type=='FACTUUR' && count($pdfObject->pages)==1) )
    //echo $pdfObject->CurOrientation."<br>\n";
    if ($pdfObject->nextFactuur == true || ($pdfObject->rapport_type=='FACTUUR' && count($pdfObject->pages)==1) )
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
      if($pdfObject->last_rapport_type <> 'VHO')
      {
        $pdfObject->SetXY(8,202); 
        $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
        $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	      $pdfObject->Cell(25,4,$pdfObject->rapport_voettext,'0','L');
      }
      $pdfObject->SetDrawColor(0,0,0);
      $pdfObject->Line($pdfObject->marge,200,297-$pdfObject->marge,200);
    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}

function FooterFRONT_L58($object)
{
   $pdfObject=&$object;
}


function FooterVHO_L58($object)
{
   $pdfObject=&$object;
   $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
   $pdfObject->SetXY(8,202);
   $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
   $rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend. Koersen met een * gemarkeerd zijn minstens 1 dag oud.",$pdf->rapport_taal);
   $pdfObject->Cell(25,4,$rapport_voettext,'0','L');
}






?>