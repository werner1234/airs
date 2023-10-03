<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/07/04 16:05:11 $
 		File Versie					: $Revision: 1.3 $

 		$Log: PDFRapport_footers_L37.php,v $
 		Revision 1.3  2012/07/04 16:05:11  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/05/30 16:02:38  rvv
 		*** empty log message ***

 		Revision 1.1  2012/05/27 08:33:10  rvv
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

function Footer_basis_L37($object)
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

   $pdfObject->setY(-15);
   $pdfObject->SetTextColor(150,150,150);
   $pdfObject->SetFont($pdfObject->rapport_font,'',8);
   $pdfObject->MultiCell(280,4,"P.O. Box 6012 • 3600 HA Maarssen • The Netherlands • address: Straatweg 33 • Maarssen\nTelephone +31(0)346-557030 • Telefax +31(0)346-557031 • info@dijkstrabeaumont.nl • KvK Utrecht 30211329\nwww.dijkstrabeaumont.nl",0,'C');
   $pdfObject->setY(-15);
   $pdfObject->SetTextColor(192,45,56);
   $pdfObject->SetFont($pdfObject->rapport_font,'',8);
   $footerLines=explode("\n","P.O. Box 6012 • 3600 HA Maarssen • The Netherlands • address: Straatweg 33 • Maarssen\nTelephone +31(0)346-557030 • Telefax +31(0)346-557031 • info@dijkstrabeaumont.nl • KvK Utrecht 30211329\nwww.dijkstrabeaumont.nl");

   $xpos=6;
   foreach ($footerLines as $index=>$line)
   {
     $newx=$xpos+(140)-(0.5*$pdfObject->GetStringWidth($line));
     $woorden=explode("•",$line);
     foreach ($woorden as $woordIndex=>$woord)
     {
         $stringWidth=$pdfObject->GetStringWidth($woord.'•');
         $newx+=$stringWidth;
         $pdfObject->setX($newx);
         if($woordIndex <> count($woorden)-1)
           $pdfObject->Cell(5,4,'•');
     }
     $pdfObject->ln(4);
   }


//listarray($newString);exit;
 //  $pdfObject->MultiCell(280,4,$newString,0,'C');
 //  $pdfObject->CurrentFont['cw'][' ']=$olsSpace;


$pdfObject->SetTextColor(0,0,0);
	     	     $pdfObject->SetFillColor(0,0,0);
}

function FooterFRONT_L34($object)
{
  $pdfObject=&$object;
}









?>