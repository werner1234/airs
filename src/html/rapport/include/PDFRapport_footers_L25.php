<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/02/01 18:11:55 $
 		File Versie					: $Revision: 1.6 $

 		$Log: PDFRapport_footers_L25.php,v $
 		Revision 1.6  2020/02/01 18:11:55  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2017/04/08 18:22:43  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/12/03 19:22:25  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/01/27 17:07:24  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/01/24 09:52:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/01/03 09:16:56  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/12/21 08:22:32  rvv
 		*** empty log message ***
 		


*/

function Footer_basis_L25($object)
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
    elseif($pdfObject->frontPage == true)
    {
      $pdfObject->frontPage=false;
    }
}

function FooterFRONT_L25($object)
{
   $pdfObject=&$object;
}

?>