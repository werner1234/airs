<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/12/14 07:58:54 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20071214_PREinstall.php,v $
 		Revision 1.1  2007/12/14 07:58:54  rvv
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

$query['beursen']="CREATE TABLE `Beurzen` (
  `id` int(11) NOT NULL auto_increment,
  `beurs` varchar(4) default NULL,
  `omschrijving` varchar(60) default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
);";

$query['BbLandcodes']="CREATE TABLE `BbLandcodes` (
  `id` int(11) NOT NULL auto_increment,
  `bbLandcode` varchar(2) default NULL,
  `omschrijving` varchar(25) default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
);";




$DB= new DB();
foreach ($query as $tabel=>$query)
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

$tst->changeField("Fondsen","beurs",array("Type"=>"varchar(4)","Null"=>false)); 
$tst->changeField("Fondsen","bbLandcode",array("Type"=>"varchar(2)","Null"=>false)); 


?>