<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2012/03/09 09:23:28 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: 20091115_PREinstall.php,v $
 		Revision 1.2  2012/03/09 09:23:28  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2009/11/15 16:51:06  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/10/17 15:43:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/10/17 13:27:49  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/06 10:18:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/10/09 06:23:57  cvs
 		gebruikerstabel ivm CRM
 		
 		Revision 1.1  2007/09/27 13:35:24  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/08/24 11:26:49  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2007/08/24 11:25:17  cvs
 		*** empty log message ***
 		
 
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tables['CRM_evenementen'] ="CREATE TABLE `CRM_evenementen` (
  `id` int(11) NOT NULL auto_increment,
  `rel_id` int(11) NOT NULL,
  `evenement` varchar(30) NOT NULL,
  `add_date` datetime NOT NULL,
  `add_user` varchar(10) NOT NULL,
  `change_date` datetime NOT NULL,
  `change_user` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `rel_id` (`rel_id`),
  KEY `evenement` (`evenement`)
)";

$tables['dd_datastore01'] ="CREATE TABLE `dd_datastore01` (
  `id` int(11) NOT NULL auto_increment,
  `change_user` varchar(10) collate latin1_general_ci NOT NULL,
  `change_date` datetime NOT NULL,
  `add_user` varchar(10) collate latin1_general_ci NOT NULL,
  `add_date` datetime NOT NULL,
  `referenceId` int(11) NOT NULL,
  `filename` varchar(60) collate latin1_general_ci NOT NULL,
  `filesize` int(11) NOT NULL,
  `filetype` varchar(50) collate latin1_general_ci NOT NULL,
  `description` varchar(127) collate latin1_general_ci NOT NULL,
  `blobdata` mediumblob NOT NULL COMMENT 'max 16 mb',
  `blobCompressed` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `reference` (`referenceId`)
) ;";


$tables['dd_logging'] ="CREATE TABLE `dd_logging` (
  `id` int(11) NOT NULL auto_increment,
  `dd_id` int(11) NOT NULL,
  `datastore` varchar(25) collate latin1_general_ci NOT NULL,
  `rootReference` tinyint(4) NOT NULL,
  `user` varchar(20) collate latin1_general_ci NOT NULL,
  `datum` datetime NOT NULL,
  `ip` varchar(20) collate latin1_general_ci NOT NULL,
  `txt` text collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `datesearch` (`datum`,`user`),
  KEY `usersearch` (`user`,`datum`)
) ;";

$tables['dd_reference'] ="CREATE TABLE `dd_reference` (
  `id` int(11) NOT NULL auto_increment,
  `change_user` varchar(10) collate latin1_general_ci NOT NULL,
  `change_date` datetime NOT NULL,
  `add_user` varchar(10) collate latin1_general_ci NOT NULL,
  `add_date` datetime NOT NULL,
  `dd_id` int(11) NOT NULL,
  `datastore` varchar(25) collate latin1_general_ci NOT NULL,
  `rootReference` tinyint(4) NOT NULL,
  `description` varchar(127) collate latin1_general_ci NOT NULL,
  `keywords` text collate latin1_general_ci NOT NULL,
  `securitylevel` tinyint(4) NOT NULL default '5',
  `module` varchar(40) collate latin1_general_ci NOT NULL COMMENT 'tablename for linked id',
  `module_id` int(11) NOT NULL COMMENT 'id of item in the module defined table',
  PRIMARY KEY  (`id`),
  KEY `moduleId` (`module`,`module_id`),
  FULLTEXT KEY `search` (`description`,`keywords`)
) ;";



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