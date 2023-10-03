<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/08/09 15:05:04 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140726_PREinstall.php,v $
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/06/11 15:40:17  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/05/25 14:36:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/05/03 15:46:30  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/19 16:14:54  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/02 15:54:35  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/03/29 16:25:05  rvv
 		*** empty log message ***
 		
	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("StandaardVeldVulling","waarde",array("Type"=>"text","Null"=>false));
$tst->changeField("CRM_eigenVelden","uitlijning",array("Type"=>"varchar(1)","Null"=>false));
$tst->changeField("CRM_eigenVelden","getalformat",array("Type"=>"varchar(3)","Null"=>false));
$tst->changeField("Orders","giraleOrder",array("Type"=>"tinyint(3)","Null"=>false));
?>