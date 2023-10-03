<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/09/04 10:00:32 $
    File Versie         : $Revision: 1.1 $

    $Log: updatereconMatrix.php,v $
    Revision 1.1  2019/09/04 10:00:32  cvs
    call 7934


*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/reconMonitor_importMatrixHelper.php");

require("../../config/checkLoggedIn.php");

if (count($_POST) == 0)
{
  exit;
}


$hlp = new reconMonitor_importMatrixHelper();

$hlp->ajaxUpdate($_POST);

echo '{ "result": "ok"}';
