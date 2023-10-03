<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/04/22 13:40:57 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: 20080422_PREinstall.php,v $
 		Revision 1.2  2008/04/22 13:40:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/04/22 13:23:29  rvv
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


$queries['Rating']="CREATE TABLE `Rating` (
  `id` int(11) NOT NULL auto_increment,
  `rating` varchar(26) default NULL,
  `omschrijving` varchar(60) default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`);
  ";

$DB= new DB();
foreach ($queries as $tabel=>$query)
{
      $testQuery = "SHOW TABLES LIKE '".$tabel."'";
      $DB->SQL($testQuery);
      $DB->Query();
      if($DB->records() == 0)
      {
         $DB->SQL($query);
         $DB->Query();
      }
}

$tst = new SQLman();
$tst->changeField("Fondsen","rating",array("Type"=>"varchar(26)","Null"=>false)); 









?>