<?php
/*
    AE-ICT sourcemodule created 08 feb 2019
    Author              : Chris van Santen
    Filename            : apiMdlZ_helper.php

*/

class apiMdlZ_helper
{
  var $batchnr;
  var $baseDir;
//  var $urlPrefix = "https://apitest2.airshost.nl/content/";
  var $urlPrefix = "https://vry-api.airshost.nl/content/";

  var $table = "API_moduleZ_files";
  function __construct($batchnr="")
  {
    $this->baseDir = getcwd()."/content/";
    $this->user = "api";
    $this->db = new DB();
    if ($batchnr != "")
    {
      $this->batchnr = $batchnr;
    }
    else
    {
      $this->newBatch();
    }
  }

  function newBatch()
  {
    $this->batchnr = date("YmdHi_").rand(1111,9999);
  }

  function getLastBatch()
  {
    $query = "SELECT batch FROM {$this->table} ORDER BY batch DESC";
    $rec = $this->db->lookupRecordByQuery($query);
    $this->batchnr = $rec["batch"];
  }

  function addFile($file, $portefeuille, $batch="",$subject="",$body="")
  {
    if ($subject == "")
    {
      $subject = "Effectenrapportage";
    }
    if ($body == "")
    {
      $body = str_replace($portefeuille, "", $file);
      $body = "Periode ".substr($body,1,-4);
    }

    $msg = serialize(array(
      "subject" => $subject,
      "body"    => $body ));
    $filename = $this->baseDir.$file;
    $query = "
    INSERT INTO {$this->table} SET
      `add_date` = NOW(),
      `add_user` = '{$this->user}',
      `batch` = '{$batch}',
      `file` = '{$filename}',
      `portefeuille` = '{$portefeuille}',
      `msg` = '{$msg}'
    ";

    $this->db->executeQuery($query);
  }

  function selectBatch()
  {
    $out = array();
    $query = "SELECT * FROM {$this->table} WHERE batch = '{$this->batchnr}'";
    $this->db->executeQuery($query);
    while ($rec = $this->db->nextRecord())
    {

      $rec["url"] = utf8_encode($this->urlPrefix.basename($rec["file"]));
      $out[] = $rec;
    }
    return $out;
  }

  function deleteBatch()
  {
    // aetodo: bestanden nog unlinken..
    $query = "DELETE FROM {$this->table} WHERE batch = '{$this->batchnr}'";
    $out = $this->db->executeQuery($query);
    $rows = $this->db->mutaties();
    return ($rows != 0);
  }

  function filesToBatch()
  {
    $query = "UPDATE {$this->table} SET batch = '{$this->batchnr}' WHERE batch = ''";
    $this->db->executeQuery($query);
  }

}
