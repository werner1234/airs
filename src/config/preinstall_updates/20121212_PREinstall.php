<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();

$tst->changeField("Vermogensbeheerders","ddVerwijderen",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("CRM_naw","inkomenIndicatie",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","vermogenOnroerendGoed",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","vermogenHypotheek",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","vermogenOverigVermogen",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","vermogenOverigSchuld",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","vermogenTotaalBelegbaar",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","vermogenBelegdViaDC",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","huidigesamenstellingAandelen",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","huidigesamenstellingObligaties",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","huidigesamenstellingOverige",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","huidigesamenstellingLiquiditeiten",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","huidigesamenstellingTotaal",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","verplichtingenBelasting",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","verplichtingenAssurantien",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","verplichtingenKrediet",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","verplichtingenAlimentatie",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","verplichtingenStudieKinderen",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","toekomstigErfenis",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","toekomstigKapitaalsverz",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","toekomstigVerkoopZaak",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","toekomstigOptieregeling",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","toekomstigPensioenopbouw",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));
$tst->changeField("CRM_naw","part_inkomenIndicatie",array("Type"=>"double(11,2)","Null"=>false,'Default'=>'default \'0.00\''));

?>