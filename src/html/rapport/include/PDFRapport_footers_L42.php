<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/12/17 16:14:40 $
 		File Versie					: $Revision: 1.5 $

 		$Log: PDFRapport_footers_L42.php,v $
 		Revision 1.5  2014/12/17 16:14:40  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/12/06 18:13:44  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/08/24 15:48:47  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/07/28 09:59:15  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/01/27 14:14:24  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/12/02 11:05:56  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/10/17 15:55:14  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/10/07 14:57:17  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/09/16 12:45:46  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/05/27 08:33:10  rvv
 		*** empty log message ***
 		
 	

*/

function Footer_basis_L42($object)
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
// geen footer

    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function FooterFRONT_L42($object)
{
   $pdfObject=&$object;
}

function FooterOIB_L42($object)
{
   $pdfObject=&$object;
}

function FooterPERF_L42($object)
{
   $pdfObject=&$object;
}

function FooterTRANS_L42($object)
{
   $pdfObject=&$object;
}

function FooterMUT_L42($object)
{
   $pdfObject=&$object;
}

function FooterVHO_L42($object)
{
   $pdfObject=&$object;
   //echo $pdfObject->GetY()."<br>\n"; $this->pdf->)
   $pdfObject->SetXY(80,202);
     $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
  $pdfObject->MultiCell(200,4,"Koersen met een * zijn niet actueel.", 0, "L");
}

function FooterVOLK_L42($object)
{
   $pdfObject=&$object;
   //echo $pdfObject->GetY()."<br>\n";
   $pdfObject->SetXY(80,202);
     $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
  $pdfObject->MultiCell(200,4,"Koersen met een * zijn niet actueel.", 0, "L");
}

?>