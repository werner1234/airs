<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/08/31 15:22:34 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110817_PREinstall.php,v $
 		Revision 1.1  2011/08/31 15:22:34  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/08/07 09:06:42  rvv
 		*** empty log message ***

 		Revision 1.1  2011/07/23 17:21:11  rvv
 		*** empty log message ***

 		Revision 1.1  2011/06/25 20:07:27  rvv
 		*** empty log message ***

*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tables['trackAndTrace']="CREATE TABLE `trackAndTrace` (
  `id` bigint(20) NOT NULL auto_increment,
  `tabel` varchar(50) NOT NULL default '',
  `recordId` bigint(20) NOT NULL default '0',
  `veld` varchar(50) NOT NULL default '',
  `oudeWaarde` varchar(255) NOT NULL default '',
  `nieuweWaarde` varchar(255) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `add_date` (`add_date`),
  KEY `tabelId` (`tabel`,`recordId`)
) ENGINE=MyISAM;
";

$tables['usageLog']="CREATE TABLE `usageLog` (
  `id` bigint(20) NOT NULL auto_increment,
  `object` varchar(50) NOT NULL default '',
  `query` mediumtext NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `date_user` (`add_date`,`add_user`)
) ENGINE=MyISAM;
";

$db=new DB();
foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","OrderCheck",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Vermogensbeheerders","kwartaalCheck",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));

?>