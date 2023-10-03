<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/07/24 06:41:13 $
 		File Versie					: $Revision: 1.2 $

 		$Log: getRekening.php,v $
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

$zoek = $_GET["term"];

$db = new DB();

$query = "
SELECT 
  Rekeningen.Rekening, 
  Rekeningen.Portefeuille, 
  Portefeuilles.Client,
   Rekeningen.Depotbank
FROM 
  Rekeningen
JOIN Portefeuilles ON
   Portefeuilles.Portefeuille = Rekeningen.Portefeuille
WHERE 
  Rekeningen.Rekening LIKE '".$zoek."%' OR  
  Rekeningen.Portefeuille LIKE '".$zoek."%'   
ORDER BY 
  Rekeningen.Rekening 
LIMIT 35";


$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $output[] = array(
    "label"      => $rec["Rekening"]." | ".$rec["Depotbank"]." | ".$rec["Portefeuille"]." | ".$rec["Client"],
    "Rekening"   => $rec["Rekening"],
    "value"      => $rec["Rekening"]);
}

echo json_encode($output);

?>