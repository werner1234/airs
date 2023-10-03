<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/24 06:36:05 $
    File Versie         : $Revision: 1.3 $

    $Log: updateCrmInport.php,v $
    Revision 1.3  2018/07/24 06:36:05  cvs
    call 7041

    Revision 1.2  2017/11/17 11:00:04  cvs
    call 6145

    Revision 1.1  2017/11/15 11:13:25  cvs
    call 6145

    Revision 1.3  2017/05/29 07:50:07  cvs
    no message

    Revision 1.2  2016/12/02 14:03:03  cvs
    call 5086

    Revision 1.1  2016/10/24 10:24:02  cvs
    call 5086



*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/AIRS_cls_CRM_naw_importHelper.php");
error_reporting(E_ERROR);
require("../../config/checkLoggedIn.php");

if (trim($_POST['action']) == "")
{
  exit;
}


session_start();
$USR = $_SESSION["USR"];

if ( $import = new CRM_naw_importHelper($_POST["action"]))
{
  echo json_encode($import->settings);
}
else
{
  echo json_encode(array("result"=>false));
}

