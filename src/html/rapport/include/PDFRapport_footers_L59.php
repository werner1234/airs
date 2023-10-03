<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/09/24 09:55:46 $
 		File Versie					: $Revision: 1.4 $

 		$Log: PDFRapport_footers_L59.php,v $
 		Revision 1.4  2015/09/24 09:55:46  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/07/22 13:16:13  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/01/31 20:03:27  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/01/28 17:04:14  rvv
 		*** empty log message ***
 		
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

function Footer_basis_L59($object)
{
   $pdfObject=&$object;
    
    
   $pdfObject->setXY($pdfObject->marge,-20);
   $pdfObject->SetTextColor(100,100,100);
   $pdfObject->SetFont($pdfObject->rapport_font,'',8);
   $voettekst="De Veste | Liesboslaan 57a | 4813 EB Breda | The Netherlands\n Telefoon +31 (0)76 523 66 00 | Fax +31 (0)164 673 946 | E-mail info@deveste.net | Internet www.deveste.net\n IBAN NL89 RABO 0184 3206 66 | BIC RABONL2U | K.v.K nr. 20095512";
   
   if($pdfObject->CurOrientation=='P')
     $pageWidth=210;
   else
     $pageWidth=290;
       
   $width=$pageWidth-$pdfObject->marge*2;
   $pdfObject->MultiCell($width,4,$voettekst,0,'C');
   $pdfObject->setY(-20);
   $pdfObject->SetTextColor(52,116,188);
   $pdfObject->SetFont($pdfObject->rapport_font,'',8);
   $footerLines=explode("\n",$voettekst);

   $xpos=5.5;
   foreach ($footerLines as $index=>$line)
   {
     $newx=$xpos+($width/2)-(0.5*$pdfObject->GetStringWidth($line));
     $woorden=explode("|",$line);
     foreach ($woorden as $woordIndex=>$woord)
     {
         $stringWidth=$pdfObject->GetStringWidth($woord.'•');
         $newx+=$stringWidth;
         $pdfObject->setX($newx);
         if($woordIndex <> count($woorden)-1)
           $pdfObject->Cell(5,4,'|');
     }
     $pdfObject->ln(4);
   }

}

function FooterParticipatie_L59($object)
{
   $pdfObject=&$object;
   
}








?>