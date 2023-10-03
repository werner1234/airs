<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/11/23 19:09:29 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20111119_PREinstall.php,v $
 		Revision 1.1  2011/11/23 19:09:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/10/23 13:29:57  rvv
 		*** empty log message ***

 		Revision 1.1  2011/08/07 09:06:42  rvv
 		*** empty log message ***

*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Orders","OrderSoort",array("Type"=>"varchar(1)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Vermogensbeheerders","module_bestandsvergoeding",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("BestandsvergoedingPerPortefeuille","Fonds",array("Type"=>"varchar(25)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Fondsen","standaardSector",array("Type"=>"varchar(15)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Beleggingssectoren","standaard",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));


$db=new DB();
$db2=new DB();
$query="SELECT id,batchId FROM Orders";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
    $queryC="SELECT id FROM Orders WHERE batchId = '".$data['batchId']."' AND batchId > 0 ";
    $queryE="SELECT OrderRegels.id FROM Orders JOIN OrderRegels ON OrderRegels.orderid = Orders.orderid WHERE Orders.id = '".$data['id']."'";
    if($db2->QRecords($queryC) > 1)
      $type='C';
    elseif($db2->QRecords($queryE) == 1)
      $type='E';
    else
      $type='M';

    $query="UPDATE Orders SET OrderSoort='$type' WHERE id='".$data['id']."'";
    $db2->SQL($query);
    $db2->Query();
}

?>