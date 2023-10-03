<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/02/22 10:08:37 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20150221_PREinstall.php,v $
 		Revision 1.1  2015/02/22 10:08:37  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/02/15 10:39:12  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/01/24 20:02:43  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/01/04 13:14:14  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/12/13 12:34:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		

	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$db = new DB();
$query="ALTER TABLE `emailQueue` ADD INDEX `crmId` (`crmId`)";
$db->SQL($query);
$db->Query();

$tst = new SQLman();
$tst->changeField("CRM_naw_cashflow","indexatie",array("Type"=>"double","Null"=>false));

$tables['Rendementsheffing']="CREATE TABLE `Rendementsheffing` (
  `id` int(11) NOT NULL auto_increment,
  `jaar` date default '0000-00-00',
  `vrijstellingEenP` double NOT NULL default '0',
  `vrijstellingTweeP` double NOT NULL default '0',
  `percentage` double NOT NULL default '0',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
)
";

foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}
?>