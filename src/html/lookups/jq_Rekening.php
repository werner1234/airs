<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/10/14 13:04:08 $
 		File Versie					: $Revision: 1.3 $
*/

/**
 * Ajax Lookup
 * 
 * @author RM
 * @since 16-10-2014
 * 
 * Loads local data including vars and databases
 * 
 * Loads the ajax class
 * 
 */
include_once('../../config/local_vars.php');
include_once('../../config/vars.php');
require("../../config/checkLoggedIn.php");

if (strlen(trim($_GET["term"])) < 2)
{
  exit;
}

$zoek = $_GET["term"];    

$db = new DB();

$query = 

$query = "SELECT Rekening, Portefeuille, Tenaamstelling FROM Rekeningen WHERE consolidatie=0 AND Rekening LIKE '".$zoek."%' ORDER BY Rekening LIMIT 50";
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
   $factuurTekst = (str_replace("\r\n", "##", $rec["factuurTekst"]));
  $output[] = array(
    "label"        => trim($rec["Rekening"])." / ".$rec["Portefeuille"]." / ".$rec["Tenaamstelling"],
    "Rekening"       => $rec["Rekening"]
    );
}

echo json_encode($output);

?>