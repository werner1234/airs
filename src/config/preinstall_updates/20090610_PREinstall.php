<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/06/10 14:29:24 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20090610_PREinstall.php,v $
 		Revision 1.1  2009/06/10 14:29:24  rvv
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
$skipFields=array('id','rel_id','change_user','change_date','add_user','add_date','verzendAanhef','verzendAdres','verzendPc','verzendPlaats','verzendLand');

$fields=array();
$DB=new DB();
$query="SHOW FIELDS FROM CRM_naw_cf";
$DB->SQL($query);
$DB->Query();
while($data=$DB->nextRecord())
{
  if(!in_array($data['Field'],$skipFields))
  {
    $tst->changeField("CRM_naw",$data['Field'],array("Type"=>$data['Type'],"Null"=>false));   
    $fields[]=$data['Field'];
  }
  
}

$records=array();
$query="SELECT rel_id FROM CRM_naw_cf";
$DB->SQL($query);
$DB->Query();
while($data=$DB->nextRecord())
{
  $records[]=$data['rel_id'];
}

foreach ($records as $rel_id)
{
  foreach ($fields as $field)
  {
    $query="SELECT $field FROM CRM_naw WHERE id='$rel_id'";
    $DB->SQL($query);
    $oudeWaarde=$DB->lookupRecord();
    if($oudeWaarde[$field] == '' || $oudeWaarde[$field]='0000-00-00') //Alleen wanneer het doelveld leeg is de waarde kopieeren uit de cf.
    {
      $query="SELECT $field FROM CRM_naw_cf WHERE rel_id='$rel_id'";
      $DB->SQL($query);
      $nieuweWaarde=$DB->lookupRecord();
      
      $query="UPDATE CRM_naw SET $field='".$nieuweWaarde[$field]."' WHERE id='$rel_id'";
      $DB->SQL($query);
      $DB->Query();      
    }
  }
}

	 

$tst->changeField("Clienten","pc",array("Type"=>"varchar(17)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringInVermogensbeheer",array("Type"=>"varchar(17)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringInVermogensbeheerSinds",array("Type"=>"date","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringBelegtInEigenbeheerSinds",array("Type"=>"date","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringBelegtInVermogensadviesSinds",array("Type"=>"date","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringInExecutionOnlySinds",array("Type"=>"date","Null"=>false)); 

$query="ALTER TABLE CRM_naw CHANGE ervaringBelegtInProducten ervaringInExecutionOnly varchar(20)";
$db=new DB();
$db->SQL($query);
$db->query();

?>