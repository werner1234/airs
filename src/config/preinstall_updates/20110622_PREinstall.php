<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2011/06/22 12:04:05 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20110622_PREinstall.php,v $
 		Revision 1.1  2011/06/22 12:04:05  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2009/01/20 17:53:41  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/06 10:18:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/10/09 06:23:57  cvs
 		gebruikerstabel ivm CRM
 		
 		Revision 1.1  2007/09/27 13:35:24  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/08/24 11:26:49  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2007/08/24 11:25:17  cvs
 		*** empty log message ***
 		
 
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->tableExist("PositieLijst",true);

$tst->changeField("PositieLijst","portefeuille",array("Type"=>"varchar(20)","Null"=>false));
$tst->changeField("PositieLijst","depotbank",array("Type"=>"varchar(20)","Null"=>false));
$tst->changeField("PositieLijst","vermogensbeheerder",array("Type"=>"varchar(20)","Null"=>false));
$tst->changeField("PositieLijst","datum",array("Type"=>"date","Null"=>false));
$tst->changeField("PositieLijst","fondsCode",array("Type"=>"varchar(25)","Null"=>false));
$tst->changeField("PositieLijst","fondsCodeNumeriek",array("Type"=>"varchar(25)","Null"=>false));
$tst->changeField("PositieLijst","fondsSoort",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("PositieLijst","fondsOmschrijving",array("Type"=>"varchar(100)","Null"=>false));
$tst->changeField("PositieLijst","fondsValuta",array("Type"=>"varchar(10)","Null"=>false));
$tst->changeField("PositieLijst","ISIN",array("Type"=>"varchar(20)","Null"=>false));
$tst->changeField("PositieLijst","aantal",array("Type"=>"double","Null"=>false));
$tst->changeField("PositieLijst","optieSoort",array("Type"=>"varchar(10)","Null"=>false));
$tst->changeField("PositieLijst","soort",array("Type"=>"varchar(15)","Null"=>false));
$tst->changeField("PositieLijst","waardeInEUR",array("Type"=>"double","Null"=>false));
$tst->changeField("PositieLijst","waardeInValuta",array("Type"=>"double","Null"=>false));
$tst->changeField("PositieLijst","kostprijs",array("Type"=>"double","Null"=>false));
$tst->changeField("PositieLijst","koers",array("Type"=>"double","Null"=>false));
$tst->changeField("PositieLijst","valutakoers",array("Type"=>"double","Null"=>false));
$tst->changeField("PositieLijst","batchid",array("Type"=>"varchar(30)","Null"=>false));

$tst->tableExist("TijdelijkePositieLijst",true);

$tst->changeField("TijdelijkePositieLijst","portefeuille",array("Type"=>"varchar(20)","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","depotbank",array("Type"=>"varchar(20)","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","vermogensbeheerder",array("Type"=>"varchar(20)","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","datum",array("Type"=>"date","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","fondsCode",array("Type"=>"varchar(25)","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","fondsCodeNumeriek",array("Type"=>"varchar(25)","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","fondsSoort",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","fondsOmschrijving",array("Type"=>"varchar(100)","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","fondsValuta",array("Type"=>"varchar(10)","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","ISIN",array("Type"=>"varchar(20)","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","aantal",array("Type"=>"double","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","optieSoort",array("Type"=>"varchar(10)","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","soort",array("Type"=>"varchar(15)","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","waardeInEUR",array("Type"=>"double","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","waardeInValuta",array("Type"=>"double","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","kostprijs",array("Type"=>"double","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","koers",array("Type"=>"double","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","valutakoers",array("Type"=>"double","Null"=>false));
$tst->changeField("TijdelijkePositieLijst","batchid",array("Type"=>"varchar(30)","Null"=>false));


?>