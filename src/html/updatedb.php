<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/11/17 08:02:42 $
 		File Versie					: $Revision: 1.10 $

 		$Log: updatedb.php,v $
 		Revision 1.10  2017/11/17 08:02:42  cvs
 		call 6145
 		
 		Revision 1.9  2015/01/30 15:26:16  rm
 		no message
 		
 		Revision 1.8  2014/12/24 09:54:51  cvs
 		call 3105
 		
 		Revision 1.7  2014/03/12 10:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2013/12/16 08:21:00  cvs
 		*** empty log message ***

 		Revision 1.5  2013/11/15 10:22:21  cvs
 		aanpassing tbv Adventexport

 		Revision 1.4  2012/06/06 10:05:12  cvs
 		factuurregels uit CRM_uren

 		Revision 1.3  2012/03/09 09:08:56  cvs
 		*** empty log message ***

 		Revision 1.2  2011/10/22 06:45:09  cvs
 		Urenregistratie voor TRA

 		Revision 1.1  2011/06/22 11:47:03  cvs
 		*** empty log message ***



*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

//$tst->changeField("Gebruikers","crmImport",array("Type"=>" tinyint","Null"=>false));

$tst->changeField("Gebruikers","taal",array("Type"=>" varchar(5)","Null"=>false));


$tst->tableExist("CRM_eigenVelden",true);
$tst->changeField("CRM_eigenVelden","omschrijving_en",array("Type"=>" varchar(150)","Null"=>false));
$tst->changeField("CRM_eigenVelden","omschrijving_fr",array("Type"=>" varchar(150)","Null"=>false));
$tst->changeField("CRM_eigenVelden","omschrijving_du",array("Type"=>" varchar(150)","Null"=>false));

//$db = new DB();
//$q = ' 
//CREATE TABLE `degiroTransactieCodes` (
//  `id` int(11) NOT NULL auto_increment,
//  `change_user` varchar(10) default NULL,
//  `change_date` datetime default NULL,
//  `add_user` varchar(10) default NULL,
//  `add_date` datetime default NULL,
//  `giroCode` varchar(10) NOT NULL,
//  `omschrijving` varchar(50) NOT NULL,
//  `doActie` varchar(10) NOT NULL,
//  PRIMARY KEY  (`id`)
//) ;
//
//';
//
//$db->executeQuery($q);

echo (int)$tst->counter['skipped']. " mutatie eerder verwerkt<br>";
echo (int)$tst->counter['succes']. " mutatie nu verwerkt<br>";
listarray($tst->counter["SQL"]);

echo "klaar";

?>
