<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/05/04 16:25:11 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110416_PREinstall.php,v $
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

$tst->changeField("emittentPerFonds","depotbank",array("Type"=>"varchar(10)","Null"=>false));
$tst->changeField("Portefeuilles","BestandsvergoedingUitkeren",array("Type"=>"tinyint(4)","Null"=>false));

$db = new DB();

$tables['Bestandsvergoedingen']="CREATE TABLE `Bestandsvergoedingen` (
  `id` int(11) NOT NULL auto_increment,
  `vermogensbeheerder` varchar(5) NOT NULL default '',
  `emittent` varchar(15) NOT NULL default '',
  `depotbank` varchar(10) default NULL,
  `datumBerekend` date NOT NULL default '0000-00-00',
  `waardeBerekend` double NOT NULL default '0.0',
  `datumHerrekend` date NOT NULL default '0000-00-00',
  `waardeHerrekend` double NOT NULL default '0.0',  
  `datumGeaccoordeerd` date NOT NULL default '0000-00-00',
  `datumOntvangen` date NOT NULL default '0000-00-00',
  `datumUitbetaald` date NOT NULL default '0000-00-00',
  `status` text NOT NULL,
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