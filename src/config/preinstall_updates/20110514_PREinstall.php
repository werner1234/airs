<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/05/25 11:09:36 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110514_PREinstall.php,v $
 		Revision 1.1  2011/05/25 11:09:36  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/05/04 16:25:11  rvv
 		*** empty log message ***


*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","verrekeningBestandsvergoeding",array("Type"=>"int","Null"=>false));
$tst->changeField("Vermogensbeheerders","bestandsvergoedingBtw",array("Type"=>"int","Null"=>false));
$tst->changeField("Vermogensbeheerders","bestandsvergoedingNiveau",array("Type"=>"int","Null"=>false));
$tst->changeField("Bestandsvergoedingen","periodeVan",array("Type"=>"date","Null"=>false));
$tst->changeField("Bestandsvergoedingen","periodeTm",array("Type"=>"date","Null"=>false));


$tables['BestandsvergoedingPerPortefeuille']="CREATE TABLE `BestandsvergoedingPerPortefeuille` (
  `id` int(11) NOT NULL auto_increment,
  `bestandsvergoedingId` int(11) NOT NULL,
  `portefeuille` varchar(12) default NULL,
  `bedragBerekend` double NOT NULL default '0.0',
  `bedragUitbetaald` double NOT NULL default '0.0',
  `datumUitbetaald` date NOT NULL default '0000-00-00',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
);";

$db=new DB();
foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}

?>