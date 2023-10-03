<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/10/18 11:22:28 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140913_PREinstall.php,v $
 		Revision 1.1  2014/10/18 11:22:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/30 16:27:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","ScenarioGewenstProfiel",array("Type"=>"tinyint(3)","Null"=>false));
$tst->changeField("Risicoklassen","uitsluitenScenario",array("Type"=>"tinyint(3)","Null"=>false));
$tst->changeField("veldopmaak","formExtraTxt",array("Type"=>"text","Null"=>false));


?>