<?php
/*
    AE-ICT sourcemodule created 06 nov. 2019
    Author              : Chris van Santen
    Filename            : reconV3Selections.php

    $Log: reconV3Selections.php,v $
    Revision 1.5  2019/11/29 13:17:49  cvs
    call 7937

*/

include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/AE_cls_config.php");
include_once("../../config/JSON.php");
require("../../config/checkLoggedIn.php");

$cfg = new AE_config();

$req = $_POST;
$names = array();
$banks = array();
if (!$selections = $cfg->getData("reconV3Makros"))
{
  $cfg->addItem("reconV3Makros","");
}

$data = (array)json_decode($selections);
foreach ($data as $item)
{
  $item = (array)$item;
  $makros[$item["name"]] = $item["chks"];
  $banks[$item["name"]] = $item["bank"];
  $names[] = $item["name"];
}
$out = "";

switch ($req["action"])
{
  case "save":
    $makros[$req["selectName"]] = implode(", ",$req["vbs"]);
    $banks[$req["selectName"]] = $req["bank"];
    break;
  case "delete":
    unset($makros[$req["selected"]]);
    break;
  case "load":
    $out = json_encode(array(
      "action"     => $req["action"],
      "selected"   => $req["selected"],
      "selectName" => $req["selected"],
      "bank"       => $banks[$req["selected"]],
      "vbs"        => explode(", ",$makros[$req["selected"]])
    ));
    break;
  case "getNames":
    sort($names);
    $out = json_encode($names);
    break;
}

if ($out != "")
{
  echo $out;
}
else
{
  saveMakros($makros, $banks);
  $out = array(
    "action" => $req["action"],

  ) ;
  $out = json_encode(array(
           "action"     => $req["action"],
           "selected"   => $req["selected"],
           "selectName" => $req["selectName"],
           "bank"       => $req["bank"],
           "names"      => $names
         ));
  echo $out;
}

function saveMakros($makroArray, $banks)
{
  global $cfg, $names, $req;
  $names = array();

  foreach ($makroArray as $k=>$v)
  {
    $row[] = '{"name":"'.$k.'", "bank":"'.$banks[$k].'", "chks":"'.$v.'"}';
    $names[] = $k;
  }
  $out = "[".implode(",", $row)."]";
  $cfg->putData("reconV3Makros",$out);
}
