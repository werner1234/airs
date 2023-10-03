<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/10/18 11:22:28 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20141011_PREinstall.php,v $
 		Revision 1.1  2014/10/18 11:22:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/30 16:27:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Rekeningen","Rekening",array("Type"=>"varchar(20)","Null"=>false));
$tst->changeField("Rekeningen","Portefeuille",array("Type"=>"varchar(12)","Null"=>false));
$tst->changeField("Rekeningen","Valuta",array("Type"=>"varchar(4)","Null"=>false));
$tst->changeField("Rekeningen","Memoriaal",array("Type"=>"tinyint(4)","Null"=>false));
$tst->changeField("Rekeningen","Tenaamstelling",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("Rekeningen","Termijnrekening",array("Type"=>"tinyint(4)","Null"=>false));
$tst->changeField("Rekeningen","add_date",array("Type"=>"datetime","Null"=>false));
$tst->changeField("Rekeningen","add_user",array("Type"=>"varchar(10)","Null"=>false));
$tst->changeField("Rekeningen","change_date",array("Type"=>"datetime","Null"=>false));
$tst->changeField("Rekeningen","change_user",array("Type"=>"varchar(10)","Null"=>false));

?>