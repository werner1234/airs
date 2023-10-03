<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/03 06:30:51 $
 		File Versie					: $Revision: 1.6 $

 		$Log: AIRS_cls_reconJob.php,v $
 		Revision 1.6  2018/09/03 06:30:51  cvs
 		no message
 		
 		Revision 1.5  2018/04/04 08:09:25  cvs
 		call 6572
 		
 		Revision 1.4  2018/03/28 13:12:37  cvs
 		call 3503
 		
 		Revision 1.3  2018/03/28 12:34:10  cvs
 		call 3503
 		
 		Revision 1.2  2018/03/09 12:46:13  cvs
 		call 3503
 		
 		Revision 1.1  2017/06/26 14:18:45  cvs
 		Rejected commit: Default


*/

class AIRS_cls_reconJob
{
  var $user;
  var $tableName = "reconJobs";
  var $batch;
  var $currentJob = array();
  var $currentFileset = array();
  var $filePath = "";
  var $startSec;
  var $uploads_dir = "reconData/";
  var $returnMessage = "";

  function AIRS_cls_reconJob($batch = "",$init=false)
  {
    global $USR, $__appvar;
    $this->batch = ($batch != "")?$batch:date("Ymd_Hi")."-".rand(1000,9999);
	  $this->user = $USR;
	  $this->filePath = $__appvar["basedir"]."/html/reconData/";
	  $this->startSec = $this->nowSeconds();
	  if ($init)
    {
      $this->initModule();
    }

  }

  function updateJob($keyValues=array())
  {
    if (count($keyValues) > 0 AND count($this->currentJob) > 0)
    {
      $query = "
      UPDATE 
        `".$this->tableName."`
      SET
        ";
      foreach ($keyValues as $field=>$value)
      {
        $queryArray[] = "`$field` = '$value'";
      }
      $query .= implode(", ",$queryArray);
      $query .= "
      WHERE 
        `batchnr` = '".$this->batch."'";

      $db = new DB();
      $db->executeQuery($query);

      // reload job
      $this->getJob();
    }


  }

  function getJob()
  {
    $db = new DB();
    $query = "SELECT * FROM `".$this->tableName."` WHERE `batchnr` = '".$this->batch."'";
    if (!$rec = $db->lookupRecordByQuery($query))
    {
      $query2 = "
      INSERT INTO 
        `".$this->tableName."` 
      SET
        `add_user` = '".$this->user."', 
        `add_date` = NOW(),
        `change_user` = '".$this->user."', 
        `change_date` = NOW(),
        `batchnr` = '".$this->batch."',
        `afgewerkt` = 0,
        `status`    = 'pending',
        `prio`      = 5,
        `queued`    = 1,
        `naam`      = 'alleen bestanden'";
      $db->executeQuery($query2);
      $query = "SELECT * FROM `".$this->tableName."` WHERE `batchnr` = '".$this->batch."'";
      $rec = $db->lookupRecordByQuery($query);
    }
    if (count($rec) > 1)
    {
      $this->currentJob = $rec;
      $this->currentFileset = array();
      $files = explode(",", $rec["bestanden"]);
      foreach ($files as $file)
      {
        $filename = $this->filePath.$file;
        if (trim($file) != 0 AND file_exists($filename))
        {
          $this->currentFileset[] = $filename;
        }
      }
      return true;
    }
    else
    {
      $this->currentJob = array();
      $this->currentFileset = array();
      return false;
    }

  }

  function getCopyValues()
  {
    $out = array();
    $skipFields = array("id","add_user","add_date","change_user","change_date","batchnr","bestanden");
    foreach ($this->currentJob as $k=>$v)
    {
      if (in_array($k, $skipFields))
      {
        continue;  // skip field
      }
      $out[$k] = $v;  // copy field
    }
//    debug($out,"copyValues");
    return $out;
  }

  function deleteJobs($id)
  {
    if (trim($id) != "")
    {

      $queryWhere = "id IN ($id) ";
      $db = new DB();
      // verwijder de fysieke bestanden
      $query = "SELECT bestanden FROM `".$this->tableName."` WHERE ".$queryWhere;
      $db->executeQuery($query);
      while ($rec = $db->nextRecord())
      {
        $files = explode(",",$rec["bestanden"]);
        foreach($files as $item)
        {
          if (file_exists($this->filePath.$item) AND trim($item) != "")
          {
            unlink($this->filePath.$item);
          }
        }
      }
      // verwijder de batches
      $query = "DELETE FROM `".$this->tableName."` WHERE ".$queryWhere;
      $db->executeQuery($query);
      return "Jobs zijn verwijderd";
    }
    else
    {
      return "Jobs verwijderen mislukt";
    }

  }

