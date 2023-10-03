<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/03/25 14:00:31 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20080221_PREinstall.php,v $
 		Revision 1.1  2008/03/25 14:00:31  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/10/09 06:23:57  cvs
 		gebruikerstabel ivm CRM
 		
 		Revision 1.1  2007/09/27 13:35:24  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/08/24 11:26:49  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2007/08/24 11:25:17  cvs
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

$query = "CREATE TABLE CRM_naw_copy LIKE CRM_naw;" ;
$db->SQL($query);
$db->Query();
$query = "INSERT CRM_naw_copy SELECT * FROM CRM_naw;";
$db->SQL($query);
$db->Query();

$query = "CREATE TABLE CRM_naw_cf_copy LIKE CRM_naw_cf;";
$db->SQL($query);
$db->Query();
$query = "INSERT CRM_naw_cf_copy SELECT * FROM CRM_naw_cf;";
$db->SQL($query);
$db->Query();

$query = "
UPDATE CRM_naw, CRM_naw_cf SET
   CRM_naw.verzendAdres  = CRM_naw_cf.verzendAdres
 , CRM_naw.verzendAanhef = CRM_naw_cf.verzendAanhef
 , CRM_naw.verzendPc     = CRM_naw_cf.verzendPc
 , CRM_naw.verzendPlaats = CRM_naw_cf.verzendPlaats
 , CRM_naw.verzendLand   = CRM_naw_cf.verzendLand
WHERE 
CRM_naw.id = CRM_naw_cf.rel_id AND
CRM_naw.verzendAdres = '' AND
CRM_naw.verzendAanhef =  '' AND
CRM_naw.verzendPc     =  '' AND
CRM_naw.verzendPlaats =  '' AND
CRM_naw.verzendLand  =  '' ;
";

$db->SQL($query);
$db->Query();







?>