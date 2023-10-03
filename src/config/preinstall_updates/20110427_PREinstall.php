<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/05/04 16:25:11 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110427_PREinstall.php,v $
 		Revision 1.1  2011/05/04 16:25:11  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/01/29 15:54:40  rvv
 		*** empty log message ***

 		Revision 1.1  2011/01/26 17:18:16  rvv
 		*** empty log message ***


*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("ModelPortefeuilles","Fixed",array("Type"=>"tinyint(4)","Null"=>false));
$tst->changeField("ModelPortefeuilles","FixedDatum",array("Type"=>"date","Null"=>false));

$db = new DB();

$tables['ModelPortefeuilleFixed']="CREATE TABLE `ModelPortefeuilleFixed` (
  `id` int(11) NOT NULL auto_increment,
  `Portefeuille` varchar(12) NOT NULL default '',
  `Fonds` varchar(25) NOT NULL default '',
  `Percentage` double NOT NULL default '0',
  `Datum` date NOT NULL default '0000-00-00',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
);";

foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}



?>