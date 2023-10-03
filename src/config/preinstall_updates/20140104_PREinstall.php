<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/01/04 17:10:04 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140104_PREinstall.php,v $
 		Revision 1.1  2014/01/04 17:10:04  rvv
 		*** empty log message ***
 		
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Portefeuilles","SoortOvereenkomst",array("Type"=>"varchar(30)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("laatstePortefeuilleWaarde","kansOpDoelvermogen",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));

  





?>