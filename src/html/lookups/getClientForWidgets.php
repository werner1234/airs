<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/01/20 13:55:46 $
 		File Versie					: $Revision: 1.6 $

 		$Log: getClientForWidgets.php,v $
 		Revision 1.6  2020/01/20 13:55:46  cvs
 		call 8357
 		
 		Revision 1.5  2018/07/24 06:41:13  cvs
 		call 7041
 		
 		Revision 1.4  2018/04/25 06:34:45  cvs
 		call 6824
 		
 		Revision 1.3  2017/09/27 14:31:49  cvs
 		call 6159
 		
 		Revision 1.2  2017/09/06 08:18:34  cvs
 		megaupdate 201709
 		
 		Revision 1.1  2017/05/30 14:14:42  cvs
 		no message
 		
 		Revision 1.2  2016/10/19 07:17:33  cvs
 		call 3856
 		
 		Revision 1.1  2016/09/02 13:39:33  cvs
 		no message

*/

include_once("../../config/local_vars.php");

include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
require("../../config/checkLoggedIn.php");

//if (strlen(trim($_GET["term"])) < 2)
//{
//  exit;
//}


$DB = new DB();
$cfg = new AE_config();

$USR = $_SESSION['usersession']['gebruiker']["Gebruiker"];

$extraJoin = "";
if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
{
  $extraWhere = " (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') AND ";
}
else
{
  $extraJoin =
    "
     INNER JOIN VermogensbeheerdersPerGebruiker ON 
       Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
     JOIN Gebruikers ON 
       Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";

  $extraWhere = " (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND ";
}


$zoek = $_GET["term"];

$db = new DB();

$query = "

SELECT
	CRM_naw.id,
	CRM_naw.naam,
	CRM_naw.portefeuille,
	CRM_naw.zoekveld,
	IF (emailZakelijk ='', email , emailZakelijk) as email
FROM
	(CRM_naw)
LEFT JOIN Portefeuilles ON CRM_naw.Portefeuille = Portefeuilles.Portefeuille
$extraJoin
WHERE
  $extraWhere
  CRM_naw.aktief = 1
AND (
	CRM_naw.portefeuille LIKE '%".$zoek."%'
	OR CRM_naw.zoekveld LIKE '%".$zoek."%'
	OR CRM_naw.naam LIKE '%".$zoek."%'
)
ORDER BY 
  CRM_naw.zoekveld,
  CRM_naw.portefeuille
LIMIT 50
";


$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $rec["naam"] = str_replace("'", "`", $rec["naam"]);
  $output[] = array(
    "label"         => $rec["portefeuille"]." | ".$rec["naam"] ." (".trim($rec["email"]).")",
    "value"         => $rec["portefeuille"],
    "portefeuille"  => $rec["portefeuille"],
    "naam"          => $rec["naam"],
    "email"         => trim($rec["email"]),
    "relId"         => $rec["id"]);
}

echo json_encode($output);

?>