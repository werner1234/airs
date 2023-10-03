<?php
/**
 * Created by PhpStorm.
 * User: bdl
 * Date: 30-10-2017
 * Time: 10:02
 */


include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
require("../../config/checkLoggedIn.php");

if (trim($_POST['msCategoriesoort']) == "")
{
  exit;
}

$USR = $_SESSION["USR"];
$data = array();
$db = new DB();

        $query = "SELECT msCategorie
                          FROM doorkijk_msCategoriesoort
                          WHERE  msCategorieSoort = '" . mysql_real_escape_string($_POST['msCategoriesoort']) . "' 
                          ORDER BY msCategorie
                     ";
        //debug($query);
        $db->executeQuery($query);
        while ($rec = $db->nextRecord())
        {
            $data[] = array(
                "id"   => $rec["msCategorie"],
                "desc" => $rec["msCategorie"]
            );
        }

echo json_encode($data);