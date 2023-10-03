<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/01/29 15:54:40 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110129_PREinstall.php,v $
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
$tst->changeField("HistorischePortefeuilleIndex","extra",array("Type"=>"text","Null"=>false));





?>