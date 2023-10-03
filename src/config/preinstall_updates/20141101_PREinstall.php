<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/11/12 16:35:50 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: 20141101_PREinstall.php,v $
 		Revision 1.3  2014/11/12 16:35:50  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/11/02 14:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/11/01 22:08:51  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/10/19 08:50:05  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/10/18 11:22:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/30 16:27:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();


$tst->changeField("BbLandcodes","settlementDays",array("Type"=>"tinyint(4)","Null"=>false));
$tst->changeField("benchmarkverdeling","toelichting",array("Type"=>"varchar(200)","Null"=>false));

$queries=array();
$db=new DB();
$query="DESC laatstePortefeuilleWaarde";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  if($data['Field']=='rente')
    $queries[] = "ALTER TABLE `laatstePortefeuilleWaarde` CHANGE COLUMN `rente` `mutatieOpgelopenRente` double NOT NULL default '0'";
}

foreach($queries as $query)
{
  $db->SQL($query);
  $db->Query();
}
$grootboeken=array();
$tst->changeField("laatstePortefeuilleWaarde","toelichting",array("Type"=>"varchar(200)","Null"=>false));

$tmp=array('BEH','BEW','KNBA','KOBU','KOST','ROER','TOB','DIV','VTRES','HUUR','VMAR','VKSTO','RENTE','RENOB','RENME','DIVBE','OG');
foreach($tmp as $grootboek)
  $tst->changeField("laatstePortefeuilleWaarde",$grootboek,array("Type"=>"double","Null"=>false));






?>