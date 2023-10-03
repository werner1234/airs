<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/02/14 08:59:58 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20080214_PREinstall.php,v $
 		Revision 1.1  2008/02/14 08:59:58  rvv
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

$queries['AutoRun']="CREATE TABLE `AutoRun` (
  `id` int(11) NOT NULL auto_increment,
  `Vermogensbeheerder` varchar(10) NOT NULL default '',
  `Rapportage` varchar(25) NOT NULL default '',
  `BestandsNaam` varchar(25) NOT NULL default '',
  `Trigger` varchar(25) NOT NULL default '',
  `Export_pad` varchar(255) NOT NULL default '',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
);";


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


//$tst = new SQLman();

//$tst->changeField("Grootboekrekeningen","FondsGebruik",array("Type"=>"tinyint(4)","Null"=>false)); 




?>