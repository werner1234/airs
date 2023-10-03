<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/11/23 19:09:29 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20111105_PREinstall.php,v $
 		Revision 1.1  2011/11/23 19:09:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/10/23 13:29:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/08/07 09:06:42  rvv
 		*** empty log message ***

*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("CRM_naw_rekeningen","BICcode",array("Type"=>"varchar(20)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("CRM_naw","zoekveld",array("Type"=>"varchar(100)","Null"=>false,'Default'=>'default \'0\''));

?>