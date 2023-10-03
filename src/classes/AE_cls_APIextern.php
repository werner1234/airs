<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/03/11 13:40:21 $
    File Versie         : $Revision: 1.3 $

    $Log: AE_cls_APIextern.php,v $
    Revision 1.3  2019/03/11 13:40:21  cvs
    call 7364

    Revision 1.2  2019/03/08 09:43:38  cvs
    call 7364

    Revision 1.1  2019/03/01 08:54:32  cvs
    call 7364

    Revision 1.5  2018/04/19 07:04:26  cvs
    call 6791

    Revision 1.4  2017/10/25 14:05:13  cvs
    error attachments opgelost

    Revision 1.3  2017/03/03 13:00:38  cvs
    quotes in queries gaan fout

    Revision 1.2  2016/04/22 11:57:48  cvs
    datum in emailfilename bij doorzetten naar digidoc

    Revision 1.1  2016/04/22 09:42:04  cvs
    call 4296 naar ANO



*/

class AE_cls_APIextern
{
  var $status = "";
  var $user = "";
  var $table = "apiQueueExtern";
  var $error = false;
  var $db;
  var $fieldArray  = array();
  var $mailDoubles = array();
  var $skipFields = array("id", "add_date", "add_user", "change_date", "change_user");
  function AE_cls_APIextern()
  {
    $this->user = $_SESSION["USR"];
    $this->db = new DB();
    $this->buildFieldArray();
  }

  function buildFieldArray()
  {
     $query = "show fields from CRM_naw";
     $this->db->executeQuery($query);
     while($rec = $this->db->nextRecord())
     {
       $fld = $rec["Field"];
       if (!in_array($fld, $this->skipFields))
       {
         $this->fieldArray[] = $fld;
       }
     }
  }

  function messageCount()
  {
    $this->messageCount = imap_num_msg($this->mailbox);
    return $this->messageCount;
  }

  function populateQueue()
  {

    $query = "
    SELECT
      id,
      add_date,
      eventCode,
      dataFields
    FROM
      {$this->table}
    WHERE
      finished = 0
    ORDER BY
      id DESC
    ";
    $this->db->executeQuery($query);
    while ($rec = $this->db->nextRecord())
    {

      $datafields = (array)json_decode($rec["dataFields"]);
      foreach ($datafields as $k=>$v)
      {
        $rec[$k] = $v;
      }

      if ($rec["email"] != "")
      {
        $this->mailDoubles[$rec["email"]]++;
      }

      $crmId = $this->matchEmail($rec["email"]);
      $crmId2 = $this->matchZoekveld($rec["zoekveld"]);
      if ($crmId != "" OR $crmId2 != "" )
      {
        $rec["match"] = "";
        if ($crmId AND $crmId2)
        {
          $rec["match"] = "Z/E";
        }
        elseif ($crmId2)
        {
          $rec["match"] = "Z";
        }
        elseif ($crmId )
        {
          $rec["match"] = "E";
        }

        $rec["crmId"] = $crmId != 0?$crmId:$crmId2;
        $rows["match"][] = $rec;
        $this->updateCrmId($rec, $rec["crmId"]);
      }
      else
      {
        $rows["nomatch"][] = $rec;
      }

    }

    return $rows;
  }

  function updateCrmId($record, $crmId)
  {
    $db = new DB();
    $query = "
    UPDATE 
      {$this->table}
    SET 
      `crmId` = $crmId
    WHERE
      `id` = {$record["id"]}
    ";
    $db->executeQuery($query);

  }

  function initTables()
  {
    include_once("../classes/AE_cls_SQLman.php");
    $tst = new SQLman();

    $tst->tableExist($this->table,true);  // table aanmaken als die nog niet bestaat
    $tst->changeField($this->table,"submitterIp",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->table,"eventCode",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->table,"action",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->table,"dataFields",array("Type"=>"TEXT","Null"=>false));
    $tst->changeField($this->table,"ignored",array("Type"=>"tinyint","Null"=>false));
    $tst->changeField($this->table,"finished",array("Type"=>"tinyint","Null"=>false));
    $tst->changeField($this->table,"crmId",array("Type"=>"int","Null"=>false));

  }
  function errorState()
  {
    if ($this->error)
    {
      return $this->status;
    }
    else
    {
      return false;
    }
  }

  function lastStatus()
  {
    return $this->status;
  }

