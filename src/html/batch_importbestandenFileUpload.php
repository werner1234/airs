<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/09/02 08:47:05 $
    File Versie         : $Revision: 1.2 $

    $Log: batch_importbestandenFileUpload.php,v $
    Revision 1.2  2019/09/02 08:47:05  cvs
    call 7995

    Revision 1.1  2018/05/07 14:55:37  cvs
    call 6734

    Revision 1.1  2018/03/09 12:45:08  cvs
    call 3503



*/

include_once("wwwvars.php");
session_start();
$path = $__appvar["basedir"]."/temp/combine";
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
    echo "Fout: veboden bestandsformaat";
    exit;
  }

  $tmp_name = $_FILES["file"]["tmp_name"];
  $name = basename($_FILES["file"]["name"]);
  $filename = $name;

  move_uploaded_file($tmp_name, $path . "/" .$_SESSION["importCombine"]."--". $filename);
}