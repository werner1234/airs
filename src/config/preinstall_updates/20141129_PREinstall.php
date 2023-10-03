<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/12/06 18:07:08 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20141129_PREinstall.php,v $
 		Revision 1.1  2014/12/06 18:07:08  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		

	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","OrderuitvoerBewaarder",array("Type"=>"tinyint","Null"=>false));
$tst->changeField("klantMutaties","emailAdres",array("Type"=>"varchar(50)","Null"=>false));

?>