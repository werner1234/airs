<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/10/12 15:46:00 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20131012_PREinstall.php,v $
 		Revision 1.1  2013/10/12 15:46:00  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/08/21 15:32:58  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/22 06:35:57  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tables['emailLog'] ="CREATE TABLE `emailLog` (
  `id` int(11) NOT NULL auto_increment,
  `zender` varchar(150) NOT NULL default '',
  `ontvangers` varchar(150) NOT NULL default '',
  `onderwerp` varchar(150) NOT NULL default '',
  `verzonden` tinyint NOT NULL default '0',
  `vanaf` varchar(50) NOT NULL default '',
  `foutmelding` varchar(200) NOT NULL default '',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `add_date` (`add_date`)
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