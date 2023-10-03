<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","OrderLoggingOpNota",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("custom_txt","OrderLoggingOpNota",array("Type"=>"varchar(255)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("UpdateHistory","tableDef",array("Type"=>"mediumtext","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("CRM_naw_adressen","kaartVerstuurd",array("Type"=>"datetime","Null"=>false,'Default'=>'default \'0000-00-00 00:00:00\''));
$tst->changeField("FactuurHistorie","betaald",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("GrootboekPerVermogensbeheerder","Onttrekking",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Indices","toelichting",array("Type"=>"varchar(200)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("OrderRegels","CheckResult",array("Type"=>"text","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Orders","printDate",array("Type"=>"datetime","Null"=>false,'Default'=>'default \'0000-00-00 00:00:00\''));
$tst->changeField("Orders","batchId",array("Type"=>"int(11)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Bedrijfsgegevens","crypted",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));


$db = new DB();

$tables['help_tekst']="CREATE TABLE `help_tekst` (
  `id` int(11) NOT NULL auto_increment,
  `titel`  varchar(150) NOT NULL default '',
  `url`  varchar(150) NOT NULL default '',
  `txt` text,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `titel` (`titel`)
) ;
";

$tables['help_velden']="CREATE TABLE `help_velden` (
  `id` int(11) NOT NULL auto_increment,
  `veld`  varchar(150) NOT NULL default '',
  `txt` text,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `veld` (`veld`)
) ;
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