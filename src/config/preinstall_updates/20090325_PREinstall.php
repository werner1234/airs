<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/03/25 17:39:22 $
 		File Versie					: $Revision: 1.2 $

 		$Log: 20090325_PREinstall.php,v $
 		Revision 1.2  2009/03/25 17:39:22  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/03/25 17:22:23  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2009/02/18 08:55:48  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2009/01/28 16:34:47  rvv
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

$queries['CategorienPerVermogensbeheerder'] = " CREATE TABLE `CategorienPerVermogensbeheerder` (
  `id` int(11) NOT NULL auto_increment,
  `Beleggingscategorie` varchar(15) default NULL,
  `Vermogensbeheerder` varchar(10) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
); 
";


$DB= new DB();
foreach ($queries as $tabel=>$query)
{
      $testQuery = "SHOW TABLES LIKE '".$tabel."'";
      $DB->SQL($testQuery);
      $DB->Query();
      if($DB->records() == 0)
      {
         $DB->SQL($query);
         $DB->Query();
      }
}




?>