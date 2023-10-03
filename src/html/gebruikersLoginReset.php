<?php

include_once("wwwvars.php");
if ($_GET["user"] == "")
{
  echo "verkeerde aanroep";
  exit;
}

include_once $__appvar["basedir"].'/classes/AE_cls_secruity.php';
$sec = new AE_cls_secruity($_GET["user"]);

$sec->deleteLogin();
header("location: gebruikerEdit.php?action=edit&id=".$_GET["id"]."&wwb=reset");