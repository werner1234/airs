<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/02/12 08:43:51 $
    File Versie         : $Revision: 1.7 $

    $Log: dd_naw.php,v $
    Revision 1.7  2020/02/12 08:43:51  cvs
    call 8418

    Revision 1.6  2018/07/24 06:39:25  cvs
    call 7041

    Revision 1.5  2018/04/13 10:23:46  cvs
    call 6791

    Revision 1.4  2018/04/06 11:25:33  cvs
    call 6791

    Revision 1.3  2018/03/07 15:10:46  cvs
    call 6695

    Revision 1.2  2016/04/22 11:19:15  cvs
    zoeken op naam en portefeuille

    Revision 1.1  2016/04/22 10:10:07  cvs
    call 4296 naar ANO



*/
include_once('../../config/local_vars.php');
include_once('../../config/vars.php');
include_once('../../config/applicatie_functies.php');
include_once('../../classes/AE_cls_mysql.php');

require("../../config/checkLoggedIn.php");

if (strlen(trim($_GET["term"])) < 2)
{
  exit;
}

$zoek = mysql_real_escape_string($_GET["term"]);
$db = new DB();
$query = "SELECT * FROM CRM_naw WHERE (portefeuille LIKE '%$zoek%' OR naam LIKE '%$zoek%'  OR zoekveld LIKE '%$zoek%') LIMIT 15";
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{

  $extra = "";

  $output[] = array(
    "label"        => $extra." ". utf8_encode($rec["naam"])." ".utf8_encode($rec["naam1"])." / ".$rec["portefeuille"],
    "rel_id"       => $rec["id"],
    "relatie"      => utf8_encode($rec["naam"]),
  );

//  print_r($output);
}

echo json_encode($output);
