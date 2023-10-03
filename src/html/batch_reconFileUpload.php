<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/08/23 11:35:02 $
    File Versie         : $Revision: 1.2 $

    $Log: batch_reconFileUpload.php,v $
    Revision 1.2  2019/08/23 11:35:02  cvs
    call 8024

    Revision 1.1  2018/03/09 12:45:08  cvs
    call 3503



*/

include_once("wwwvars.php");
include_once ("../classes/AE_cls_fileUpload.php");
$upl = new AE_cls_fileUpload();
session_start();
//debug($_FILES);
include_once "../classes/AIRS_cls_reconJob.php";
//debug($_SESSION["reconJob"]);
$job = new AIRS_cls_reconJob($_SESSION["reconJob"]["Batch"]);
$job->getJob();
//debug($job->batch);
//debug($job->currentJob);
$uploads_dir = $job->uploads_dir;
//debug($uploads_dir,getcwd());
if ($_FILES["file"]["error"] == 0 AND $upl->checkExtension($_FILES['file']['name']))
{
  $tmp_name = $_FILES["file"]["tmp_name"];
  $name = basename($_FILES["file"]["name"]);
  $filename = $job->batch."-_-".$name;
  move_uploaded_file($tmp_name, $uploads_dir."/".$filename);
  $job->addFileToJob($filename);
}

//debug($job->currentJob);
//
//
//debug($_SESSION["reconJob"]);
//debug($_FILES);


?>