<?php
/*
    AE-ICT sourcemodule created 04 jun. 2021
    Author              : Chris van Santen
    Filename            : api_documentlinks.php


*/


global $logArray;
include_once("../../classes/AE_cls_digidoc.php");
$dd = new digidoc();
$portefeuille = sanatizePortefeuille($__ses["data"]["portefeuille"]);
$pull_id      = (int)$__ses["data"]["pull_id"];

$doctok = $__ses["data"]["doctok"];

//ad($__ses["data"]);



$whereArray = array();
if ($pull_id < 1)
{
  $error[] = "onbekend pull_id ($pull_id)";
  $output  = array();
}

if (!checkDocTok($doctok))
{
  $error[] = "Link naar document is verlopen";
  $output  = array();
}

if (count($error) == 0)
{

//  $_DB_resources[1]['server'] = "update.airs.nl";
//  $_DB_resources[1]['user']   = "chris123";
//  $_DB_resources[1]['passwd'] = "4191vj(U4)";
//  $_DB_resources[1]['db']     = "airs_ano";

  $db = new DB();

  $query = "SELECT id FROM CRM_naw WHERE portefeuille = '{$portefeuille}' ";

  if ($prtRec = $db->lookupRecordByQuery($query))
  {

    $query = "
    SELECT 
      * 
    FROM
      `dd_reference`
    WHERE
     `module` = 'CRM_naw' AND 
     `module_id` = '{$prtRec["id"]}' AND 
     `id` = {$pull_id}
    ";
//ad($query);
    if (!$ddRec = $db->lookupRecordByQuery($query))
    {
      $error[] = "Pull request afgebroken document offline";
    }
    else
    {

      $path = getcwd()."/pushDir/";
//      ad($path);
      if ($tmpFile = $dd->retrieveDocumentToFile($pull_id, $path ))
      {
//        ad($path.$tmpFile);
        $blob = base64_encode(file_get_contents($path.$tmpFile));
        $md5  = md5($blob);
        $output[] = array(
          "filename"      => $ddRec["filename"],
          "portefeuille"  => $portefeuille,
          "ref_id"        => $pull_id,
          "mimetype"      => $ddRec["filetype"],
          "encoding"      => "base64",
          "blob"          => $blob,
          "hash"          => $md5
        );
        $logArray[] = array(
          "filename"      => $ddRec["filename"],
          "portefeuille"  => $portefeuille,
          "ref_id"        => $pull_id,
          "mimetype"      => $ddRec["filetype"],
          "datastore_nr"  => $ddRec["datastore"],
          "datastore_id"  => $ddRec["dd_id"],
          "hash"          => $md5
        );
        unlink($path.$tmpFile);
      }


    }

  }
  else
  {
    $error[] = "Pull request afgebroken, ongeldig input combo";
  }
}





