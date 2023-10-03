<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/01/20 17:52:51 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20081230_PREinstall.php,v $
 		Revision 1.1  2009/01/20 17:52:51  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/12/24 11:31:56  rvv
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
$tst->changeField("Vermogensbeheerders","CrmTerugRapportage",array("Type"=>"tinyint(4)","Null"=>false)); 

$query['klantMutaties']="  
CREATE TABLE `klantMutaties` (
  `id` bigint(20) NOT NULL auto_increment,
  `tabel` varchar(50) NOT NULL default '',
  `recordId` bigint(20) NOT NULL default '0',
  `veld` varchar(50) NOT NULL default '',
  `oudeWaarde` varchar(255) NOT NULL default '',
  `nieuweWaarde` varchar(255) NOT NULL default '',
  `verwerkt` tinyint(3) NOT NULL default '0',
  `Vermogensbeheerder` varchar(10) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(15) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) 
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
 

?>