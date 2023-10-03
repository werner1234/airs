<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/01/04 17:10:04 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20131223_PREinstall.php,v $
 		Revision 1.1  2014/01/04 17:10:04  rvv
 		*** empty log message ***
 		
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Fondsen","KoersAltijdAanvragen",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));


?>