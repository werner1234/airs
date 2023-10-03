<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/01/24 20:02:43 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20150117_PREinstall.php,v $
 		Revision 1.1  2015/01/24 20:02:43  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/01/04 13:14:14  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/12/13 12:34:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		

	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();

$tables['FondsParametersVanaf']="CREATE TABLE `FondsParametersVanaf` (
  `id` int(11) NOT NULL auto_increment,
  `Fonds` varchar(25) default '',
  `Vanaf` date default '0000-00-00',
  `Rentedatum` datetime NOT NULL,
  `Renteperiode` tinyint(4) NOT NULL,
  `Rente30_360` tinyint(4) NOT NULL,
  `variabeleCoupon` tinyint(1) NOT NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `FondsVanaf` (`Fonds`,`Vanaf`)
)
";


foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}


//$tst->changeField("Rentepercentages","Rentedatum",array("Type"=>"datetime","Null"=>false));
//$tst->changeField("Rentepercentages","Renteperiode",array("Type"=>"tinyint(4)","Null"=>false));
//$tst->changeField("Rentepercentages","Rente30_360",array("Type"=>"tinyint(4)","Null"=>false));
//$tst->changeField("Rentepercentages","variabeleCoupon",array("Type"=>"tinyint(1)","Null"=>false));




?>