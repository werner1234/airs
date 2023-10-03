<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/01/26 17:18:16 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20110105_PREinstall.php,v $
 		Revision 1.1  2011/01/26 17:18:16  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
		 
$tst = new SQLman();

$tst->changeField("Portefeuilles","BeheerfeeLiquiditeitenViaModel",array("Type"=>"tinyint(4)","Null"=>false)); 
$tst->changeField("custom_txt","extraKoppeling",array("Type"=>"varchar(255)","Null"=>false)); 




?>