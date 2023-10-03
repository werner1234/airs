<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/24 06:36:05 $
    File Versie         : $Revision: 1.2 $

    $Log: getKeuzePerVb.php,v $
    Revision 1.2  2018/07/24 06:36:05  cvs
    call 7041

    Revision 1.1  2017/09/20 06:13:37  cvs
    megaupdate




*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");

require("../../config/checkLoggedIn.php");

if (trim($_POST['vb']) == "")
{
  exit;
}


session_start();
$USR = $_SESSION["USR"];
$data = array();
$db = new DB();

foreach ($_POST as $k=>$v)
{
  $_POST[$k] = mysql_real_escape_string($v);
}


$query = "
  SELECT 
    * 
  FROM 
    `KeuzePerVermogensbeheerder` 
  WHERE 
    `vermogensbeheerder` = '".$_POST['vb']."'  AND
    `categorie` = '".$_POST['cat']."' 
  ORDER BY
    `waarde`";

$data[] = array(
  "id"   => "",
  "desc" => "---"
);
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $data[] = array(
    "id"   => $rec["waarde"],
    "desc" => $rec["waarde"]
  );
}

echo json_encode($data);


?>