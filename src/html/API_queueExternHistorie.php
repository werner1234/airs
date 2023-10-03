<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/03/11 13:30:18 $
 		File Versie					: $Revision: 1.1 $

 		$Log: API_queueExternHistorie.php,v $
 		Revision 1.1  2019/03/11 13:30:18  cvs
 		call 7364
 		

 		
*/


include("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");
include_once("../classes/AE_cls_APIextern.php");
$apiExtern = new AE_cls_APIextern();

if ($_POST["datum"])
{
   $apiExtern->exportHistoryToCSV($_POST["datum"]);
}

