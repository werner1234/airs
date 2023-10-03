<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/07/22 06:35:57 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20130706_PREinstall.php,v $
 		Revision 1.1  2013/07/22 06:35:57  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tables['pdfTemplateAfbeelding'] ="CREATE TABLE `pdfTemplateAfbeelding` (
  `id` int(11) NOT NULL auto_increment,
  `templateFile` varchar(255) default NULL,
  `pagina` tinyint(4) default NULL,
  `image` varchar(255),
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `imageWidth` int(11),
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
)";

$tables['pdfTemplateText'] ="CREATE TABLE `pdfTemplateText` (
  `id` int(11) NOT NULL auto_increment,
  `templateFile` varchar(255) default NULL,
  `pagina` tinyint(4) default NULL,
  `tekst` mediumblob,
  `fontName` varchar(100) default NULL,
  `fontSize` tinyint(4) default NULL,
  `fontStyle` varchar(1) default NULL,
  `lineHeight` tinyint(4) default NULL,
  `lineAlign` varchar(1) default NULL,
  `lineBorder` tinyint(4) default NULL,
  `lineWidth` int(11) default NULL,
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
);";

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