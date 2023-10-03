<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/02/22 18:36:11 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140222_PREinstall.php,v $
 		Revision 1.1  2014/02/22 18:36:11  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/02/02 10:40:10  rvv
 		*** empty log message ***
 		
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


$tst = new SQLman();
$tst->changeField("CRM_naw_RtfTemplates","verplichteVelden",array("Type"=>"text","Null"=>false));
$tst->changeField("CRM_eigenVelden","extraVeldData",array("Type"=>"text","Null"=>false));
$tst->changeField("Vermogensbeheerders","CRM_PanasonicKoppeling",array("Type"=>"tinyint(4)","Null"=>false));

$velden=array('debiteur'=>'Clienten','crediteur'=>'Leveranciers','prospect'=>'Prospects','overige'=>'Overige');

$db=new DB();
foreach($velden as $veld=>$omschrijving)
{
  $query="SELECT id FROM CRM_eigenVelden WHERE veldnaam='$veld'";
  if($db->QRecords($query) < 1)
  {
    $query="INSERT INTO CRM_eigenVelden SET veldnaam='$veld',omschrijving='$omschrijving',veldtype='Checkbox', relatieSoort=1, add_user='SYS',change_user='SYS',add_date=now(),change_date=now()";
    $db->SQL($query);
	$db->query();
  }
}


?>