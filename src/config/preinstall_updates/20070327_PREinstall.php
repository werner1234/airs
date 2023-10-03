<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/03/27 15:02:40 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20070327_PREinstall.php,v $
 		Revision 1.1  2007/03/27 15:02:40  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/21 16:10:31  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/11 10:58:12  rvv
 		modelportefeuille
 		
 		Revision 1.1  2006/12/07 16:10:48  rvv
 		*** empty log message ***
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("TijdelijkeRapportage","historischeRapportageValutakoers",array("Type"=>"double","NULL"=>false));
$tst->changeField("Vermogensbeheerders","grafiek_sortering",array("Type"=>"tinyint(4)","NULL"=>false));





?>