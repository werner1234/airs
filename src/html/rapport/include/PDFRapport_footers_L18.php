<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/06/29 16:52:23 $
 		File Versie					: $Revision: 1.5 $

 		$Log: PDFRapport_footers_L18.php,v $
 		Revision 1.5  2011/06/29 16:52:23  rvv
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

function Footer_basis_L18($object)
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
    }
    else
    {
      $pdfObject->Rect(8,200,280,2,'F','F',$pdfObject->rapport_voet_bgcolor);
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->SetY(-20);
      $y = $pdfObject->getY();
      $pdfObject->MultiCell(260,4,vertaalTekst($pdfObject->last_rapport_titel,$pdfObject->rapport_taal),0,'R');

      $pdfObject->Rect(272,$y-1,6,6,'F','F',$pdfObject->rapport_voet_bgcolor);
      $pdfObject->SetXY(270,$y);

      $pdfObject->SetTextColor(255,255,255);
      $pdfObject->MultiCell(10,4,vertaalTekst($pdfObject->customPageNo,$pdfObject->rapport_taal),0,'C');

      $pdfObject->customPageNo++;
      if($pdfObject->rapport_type == "FRONT" && $pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
        $pdfObject->customPageNo =2;
    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}

function FooterFRONT_L18($object)
{
  $pdfObject=&$object;
  $pdfObject->Rect(8,200,280,2,'F','F',$pdfObject->rapport_voet_bgcolor);
}







?>