<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/08/07 09:06:42 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110806_PREinstall.php,v $
 		Revision 1.1  2011/08/07 09:06:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/07/23 17:21:11  rvv
 		*** empty log message ***

 		Revision 1.1  2011/06/25 20:07:27  rvv
 		*** empty log message ***

*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","CRM_eigenTemplate",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));


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