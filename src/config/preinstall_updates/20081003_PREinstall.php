<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/12/24 11:31:56 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20081003_PREinstall.php,v $
 		Revision 1.1  2008/12/24 11:31:56  rvv
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


$tst->changeField("Remisiers","methode",array("Type"=>"int","Null"=>false)); 
$tst->changeField("Remisiers","percentage",array("Type"=>"double","Null"=>false)); 
$tst->changeField("Remisiers","btw",array("Type"=>"double","Null"=>false)); 
$tst->changeField("Remisiers","bodemVermogen",array("Type"=>"double","Null"=>false)); 

$tst->changeField("Vermogensbeheerders","ATT",array("Type"=>"tinyint(4)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","AfdrukvolgordeATT",array("Type"=>"tinyint(4)","Null"=>false)); 



?>