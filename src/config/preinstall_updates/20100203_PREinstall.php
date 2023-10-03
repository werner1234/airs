<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/02/03 17:09:40 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100203_PREinstall.php,v $
 		Revision 1.1  2010/02/03 17:09:40  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/06 10:18:42  rvv
 		*** empty log message ***
 		
		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
//$tst->changeField("Gebruikers","bgkleur",array("Type"=>"varchar(6)","Null"=>false)); 
//$tst->changeField("CRM_naw","part_inkomenSoort",array("Type"=>"varchar(20)","Null"=>false)); 
//$tst->changeField("CRM_naw","part_inkomenIndicatie",array("Type"=>"double(10,2)","Null"=>false)); 
//$tst->changeField("CRM_naw","beleggingsDoelstelling",array("Type"=>"varchar(60)","Null"=>false)); 

$db=new DB();
$tables[] ="CREATE TABLE `CRM_naw_adressen` (
  `id` bigint(20) NOT NULL auto_increment,
  `rel_id` bigint(20) default NULL,
  `naam` varchar(255) NOT NULL default '',
  `naam1` varchar(255) NOT NULL default '',
  `adres` varchar(200) NOT NULL default '',
  `pc` varchar(17) NOT NULL default '',
  `plaats` varchar(30) NOT NULL default '',
  `land` varchar(25) NOT NULL default '',
  `evenement` varchar(30) NOT NULL default '',
  `memo` text,
  `add_user` varchar(10) default NULL,
  `add_date` date default NULL,
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
);";

$tables[] ="CREATE TABLE `CRM_naw_rekeningen` (
  `id` bigint(20) NOT NULL auto_increment,
  `rel_id` bigint(20) default NULL,
  `rekening` varchar(20) NOT NULL default '',
  `bank` varchar(50) NOT NULL default '',
  `omschrijving` varchar(60) NOT NULL default '',
  `add_user` varchar(10) default NULL,
  `add_date` date default NULL,
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
);
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