<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/24 06:36:05 $
    File Versie         : $Revision: 1.4 $

    $Log: updateRekmutAfvink.php,v $
    Revision 1.4  2018/07/24 06:36:05  cvs
    call 7041

    Revision 1.3  2017/05/29 07:50:07  cvs
    no message

    Revision 1.2  2016/12/02 14:03:03  cvs
    call 5086

    Revision 1.1  2016/10/24 10:24:02  cvs
    call 5086



*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/AIRS_rekeningAfvinkHelper.php");


require("../../config/checkLoggedIn.php");

if (trim($_POST['items']) == "")
{
  exit;
}

$USR = $_SESSION["USR"];
$afh = new AIRS_rekeningAfvinkHelper($_SESSION["afvinkVB"]);


$db = new DB();


//debug($_POST );
//exit;

$rawItems = explode(";", $_POST["items"]);
$items = array();
///debug($rawItems);
foreach ($rawItems as $i)
{
  if (trim($i) <> "")   { $items[] = trim($i); }
}

switch ($_POST["action"])
{
  case "fondsMatch":
    $afh->fondsMatch($_POST);
    break;
  case "btnMatch":
    if (count($items) > 0)
    {
      $match = $afh->matchcode();
      foreach($items as $i)
      {
        $afh->match($i);
      }
    }
    break;
  case "btnGoedkeur":
    foreach ($items as $i)
    {
      $afh->updateGoedkeur($i);
    }
    break;
  case "btnGrootboek":
    foreach ($items as $i)
    {
      $afh->updateGrootboek($i);
    }

    break;
}
return true;




?>