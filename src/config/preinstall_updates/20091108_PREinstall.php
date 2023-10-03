<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/11/08 14:19:08 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20091108_PREinstall.php,v $
 		Revision 1.1  2009/11/08 14:19:08  rvv
 		*** empty log message ***
 		

 		*** empty log message ***
 		
 
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("ae_config","field",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","prospectStatus",array("Type"=>"varchar(30)","Null"=>false)); 

$tables['CRM_evenementen'] ="CREATE TABLE `CRM_evenementen` (
  `id` int(11) NOT NULL auto_increment,
  `rel_id` int(11) NOT NULL,
  `evenement` varchar(30) NOT NULL,
  `add_date` datetime NOT NULL,
  `add_user` varchar(10) NOT NULL,
  `change_date` datetime NOT NULL,
  `change_user` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `rel_id` (`rel_id`),
  KEY `evenement` (`evenement`)
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