  function deleteBatch($batch)
  {
    if (trim($batch) != "")
    {
      $queryWhere = "batchnr LIKE '$batch%' ";   // selecteer main en sub batches
      $db = new DB();
      // verwijder de fysieke bestanden
      $query = "SELECT bestanden FROM `".$this->tableName."` WHERE ".$queryWhere;
      $db->executeQuery($query);
      while ($rec = $db->nextRecord())
      {
        $files = explode(",",$rec["bestanden"]);
        foreach($files as $item)
        {
          if (file_exists($this->filePath.$item) AND trim($item) != "")
          {
            unlink($this->filePath.$item);
          }
        }
      }
      // verwijder de batches
      $query = "DELETE FROM `".$this->tableName."` WHERE ".$queryWhere;
      $db->executeQuery($query);
      return "Jobs zijn verwijderd";
    }
    else
    {
      return "Jobs verwijderen mislukt";
    }

  }

  function addFileToJob($filename)
  {
    $this->getJob();
    $db = new DB();
    $query = "
    UPDATE
      `".$this->tableName."` 
    SET
      `bestanden` = '".$this->currentJob["bestanden"].",".trim($filename)."'
    WHERE
      `batchnr` = '".$this->currentJob["batchnr"]."'
    ";
    $db->executeQuery($query);
  }

