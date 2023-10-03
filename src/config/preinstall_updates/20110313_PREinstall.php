<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/05/04 16:25:11 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110313_PREinstall.php,v $
 		Revision 1.1  2011/05/04 16:25:11  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/01/29 15:54:40  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/01/26 17:18:16  rvv
 		*** empty log message ***


*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("HistorischePortefeuilleIndex","gerealiseerd",array("Type"=>"double","Null"=>false));
$tst->changeField("HistorischePortefeuilleIndex","ongerealiseerd",array("Type"=>"double","Null"=>false));
$tst->changeField("HistorischePortefeuilleIndex","rente",array("Type"=>"double","Null"=>false));
$tst->changeField("HistorischePortefeuilleIndex","gemiddelde",array("Type"=>"double","Null"=>false));
$tst->changeField("HistorischePortefeuilleIndex","extra",array("Type"=>"text","Null"=>false));

$tst->changeField("CRM_naw","accountEigenaar",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("CRM_naw_rekeningen","IBAN",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("CRM_naw_dossier","type",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("CRM_naw_kontaktpersoon","functie",array("Type"=>"varchar(100)","Null"=>false));





?>