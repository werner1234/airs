<?php

include("wwwvars.php");

include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("Portefeuilles","selectieveld1",array("Type"=>"varchar(40)","Null"=>false));
$tst->changeField("Portefeuilles","selectieveld2",array("Type"=>"varchar(40)","Null"=>false));
$tst->changeField("Portefeuilles","AfwStartdatumRend",array("Type"=>"date","Null"=>false));
