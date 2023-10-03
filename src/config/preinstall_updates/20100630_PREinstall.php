<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/06/30 16:07:59 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100630_PREinstall.php,v $
 		Revision 1.1  2010/06/30 16:07:59  rvv
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



$tst->changeField("CRM_naw","beleggingsHorizon",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","inContactDoor",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","risicoprofiel",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","beleggingsDoelstelling",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringMetGestructureerdeProducten",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringMetFutures",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringMetOpties",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringMetIndividueleAandelen",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringMetBeleggingsFondsen",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringMetVastrentende",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringInExecutionOnly",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringBelegtInVermogensadvies",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringInVermogensbeheer",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","ervaringBelegtInEigenbeheer",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","part_inkomenSoort",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","inkomenSoort",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","verzendFreq",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","part_legitimatie",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("CRM_naw","legitimatie",array("Type"=>"varchar(60)","Null"=>false)); 




?>