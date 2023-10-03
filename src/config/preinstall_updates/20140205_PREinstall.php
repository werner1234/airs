<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/02/22 18:36:11 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140205_PREinstall.php,v $
 		Revision 1.1  2014/02/22 18:36:11  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/02/02 10:40:10  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/01/18 17:21:41  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/11/13 15:54:00  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/09/01 13:29:55  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/08/21 15:32:58  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/22 06:35:57  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("TijdelijkeRekeningmutaties","settlementDatum",array("Type"=>"date","Null"=>false));
$tst->changeField("Rekeningmutaties","settlementDatum",array("Type"=>"date","Null"=>false)); 

?>