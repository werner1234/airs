<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/24 06:36:05 $
    File Versie         : $Revision: 1.2 $

    $Log: getRekeningValidatePerVb.php,v $
    Revision 1.2  2018/07/24 06:36:05  cvs
    call 7041

    Revision 1.1  2017/09/20 06:13:37  cvs
    megaupdate




*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");

require("../../config/checkLoggedIn.php");

if (trim($_POST['portefeuille']) == "")
{
  exit;
}



$USR = $_SESSION["USR"];
$data = array();
$db = new DB();
$db = new DB();
$query="
SELECT 
  check_rekeningATT,
  check_rekeningCat,
  check_rekeningDepotbank 
FROM 
  `Vermogensbeheerders` 
JOIN `Portefeuilles` ON 
  `Portefeuilles`.Vermogensbeheerder = `Vermogensbeheerders`.Vermogensbeheerder
WHERE 
  `Portefeuille` = '".mysql_real_escape_string($_POST["portefeuille"])."'";

$valRec = $db->lookupRecordByQuery($query);
$data["AttributieCategorie"] = $valRec['check_rekeningATT'];
$data["Beleggingscategorie"] = $valRec['check_rekeningCat'];
$data["rekeningDepotbank"]   = $valRec['check_rekeningDepotbank'];

echo json_encode($data);

?>