<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/11/23 19:09:29 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110602_PREinstall.php,v $
 		Revision 1.1  2011/11/23 19:09:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/05/25 11:09:36  rvv
 		*** empty log message ***

 		Revision 1.1  2011/05/04 16:25:11  rvv
 		*** empty log message ***


*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tables['emailQueue']="CREATE TABLE `emailQueue` (
  `id` int(11) NOT NULL auto_increment,
  `crmId` int(11) NOT NULL default '0',
  `status` varchar(20) NOT NULL default '',
  `senderName` varchar(100) NOT NULL default '',
  `senderEmail` varchar(100) NOT NULL default '',
  `receiverName` varchar(100) NOT NULL default '',
  `receiverEmail` varchar(100) NOT NULL default '',
  `subject` varchar(200)  NOT NULL default '',
  `bodyHtml` text NOT NULL,
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
);";

$tables['emailQueueAttachments']="CREATE TABLE `emailQueueAttachments` (
  `id` int(11) NOT NULL auto_increment,
  `emailQueueId` int(11) NOT NULL default '0',
  `filename` varchar(200) NOT NULL,
  `attachment` blob NOT NULL,
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `emailQueueId` (`emailQueueId`)
);";

$db=new DB();
foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}

?>