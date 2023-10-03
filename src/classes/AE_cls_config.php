<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/05/12 11:13:40 $
 		File Versie					: $Revision: 1.5 $

 		$Log: AE_cls_config.php,v $
*/

class AE_config
{
  var $user;
  var $logfileName    = "ae_config_log.txt";
  var $logToFile      = false;
  var $logfileMaxRows = 5000; // log rto

  function AE_config($dbId=1)
  {
    global $USR;
	  $this->user = $USR;
	  $this->dbId=$dbId;
  }

  function enableLogging()
  {
    global $__debug;
    if ($__debug)
    {
      $this->logToFile = true;
    }
  }

  function disableLogging()
  {
    $this->logToFile = false;
  }

  function addItem($field, $value)
  {
    $db = new DB($this->dbId);
    $query = "SELECT * FROM ae_config WHERE field = '$field'";   // any fields existing, then delete then first
    if ($db->QRecords($query) > 0)
    {
      return $this->putData($field,$value);
    }
    else
    {
      $query = "INSERT INTO ae_config SET
       ae_config.value = '$value'
      ,  ae_config.add_date = NOW()
      , ae_config.add_user = '$this->user'
      , ae_config.change_date = NOW()
      , ae_config.change_user = '$this->user'
      , ae_config.field = '$field'";

      $db->SQL($query);
      if ($db->Query())
        return true;
      else
        return false;
    }
  }

  function getData($field,$lock=false)
  {
    $db = new DB($this->dbId);
    $bt = debug_backtrace();
    $query = "SELECT ae_config.value,ae_config.lock FROM ae_config WHERE field = '$field' limit 1";
    $db->SQL($query);
    if ($record = $db->lookupRecord())
    {
      $this->log($field, $record['value'], "read:".basename($bt[0]["file"]).":".$bt[0]["line"]);
      return $record['value'];
    }
    else
    {
      $this->log($field, $record['value'], "readFAIL");
      return false;
    }

  }


  function deleteField($field)
  {
    $db = new DB($this->dbId);
    $query = "DELETE FROM ae_config WHERE field = '$field'";
    $db->SQL($query);
    if($db->Query())
    {
      $this->log($field, "", "delete");
      return true;
    }
    else
    {
      $this->log($field, "", "deleteFAIL");
      return false;
    }

  }

  function getById($id)
  {
    $db = new DB($this->dbId);

    $query = "SELECT ae_config.value,ae_config.lock FROM ae_config WHERE id = '$id' limit 1";
    $db->SQL($query);
    if ($record = $db->lookupRecord())
    {
      return $record['value'];
    }
    else
      return false;
  }



  function putData($field,$value)
  {
    $db = new DB($this->dbId);
    $query = "UPDATE ae_config SET
      ae_config.value = '$value'
    , ae_config.change_date = NOW()
    , ae_config.change_user = '$this->user'
    WHERE ae_config.field = '$field'";
    $db->SQL($query);
    if ($db->Query())
    {
      $this->log($field, $value, "write");
      return true;
    }
    else
    {
      $this->log($field, $value, "writeFAIL");
      return false;
    }

  }

  function setLock($field)
  {
    $db = new DB($this->dbId);
    $query = "UPDATE ae_config SET
      ae_config.lock = 1
    , ae_config.change_date = NOW()
    , ae_config.change_user = '$this->user'
    WHERE ae_config.field = '$field'";
    $db->SQL($query);
    if ($db->Query())
      return true;
    else
      return false;
  }

  function releaseLock($field)
  {
    $db = new DB($this->dbId);
    $query = "UPDATE ae_config SET
      ae_config.lock = 0
    , ae_config.change_date = NOW()
    , ae_config.change_user = '$this->user'
    WHERE field = '$field'";
    $db->SQL($query);
    if ($db->Query())
      return true;
    else
      return false;
  }

  function isLocked($field)
  {
    $db = new DB($this->dbId);
    $query = "SELECT ae_config.lock FROM ae_config WHERE field = '$field'";
    $db->SQL($query);
    if ($record = $db->lookupRecord())
    {
      if ($record['lock'] == 1)
        return true;
      else
        return false;
    }
  }

  function log($field, $value, $action="read")
  {
    global $__appvar, $USR;

    if (!$this->logToFile)
    {
      return;
    }
    $writeFilename = $__appvar["tempdir"].$this->logfileName;

    if (!$writeHandle = fopen($writeFilename, 'a+'))
    {
      echo "Cannot open file ($writeHandle)";
      return false;
    }


    $outp = date("Ymd H:i ")."{$USR}->{$action} {$field} = {$value} ";
    fwrite($writeHandle, $outp."\n");
    fclose($writeHandle);
    return true;

  }


}

