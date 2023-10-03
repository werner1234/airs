<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/06/13 15:49:12 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100613_PREinstall.php,v $
 		Revision 1.1  2010/06/13 15:49:12  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/24 19:15:41  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tables['FondsOmschrijvingVanaf'] ="CREATE TABLE `FondsOmschrijvingVanaf` (
  `id` int(11) NOT NULL auto_increment,
  `Fonds` varchar(25)  default '',
  `Omschrijving` varchar(50) default '',
  `Vanaf` date default '0000-00-00',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)  ,
  KEY `FondsVanaf` (`Fonds`,`Vanaf`)
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
$tst->changeField("Gebruikers","CRMeigenRecords",array("Type"=>"tinyint(4)","Null"=>false)); 






?>