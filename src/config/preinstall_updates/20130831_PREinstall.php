<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/11/13 15:54:00 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: 20130831_PREinstall.php,v $
 		Revision 1.2  2013/11/13 15:54:00  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/09/01 13:29:55  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/08/21 15:32:58  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/22 06:35:57  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tables['standaardTaken'] ="CREATE TABLE `standaardTaken` (
  `id` int(11) NOT NULL auto_increment,
  `hoofdtaak` varchar(256) NOT NULL default '',
  `taak` varchar(256) NOT NULL default '',
  `add_date` datetime default NULL,
  `add_user` varchar(15) default NULL,
  `change_user` varchar(15) default NULL,
  `change_date` datetime default NULL,
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
unlink("../html/rapport/include/RapportTRANS_L50.php");
//$tst = new SQLman();
//$tst->changeField("ModelPortefeuilles","Beleggingscategorie",array("Type"=>"varchar(15)","Null"=>false,'Default'=>'default \'\''));
//$tst->changeField("TijdelijkeRapportage","portefeuille",array("Type"=>"varchar(13)","Null"=>false,'Default'=>'default \'\''));



?>