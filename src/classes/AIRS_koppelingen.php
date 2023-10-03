<?php
/*
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/05/27 12:11:35 $
    File Versie         : $Revision: 1.5 $

    $Log: AIRS_koppelingen.php,v $
    Revision 1.5  2019/05/27 12:11:35  cvs
    no message

    Revision 1.3  2019/05/27 08:01:16  cvs
    call 7753

    Revision 1.2  2018/12/14 08:30:23  cvs
    call 7410

    Revision 1.1  2018/09/14 09:51:05  cvs
    Naar VRY omgeving ter TEST


*/

class AIRS_koppelingen
{
  var $tableName = "airsKoppelingen";
  var $dataSet   = array();
  var $index     = "externId";
  var $module    = "";
  var $airsTable = "";
  var $changedRecs = array();
  function AIRS_koppelingen($module="")
  {
//    $this->initModule();
    $this->setModule($module);
  }

  function setModule($module)    {  $this->module = $module;  }

  function setAirsTable($table)  {  $this->airsTable = $table;  }

  function addItem ($data)
  {
    if ($this->module == "" OR $this->airsTable == "")
    {
      return;
    }

    global $USR;
    $monitorFields = array( "airsDescription");
    $changes = array();
    $prevRec = array();
    $db = new DB();
    $query = "SELECT * FROM `{$this->tableName}` 	WHERE `externId` = '{$data["externId"]}' ";

    if($prevRec = $db->lookupRecordByQuery($query))
    {
      $query = "UPDATE `{$this->tableName}` SET ";
      $qEnd   = "WHERE `externId` = '{$data["externId"]}'";
    }
    else
    {
      $query = "
      INSERT INTO  
        `{$this->tableName}` 
      SET 
        `add_user` = '{$USR}', 
        `add_date` = NOW(), 
        `module`   = '{$this->module}',
        `airsTable` = '{$this->airsTable}', ";
      $qEnd   = "";
      $changes = -1; // insert
    }

    foreach ($data as $fld=>$value)
    {
      if (
        $prevRec["id"] > 0 AND                      // there is old data
        in_array($fld, $monitorFields) AND          // field is monitored
        $prevRec[$fld] != $value                    // and changed
      )
      {
        $changes[$fld] = array(
          "old" => $prevRec[$fld],
          "new" => $value,
        );
      }

      $query .= "\n\t`$fld` = '".mysql_real_escape_string($value)."', ";
    }

    $query .=  "
      change_date = NOW(),
      change_user = '{$USR}' 
      ".$qEnd;
    $db->executeQuery($query);
    if ($changes == -1)
    {
      return -1;
    }

    if (count($changes) > 0)
    {
      return array(
        "module" => $prevRec["module"],
        "externId" => $prevRec["externId"],
        "airsTable" => $prevRec["airsTable"],
        "airsDescription" => $prevRec["airsDescription"],
        "airsId" => $prevRec["airsId"],
        "changes" => $changes,
      );
    }
    else
    {
      return 1;
    }
  }

  function getModuleRecords($module, $index="ext")
  {

    $idxField = ($index == "ext")?"externId":"airsId";

    $this->index = $idxField;
    $db = new DB();
    $query = "
		SELECT 
		  *
		FROM
		  `{$this->tableName}`
		WHERE
		  `module` = '{$module}'
		ORDER BY      
		  `externDescription` ";

    $db->executeQuery($query);

    while ($rec = $db->nextRecord())
    {
      $this->dataSet[$module][$rec[$idxField]]	 = $rec;
    }
    return count($this->dataSet[$module][$rec[$idxField]]);
  }

  function getModuleRecordsProducts($module, $index="ext")
  {

    $idxField = ($index == "ext")?"externId":"airsId";

    $this->index = $idxField;
    $db = new DB();
    $query = "
  SELECT 
    *
  FROM
    `{$this->tableName}`
  WHERE
    `module` = '{$module}'
  ORDER BY      
    `externDescription` ";

    $db->executeQuery($query);

    while ($rec = $db->nextRecord())
    {
      $active = true;
      $extra = unserialize($rec["externExtra"]);
      if ($extra["active"] != 1)
      {
        $active = false;
      }

      if ($active)
      {
        $this->dataSet[$module][$rec[$idxField]]	 = $rec;
      }

    }
    return count($this->dataSet[$module][$rec[$idxField]]);
  }



  function showAirsDescription($id, $module)
  {

    if (count($this->dataSet[$this->module]) == 0)
    {
      $this->getModuleRecords($module);
    }
    $out = $this->dataSet[$module][$id]["airsDescription"];
    return ($out != "")?$out:"niet gevonden! ($id)";
  }

  function showExternDescription($id, $module)
  {
    if (count($this->dataSet[$module]) == 0)
    {
      $this->getModuleRecords($module);
    }
    $out = $this->dataSet[$module][$id]["externDescription"];
    return ($out != "")?$out:"niet gevonden! ($id)";
  }

  function getExtra($id)
  {
    if (count($this->dataSet) == 0)
    {
      $this->getModuleRecords();
    }
    return unserialize($this->dataSet[$id]["externExtra"]);
  }

  function initModule()
  {
    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"module",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"externId",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"externDescription",array("Type"=>" varchar(150)","Null"=>false));
    $tst->changeField($this->tableName,"externExtra",array("Type"=>" text","Null"=>false));
    $tst->changeField($this->tableName,"airsDescription",array("Type"=>" varchar(150)","Null"=>false));
    $tst->changeField($this->tableName,"airsTable",array("Type"=>" varchar(100)","Null"=>false));
    $tst->changeField($this->tableName,"airsId",array("Type"=>" int","Null"=>false));

  }




}

