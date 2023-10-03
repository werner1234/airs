<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/01/04 17:10:04 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20131221_PREinstall.php,v $
 		Revision 1.1  2014/01/04 17:10:04  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/11/17 11:19:40  rvv
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

$db=new DB();
$query="SHOW tables like 'dd_datastore%'";
$db->SQL($query);
$db->Query();
$tables=array();
while($data=$db->nextRecord('num'))
  $tables[]=$data[0];

$tst = new SQLman();
foreach($tables as $table)
  $tst->changeField($table,"filename",array("Type"=>"varchar(200)","Null"=>false,'Default'=>'default \'\''));

$tst->changeField("scenariosPerVermogensbeheerder","kleurcode",array("Type"=>"varchar(255)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Vermogensbeheerders","FACTUURHISTORIE_gebruikLaatsteWaarde",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));



?>