  function initModule()
  {
    include_once("AE_cls_SQLman.php");
    $tst = new SQLman();

    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"naam",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"prio",array("Type"=>"int","Null"=>false));
    $tst->changeField($this->tableName,'status',array("Type"=>"varchar(20)","Null"=>false));
    $tst->changeField($this->tableName,'bestanden',array("Type"=>"text","Null"=>false));
    $tst->changeField($this->tableName,'foutLog',array("Type"=>"text","Null"=>false));
    $tst->changeField($this->tableName,'log',array("Type"=>"text","Null"=>false));
    $tst->changeField($this->tableName,'vermogenbeheerders',array("Type"=>"text","Null"=>false));
    $tst->changeField($this->tableName,'depotbank',array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,'verwerkingsTijd',array("Type"=>"int","Null"=>false));
    $tst->changeField($this->tableName,'afgewerkt',array("Type"=>"tinyint","Null"=>false));
    $tst->changeField($this->tableName,'queued',array("Type"=>"tinyint","Null"=>false));
    $tst->changeField($this->tableName,'reconDatum',array("Type"=>"date","Null"=>false));
    $tst->changeField($this->tableName,'batchnr',array("Type"=>"varchar(30)","Null"=>false));
    $tst->changeField($this->tableName,'soort',array("Type"=>"varchar(20)","Null"=>false));
    $tst->changeField($this->tableName,'uitvoer',array("Type"=>"varchar(10)","Null"=>false));
  }

  function jobRunning()
  {
    $db = new DB();
    $query = "SELECT * FROM `".$this->tableName."` WHERE status = 'verwerken' AND afgewerkt = 0";
    if ($db->lookupRecordByQuery($query))
    {
      $this->returnMessage = "processing previous batch";
      return true;
    }
    else
    {
      return false;
    }
  }

  function nextJob()
  {
    $db = new DB();
    $query = "
    SELECT
      *
    FROM
      `".$this->tableName."`
    WHERE
      afgewerkt = 0 AND
      queued = 1 AND  
      bestanden <> 'alleen bestanden' AND
      status IN ('aangemaakt')
    ORDER BY
      prio,
      batchnr ASC
    ";
//    debug($query);
    if ($rec = $db->lookupRecordByQuery($query))
    {

      switch ($rec["prio"])
      {
        case "9":
        case "8":
          if ($this->nowMinutes() < (19 * 60))  // voor 19 uur
          {
            $this->returnMessage = "paused till 19 uur";
            return false;
          }
          break;
        case "7":
        case "6":
        case "5":
          if ($this->nowMinutes() < (15 * 60))  // voor 15 uur
          {
            $this->returnMessage = "paused till 15 uur";
            return false;
          }
          break;
        case "4":
        case "3":
        case "2":
          if ($this->nowMinutes() < (9.5 * 60))  // voor 9.30 uur
          {
            $this->returnMessage = "paused till 9.30 uur";
            return false;
          }
          break;
      }

      $this->batch = $rec["batchnr"];
      $this->returnMessage = "processing batch ". $this->batch;
      $this->getJob();
      return $this->currentJob;
    }
    else
    {
      $this->returnMessage = "no jobs to process";
      return false;
    }
  }

  function removeTijdelijkeReconRows()
  {
    $db = new DB();
    $query = "DELETE FROM tijdelijkeRecon WHERE batch = '".$this->batch."' ";
    $db->executeQuery($query);
  }

  function addToLog($txt)
  {
    $logC = $this->currentJob["log"];
    $log = date("H:i:s")." >> ".$txt."\n".$logC;
    $this->updateJob(array("log"=>$log));
  }

  function combineSubjobs($jobs)
  {
    if (count($jobs) > 0)
    {
      $db = new DB();
      $query = "UPDATE tijdelijkeRecon SET batch = '".$this->batch."' WHERE batch IN ('".implode( "','",$jobs)."')";
      $db->executeQuery($query);

    }
  }

  function errorLog($errorArray, $file)
  {
    $logC = $this->currentJob["foutLog"];
    $log = date("H:i:s")." >> ".$file."\n";
    foreach($errorArray as $errorRow)
    {
      $log .= "  * $errorRow \n";
    }
    $log .= "\n".$logC;
    $this->updateJob(array("foutLog"=>$log));
  }

  function setStatus($status)
  {
    switch ($status)
    {
      case "verwerken":
        $verwerking = $this->nowSeconds() - $this->startSec;
        $this->updateJob(array(
          "status" => $status,
          "queued" => 0,
          "change_date" => date("Y-m-d H:i:s"),
          "change_user" => "jobMan",
          "verwerkingsTijd" => $verwerking
        ));
        $this->addToLog("statusupdate naar $status");
        break;
      case "klaar":
      case "afgekeurd":
      $verwerking = $this->nowSeconds() - $this->startSec;
      $this->updateJob(array(
                         "status" => $status,
                         "afgewerkt" => 1,
                         "change_date" => date("Y-m-d H:i:s"),
                         "change_user" => "jobMan",
                         "verwerkingsTijd" => $verwerking
                       ));
      case "combined":
        $verwerking = $this->nowSeconds() - $this->startSec;
        $this->updateJob(array(
                           "status" => $status,
                           "afgewerkt" => 1,
                           "change_date" => date("Y-m-d H:i:s"),
                           "change_user" => "jobMan",
                           "verwerkingsTijd" => $verwerking
                         ));
        $this->addToLog("statusupdate naar $status");
        break;
      default:
    }
  }

  function reconRows($batch)
  {
    $db = new DB();
    $query = "SELECT count(id) as rows FROM tijdelijkeRecon WHERE batch = '".$batch."'";
    $res = $db->lookupRecordByQuery($query);
    return (int) $res["rows"];
  }

  function process2Files($files)
  {
//    debug($files);
    $this->updateJob(array(
      "bestanden" => "",     // reset bestanden
      "soort"     => "2file"
    ));
    if ($files["positionFile"]["error"] == 0)
    {
      $tmp_name = $files["positionFile"]["tmp_name"];
      $name = basename($files["positionFile"]["name"]);
      $filename = $this->batch."-_-".$name;
      move_uploaded_file($tmp_name, $this->uploads_dir."/".$filename);
      $this->addFileToJob($filename);
    }
    if ($files["cashFile"]["error"] == 0)
    {
      $tmp_name = $files["cashFile"]["tmp_name"];
      $name = basename($files["cashFile"]["name"]);
      $filename = $this->batch."-_-".$name;
      move_uploaded_file($tmp_name, $this->uploads_dir."/".$filename);
      $this->addFileToJob($filename);
    }
    // eerste file is de positie tweede is cash

  }

  function nowSeconds()
  {
    return ((int)date("H")*3600) + ((int)date("i") * 60) + (int)date("s");
  }

  function nowMinutes()
  {
    return ((int)date("H")*60) + (int)date("i")  ;
  }
}

?>