<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/07/11 16:02:37 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100711_PREinstall.php,v $
 		Revision 1.1  2010/07/11 16:02:37  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
		 
$tst = new SQLman();

$tst->changeField("CRM_naw","rapportageVinkSelectie",array("Type"=>"text","Null"=>false)); 
$tst->changeField("GrootboekPerVermogensbeheerder","Onttrekking",array("Type"=>"tinyint(4)","Null"=>false)); 




?>