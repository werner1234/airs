<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/05/03 15:46:30 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140426_PREinstall.php,v $
 		Revision 1.1  2014/05/03 15:46:30  rvv
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
$tst->changeField("taken","standaardtaakId",array("Type"=>"int(11)","Null"=>false));

$db = new DB();
$query="UPDATE taken JOIN standaardTaken ON taken.kop = standaardTaken.taak SET taken.standaardtaakId=standaardTaken.id WHERE taken.standaardtaakId < 1";
$db->SQL($query);
$db->Query();

?>