<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/08/04 15:22:36 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100804_PREinstall.php,v $
 		Revision 1.1  2010/08/04 15:22:36  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/07/31 16:10:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/07/25 14:34:30  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/28 15:50:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/24 19:15:41  rvv
 		*** empty log message ***
 		
 	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Gebruikers","mutatiesAanleveren",array("Type"=>"tinyint","Null"=>false)); 

$tables['ZorgplichtPerBeleggingscategorie'] ="CREATE TABLE `ZorgplichtPerBeleggingscategorie` (
  `id` int(11) NOT NULL auto_increment,
  `Vermogensbeheerder` varchar(10) default NULL,
  `Zorgplicht` varchar(50)  default NULL,
  `Beleggingscategorie` varchar(25)  default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10)  default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10)  default NULL,
  PRIMARY KEY  (`id`),
  KEY `VermogensbeheerderBeleggingscategorie` (`Vermogensbeheerder`,`Beleggingscategorie`),
  KEY `VermogensbeheerderZorgplicht` (`Vermogensbeheerder`,`Zorgplicht`)
)";

$tables['ZorgplichtPerRisicoklasse'] ="CREATE TABLE `ZorgplichtPerRisicoklasse` (
  `id` int(11) NOT NULL auto_increment,
  `Vermogensbeheerder` varchar(10) default NULL,
  `Zorgplicht` varchar(50) default NULL,
  `Risicoklasse` varchar(50) default NULL,
  `Minimum` double default NULL,
  `Maximum` double default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `VermogensbeheerderZorgplicht` (`Vermogensbeheerder`,`Zorgplicht`),
  KEY `VermogensbeheerderRisicoklasse` (`Vermogensbeheerder`,`Risicoklasse`)
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