<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("VragenVragenlijsten","titel",array("Type"=>"varchar(255)","Null"=>false));
$tst->changeField("VragenVragenlijsten","tekstRisicoprofiel",array("Type"=>"tinyint","Null"=>false,'Default'=>'default 1'));
$tst->changeField("VragenVragenlijsten","extraInfo",array("Type"=>"mediumtext","Null"=>false));

