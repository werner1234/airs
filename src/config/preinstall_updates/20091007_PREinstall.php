<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/10/17 13:27:49 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20091007_PREinstall.php,v $
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
$tst->changeField("Orders","fondsOmschrijving",array("Type"=>"varchar(50)","Null"=>false)); 

$queries[] ="UPDATE Orders SET fondsOmschrijving = Fonds";
$queries[] ="UPDATE Orders JOIN Fondsen ON Fondsen.Omschrijving=Orders.FondsOmschrijving SET Orders.Fonds = Fondsen.Fonds"; 
//$queries[] ="UPDATE Orders JOIN Fondsen ON Fondsen.Fonds=Orders.Fonds SET Orders.FondsOmschrijving = Fondsen.Omschrijving"; 
$queries[] ="CREATE INDEX  orderid  ON Orders (orderid)";
$queries[] ="CREATE INDEX  fonds  ON Orders  (fonds)";
$queries[] ="CREATE INDEX  laatsteStatus  ON Orders  (laatsteStatus)";
$queries[] ="CREATE INDEX  orderid  ON OrderUitvoering  (orderid)";
$queries[] ="CREATE INDEX  orderid  ON OrderRegels  (orderid)";
$tables['OrderUitvoering'] ="CREATE TABLE `OrderUitvoering` (
  `id` int(11) NOT NULL auto_increment,
  `orderid` varchar(16) NOT NULL,
  `uitvoeringsAantal` double NOT NULL,
  `uitvoeringsDatum` datetime NOT NULL,
  `uitvoeringsPrijs` double NOT NULL,
  `add_date` datetime NOT NULL,
  `add_user` varchar(10) NOT NULL,
  `change_date` datetime NOT NULL,
  `change_user` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `orderid` (`orderid`)
)";
$db = new DB();

foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE 'OrderUitvoering'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}


foreach($queries as $query)
{
  $db->SQL($query);
  $db->Query();
}


		 

 



?>