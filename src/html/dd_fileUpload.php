<?php
/*
    AE-ICT sourcemodule created 28 jun. 2021
    Author              : Chris van Santen
    Filename            : transaktie_fileUpload.php

  hoort bij   html/transaktie_CS_upload.php
  call 9615
*/
include_once("wwwvars.php");
session_start();

if ($_SESSION["dd_path"] != "")
{
  $path = $_SESSION["dd_path"];
}
else
{
  echo "FOUT: config missing.";
  exit;
}

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

$tmp_name = $_FILES["file"]["tmp_name"];
$filename = basename($_FILES["file"]["name"]);

move_uploaded_file($tmp_name, $path . "/" . $filename);

