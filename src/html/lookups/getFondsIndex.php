<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/07/24 06:41:13 $
 		File Versie					: $Revision: 1.2 $

 		$Log: getFondsIndex.php,v $
 		Revision 1.2  2018/07/24 06:41:13  cvs
 		call 7041
 		
 		Revision 1.1  2016/09/02 13:39:33  cvs
 		no message
 		


*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
require("../../config/checkLoggedIn.php");

if (strlen(trim($_GET["term"])) < 2)
{
  exit;
}

$DB = new DB();

//debug($_GET);

$zoek = $_GET["term"];

$db = new DB();

$query = "
SELECT
  *
FROM
  Fondsen
WHERE
 ( Fonds LIKE '%$zoek%' OR ISINCode LIKE '%$zoek%' OR Omschrijving LIKE '%$zoek%' ) 
AND
  (EindDatum > NOW() OR EindDatum = '0000-00-00')
AND
  fondssoort NOT IN ('OPT','STOCK')
ORDER BY
  Fondsen.fondssoort,
  Fondsen.Omschrijving
LIMIT 30
";


$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $output[] = array(
    "label"         => $rec["Fonds"]." :: ".$rec["Omschrijving"],
    "value"         => $rec["Fonds"],
    "Omschrijving"  => $rec["Omschrijving"],
    "Fonds"         => $rec["Fonds"]);
}


echo json_encode($output);



?>