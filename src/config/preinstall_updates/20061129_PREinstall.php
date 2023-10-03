<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/03/27 15:02:40 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20061129_PREinstall.php,v $
 		Revision 1.1  2007/03/27 15:02:40  rvv
 		*** empty log message ***
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Portefeuilles","BeheerfeeMinJaarBedrag",array("Type"=>"double", "Null"=>false));
$tst->changeField("Portefeuilles","OptieToestaan",array("Type"=>"tinyint(4)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","OptieTools",array("Type"=>"tinyint(4)","Null"=>false)); 

?>