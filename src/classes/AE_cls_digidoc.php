<?php
/*
    AE-ICT sourcemodule created 12 okt. 2022
    Author              : Chris van Santen
    Filename            : AE_cls_digidoc.php

*/

include_once($__appvar["basedir"]."/classes/AE_cls_mysql.php");

class digidoc
{
  var $classResource;
  var $debug = true;
  var $error = false;
  var $counter = array();
  var $dd_maxFileSizeMb;
  var $dd_currentDataStore;
  var $cfg;
  var $dd_user;
  var $useZlib = true;
  var $dd_filePushType;
  var $referenceId;

  function digidoc($dbId=1)
  {
     global $USR;
     $this->classResource = new DB($dbId);
     $this->cfg = new AE_config($dbId);

     $this->dd_currentDataStore = $this->cfg->getData("dd_currentDataStore");
     if( $this->dd_currentDataStore=='' )
     {
       $this->dd_currentDataStore = 1;
     }

     $this->dd_maxFileSizeMb      = $this->cfg->getData("dd_maxFileSizeMb");
     if( $this->dd_maxFileSizeMb == '' )
     {
       $this->dd_maxFileSizeMb='1024';
     }

     $this->dd_filePushType     = ($this->cfg->getData("dd_filePushType") <> "inline")?"attachment":"inline";
     if( $this->dd_filePushType == '' )
     {
       $this->dd_filePushType='attachment';
     }

     $this->dd_user = $USR;
  }

  function showInfo()
  {
    echo "<PRE>";
    print_r($this);
    echo "<hr>";
    echo $this->checkDataStoreSize($this->dd_currentDataStore)?"Oke":"te groot";

    echo "</PRE>";
  }


  function StoreName($storeNumber="current")
  {
  	$store = (is_numeric($storeNumber))?$storeNumber:$this->dd_currentDataStore;
  	if ($store < 10)
    {
      $store = substr("0".$store,-2);
    }
    $tableName = "dd_datastore".$store;
    return $tableName;
  }

  function exit_with_error($txt)
  {
	  $this->errorstr = $txt;
	  if ($this->debug)
	  {
      $out = addslashes("Digidoc class: $txt");
      echo "<script>alert('$out');</script>";
	  }
	  $this->error = true;
    return false;
  }

  /**
   * Haal gegevens van een table op
   *
   * @param bij output
   * @param "records" geeft het aantal records terug,
   * @param "size" geeft tablegrootte,
   * @param "create" aanmaakdatum,
   * @param "update" mutatiedatum
   */
  function tableStatus($tableName, $output="")
  {
    $db     = $this->classResource;
    $query  = "SHOW TABLE STATUS LIKE '".$tableName."' ";
    $db->executeQuery($query);
    $rec    = $db->nextRecord();
    $output = strtolower($output);
    switch ($output)
    {
      case "records":
        $out = $rec["Rows"];
        break;
      case "size":
        $out = number_format((($rec["Data_length"]/1024)/1024),2)." Mb";
        break;
      case "create":
        $out = dbdatum($rec["Create_time"])." ".dbtijd($rec["Create_time"]);
        break;
      case "update":
        $out = dbdatum($rec["Update_time"])." ".dbtijd($rec["Update_time"]);
        break;

      default:
        $out = $rec;
    }
    return $out;

  }



  /**
   * Create new datastore and increment actualstore value in config table
   */
  function createDataStore()
  {
    $db             = $this->classResource;
    $createRequest  = "SHOW CREATE TABLE dd_datastore01";
    $db->executeQuery($createRequest);
    $crRec          = $db->nextRecord();
    $createQuery    = $crRec["Create Table"];
    $n              = $this->dd_currentDataStore + 1;
    $newTable       = $this->StoreName($n);
    $createQuery    = str_replace("dd_datastore01", $newTable, $createQuery);
    //$this->cfg->addItem("dd_currentDataStore",$n);
    $db->executeQuery($createQuery);

    $createCheck = "SHOW TABLE STATUS LIKE '$newTable'";
    $db->executeQuery($createCheck);
    $crChk = $db->nextRecord();
    if( $crChk['Name'] ==  $newTable )
    {
      $this->cfg->addItem("dd_currentDataStore",$n);
      $this->dd_currentDataStore = $n;
    }
    sleep(1);
  }

   /**
   * Check if datastore exceeds max size
   * @param $storeNumber = # of store to check, blank is current store
   */

