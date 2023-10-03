<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/07/31 15:54:45 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20130731_PREinstall.php,v $
 		Revision 1.1  2013/07/31 15:54:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/22 06:35:57  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("laatstePortefeuilleWaarde","afmstdev",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));

$db=new DB();
$query="SHOW index FROM CRM_naw_dossier";
$db->SQL($query);
$db->Query();
$indexFields=array();
while($data=$db->nextRecord())
  $indexFields[]=$data['Column_name'];
  
if(!in_array('rel_id',$indexFields))
{
  $query="CREATE INDEX rel_id ON CRM_naw_dossier (rel_id)";
  $db->SQL($query);
  $db->Query();
}


?>