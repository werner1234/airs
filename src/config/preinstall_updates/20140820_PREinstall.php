<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/08/30 16:27:26 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140820_PREinstall.php,v $
 		Revision 1.1  2014/08/30 16:27:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("externeQueries","categorie",array("Type"=>"varchar(30)","Null"=>false));
$tst->changeField("externeQueries","memo",array("Type"=>"text","Null"=>false));

$tables['externeQueryCategorien'] ="CREATE TABLE `externeQueryCategorien` (
  `id` int(11) NOT NULL auto_increment,
  `categorie` varchar(30) NOT NULL default '',
  `omschrijving` varchar(100) NOT NULL default '',
  `volgorde` tinyint(3) NOT NULL default 0,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";


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