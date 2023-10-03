<?php
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","documentloosPortaal",array("Type"=>"tinyint(1)","Null"=>false));
$tst->changeIndex('Rekeningmutaties','Boekdatum',array('columns'=>array('Boekdatum')));
$tst->changeIndex('Fondskoersen','koersDatum',array('columns'=>array('Fonds','Datum')));

