<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/11/13 15:53:45 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20131113_PREinstall.php,v $
 		Revision 1.1  2013/11/13 15:53:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/09/25 15:57:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/08/04 10:46:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/31 15:54:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/22 06:35:57  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Depotbanken","IbanVoorloop",array("Type"=>"varchar(32)","Null"=>false,'Default'=>'default \'\''));

?>