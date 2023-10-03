<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/06/25 20:07:27 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110625_PREinstall.php,v $
 		Revision 1.1  2011/06/25 20:07:27  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/06/13 14:34:33  rvv
 		*** empty log message ***


*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("TijdelijkeRapportage","beleggingscategorieOmschrijving",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("TijdelijkeRapportage","beleggingssectorOmschrijving",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("TijdelijkeRapportage","hoofdcategorie",array("Type"=>"varchar(15)","Null"=>false));
$tst->changeField("TijdelijkeRapportage","hoofdsector",array("Type"=>"varchar(15)","Null"=>false));
$tst->changeField("TijdelijkeRapportage","hoofdcategorieOmschrijving",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("TijdelijkeRapportage","hoofdsectorOmschrijving",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("TijdelijkeRapportage","hoofdcategorieVolgorde",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'127\''));
$tst->changeField("TijdelijkeRapportage","hoofdsectorVolgorde",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'127\''));
$tst->changeField("TijdelijkeRapportage","beleggingssectorVolgorde",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'127\''));
$tst->changeField("TijdelijkeRapportage","beleggingscategorieVolgorde",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'127\''));
$tst->changeField("TijdelijkeRapportage","valutaVolgorde",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'127\''));
$tst->changeField("TijdelijkeRapportage","regioOmschrijving",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("TijdelijkeRapportage","regioVolgorde",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'127\''));
$tst->changeField("TijdelijkeRapportage","attributieCategorieVolgorde",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'127\''));
$tst->changeField("TijdelijkeRapportage","attributieCategorieOmschrijving",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("TijdelijkeRapportage","valutaOmschrijving",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("KeuzePerVermogensbeheerder","Afdrukvolgorde",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));

?>