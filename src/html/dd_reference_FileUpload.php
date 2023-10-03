<?php
/*
    AE-ICT sourcemodule created 18 sep. 2020
    Author              : Chris van Santen
    Filename            : dd_reference_FileUpload.php

*/

include_once("wwwvars.php");
session_start();
$path = $__appvar["basedir"]."/temp/dd";

if (!file_exists($path))
{
  mkdir($path);
}

if ($_FILES["file"]["error"] == 0)
{
  include_once ("../classes/AE_cls_fileUpload.php");
  $upl = new AE_cls_fileUpload();
  if (!$upl->checkExtension($_FILES['file']['name']))
  {
    echo "Fout: ongeldig bestandsformaat";
    exit;
  }

  $tmp_name = $_FILES["file"]["tmp_name"];
  $name = basename($_FILES["file"]["name"]);
  $filename = cnvFilename(utf8_decode($name));

  move_uploaded_file($tmp_name, $path . "/" .$_SESSION["importCombine"]."--". $filename);
}
