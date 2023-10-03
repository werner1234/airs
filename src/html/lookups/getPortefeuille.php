<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/27 13:37:56 $
 		File Versie					: $Revision: 1.7 $

 		$Log: getPortefeuille.php,v $
 		Revision 1.7  2020/05/27 13:37:56  cvs
 		no message
 		
 		Revision 1.6  2018/10/14 06:51:57  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/07/24 06:41:13  cvs
 		call 7041
 		
 		Revision 1.4  2018/06/20 16:37:55  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/09/06 08:18:34  cvs
 		megaupdate 201709
 		
 		Revision 1.2  2016/09/02 13:39:33  cvs
 		no message
 		
 		Revision 1.1  2016/06/14 06:20:10  cvs
 		call 4564 naar TEST
 		
 		Revision 1.1  2016/03/18 14:27:25  cvs
 		call 3691
 		
 		

*/

include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
require("../../config/checkLoggedIn.php");

if (strlen(trim($_GET["term"])) < 2)
{
  exit;
}

error_reporting(0);
$extra  = "";
//$zoek = mysql_real_escape_string($_GET["term"]);
$zoek = $_GET["term"];
if ($_GET["depot"] != "")
{
  $extra .= " Depotbank = '".$_GET["depot"]."' AND ";
}
if ($_GET["vb"] != "")
{
  $extra .= " Vermogensbeheerder = '".$_GET["vb"]."' AND ";
}

if($_GET["includeConsolidatie"] == "1")
{
  $extra .= " consolidatie<2 AND ";
}
else
{
  $extra .= " consolidatie=0 AND ";
}

$db = new DB();
$query = "SELECT * FROM Portefeuilles WHERE $extra (Portefeuille LIKE '%".$zoek."%'  OR Client  LIKE '%".$zoek."%') AND Einddatum > NOW() LIMIT 35";

$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $output[] = array(
    "label"         => trim($rec["Portefeuille"])." | ".$rec["Depotbank"]." | ".$rec["Client"]. " | ".$rec["Vermogensbeheerder"],
    "value"         => $rec["Portefeuille"],
    "portefeuille"  => $rec["Portefeuille"],
    "depot"         => $rec["Depotbank"],
    "info"          => $rec["Client"],
    "vb"            => $rec["Vermogensbeheerder"]

  );
}

echo json_encode($output);

?>