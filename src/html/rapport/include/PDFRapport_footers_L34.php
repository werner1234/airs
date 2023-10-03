<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/09/05 18:19:11 $
 		File Versie					: $Revision: 1.3 $

 		$Log: PDFRapport_footers_L34.php,v $
 		Revision 1.3  2012/09/05 18:19:11  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2011/11/16 19:22:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/11/05 16:05:17  rvv
 		*** empty log message ***

 		Revision 1.5  2011/04/13 09:54:38  rvv
 		*** empty log message ***

 		Revision 1.4  2011/04/09 14:35:27  rvv
 		*** empty log message ***

 		Revision 1.3  2011/03/17 05:01:11  rvv
 		*** empty log message ***

 		Revision 1.2  2011/02/13 17:50:29  rvv
 		*** empty log message ***

 		Revision 1.1  2011/02/06 14:36:59  rvv
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

function Footer_basis_L34($object)
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
      if($pdfObject->rapport_type == "FRONT" && $pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
        $pdfObject->customPageNo =1;
    }

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
	 $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
   if(is_array($pdfObject->pageTop))
   {
     if(!is_array($pdfObject->pageBottom))
       $pdfObject->pageBottom=array($pdfObject->pageTop[0],$pdfObject->GetY());
     $pdfObject->Line($pdfObject->pageTop[0],$pdfObject->pageTop[1],$pdfObject->pageBottom[0],$pdfObject->pageBottom[1]);
     unset($pdfObject->pageTop);
     unset($pdfObject->pageBottom);
   }
       $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

}

function FooterFRONT_L34($object)
{
  $pdfObject=&$object;
}









?>