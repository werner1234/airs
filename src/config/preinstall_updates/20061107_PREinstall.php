<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/12/07 16:10:48 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20061107_PREinstall.php,v $
 		Revision 1.1  2006/12/07 16:10:48  rvv
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

$tst->changeField("Vermogensbeheerders","OIR",array("Type"=>"tinyint(4)", "Null"=>false));
$tst->changeField("Vermogensbeheerders","GRAFIEK",array("Type"=>"tinyint(4)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","AfdrukvolgordeOIR",array("Type"=>"tinyint(4)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","AfdrukvolgordeGrafiek",array("Type"=>"tinyint(4)","Null"=>false)); 

?>