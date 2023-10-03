<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/11/23 19:09:29 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20111012_PREinstall.php,v $
 		Revision 1.1  2011/11/23 19:09:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/08/07 09:06:42  rvv
 		*** empty log message ***

 		Revision 1.1  2011/07/23 17:21:11  rvv
 		*** empty log message ***

 		Revision 1.1  2011/06/25 20:07:27  rvv
 		*** empty log message ***

*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst->changeField("CRM_naw","autoType",array("Type"=>"varchar(100)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("CRM_naw","autoBouwjaar",array("Type"=>"varchar(10)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("CRM_naw","autoKenteken",array("Type"=>"varchar(10)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("CRM_naw","BIC",array("Type"=>"varchar(20)","Null"=>false,'Default'=>'default \'\''));

?>