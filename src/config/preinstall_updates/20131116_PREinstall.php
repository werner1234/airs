<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/11/17 11:19:40 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20131116_PREinstall.php,v $
 		Revision 1.1  2013/11/17 11:19:40  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/11/13 15:54:00  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/09/01 13:29:55  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/08/21 15:32:58  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/22 06:35:57  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tables['scenariosPerVermogensbeheerder'] ="CREATE TABLE `scenariosPerVermogensbeheerder` (
  `id` int(11) NOT NULL auto_increment,
  `Vermogensbeheerder` varchar(10) default NULL,
  `scenario` varchar(150) NOT NULL default '',
  `percentage` float NOT NULL default '0',
  `add_date` datetime default NULL,
  `add_user` varchar(15) default NULL,
  `change_user` varchar(15) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`)
)";

$tables['CRM_naw_cashflow'] ="CREATE TABLE `CRM_naw_cashflow` (
  `id` int(11) NOT NULL auto_increment,
  `rel_id` bigint(20) NOT NULL,
  `datum` date NOT NULL default '0000-00-00',
  `bedrag` double(11,2) NOT NULL default '0.00',
  `add_date` datetime default NULL,
  `add_user` varchar(15) default NULL,
  `change_user` varchar(15) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`)
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

$tst = new SQLman();
$tst->changeField("CRM_naw","startvermogen",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","doelvermogen",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","startdatum",array("Type"=>"date","Null"=>false,'Default'=>'default \'0000-00-00\''));
$tst->changeField("CRM_naw","doeldatum",array("Type"=>"date","Null"=>false,'Default'=>'default \'0000-00-00\''));
$tst->changeField("CRM_naw","gewenstRisicoprofiel",array("Type"=>"varchar(50)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Rekeningen","typeRekening",array("Type"=>"varchar(25)","Null"=>false,'Default'=>'default \'\'')); 
$tst->changeField("CRM_eigenVelden","headerBreedte",array("Type"=>"int(11)","Null"=>false,'Default'=>'default \'0\''));


?>