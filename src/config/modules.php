<?php
/*
    AE-ICT sourcemodule created 02 mei 2022
    Author              : Chris van Santen
    Filename            : modules.php

    superusers WMP, TEST, HOME, BOX, RCN, FCM, WWO ,DTI

*/

if (isset($_SESSION["modules"]))
{
  $__modules = $_SESSION["modules"];
}
else
{
  if (!isset($__modules))
  {
    $__modules   = array();
    $_tempAccess = array(
      "DEFAULT"  => array("superuser" => true,  "blanco-api" => true,  "advent" => true,  "beleggersgiro" => true,  "apiExternal" => true),
      "PEN56"    => array("superuser" => true),
      "PEN52"    => array("blanco-api" => true),
      "ABNTST"   => array("superuser" => false,  "blanco-api" => false,  "advent" => false,  "beleggersgiro" => false,  "apiExternal" => false),
      "ACC"      => array("superuser" => false,  "blanco-api" => false,  "advent" => false,  "beleggersgiro" => false,  "apiExternal" => false),
      "CAW"      => array("superuser" => false,  "blanco-api" => false,  "advent" => false,  "beleggersgiro" => false,  "apiExternal" => false),
      "FUC"      => array("superuser" => false,  "blanco-api" => false,  "advent" => false,  "beleggersgiro" => false,  "apiExternal" => false),
      "LAZ"      => array("superuser" => false,  "blanco-api" => false,  "advent" => false,  "beleggersgiro" => false,  "apiExternal" => false),
      "SER"      => array("superuser" => false,  "blanco-api" => false,  "advent" => false,  "beleggersgiro" => false,  "apiExternal" => false),
    );

    $_moduleNames = array ("superuser", "blanco-api", "advent", "beleggersgiro", "apiExternal");

    $bc = (key_exists($__appvar["bedrijf"], $_tempAccess)?$__appvar["bedrijf"]:"DEFAULT");

    foreach($_moduleNames as $mod)
    {
      $__modules[$mod] = (isset($_tempAccess[$bc][$mod]) AND $_tempAccess[$bc][$mod]);
    }

    unset($_moduleNames);
    unset($_tempAccess);

  }
}
