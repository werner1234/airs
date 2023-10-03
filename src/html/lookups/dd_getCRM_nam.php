<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/24 06:39:25 $
    File Versie         : $Revision: 1.2 $

    $Log: dd_getCRM_nam.php,v $
    Revision 1.2  2018/07/24 06:39:25  cvs
    call 7041

    Revision 1.1  2016/04/22 10:10:07  cvs
    call 4296 naar ANO



*/
include_once('../../config/local_vars.php');
include_once('../../config/vars.php');
include_once('../../config/applicatie_functies.php');
include_once('../../classes/AE_cls_mysql.php');

require("../../config/checkLoggedIn.php");

if ((int)$_POST["id"] == 0)
{
  exit;
}


$db = new DB();
$query = "SELECT * FROM CRM_naw WHERE id = ".(int)$_POST["id"]." ";

$rec = $db->lookupRecordByQuery($query);

echo $rec["naam"]." ".$rec["naam1"]." (".$rec["portefeuille"].") ";
