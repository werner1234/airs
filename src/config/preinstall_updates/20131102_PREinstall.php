<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/11/13 15:53:45 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20131102_PREinstall.php,v $
 		Revision 1.1  2013/11/13 15:53:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/09/25 15:57:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/08/04 10:46:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/31 15:54:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/22 06:35:57  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("ModelPortefeuilles","AfdrukNiveau",array("Type"=>"varchar(10)","Null"=>false,'Default'=>'default \'\''));

$tables['StandaarddeviatiePerPortefeuille'] ="CREATE TABLE `StandaarddeviatiePerPortefeuille` (
  `id` int(11) NOT NULL auto_increment,
  `Vermogensbeheerder` varchar(10) default NULL,
  `Portefeuille` varchar(12) default NULL,
  `Minimum` double default NULL,
  `Maximum` double default NULL,
  `Norm` double NOT NULL default '0',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `Portefeuille` (`Portefeuille`)
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