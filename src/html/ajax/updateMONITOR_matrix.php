<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/06/10 09:21:18 $
    File Versie         : $Revision: 1.1 $

    $Log: updateMONITOR_matrix.php,v $
    Revision 1.1  2020/06/10 09:21:18  cvs
    call 8579


*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/MONITOR_importMatrixHelper.php");

require("../../config/checkLoggedIn.php");

if (count($_POST) == 0)
{
  exit;
}


$hlp = new MONITOR_importMatrixHelper();

$hlp->setKlaargezet($_POST["bedrijf"], $_POST["datum"], $_POST["prio1"]);

echo '{ "result": "ok"}';
