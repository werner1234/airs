<?php

/*
    AE-ICT sourcemodule created 15 aug 2018
    Author              : Chris van Santen
    Filename            : api-report.php

    Author              : $Author: cvs $
*/

include_once ("apiMdlZ_helper.php");

$hlp = new apiMdlZ_helper();

$data = $__ses["data"];
switch (strtolower($__ses["action"]))
{
  case "addfile":
    $hlp->addFile($data["file"],$data["portefeuille"], $data["batch"]);
    $output["result"] = "ok";
    break;
  case "reportdeletebatch":
    $hlp->batchnr = $data["batch"];
    if ($hlp->deleteBatch())
    {
      $output["result"] = "ok";
    }
    else
    {
      $error[] = "invalid batch for delete";
    }

    break;
  case "reportgetlastbatch":
    $hlp->getLastBatch();
    $dataSet = $hlp->selectBatch();
    if (count($dataSet) > 0)
    {
      foreach ( $dataSet as $rec)
      {

        $file = str_replace($rec["portefeuille"],"",basename($rec["url"],".pdf"));
        $msg = unserialize($rec["msg"]);

        if ( count($msg) > 1 AND
             ( $msg["subject"] != "" AND $msg["body"] != "" )
           )
        {
          $subject = $msg["subject"];
          $content = $msg["body"];
        }
        else
        {
          $subject = "Effectenrapportage";
          $content = "Periode $file";
        }
        $output[] =array(
          "AccountNumber" => $rec["portefeuille"],
          "FileReference" => $rec["url"],
          "Subject"       => $subject,
          "Content"       => $content,
          "batch"         => $hlp->batchnr
        );
      }
    }
    else
    {
      $output = array();
    }
    break;
  case "reportgetreports":
    if ($data["batch"] != "")
    {
      $hlp->batchnr = $data["batch"];
      $dataSet = $hlp->selectBatch();
    }
    else
    {
      $hlp->newBatch();
      $hlp->filesToBatch();
      $dataSet = $hlp->selectBatch();
    }
    if (count($dataSet) > 0)
    {
      foreach ( $dataSet as $rec)
      {
        $file = str_replace($rec["portefeuille"],"",basename($rec["url"],".pdf"));

        $msg = unserialize($rec["msg"] );
        if ( count($msg) > 1 AND
             ( $msg["subject"] != "" AND $msg["body"] != "" )
           )
        {
          $subject = $msg["subject"];
          $content = $msg["body"];
        }
        else
        {
          $subject = "Effectenrapportage";
          $content = "Periode $file";
        }

        $output[] =array(
          "AccountNumber" => $rec["portefeuille"],
          "FileReference" => $rec["url"],
          "Subject"       => $subject,
          "Content"       => $content,
          "batch"         => $hlp->batchnr
        );
      }
    }
    else
    {
      $output = array();
    }

    break;
  default:
}


