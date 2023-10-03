<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/01/10 08:44:09 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100109_PREinstall.php,v $
 		Revision 1.1  2010/01/10 08:44:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/12/20 14:30:43  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/12/13 17:24:46  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/11/15 16:51:06  rvv
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
$tst->changeField("Fondsen","variabeleCoupon",array("Type"=>"tinyint(1)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","ddInleesPortefeuillePreg",array("Type"=>"varchar(200)","Null"=>false)); 
 

?>