<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Vermogensbeheerders","transactieMeldingType",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));

$tables['grootboeknummers'] ="CREATE TABLE `grootboeknummers` (
  `id` int(11) NOT NULL auto_increment,
  `vermogensbeheerder` varchar(10) NOT NULL default '',
  `grootboekrekening` varchar(5) default NULL,
  `rekeningnummer` varchar(6) NOT NULL default '',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `vermogensbeheerder` (`vermogensbeheerder`)
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