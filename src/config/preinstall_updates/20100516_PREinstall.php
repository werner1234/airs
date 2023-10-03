<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/05/16 11:05:48 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100516_PREinstall.php,v $
 		Revision 1.1  2010/05/16 11:05:48  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/24 19:15:41  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("CRM_naw","tel4_oms",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","tel4",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","tel5_oms",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","tel5",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","tel6_oms",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","tel6",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","tel_toev1",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","tel_toev2",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","tel_toev3",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","tel_toev4",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","tel_toev5",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","tel_toev6",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","emailPartner",array("Type"=>"tinytext","Null"=>false)); 
$tst->changeField("CRM_naw","emailPartnerZakelijk",array("Type"=>"tinytext","Null"=>false)); 
$tst->changeField("CRM_naw","verzendPaAanhef",array("Type"=>"tinytext","Null"=>false)); 
$tst->changeField("CRM_naw","maandrapportage",array("Type"=>"text","Null"=>false)); 
$tst->changeField("CRM_naw","kwartaalrapportage",array("Type"=>"text","Null"=>false));
$tst->changeField("CRM_naw","halfjaarrapportage",array("Type"=>"text","Null"=>false)); 
$tst->changeField("CRM_naw","jaarrapportage",array("Type"=>"text","Null"=>false)); 
	


?>