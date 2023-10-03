<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/09/25 15:57:42 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20130814_PREinstall.php,v $
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
$tst->changeField("TijdelijkeBulkOrders","koersLimiet",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("TijdelijkeBulkOrders","pagina",array("Type"=>"int(11)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("TijdelijkeBulkOrders","checkResult",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("TijdelijkeBulkOrders","checkResultRegels",array("Type"=>"text","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("TijdelijkeBulkOrders","statusLog",array("Type"=>"text","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("TijdelijkeBulkOrders","regelNr",array("Type"=>"int(11)","Null"=>false,'Default'=>'default \'0\''));

?>