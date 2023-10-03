<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/03/27 08:03:41 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20121124_PREinstall.php,v $
 		Revision 1.1  2013/03/27 08:03:41  rvv
 		*** empty log message ***
 		
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

$tst->changeField("Vermogensbeheerders","check_module_portefeuilleWaarde",array("Type"=>"tinyint(1)","Null"=>false,'Default'=>'default \'0\''));
$tables['laatstePortefeuilleWaarde'] ="CREATE TABLE `laatstePortefeuilleWaarde` (
  `id` int(11) NOT NULL auto_increment,
  `portefeuille` varchar(12) NOT NULL default '',
  `laatsteWaarde` double default NULL,
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
)";

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