<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/02/15 11:42:26 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20090215_PREinstall.php,v $
 		Revision 1.1  2009/02/15 11:42:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/06 10:18:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/10/09 06:23:57  cvs
 		gebruikerstabel ivm CRM
 		
 		Revision 1.1  2007/09/27 13:35:24  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/08/24 11:26:49  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2007/08/24 11:25:17  cvs
 		*** empty log message ***
 		
 
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$query[] = "CREATE TABLE `Schaduwkoersen` (
  `id` int(11) NOT NULL auto_increment,
  `Fonds` varchar(25) default NULL,
  `Datum` datetime default NULL,
  `Koers` double default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `Fonds` (`Fonds`),
  KEY `Datum` (`Datum`),
  KEY `change_date` (`change_date`)
);";

$db = new DB();
for ($a=0; $a < count($query); $a++)
{
	$db->SQL($query[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20090216 $query ".$a." mislukt, neem aub contact op met AIRS.";
	}
}

?>