<?php
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("custom_templates", "meertalig", array("Type" => "tinyint(1)", "Null" => false));