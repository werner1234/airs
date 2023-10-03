<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/09/04 09:02:04 $
 		File Versie					: $Revision: 1.8 $

 		$Log: wwb-smsRequest.php,v $
 		Revision 1.8  2019/09/04 09:02:04  cvs
 		call 7815
 		
 		Revision 1.7  2019/09/03 06:39:20  cvs
 		call 7815
 		
 		Revision 1.5  2018/07/24 06:42:52  cvs
 		call 7041
 		
 		Revision 1.4  2017/12/15 07:45:13  cvs
 		call 6205
 		
 		Revision 1.3  2017/04/19 07:56:34  cvs
 		call 5761
 		
 		Revision 1.2  2017/01/04 13:05:39  cvs
 		call 5542, uitrol WWB en TGC
 		
 		Revision 1.1  2016/03/18 14:27:25  cvs
 		call 3691
 		
 		

*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_crypt.php");
include_once("../../classes/AE_cls_mysql.php");
include_once $__appvar["basedir"].'/classes/AE_cls_secruity.php';

//require("../../config/checkLoggedIn.php");


$ref = explode("/",$_SERVER["HTTP_REFERER"]);

if ($ref[count($ref)-1] != "login.php")
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
$sec = new AE_cls_secruity($username);

if ($sec->beleid == "")  // klassiek
{
  if  (strtolower($_POST["passwd"]) == strtolower($userRec["Wachtwoord"]))
  {
   // echo "ok";
  }
  else  
  {
    echo "invalidLogin";
    return;
  }

}

$usernameOK = strtolower($_POST["user"]) == strtolower($userRec["Gebruiker"]);

$query = "SELECT * FROM GebruikersLogin WHERE userID = ".$userRec["id"];
$loginRec = $sec->loginRec;

if ($loginRec["huidigWW"] <> "")  
{
  $passwdChecked = ($usernameOK AND $sec->pwHash($_POST["passwd"]) == $loginRec["huidigWW"]);
}
else
{
  $passwdChecked = ($usernameOK AND $_POST["passwd"] == $userRec["Wachtwoord"]);
}  

if (!$passwdChecked AND $sec->beleid <> "")
{
  echo "invalidLogin";
  return;
}
//debug($userRec);
if ($userRec["mobiel"] == "")
{
  echo "geenSMSnr";
  return;
}

if ($sec->twoFactor )
{

  if ($loginRec["smsJul"]+300 > mktime() AND $loginRec["loginSucces"] == 0)
  {
    echo "reuseSMS";
  }
  else
  {
    $smscode = rand ( 11111 , 99999 );
    $data = "sms:".$userRec["mobiel"]."|code:".$smscode;

    $sec->updateLogin(array(
                        "laatsteIP"      => $_SERVER["REMOTE_ADDR"],
                        //"laatsteLogin"   => date("Y-m-d H:i:s"),
                        "laatsteSMScode" => $smscode,
                        "SMSstamp" => date("Y-m-d H:i:s"),
                        "loginSucces" => 0
                      ));
    $result = apiCall($data);
    if ($result <> "ok")
    {
      echo "smsOffline";
    }
    else
    {
      echo $userRec["mobiel"];
    }
  }

}
else
{
  echo "validSMS";  
}





function apiCall($data, $toJSON = true)
{
  global $__appvar;
  $crypt = new AE_crypt();
  $crypt->setKey($__appvar["SMSapiKey"]);
  $data =$crypt->md5_encrypt($data);
 // $data = base64_encode($data);
  
 
   $data_to_post = array(
  'apiKey' => $_POST["location"],
  'data' =>$data );


  $form_url = 'http://api.airs.nl';

    // Initialize cURL
  $curl = curl_init();

  // Set the options
  curl_setopt($curl,CURLOPT_URL, $form_url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
  // This sets the number of fields to post
  curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));

  // This is the fields to post in the form of an array.
  curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);

  //execute the post
  $result = curl_exec($curl);

  //close the connection
  curl_close($curl);

  return $result;
}

?>