<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/10/12 16:30:27 $
 		File Versie					: $Revision: 1.2 $

 		$Log: PDFRapport_footers_L7.php,v $
 		Revision 1.2  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/12/21 08:22:32  rvv
 		*** empty log message ***
 		


*/

function Footer_basis_L7($object)
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

function FooterFRONT_L7($object)
{
   $pdfObject=&$object;


}

function FooterFACTUUR_L7($object)
{
  $pdfObject=&$object;


}





?>