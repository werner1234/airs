<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/05/25 14:36:18 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140525_PREinstall.php,v $
 		Revision 1.1  2014/05/25 14:36:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/05/03 15:46:30  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/19 16:14:54  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/02 15:54:35  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/03/29 16:25:05  rvv
 		*** empty log message ***
 		
	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("CRM_naw_cashflow","totDatum",array("Type"=>"date","Null"=>false));

$DB=new DB();
$query="SHOW FIELDS FROM CRM_naw_cashflow";
$DB->SQL($query);
$DB->Query();
while($data=$DB->nextRecord())
{
  if($data['Field'] == 'totDoelvermogen')
  {
    $query="ALTER TABLE CRM_naw_cashflow DROP totDoelvermogen";
	  $DB->SQL($query);
    $DB->Query();
  }
}

?>