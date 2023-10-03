<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/07/02 10:02:23 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20090702_PREinstall.php,v $
 		Revision 1.1  2009/07/02 10:02:23  rvv
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
$tst->changeField("CRM_naw","beleggingsDoelstelling",array("Type"=>"varchar(100)","Null"=>false)); 


?>