<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/01/23 07:31:23 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20080123_PREinstall.php,v $
 		Revision 1.1  2008/01/23 07:31:23  rvv
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

//$tst->changeField("Vermogensbeheerders","naamInExport",array("Type"=>"tinyint(4)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","uitgebreideAutoupdate",array("Type"=>"tinyint(4)","Null"=>false)); 
$tst->changeField("Gebruikers","Beheerder",array("Type"=>"tinyint(4)","Null"=>false)); 


?>