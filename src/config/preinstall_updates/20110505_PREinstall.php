<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/11/23 19:09:29 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110505_PREinstall.php,v $
 		Revision 1.1  2011/11/23 19:09:29  rvv
 		*** empty log message ***
 		
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
$tst->changeField("Vermogensbeheerders","VerouderdeKoersDagen",array("Type"=>"int","Null"=>false));



?>