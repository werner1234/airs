<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.1 $

 		$Log: updatedbAfvink.php,v $
 		Revision 1.1  2018/09/23 17:14:23  cvs
 		call 7175
 		



*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->tableExist("RekeningmutatiesAfvink",true);
$tst->changeField("RekeningmutatiesAfvink","matchCode",array("Type"=>" varchar(20)","Null"=>false));
$tst->changeField("RekeningmutatiesAfvink","rekmut_id",array("Type"=>" int","Null"=>false));
$tst->changeField("RekeningmutatiesAfvink","status",array("Type"=>" tinyint","Null"=>false));
$tst->changeField("RekeningmutatiesAfvink","grootboek",array("Type"=>" varchar(10)","Null"=>false));
$tst->changeField("RekeningmutatiesAfvink","opmerking",array("Type"=>" text","Null"=>false));
$tst->changeField("RekeningmutatiesAfvink","bedrag",array("Type"=>" double","Null"=>false));
$tst->changeField("RekeningmutatiesAfvink","volgnr",array("Type"=>" int","Null"=>false));

$tst->changeField("RekeningmutatiesAfvink","bedragOrg",array("Type"=>" double","Null"=>false));
$tst->changeField("RekeningmutatiesAfvink","grootboekOrg",array("Type"=>" varchar(10)","Null"=>false));
$tst->changeField("RekeningmutatiesAfvink","boekdatumOrg",array("Type"=>" date","Null"=>false));
$tst->changeField("RekeningmutatiesAfvink","batchCode", array("Type"=>" varchar(20)","Null"=>false));
$tst->changeField("RekeningmutatiesAfvink","rekening", array("Type"=>" varchar(32)","Null"=>false));
$tst->changeField("RekeningmutatiesAfvink","omschrijving", array("Type"=>" varchar(50)","Null"=>false));


echo (int)$tst->counter['skipped']. " mutatie eerder verwerkt<br>";
echo (int)$tst->counter['succes']. " mutatie nu verwerkt<br>";
listarray($tst->counter["SQL"]);

echo "klaar";

?>