  function matchEmail($email)
  {
    if (trim($email) == "")
    {
      return false;
    }

    $db = new DB();
    $email = mysql_real_escape_string($email);
    $query = "SELECT id FROM CRM_naw WHERE email = '{$email}'";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec["id"];
    }
    else
    {
      return false;
    }

  }

  function matchZoekveld($zoekveld)
  {

    if (trim($zoekveld) == "")
    {
      return false;
    }

    $db = new DB();

    $query = "SELECT id FROM CRM_naw WHERE zoekveld = '".trim($zoekveld)."'";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec["id"];
    }
    else
    {
      return false;
    }

  }


  function exportHistoryToCSV($datum)
  {
    $dat = explode("-",$datum);
    $db = new DB();
    $query = "SELECT * FROM {$this->table} WHERE date(add_date) >= '".$dat[2]."-".$dat[1]."-".$dat[0]."'";
    $header = false;
    $db->executeQuery($query);
    $keys   = array();
    while($rec = $db->nextRecord())
    {
      $dataFields = (array)json_decode($rec["dataFields"]);

      $dataFields["eventCode"]    = $rec["eventCode"];
      $dataFields["submitterIp"]  = $rec["submitterIp"];
      $dataFields["add_date"]     = $rec["add_date"];
      $dataFields["id"]           = $rec["id"];
      $dataFields["crmId"]        = $rec["crmId"];
      $dataFields["overgeslagen"] = ($rec["ignored"] == 1)?"J":"N";
      $dataFields["afgewerkt"]    = ($rec["finished"] == 1)?"J":"N";

      $values = array();

      foreach ($dataFields as $k=>$v)
      {

        $keys[$k]   = $k;
        $values[$k] = $v;
      }

      $outRows[] = $values;

    }

    $filename = "API_export_historie_".date("dmY-His").".csv";

    $out = "";
    foreach ($outRows as $row)
    {
      if (!$header)
      {
        foreach ($keys as $k)
        {
          $out .= "\t" . $k;
        }
        $out .= "\r\n";
        $header = true;
      }
      foreach ($keys as $k)
      {
        $out .= "\t".$row[$k];
      }
      $out .= "\r\n";
    }

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $out;
    exit;
  }

  function exportToCSV($ids, $filename="")
  {
    global $USR;
    if (count($ids) < 1)
    {
      return false;
    }
    $db = new DB();
    $query = "SELECT * FROM {$this->table} WHERE id IN (".implode(",", $ids).")";
    $header = false;
    $db->executeQuery($query);
    $keys   = array();
    while($rec = $db->nextRecord())
    {
      $dataFields = (array)json_decode($rec["dataFields"]);

      $dataFields["eventCode"]   = $rec["eventCode"];
      $dataFields["submitterIp"] = $rec["submitterIp"];
      $dataFields["add_date"]    = $rec["add_date"];
      $dataFields["id"]          = $rec["id"];
      $dataFields["crmId"]       = $rec["crmId"];

      $values = array();

      foreach ($dataFields as $k=>$v)
      {

        $keys[$k]   = $k;
        $values[$k] = $v;
      }

      $outRows[] = $values;

    }
    if ($filename == "")
    {
      $filename = "API_export_".date("dmY-His").".csv";
    }
    $out = "";
    foreach ($outRows as $row)
    {
      if (!$header)
      {
        foreach ($keys as $k)
        {
          $out .= "\t" . $k;
        }
        $out .= "\r\n";
        $header = true;
      }
      foreach ($keys as $k)
      {
        $out .= "\t".$row[$k];
      }
      $out .= "\r\n";
    }

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $out;
    exit;

  }

  function setIgnored($ids)
  {
    global $USR;
    if (count($ids) < 1)
    {
      return false;
    }
    $db = new DB();
    $query = "
    UPDATE {$this->table} SET
      change_date = NOW(),
      change_user = '$USR',
      finished  = 1,
      ignored = 1
    WHERE 
      id IN (".implode(", ",$ids).")";
    $db->executeQuery($query);
  }

  function setFinished($ids)
  {
    global $USR;
    if (count($ids) < 1)
    {
      return false;
    }
    $db = new DB();
    $query = "
    UPDATE {$this->table} SET
      change_date = NOW(),
      change_user = '$USR',
      finished  = 1
    WHERE 
      id IN (".implode(", ",$ids).")";

    $db->executeQuery($query);
  }


  function addToCRM($id)
  {
    $db = new DB();
    $q = array();
    $query = "SELECT * FROM {$this->table} WHERE id = '$id'";

    $rec = $db->lookupRecordByQuery($query);
    $datafields = (array)json_decode($rec["dataFields"]);
    foreach ($datafields as $k=>$v)
    {
      if (in_array($k, $this->fieldArray))
      {
        $q[] = " `$k` = '".mysql_real_escape_string($v)."'";
      }
    }
    $q[] = " `bron` = '".mysql_real_escape_string($rec["eventCode"])."'";
    $query = "
      INSERT INTO `CRM_naw` SET
      add_date = NOW(),
      add_user = '$USR',
      change_date = NOW(),
      change_user = '$USR',
      aktief = 1,
      prospect = 1,                      
    ";
    $query .= implode("\n, ",$q);
//    $__debug = true;
//    debug($query);
    $db->executeQuery($query);
    $crmId = $db->last_id();
    $this->updateCrmId(array("id"=>$id), $crmId);
    $this->setFinished(array($id));


  }

  function getContentById($id)
  {
    $db = new DB();
    $out = "";
    $query = "SELECT * FROM {$this->table} WHERE id = '$id'";
    $rec = $db->lookupRecordByQuery($query);
    $datafields = (array)json_decode($rec["dataFields"]);
    $datafields["eventCode"] = $rec["eventCode"];
    $datafields["submitterIp"] = $rec["submitterIp"];

    foreach ($datafields as $k=>$v)
    {
      if (in_array($k, $this->fieldArray) OR $k == "eventCode" OR $k == "submitterIp")
      {
        $out .= "
        <div class='dispRow'>
        <div class='dispKey'>$k</div>
        <div class='dispValue'>$v</div>
        </div>
        ";
      }
    }
    return $out;
  }


}