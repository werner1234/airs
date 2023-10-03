<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/02/18 10:37:59 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110218_PREinstall.php,v $
 		Revision 1.1  2011/02/18 10:37:59  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/01/29 15:54:40  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/01/26 17:18:16  rvv
 		*** empty log message ***


*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Rekeningmutaties","Aantal",array("Type"=>"double","Null"=>false));





?>