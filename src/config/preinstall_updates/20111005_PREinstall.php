<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/11/23 19:09:29 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20111005_PREinstall.php,v $
 		Revision 1.1  2011/11/23 19:09:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/01/26 17:18:16  rvv
 		*** empty log message ***


*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Orders","batchId",array("Type"=>"int(11)","Null"=>false));

$Column_name=array();

$db=new DB();
$query="SHOW KEYS IN Orders";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
  $ColName[]=$data['Column_name'];

if(count($ColName) > 0 && !(in_array('batchId',$ColName)))
{
  $query="CREATE INDEX batchId ON Orders (batchId)";
  $db->SQL($query);
  $db->Query();
}

?>