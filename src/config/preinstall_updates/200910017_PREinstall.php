<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/10/17 15:43:09 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 200910017_PREinstall.php,v $
 		Revision 1.1  2009/10/17 15:43:09  rvv
 		*** empty log message ***
 		
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
$tst->changeField("OrderRegels","interneNummer",array("Type"=>"varchar(25)","Null"=>false)); 

$tables['FactuurHistorie'] ="CREATE TABLE `FactuurHistorie` (
  `id` int(11) NOT NULL auto_increment,
  `portefeuille` varchar(12) NOT NULL,
  `factuurNr` varchar(20) NOT NULL,
  `periodeDatum` datetime NOT NULL,
  `grondslag` double NOT NULL,
  `fee` double NOT NULL,
  `btw` double NOT NULL,
  `totaalIncl` double NOT NULL,
  `add_date` datetime NOT NULL,
  `add_user` varchar(10) NOT NULL,
  `change_date` datetime NOT NULL,
  `change_user` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `portefeuille` (`portefeuille`),
  KEY `periodeDatum` (`periodeDatum`)
)";
$db = new DB();

foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}


		 

 



?>