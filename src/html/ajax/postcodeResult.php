<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/24 06:36:05 $
    File Versie         : $Revision: 1.5 $

    $Log: updateAEconfig.php,v $

*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_postcode.php");

require("../../config/checkLoggedIn.php");

if (count($_POST) == 0)
{
  exit;
}


$pc = new AE_cls_postcode();

$pc->fetch($_POST["postcode"],$_POST["huisnr"]);

echo $pc->result;
