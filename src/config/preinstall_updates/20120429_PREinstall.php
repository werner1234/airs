<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$db = new DB();

$tables['CRM_eigenVelden']="CREATE TABLE `CRM_eigenVelden` (
  `id` int(11) NOT NULL auto_increment,
  `veldnaam` varchar(60) NOT NULL default '',
  `omschrijving` varchar(150) NOT NULL default '',
  `veldtype` varchar(60) NOT NULL default '',
  `weergaveBreedte` int(11) NOT NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ";


foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}


$tst->changeField("Vermogensbeheerders","check_module_CRM_eigenVelden",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("ZorgplichtPerPortefeuille","Vanaf",array("Type"=>"date","Null"=>false,'Default'=>'default \'0000-00-00\''));
$tst->changeField("BeleggingscategoriePerFonds","duurzaamheid",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));








?>