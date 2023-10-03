<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/02/17 11:29:29 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: 20100213_PREinstall.php,v $
 		Revision 1.2  2010/02/17 11:29:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/02/14 12:43:17  rvv
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
$tst->changeField("Vermogensbeheerders","koersExport",array("Type"=>"tinyint(4)","Null"=>false)); 
$tst->changeField("CRM_naw","kaartVerstuurd",array("Type"=>"date","Null"=>false)); 
$tst->changeField("CRM_naw","kaartVerstuurdPartner",array("Type"=>"date","Null"=>false)); 
$tst->changeField("CRM_naw_dossier","duur",array("Type"=>"time","Null"=>false)); 
$tst->changeField("FactuurHistorie","betaald",array("Type"=>"tinyint(4)","Null"=>false)); 

$DB=new DB();
$query="SHOW FIELDS FROM HistorischePortefeuilleIndex";
$DB->SQL($query);
$DB->Query();
while($data=$DB->nextRecord())
{
  if($data['Field'] == 'betaald')
  {
    $query="ALTER TABLE HistorischePortefeuilleIndex DROP betaald";
	$DB->SQL($query);
    $DB->Query();
  }
}





?>