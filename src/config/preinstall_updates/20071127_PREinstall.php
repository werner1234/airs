<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2007/11/27 13:35:24 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20071127_PREinstall.php,v $
 		Revision 1.1  2007/11/27 13:35:24  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2007/11/09 07:29:42  rvv
 		*** empty log message ***
 		

 		
 
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();

$tst->changeField("CRM_naw","verzendAanhef",array("Type"=>"tinytext","Null"=>false)); 
$tst->changeField("CRM_naw","verzendAdres",array("Type"=>"tinytext","Null"=>false)); 
$tst->changeField("CRM_naw","verzendPc",array("Type"=>"varchar(17)","Null"=>false)); 
$tst->changeField("CRM_naw","verzendPlaats",array("Type"=>"varchar(30)","Null"=>false)); 
$tst->changeField("CRM_naw","verzendLand",array("Type"=>"varchar(25)","Null"=>false)); 
$tst->changeField("CRM_naw","beroep",array("Type"=>"varchar(30)","Null"=>false)); 
$tst->changeField("CRM_naw","part_beroep",array("Type"=>"varchar(30)","Null"=>false)); 

$db = new DB();

$query = "
UPDATE CRM_naw, CRM_naw_cf SET
   CRM_naw.verzendAdres  = CRM_naw_cf.verzendAdres
 , CRM_naw.verzendAanhef = CRM_naw_cf.verzendAanhef
 , CRM_naw.verzendPc     = CRM_naw_cf.verzendPc
 , CRM_naw.verzendPlaats = CRM_naw_cf.verzendPlaats
 , CRM_naw.verzendLand   = CRM_naw_cf.verzendLand
WHERE CRM_naw.id = CRM_naw_cf.rel_id
";

$db->SQL($query);

$db->Query();

?>