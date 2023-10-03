<?php
/*
    AE-ICT sourcemodule created 23 Mar 2020
    Author              : Chris van Santen
    Filename            : AE_cls_integrityCheck.php

    $Log: AE_cls_integrityCheck.php,v $
    Revision 1.3  2020/06/22 11:37:55  cvs
    call 3205

    Revision 1.2  2020/06/10 11:56:22  cvs
    call 3205

    Revision 1.1  2020/03/25 15:06:55  cvs
    call 3205

    Revision 1.2  2020/03/25 13:24:12  cvs
    call 3205

    Revision 1.1  2020/03/23 13:04:14  cvs
    call 3205

*/

class AE_cls_integrityCheck
{
  var $table     = "test";
  var $checkDays = 14;
  var $fields    = array();
  var $output    = array();
  var $checkInfo = array();
  var $requiredFields = array("change_date","id");
  var $batchId   = "";
  var $bedrijf   = "";

  public function __construct($checkAll=false)
  {
    global $__appvar;
    if ($checkAll)
    {
      $this->checkDays = 0;
    }
    $this->bedrijf = $__appvar["bedrijf"];
    $this->batchId = $this->bedrijf."-".date("Ymd-His").rand(11111,99999);
  }

  function loadTable($tableName)
  {
    $this->table  = $tableName;
    $this->filter = "";
    include_once "integrityCheckDefinitions/" . $this->table . ".php";
    foreach ($this->requiredFields as $fld)
    {
      if (!in_array($fld, $this->fields))
      {
        array_unshift($this->fields, $fld);
      }
    }
    $this->checkInfo[$this->table] = array(

      "table"       => $this->table,
      "fields"      => implode(", ",$this->fields),
      "days"        => ($this->checkDays==0)?"all":$this->checkDays,
    );

    $this->checktable();
  }

  function checktable()
  {

    $db = new DB();
    $where = ($this->checkDays > 0)?"WHERE DATE(`change_date`) >= (DATE(NOW()) - INTERVAL ".$this->checkDays." DAY)":"";
    $query = "SELECT `" . implode("`,`", $this->fields) . "` FROM `" . $this->table . "` $where ORDER BY `id`";
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $this->output[$this->table][$rec["id"]] = array("change_date"=>$rec["change_date"],"hash"=>$this->hashArray($rec));
    }
  }

  function getResults()
  {
    global $__appvar;
    $res = array(
      "bedrijf"     => $__appvar["bedrijf"],
      "timestamp"   => date("d-m-Y H:i:s"),
      "batch"       => $this->batchId
    );

    foreach ($this->checkInfo as $table=>$stats)
    {

      $stats["items"] = count($this->output[$table]);
      $res["result"][$table] = $stats;
      $res["result"][$table]["records"] = $this->output[$table];
    }
    return $res;
  }

  function pushResults()
  {
    global $__appvar;
    $db = new DB(2); // connect to update server
    $query = "INSERT INTO `integrityCheck` SET 
    add_user = '{$__appvar["bedrijf"]}'
    , vb = '{$__appvar["bedrijf"]}'
    , batch = '{$this->batchId}'
    , json  = '".json_encode($this->getResults())."'
";

    $db->executeQuery($query);
  }

  function hashArray($record)
  {
    return md5(serialize($record));
  }

  function getHashesHome($AirsData, $kTableFields)
  {
    $db = new DB();
    $fields = $kTableFields[$AirsData["table"]];
    if ($fields == "")
    {

      return false;
    }
    $query = "SELECT {$fields} FROM `{$AirsData["table"]}` WHERE id IN ({$AirsData["ids"]})";
    $db->executeQuery($query);
    $hash = array();
    while($rec = $db->nextRecord())
    {
      $hash[$rec["id"]] = array($this->hashArray($rec), $rec["change_date"]);
    }
    return $hash;
  }
}