<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/10/17 09:39:46 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20101016_PREinstall.php,v $
 		Revision 1.1  2010/10/17 09:39:46  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/10/02 14:34:33  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/07/31 16:10:42  rvv
 		*** empty log message ***

 		Revision 1.1  2010/07/25 14:34:30  rvv
 		*** empty log message ***

 		Revision 1.1  2010/04/28 15:50:26  rvv
 		*** empty log message ***

 		Revision 1.1  2010/04/24 19:15:41  rvv
 		*** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("TijdelijkeRapportage","koersDatum",array("Type"=>"date","Null"=>false));
$tst->changeField("Rekeningen","Inactief",array("Type"=>"tinyInt","Null"=>false));





?>