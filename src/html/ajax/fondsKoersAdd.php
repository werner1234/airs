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
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/AE_cls_fondskoers.php");

require("../../config/checkLoggedIn.php");

if (count($_POST) == 0)
{
  exit;
}

$fnd = new AE_cls_fondskoers();
$fnd->user = $_POST["user"];
$d = explode("-", $_POST["datum"]);
$fnd->addFondsKoers($_POST["fonds"], $d[2]."-".$d[1]."-".$d[0], $_POST["koers"]);

echo '{"result": "ok"}';


