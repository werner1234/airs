<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/11/28 16:22:52 $
 		File Versie					: $Revision: 1.2 $

 		$Log: 20101127_PREinstall.php,v $
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

$tst->changeField("CRM_naw","overlijdensdatum",array("Type"=>"date","Null"=>false));
$tst->changeField("CRM_naw","part_overlijdensdatum",array("Type"=>"date","Null"=>false));
$tst->changeField("Accountmanagers","Naam",array("Type"=>"varchar(75)","Null"=>false));

$tables['KeuzePerVermogensbeheerder']="CREATE TABLE `KeuzePerVermogensbeheerder` (
  `id` int(11) NOT NULL auto_increment,
  `vermogensbeheerder` varchar(10) default NULL,
  `categorie` varchar(50) default NULL,
  `waarde` varchar(30) default NULL,
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