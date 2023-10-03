<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/05/25 14:36:18 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140511_PREinstall.php,v $
 		Revision 1.1  2014/05/25 14:36:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/05/03 15:46:30  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/19 16:14:54  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/02 15:54:35  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/03/29 16:25:05  rvv
 		*** empty log message ***
 		
	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
//$tst->changeField("GeconsolideerdePortefeuilles","SpecifiekeIndex",array("Type"=>"varchar(25)","Null"=>false));

$tables['controleEmailHistorie'] ="CREATE TABLE `controleEmailHistorie` (
  `id` int(11) NOT NULL auto_increment,
  `categorie` varchar(100) NOT NULL default '',
  `onderwerp` varchar(200) NOT NULL default '',
  `body` mediumtext,
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


?>