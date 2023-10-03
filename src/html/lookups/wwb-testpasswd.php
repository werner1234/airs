<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/07/24 06:42:52 $
 		File Versie					: $Revision: 1.4 $

 		$Log: wwb-testpasswd.php,v $
 		Revision 1.4  2018/07/24 06:42:52  cvs
 		call 7041
 		
 		Revision 1.3  2017/09/27 11:32:36  cvs
 		call 5932
 		
 		Revision 1.2  2017/01/04 13:05:39  cvs
 		call 5542, uitrol WWB en TGC
 		
 		Revision 1.1  2016/03/18 14:27:25  cvs
 		call 3691
 		
 		

*/

include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_crypt.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/AE_cls_toegangsControle.php");
include_once $__appvar["basedir"].'/classes/AE_cls_secruity.php';
require("../../config/checkLoggedIn.php");

if (trim($_POST["user"]) == "")
{
  exit;
}

// username is max 10 lang in db en mag geen spaties bevatten
$usernameParts = explode(" ",substr($_POST['user'],0,10));
$username = $usernameParts[0];

$db = new DB();
$cfg = new AE_config();


$query = "SELECT * FROM Gebruikers WHERE Gebruiker = '".mysql_real_escape_string($username)."' ";
if (!$userRec = $db->lookupRecordByQuery($query))
{
  echo "invalidLogin";
  return;
}
$passwdChecked = false;
$sec = new AE_cls_secruity($_POST["user"]);


$query = "SELECT * FROM GebruikersLogin WHERE userID = ".(int)$userRec["id"];
$loginRec = $sec->loginRec;
//debug($loginRec);
//
//echo "\n ".$_POST["passwd"];
//echo "\ni :".$sec->pwHash($_POST["passwd"]);
//echo "\ni2 :".$sec->pwHash($_POST["passwd"]);
//echo "\nt :".$loginRec["huidigWW"];
if ($sec->pwHash($_POST["passwd"]) == $loginRec["huidigWW"])
{
  echo "false";
}
else
{
  echo "true";
}
?>