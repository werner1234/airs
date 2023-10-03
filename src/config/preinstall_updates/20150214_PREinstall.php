<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/02/15 10:39:12 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20150214_PREinstall.php,v $
 		Revision 1.1  2015/02/15 10:39:12  rvv
 		*** empty log message ***
 		
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
$db=new DB();

$tables['FondsParameterHistorie']="CREATE TABLE `FondsParameterHistorie` (
  `id` int(11) NOT NULL auto_increment,
  `Fonds` varchar(25) default '',
  `GebruikTot` date default '0000-00-00',
  `Rentedatum` datetime NOT NULL,
  `Renteperiode` tinyint(4) NOT NULL,
  `EersteRentedatum` datetime NOT NULL,
  `Lossingsdatum` date NOT NULL default '0000-00-00',
  `lossingskoers` double NOT NULL default '0',
  `Rente30_360` tinyint(4) NOT NULL,
  `variabeleCoupon` tinyint(1) NOT NULL,
  `OblSoortFloater` varchar(2) NOT NULL,
  `inflatieGekoppeld` tinyint(3) NOT NULL,
  `OblPerpetual` tinyint(3) NOT NULL,
  `OblDirtyPr` tinyint(3) NOT NULL,
  `OblMemo` text NOT NULL,
  `datumControleStatics` datetime NOT NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `FondsTot` (`Fonds`,`GebruikTot`)
)
";
//$db->SQL("DROP TABLE IF EXISTS `FondsParameterHistorie`");
//$db->Query();

$db->SQL("DROP TABLE IF EXISTS `FondsParametersVanaf`");
$db->Query();

foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}


$tst->changeField("Rentepercentages","GeldigVanaf",array("Type"=>"date","Null"=>false));




?>