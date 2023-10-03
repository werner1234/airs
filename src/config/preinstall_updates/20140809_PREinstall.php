<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/08/09 15:05:04 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140809_PREinstall.php,v $
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		

 		
	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","check_module_VRAGEN",array("Type"=>"tinyint(4)","Null"=>false));
$tst->changeField("VragenVragen","CRM_trekveld",array("Type"=>"varchar(40)","Null"=>false));


$tables=array();
$tables['veldopmaak']="CREATE TABLE `veldopmaak` (
  `id` int(11) NOT NULL auto_increment,
  `tabel` varchar(60) NOT NULL default '',
  `veld` varchar(60) NOT NULL default '',
  `uitlijning` varchar(1) NOT NULL default '',
  `getalformat` varchar(10) NOT NULL default '',
  `aantalRegels` tinyint(3) NOT NULL default 0,
  `weergaveBreedte` tinyint(3) NOT NULL default 0,
  `headerBreedte` tinyint(3) NOT NULL default 0,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ";

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