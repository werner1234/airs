<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/01/31 16:05:10 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20070131_PREinstall.php,v $
 		Revision 1.1  2007/01/31 16:05:10  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/21 16:10:31  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/11 10:58:12  rvv
 		modelportefeuille
 		
 		Revision 1.1  2006/12/07 16:10:48  rvv
 		*** empty log message ***
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("TijdelijkeRapportage","AttributieCategorie",array("Type"=>"varchar(15)","Null"=>false)); 
$tst->changeField("Fondsen","snsCode",array("Type"=>"varchar(25)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","attributieInPerformance",array("Type"=>"tinyint(4)","NULL"=>false));
$tst->changeField("BeleggingscategoriePerFonds","grafiekKleur",array("Type"=>"text","Null"=>false)); 




?>