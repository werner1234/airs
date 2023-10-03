<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/11/21 14:19:59 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20061116_PREinstall.php,v $
 		Revision 1.1  2006/11/21 14:19:59  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2006/10/23 06:22:58  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/10/23 06:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/06/28 12:20:30  jwellner
 		*** empty log message ***
 		
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");



$tst = new SQLman();

$tst->changeField("Fondsen","HeeftOptie",array("Type"=>"tinyint(4)", "Null"=>false));
$tst->changeField("Fondsen","OptieType",array("Type"=>"varchar(1)","Null"=>false)); 
$tst->changeField("Fondsen","OptieExpDatum",array("Type"=>"varchar(6)","Null"=>false)); 
$tst->changeField("Fondsen","OptieUitoefenPrijs",array("Type"=>"double","Null"=>false)); 
$tst->changeField("Fondsen","OptieBovenliggendFonds",array("Type"=>"varchar(25)","Null"=>false)); 


?>