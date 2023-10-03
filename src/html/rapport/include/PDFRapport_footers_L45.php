<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/10/12 15:54:06 $
 		File Versie					: $Revision: 1.5 $

 		$Log: PDFRapport_footers_L45.php,v $
 		Revision 1.5  2013/10/12 15:54:06  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/09/04 16:13:24  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/05/04 15:59:49  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/04/20 16:34:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/04/17 15:59:22  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/10/17 09:16:53  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/06/17 13:04:11  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/05/27 08:33:10  rvv
 		*** empty log message ***



*/

function Footer_basis_L45($object)
{
    $pdfObject=&$object;

    if ($pdfObject->nextFactuur == true )
    {
      $pdfObject->FooterFACTUUR();
      $pdfObject->nextFactuur = false;
    }
  

    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}

function FooterFRONT_L45($object)
{
   $pdfObject=&$object;


}







?>