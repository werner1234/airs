<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/08/21 15:32:58 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20130821_PREinstall.php,v $
 		Revision 1.1  2013/08/21 15:32:58  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/22 06:35:57  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tables['NormPerRisicoprofiel'] ="CREATE TABLE `NormPerRisicoprofiel` (
  `id` int(11) NOT NULL auto_increment,
  `Vermogensbeheerder` varchar(10) default NULL,
  `Risicoklasse` varchar(50) default NULL,	  
  `Beleggingscategorie` varchar(15) default NULL,
  `norm` double NOT NULL default '0',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
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
$tst->changeField("ModelPortefeuilles","Beleggingscategorie",array("Type"=>"varchar(15)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("TijdelijkeRapportage","portefeuille",array("Type"=>"varchar(13)","Null"=>false,'Default'=>'default \'\''));



?>