<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/21 07:49:56 $
 		File Versie					: $Revision: 1.4 $

 		$Log: PDFRapport_footers_L39.php,v $
 		Revision 1.4  2020/05/21 07:49:56  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/10/17 09:16:53  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/06/17 13:04:11  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/05/27 08:33:10  rvv
 		*** empty log message ***



*/

function Footer_basis_L39($object)
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
	    $pdfObject->SetY(-10);
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
	    if($pdfObject->rapport_type <> 'FACTUUR'&& $pdfObject->rapport_type <> 'HUIS')
	      $pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');
	    $pdfObject->SetY(-10);
	    $pdfObject->MultiCell(280,4,$pdfObject->customPageNo,'0','R');
//vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".

    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}


?>