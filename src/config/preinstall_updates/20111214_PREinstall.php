<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/12/31 18:13:52 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20111214_PREinstall.php,v $
 		Revision 1.1  2011/12/31 18:13:52  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/11/23 19:09:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/05/25 11:09:36  rvv
 		*** empty log message ***

 		Revision 1.1  2011/05/04 16:25:11  rvv
 		*** empty log message ***


*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tables['afmCategorien']="CREATE TABLE `afmCategorien` (
  `id` int(11) NOT NULL auto_increment,
  `afmCategorie` varchar(15)NOT NULL default '',
  `omschrijving` varchar(50) default NULL,
  `standaarddeviatie` double NOT NULL default '0',
  `correlatie` text NOT NULL,
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `afmCategorie` (`afmCategorie`)
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

$tst = new SQLman();
$tst->changeField("BeleggingscategoriePerFonds","afmCategorie",array("Type"=>"varchar(15)","Null"=>false));


?>