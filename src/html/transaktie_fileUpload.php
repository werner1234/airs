<?php
/*
    AE-ICT sourcemodule created 28 jun. 2021
    Author              : Chris van Santen
    Filename            : transaktie_fileUpload.php

  hoort bij   html/transaktie_CS_upload.php
  call 9615
*/
include_once("wwwvars.php");

if ($_FILES["file"]["error"] == 0)
{
  include_once("../classes/AE_cls_fileUpload.php");
  $upl = new AE_cls_fileUpload();
  if (!$upl->checkExtension($_FILES['file']['name']))
  {
    echo "Fout: veboden bestandsformaat";
    exit;
  }
}

$depot = $_GET["depot"];
$pathInit = false;
switch ($depot)
{
  case "cs":
    $pathInit = ( isset($__credswissImportMap) AND trim($__credswissImportMap) != "" );
    $path = $__credswissImportMap."/import/";
    break;
  case "ubs":
    $pathInit = ( isset($__ubsImportMap) AND trim($__ubsImportMap) != "" );
    $path = $__ubsImportMap."/import/";
    break;
  default:
}

if (!$pathInit)
{
  echo "<h1> niet ingeregeld</h1>";
  exit;
}

session_start();

if (!file_exists($path))
{
  mkdir($path);
}

$tmp_name = $_FILES["file"]["tmp_name"];
$filename = basename($_FILES["file"]["name"]);

$handle = fopen($tmp_name, "r");

switch ($depot)  //bestandsvalidatie
{
  case "cs":
    $data = fgetcsv($handle, 8192, ";");
    $data = fgetcsv($handle, 8192, ";");
    if (substr($data[0],0,4) == "V_SR")
    {
      move_uploaded_file($tmp_name, $path . "/" . $filename);
    }
    break;
  case "ubs":
    $valid = false;
    for ($x = 0 ; $x < 9; $x++)
    {
      $data = fgetcsv($handle, 8192, ";");
      if ($data[0] == "V01")
      {
        $valid = true;
        break;
      }
    }

    if ($valid)
    {
      move_uploaded_file($tmp_name, $path . "/" . $filename);
    }
    break;
  default:
}
