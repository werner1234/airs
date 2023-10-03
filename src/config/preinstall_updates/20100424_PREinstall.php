<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/04/24 19:15:41 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100424_PREinstall.php,v $
 		Revision 1.1  2010/04/24 19:15:41  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tables['FondsenBuitenBeheerfee'] ="CREATE TABLE `FondsenBuitenBeheerfee` (
  `id` int(11) NOT NULL auto_increment,
  `Vermogensbeheerder` varchar(10) default NULL,
  `Fonds` varchar(25)  default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)  ,
  KEY `VermogensbeheerderFonds` (`Vermogensbeheerder`,`Fonds`)
);";

$db = new DB();

foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}
$tst = new SQLman();
$tst->changeField("Grootboekrekeningen","Onttrekking",array("Type"=>"tinyint(4)","Null"=>false)); 






?>