<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/08/07 09:06:42 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110727_PREinstall.php,v $
 		Revision 1.1  2011/08/07 09:06:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/07/23 17:21:11  rvv
 		*** empty log message ***

 		Revision 1.1  2011/06/25 20:07:27  rvv
 		*** empty log message ***

*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("CRM_naw_dossier","clientGesproken",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));

$db = new DB();
$query="UPDATE CRM_naw_dossier SET clientGesproken=1";
$db->SQL($query);
$db->Query();


?>