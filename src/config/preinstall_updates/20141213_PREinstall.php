<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/12/13 19:08:09 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20141213_PREinstall.php,v $
 		Revision 1.1  2014/12/13 19:08:09  rvv
 		*** empty log message ***
 		

	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("GeconsolideerdePortefeuilles","SoortOvereenkomst",array("Type"=>"varchar(30)","Null"=>false));
$tst->changeField("GeconsolideerdePortefeuilles","SpecifiekeIndex",array("Type"=>"varchar(25)","Null"=>false));
$tst->changeField("GeconsolideerdePortefeuilles","ModelPortefeuille",array("Type"=>"varchar(24)","Null"=>false));
$tst->changeField("GeconsolideerdePortefeuilles","ZpMethode",array("Type"=>"tinyint(3)","Null"=>false));

?>