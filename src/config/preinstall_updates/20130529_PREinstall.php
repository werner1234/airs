<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
//$tst->changeField("TijdelijkeOrderRegels","client",array("Type"=>"varchar(16)","Null"=>false,'Default'=>'default \'\''));
//$tst->changeField("TijdelijkeOrderRegels","aantal",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
//$tst->changeField("TijdelijkeOrderRegels","ISINCode",array("Type"=>"varchar(26)","Null"=>false,'Default'=>'default \'\''));
//$tst->changeField("TijdelijkeOrderRegels","transactieSoort",array("Type"=>"varchar(2)","Null"=>false,'Default'=>'default \'\''));


$db = new DB();

$tables['TijdelijkeBulkOrders']="CREATE TABLE `TijdelijkeBulkOrders` (
  `id` int(11) NOT NULL auto_increment,
  `portefeuille` varchar(20) NOT NULL default '',
  `client` varchar(16) NOT NULL default '',
  `ISINCode` varchar(26) NOT NULL default '',
  `fonds` varchar(25) NOT NULL default '',
  `aantal` double NOT NULL default '0',
  `transactieSoort` varchar(2) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
)";


foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}

  
?>