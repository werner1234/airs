<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/10/26 16:07:44 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PDFRapport_footers_L10.php,v $
 		Revision 1.1  2019/10/26 16:07:44  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/12/21 08:22:32  rvv
 		*** empty log message ***
 		


*/

function Footer_basis_L10($object)
{
    $pdfObject=&$object;

    if ($pdfObject->nextFactuur == true )
    {
      //$pdfObject->FooterFACTUUR();
      $pdfObject->nextFactuur = false;
    }


    if ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->nextFactuur = true;
    }
}

function FooterFRONT_L10($object)
{
   $pdfObject=&$object;


}

function FooterFACTUUR_L10($object)
{
  $pdfObject=&$object;


}


?>