<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/24 06:38:40 $
    File Versie         : $Revision: 1.3 $

    $Log: AE-jqueryPluginInvulinstructieLookup.php,v $
    Revision 1.3  2018/07/24 06:38:40  cvs
    call 7041

    Revision 1.2  2017/03/29 13:27:39  cvs
    call 5027 invul instructies

    Revision 1.1  2016/06/20 08:21:27  cvs
    call 4848: derde bestand Kasbankl



*/

include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/AIRS_invul_instructies.php");
include_once("../../classes/AE_cls_Json.php");
session_start();
if (!isset($_SESSION["USR"]))
{
  header("HTTP/1.0 404 Not Found");
  exit;
}
$inst = new AIRS_invul_instructies($_POST["party"]);
$rec = $inst->getInstructie($_POST["script"],$_POST["field"],$_POST["value"]);

$js = new AE_Json();
echo $js->json_encode($rec);
//debug($rec);
return true;
?>