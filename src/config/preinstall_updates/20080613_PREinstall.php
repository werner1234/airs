<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/06/13 08:39:38 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20080613_PREinstall.php,v $
 		Revision 1.1  2008/06/13 08:39:38  rvv
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

$tst->changeField("Portefeuilles","tweedeAanspreekpunt",array("Type"=>"varchar(15)","Null"=>false)); 
$tst->changeField("Portefeuilles","maandAfdrukken",array("Type"=>"tinyint(4)","Null"=>false)); 
$tst->changeField("Portefeuilles","kwartaalAfdrukken",array("Type"=>"tinyint(4)","Null"=>false)); 
$tst->changeField("Portefeuilles","beperktToegankelijk",array("Type"=>"tinyint(1)","Null"=>false));  
$tst->changeField("Portefeuilles","CrmPortefeuilleInformatie",array("Type"=>"tinyint(1)","Null"=>false));  

$tst->changeField("Gebruikers","beperkingOpheffen",array("Type"=>"tinyint(1)","Null"=>false)); 

$tst->changeField("CRM_naw_cf","provisieAfspraak",array("Type"=>"text","Null"=>false)); 
$tst->changeField("CRM_naw_cf","opleidingsniveau",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","verplichtingenBelasting",array("Type"=>"double(10,2)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","verplichtingenAssurantien",array("Type"=>"double(10,2)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","verplichtingenKrediet",array("Type"=>"double(10,2)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","verplichtingenAlimentatie",array("Type"=>"double(10,2)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","verplichtingenStudieKinderen",array("Type"=>"double(10,2)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","toekomstigErfenis",array("Type"=>"double(10,2)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","toekomstigKapitaalsverz",array("Type"=>"double(10,2)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","toekomstigVerkoopZaak",array("Type"=>"double(10,2)","Null"=>false));
$tst->changeField("CRM_naw_cf","toekomstigOptieregeling",array("Type"=>"double(10,2)","Null"=>false));  
$tst->changeField("CRM_naw_cf","toekomstigPensioenopbouw",array("Type"=>"double(10,2)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","ervaringMetGestructureerdeProductenDatum",array("Type"=>"date","Null"=>false)); 
$tst->changeField("CRM_naw_cf","ervaringMetGestructureerdeProducten",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","ervaringBelegtInEigenbeheer",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","ervaringBelegtInVermogensadvies",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","ervaringBelegtInProducten",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","ervaringMetVastrentende",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","ervaringMetBeleggingsFondsen",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","ervaringMetIndividueleAandelen",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","ervaringMetOpties",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw_cf","ervaringMetFutures",array("Type"=>"varchar(20)","Null"=>false)); 

$query = "UPDATE Portefeuilles SET kwartaalAfdrukken = AantalAfdrukken";
$DB= new DB();
$DB->SQL($query);
$DB->Query();

if($DB->QRecords("SELECT id FROM CRM_selectievelden WHERE module = 'ervaring' AND omschrijving = '1'") < 1)
{
  $query = "INSERT INTO CRM_selectievelden SET module = 'ervaring', omschrijving = '1' ";
  $DB->SQL($query);
  $DB->Query();
}







?>