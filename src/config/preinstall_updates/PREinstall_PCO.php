<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/11/23 19:09:29 $
 		File Versie					: $Revision: 1.1 $

 		$Log: PREinstall_PCO.php,v $
 		Revision 1.1  2011/11/23 19:09:29  rvv
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

$tables['CRM_naw_templates']="CREATE TABLE `CRM_naw_templates` (
  `id` int(11) NOT NULL auto_increment,
  `tabs` mediumtext,
   veldenPerTab` mediumtext,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
)
";

$tables['historischeTenaamstelling']="CREATE TABLE `historischeTenaamstelling` (
  `id` int(11) NOT NULL auto_increment,
  `clientId` int(11) NOT NULL , 
  `crmId` int(11) NOT NULL ,
  `Naam` varchar(100) default NULL,
  `Naam1` varchar(100) default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `clientId` (`clientId`),
  KEY `crmId` (`crmId`)
);
";

$tables['historischeTenaamstelling']="CREATE TABLE `historischeTenaamstelling` (
  `id` int(11) NOT NULL auto_increment,
  `clientId` int(11) NOT NULL , 
  `crmId` int(11) NOT NULL ,
  `Naam` varchar(100) default NULL,
  `Naam1` varchar(100) default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `clientId` (`clientId`),
  KEY `crmId` (`crmId`)
);
";

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
$tst->changeField("Risicoklassen","verwachtRendement",array("Type"=>"float","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Vermogensbeheerders","CRM_eigenTemplate",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("historischeTenaamstelling","geldigTot",array("Type"=>"date","Null"=>false,'Default'=>'default \'0000-00-00\''));
$tst->changeField("Vermogensbeheerders","OrderCheck",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Vermogensbeheerders","kwartaalCheck",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Orders","batchId",array("Type"=>"int(11)","Null"=>false));
$tst->changeField("Gebruikers","bestandsvergoedingEdit",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Indices","toelichting",array("Type"=>"varchar(200)","Null"=>false,'Default'=>'default \'\''));

$Column_name=array();
$db=new DB();
$query="SHOW KEYS IN Orders";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
  $ColName[]=$data['Column_name'];

if(count($ColName) > 0 && !(in_array('batchId',$ColName)))
{
  $query="CREATE INDEX batchId ON Orders (batchId)";
  $db->SQL($query);
  $db->Query();
}


if(file_exists("../html/CRM_nawEditTemplate_custom.html"))
  unlink("../html/CRM_nawEditTemplate_custom.html");


?>