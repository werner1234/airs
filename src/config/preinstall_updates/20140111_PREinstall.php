<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/01/18 17:21:41 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140111_PREinstall.php,v $
 		Revision 1.1  2014/01/18 17:21:41  rvv
 		*** empty log message ***
 		
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
  `hoofdtaak` varchar(255) NOT NULL default '',
  `taak` varchar(255) NOT NULL default '',
  `add_date` datetime default NULL,
  `add_user` varchar(15) default NULL,
  `change_user` varchar(15) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`)
)";

$db = new DB();
foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}


$tst = new SQLman();
$tst->changeField("Fondsen","fondssoort",array("Type"=>"varchar(8)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("orderkosten","prijsPerStuk",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("orderkosten","beurs",array("Type"=>"varchar(4)","Null"=>false,'Default'=>'default \'\''));

$query="desc orderkosten";
$db->SQL($query);
$db->Query();
$fields=array();
while($data=$db->nextRecord())
{
  $fields[]=$data['Field'];
}
if(in_array('beleggingscategorie',$fields))
{
  $query="ALTER TABLE orderkosten  change beleggingscategorie fondssoort varchar (8)";
  $db->SQL($query);
  $db->Query();
  
}


?>