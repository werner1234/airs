<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/11/06 07:19:36 $
 		File Versie					: $Revision: 1.6 $

 		$Log: getClient.php,v $
 		Revision 1.6  2019/11/06 07:19:36  cvs
 		update 6-11-2019
 		
 		Revision 1.5  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.4  2018/07/24 06:41:13  cvs
 		call 7041
 		
 		Revision 1.3  2017/09/06 08:18:34  cvs
 		megaupdate 201709
 		
 		Revision 1.2  2016/10/19 07:17:33  cvs
 		call 3856
 		
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

$zoek = iconv("UTF-8", "Windows-1252", $_GET["term"]);
$zoek = mysql_real_escape_string($zoek);
$db = new DB();

// Deze query doet er soms 10 + seconden over om resultaat te geven
//$query = "
//
//SELECT
//  Clienten.Client,
//  Clienten.Naam,
//  Portefeuilles.Vermogensbeheerder
//FROM
//  Clienten
//LEFT JOIN Portefeuilles ON
//  Clienten.Client = Portefeuilles.Client
//WHERE
//  ( Clienten.Client LIKE '%".$zoek."%' OR
//    Clienten.Naam LIKE '%".$zoek."%'   )
//GROUP BY
//	Clienten.Client,
//  Portefeuilles.Vermogensbeheerder
//ORDER BY
//  Clienten.Client
//LIMIT 50
//
//";

// Deze query doet het in minder als 1 seconde
$query = "
SELECT 
  DISTINCT * 
FROM
  (
    SELECT
      DISTINCT Clienten.Client,
      Clienten.Naam,
      Portefeuilles.Vermogensbeheerder
    FROM
      Clienten
    INNER JOIN Portefeuilles ON
      Clienten.Client = Portefeuilles.Client
    WHERE
      ( 
        Clienten.Client LIKE '%".$zoek."%' OR
        Clienten.Naam LIKE '%".$zoek."%'   
      )

      UNION

      SELECT
        DISTINCT Clienten.Client,
        Clienten.Naam,
        'nb' AS vbh
      FROM
        Clienten
      WHERE
      ( 
        Clienten.Client LIKE '%".$zoek."%' OR
        Clienten.Naam LIKE '%".$zoek."%'   
      )
  ) a
GROUP BY 
  a.Client,
  a.Naam
ORDER BY
  1, 3 DESC
LIMIT 50 
";

//debug($query);
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{

  $output[] = array(
    "label"         => $rec["Client"]." | ".$rec["Naam"]." | ".$rec["Vermogensbeheerder"],
    "value"         => $rec["Client"],
    "Client"        => $rec["Client"],
    "Desc"          => $rec["Naam"]);
}

echo json_encode($output);

?>