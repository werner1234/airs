<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/10/17 13:27:49 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20091007_PREinstall2.php,v $
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

$db = new DB();
$db2 = new DB();
$query="SELECT Orders.orderid, Orders.uitvoeringsDatum, Orders.uitvoeringsPrijs,Orders.aantal FROM Orders WHERE uitvoeringsPrijs > 0 AND aantal >0 AND  uitvoeringsDatum > '0000-00-00 00:00:00'";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
    $query="SELECT id FROM OrderUitvoering WHERE OrderUitvoering.orderid = '".$data['orderid']."' ";
    if($db2->QRecords($query) < 1 )
    {
      $query="INSERT INTO OrderUitvoering SET 
      orderid='".$data['orderid']."', 
      uitvoeringsAantal= '".$data['aantal']."', 
      uitvoeringsDatum= '".$data['uitvoeringsDatum']."', 
      uitvoeringsPrijs= '".$data['uitvoeringsPrijs']."',
      add_date=NOW(),change_date=NOW(),add_user='convert',change_user='convert'";
      $db2->SQL($query);
      $db2->Query();
    }
}
		 

 



?>