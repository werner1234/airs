<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/07/21 17:36:35 $
 		File Versie					: $Revision: 1.4 $

 		$Log: PDFRapport_footers_L26.php,v $
 		Revision 1.4  2010/07/21 17:36:35  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2010/06/09 16:40:14  rvv
 		*** empty log message ***

 		Revision 1.2  2010/05/30 12:46:25  rvv
 		*** empty log message ***

 		Revision 1.1  2010/05/26 17:12:39  rvv
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

function Footer_basis_L26($object)
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
      $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
	  //  $pdfObject->SetY(-10);
	  //  $pdfObject->AliasNbPages('{customPageNo}');
	  //  $pdfObject->MultiCell(282,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo." van {customPageNo}",'0','R');
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

function FooterFRONT_L26($object)
{
   $pdfObject=&$object;


}







?>