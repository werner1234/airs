<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/06/13 14:34:33 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110611_PREinstall.php,v $
 		Revision 1.1  2011/06/13 14:34:33  rvv
 		*** empty log message ***
 		

*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Rating","Afdrukvolgorde",array("Type"=>"tinyint(4)","Null"=>false));
$tst->changeField("emailQueue","ccEmail",array("Type"=>"varchar(100)","Null"=>false));
$tst->changeField("emailQueueAttachments","attachment",array("Type"=>"mediumblob","Null"=>false));


?>