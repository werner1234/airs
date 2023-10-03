<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/05/06 10:18:42 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20080506_PREinstall.php,v $
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


$query['custom_txt']="  
  CREATE TABLE `custom_txt` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `txt` text,
  `field` varchar(30) NOT NULL default '',
  `type` varchar(30) NOT NULL default '',
  `Vermogensbeheerder` varchar(10) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(15) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`));
  ";
$query['IndexPerBeleggingscategorie']="  
CREATE TABLE `IndexPerBeleggingscategorie` (
  `id` int(11) NOT NULL auto_increment,
  `Vermogensbeheerder` varchar(10) default NULL,
  `Beleggingscategorie` varchar(15) default NULL,
  `Fonds` varchar(25) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
);
  ";
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
$tst->changeField("Fondsen","optieCode",array("Type"=>"varchar(30)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","txtKoppeling",array("Type"=>"varchar(30)","Null"=>false)); 








?>