   function checkDataStoreSize($storeNumber="current")
   {
     $tableInfo = $this->tableStatus($this->StoreName($storeNumber));
     $maxSize = 1024*1024*$this->dd_maxFileSizeMb;
     $actualSize = $tableInfo["Data_length"];
     return ($actualSize < $maxSize);
   }

   /**
   * pushes document to browser or file dialog
   * @param $datastore = tablename of the datastore
   * @param $id = document id to grep
   *
   * @param default behavior is inline, but can be set to download with
   * @param $this->dd_filePushType = "attachment";
   */
   function pushDocument($datastore,$id)
   {
     $db      = $this->classResource;
     $query   = "SELECT * FROM $datastore WHERE id='$id'";
     $row     = $db->lookupRecordByQuery($query);
//debug($row);
     if($row["id"] > 0)
     {
       $query   = "SELECT * FROM dd_reference WHERE `id`='{$row["referenceId"]}'";
       $refRec  = $db->lookupRecordByQuery($query);
       $file    = cnvFilename(basename($refRec["filename"]));
       $query   = "
          INSERT INTO dd_logging SET 
            `user`      = '{$this->dd_user}', 
            `ip`        = '{$_SERVER['REMOTE_ADDR']}', 
            `txt`       = '{$file} \n{$row["description"]}', 
            `datum`     = NOW(), 
            `datastore` = '{$datastore}', 
            `dd_id`     = '{$id}'";
       $db->executeQuery($query);


       $blob = ($row["blobCompressed"])?gzuncompress($row["blobdata"]):$row["blobdata"];
       header("Pragma: public");
       header("Expires: 0");
       header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
       header("Cache-Control: private",false);
       header("Content-Type: ".$row["filetype"]);
       header('Content-Disposition: '.$this->dd_filePushType.'; filename="'.$file.'";');
       header("Content-Transfer-Encoding: binary");
       header("Content-Length: ".$row["filesize"]);
       echo $blob;
     }
   }


