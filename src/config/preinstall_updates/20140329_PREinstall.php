<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/03/29 16:25:05 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140329_PREinstall.php,v $
 		Revision 1.1  2014/03/29 16:25:05  rvv
 		*** empty log message ***
 		
	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","BeheerfeeAdministratieVergoedingVast",array("Type"=>"tinyint(3)","Null"=>false));
$tst->changeField("Fondsen","inflatieGekoppeld",array("Type"=>"tinyint(3)","Null"=>false));
$tst->changeField("CRM_naw_cashflow","totDoelvermogen",array("Type"=>"tinyint(3)","Null"=>false));



?>