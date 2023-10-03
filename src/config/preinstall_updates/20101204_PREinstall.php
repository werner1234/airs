<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/12/05 09:47:44 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20101204_PREinstall.php,v $
 		Revision 1.1  2010/12/05 09:47:44  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/11/28 16:22:52  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/11/27 16:19:01  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/10/17 09:39:46  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/10/02 14:34:33  rvv
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
$db = new DB();

$tables['benchmarkverdeling']="CREATE TABLE `benchmarkverdeling` (
  `id` int(11) NOT NULL auto_increment,
  `benchmark` varchar(25) NOT NULL default '',
  `fonds` varchar(25) NOT NULL default '',
  `percentage` double NOT NULL default '0',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10)  default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
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