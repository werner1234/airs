<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/10/28 14:37:00 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 200910021_PREinstall.php,v $
 		Revision 1.1  2009/10/28 14:37:00  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/10/17 15:43:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/10/17 13:27:49  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/06 10:18:42  rvv
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
$tst->changeField("CRM_naw_kontaktpersoon","adres",array("Type"=>"varchar(200)","Null"=>false)); 
$tst->changeField("CRM_naw_kontaktpersoon","pc",array("Type"=>"varchar(17)","Null"=>false)); 
$tst->changeField("CRM_naw_kontaktpersoon","plaats",array("Type"=>"varchar(30)","Null"=>false)); 
$tst->changeField("CRM_naw_kontaktpersoon","land",array("Type"=>"varchar(25)","Null"=>false)); 





?>