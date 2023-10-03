<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/08/23 11:39:46 $
    File Versie         : $Revision: 1.2 $

    $Log: reconV3BestandenFileUpload.php,v $
    Revision 1.2  2019/08/23 11:39:46  cvs
    call 8024

    Revision 1.1  2019/07/05 11:36:12  cvs
    call 7803

    Revision 1.1  2018/05/07 14:55:37  cvs
    call 6734

    Revision 1.1  2018/03/09 12:45:08  cvs
    call 3503



*/

include_once("wwwvars.php");
include_once ("../classes/AE_cls_fileUpload.php");
$upl = new AE_cls_fileUpload();

session_start();
$path = $__appvar["basedir"]."/temp/combine";
if (!file_exists($path))
{
  mkdir($path);
}

if ($_FILES["file"]["error"] == 0 AND $upl->checkExtension($_FILES['file']['name']))
{

  $tmp_name = $_FILES["file"]["tmp_name"];
  $name = basename($_FILES["file"]["name"]);
  $filename = $name;

  move_uploaded_file($tmp_name, $path . "/" .$_SESSION["importCombine"]."--". $filename);
}