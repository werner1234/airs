<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/09/27 13:35:24 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20070926_PREinstall.php,v $
 		Revision 1.1  2007/09/27 13:35:24  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/08/24 11:26:49  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2007/08/24 11:25:17  cvs
 		*** empty log message ***
 		
 
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();

$tst->changeField("HistorischePortefeuilleIndex","PortefeuilleWaarde",array("Type"=>"double","Null"=>false)); 
$tst->changeField("HistorischePortefeuilleIndex","Stortingen",array("Type"=>"double","Null"=>false)); 
$tst->changeField("HistorischePortefeuilleIndex","Onttrekkingen",array("Type"=>"double","Null"=>false)); 
$tst->changeField("HistorischePortefeuilleIndex","Opbrengsten",array("Type"=>"double","Null"=>false)); 
$tst->changeField("HistorischePortefeuilleIndex","Kosten",array("Type"=>"double","Null"=>false)); 





?>