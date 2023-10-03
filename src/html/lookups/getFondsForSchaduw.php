<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/07/24 06:41:13 $
 		File Versie					: $Revision: 1.2 $

 		$Log: getFondsForSchaduw.php,v $
 		Revision 1.2  2018/07/24 06:41:13  cvs
 		call 7041
 		
 		Revision 1.1  2018/04/04 14:45:08  cvs
 		call 6749
 		
 		Revision 1.6  2017/10/26 06:08:35  cvs
 		call 6253
 		
 		Revision 1.5  2016/09/30 06:36:23  cvs
 		call 4848: derde bestand Kasbankl
 		
 		Revision 1.4  2016/09/02 13:39:33  cvs
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


$zoek = $_GET["term"];

if ($_REQUEST["fondsSel"] == "alle")
{
  $where = "";
}
else
{
  $where = "AND (EindDatum > NOW() OR EindDatum = '0000-00-00')";
}

$db = new DB();

$query = "
SELECT
  *
FROM
  Fondsen
WHERE
  (Fonds LIKE '%$zoek%')  
$where
ORDER BY
  fondssoort,
  Fondsen.Omschrijving
LIMIT 100
";


$db->executeQuery($query);
while ($rec = $db->nextRecord())
{

  $output[] = array(
    "label"             => $rec["Fonds"]." | ".$rec["ISINCode"]." | ".$rec["Valuta"]." | ".$rec["Omschrijving"]."",
    "value"             => $rec["Fonds"],
    "Omschrijving"      => $rec["Omschrijving"],
    "Fonds"             => $rec["Fonds"]

    );
}

echo json_encode($output);

?>