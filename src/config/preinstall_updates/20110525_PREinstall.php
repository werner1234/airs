<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/05/25 17:19:12 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110525_PREinstall.php,v $
 		Revision 1.1  2011/05/25 17:19:12  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/05/04 16:25:11  rvv
 		*** empty log message ***


*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","VerouderdeKoersDagen",array("Type"=>"int","Null"=>false));
$tst->changeField("CRM_naw_adressen","email",array("Type"=>"varchar(64)","Null"=>false));
$tst->changeField("CRM_naw_adressen","wachtwoord",array("Type"=>"varchar(32)","Null"=>false));

?>