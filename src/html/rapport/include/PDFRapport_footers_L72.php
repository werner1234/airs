<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/08/05 17:25:06 $
 		File Versie					: $Revision: 1.2 $

 		$Log: PDFRapport_footers_L72.php,v $
 		Revision 1.2  2017/08/05 17:25:06  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/09/28 15:53:55  rvv
 		*** empty log message ***
 		
 
*/

function Footer_basis_L72($object)
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

function FooterFRONT_L72($object)
{
  $pdfObject=&$object;
}



function FooterORDERPC_L72()
{

}






?>