   function retrieveDocumentToFile($refId,$path, $existingFiles = array())
   {
     $refId     = (int) $refId;
     $db        = $this->classResource;
     $query     = "SELECT * FROM dd_reference WHERE id='{$refId}'";
     $row       = $db->lookupRecordByQuery($query);

     $filename  = cnvFilename(basename($row['filename']));
     if ( in_array($filename, $existingFiles) )
     {
       $filename = $row['dd_id']."_".$filename;
     }

     $filePath  = $path."/".$filename;

     $query     = "SELECT * FROM ".$row['datastore']." WHERE id='".$row['dd_id']."'";
     $row       = $db->lookupRecordByQuery($query);

     if( $row["id"] > 0)
     {
       $query = "
        INSERT INTO `dd_logging` SET 
          `user`      = '{$this->dd_user}', 
          `ip`        = '{$_SERVER['REMOTE_ADDR']}', 
          `txt`       = '{$filename} \n{$row["description"]}', 
          `datum`     = NOW(), 
          `datastore` = '{$datastore}', 
          `dd_id`     = '{$id}'
       ";
       $db->executeQuery($query);
       $blob  = ($row["blobCompressed"])?gzuncompress($row["blobdata"]):$row["blobdata"];
       $fp    = fopen($filePath, 'w');
       fwrite($fp, $blob);
       fclose($fp);
       return $filename;
     }
     return false;
   }
   /**
    * Add document to a datastore and create the root reference record
    * @param $recordArray
    */
   function addDocumentToStore($recordArray="",$extraVelden=array())
   {
   	if (!is_array($recordArray))
   	{
   		return false;
   	}

    $add_date    = ($extraVelden["add_date"]    <> "")?"'".$extraVelden["add_date"]."'":"NOW()";
    $change_date = ($extraVelden["change_date"] <> "")?"'".$extraVelden["change_date"]."'":"NOW()";
     
   	$db = $this->classResource;
   	if ($this->checkDataStoreSize($this->dd_currentDataStore) == false) $this->createDataStore();  //create new datastore is over max size
  	$dataStore = $this->StoreName();
   	/*
   	*  query to insert the document in the current datastore
   	*/
   	if($this->useZlib)
   	{
   	  $zipped=1;
     	$blobData = gzcompress($recordArray["blobdata"]);
     	$blobData = bin2hex($blobData);
     	$blobData = pack("H*" , $blobData);
     	if(!gzuncompress($blobData))
     	{
     	  $blobData = $recordArray["blobdata"];
     	  $zipped=0;
     	}
   	}
   	else
   	{
   	  $blobData = $recordArray["blobdata"];
   	  $zipped=0;
   	}

   	$dbsize = strlen($blobData);
   	if($dbsize > 16777216)
   	{
   	   echo "
        <br><br><div style='background: maroon; color: white; padding: 10px;'>
        Opslaan van ".$recordArray["filename"]." niet mogelijk. <br/>
        Het bestand (".round($dbsize/1024/1024,1)."Mb) is groter dan 16Mb 
        </div>";
   	   return false;
   	}
   	$extraSet='';
   	foreach ($extraVelden as $key=>$value)
    {
      if ($key <> "add_date" AND $key <> "change_date")
      {
        $extraSet .= ",$key='".mysql_escape_string($value)."'";
      }

    }

    $filename = cnvFilename(mysql_escape_string($recordArray["filename"]));
   	$blobData = bin2hex($blobData);
   	$storeQuery = "INSERT INTO ".$dataStore." SET ".
   	              "  add_user       = '".$this->dd_user."' ".
   	              ", change_user    = '".$this->dd_user."' ".
   	              ", add_date       = $add_date ".
   	              ", change_date    = $change_date  ".
   	              ", filename       = '".$filename."' ".
   	              ", filesize       = '".$recordArray["filesize"]."' ".
   	              ", filetype       = '".$recordArray["filetype"]."' ".
   	              ", description    = '".mysql_escape_string($recordArray["description"])."' ".
   	              ", blobCompressed = ".$zipped." ".
   	              ", blobdata       = unhex('$blobData') ";

   	$db->executeQuery($storeQuery);
   	$blobid = $db->last_id();        // last inserted id ophalen
   	if($blobid < 1)
   	{
   	  $querySize = strlen($storeQuery);
      $db->SQL("SHOW VARIABLES LIKE 'max_allowed_packet'");
      $maxQuerySize = $db->lookupRecord();
      $maxQuerySize = $maxQuerySize['Value'];
      if($querySize > $maxQuerySize)
      {
        echo "Opslaan van ".$recordArray["filename"]." in database mislukt. Query is (".round(($querySize/1024),1)." kb) en is groter dan het huidige maximum van (".round(($maxQuerySize/1024),1)." kb) <br>\n";
      }
      else
      {
        echo "Opslaan van ".$recordArray["filename"]." in database mislukt?";
      }
   	  return false;
   	}
   	/*
   	*  query to insert the root reference of the document in the reference table
   	*/
   	$referenceQuery = "INSERT INTO dd_reference SET ".
   	                  "  add_user      = '".$this->dd_user."' ".
   	                  ", change_user   = '".$this->dd_user."' ".
   	                  ", add_date      = $add_date ".
   	                  ", change_date   = $change_date ".
   	                  ", dd_id         = $blobid ".
   	                  ", datastore     = '$dataStore' ".
   	                  ", filename       = '".mysql_escape_string($recordArray["filename"])."' ".
   	                  ", filesize       = '".$recordArray["filesize"]."' ".
   	                  ", filetype       = '".$recordArray["filetype"]."' ".
   	                  ", categorie   = '".mysql_escape_string($recordArray["categorie"])."' ".
   	                  ", rootReference = 1 ".
   	                  " $extraSet ".
   	                  ", description   = '".mysql_escape_string($recordArray["description"])."' ".
   	                  ", module        = '".mysql_escape_string($recordArray["module"])."' ".
   	                  ", module_id     = '".$recordArray["module_id"]."' ".
   	                  ", keywords      = '".mysql_escape_string($recordArray["keywords"])."' ";
   $db->executeQuery($referenceQuery);
   $referenceId       = $db->last_id(); // last inserted id ophalen
   $this->referenceId = $referenceId;
    /*
   	*  query to update the link the root reference to the document
   	*/
   $storeUpdateQuery = "UPDATE ".$dataStore." SET ".
                       "referenceId = $referenceId ".
                       "WHERE id = $blobid";
   $db->executeQuery($storeUpdateQuery);
   return true;
   }

  function sanitizeFileName($in)
  {
    $out = preg_replace('/[^.a-z0-9A-Z_-]+/', '', $in);
    return trim($out);
  }
}

