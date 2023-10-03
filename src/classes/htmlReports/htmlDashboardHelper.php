<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/21 11:14:03 $
    File Versie         : $Revision: 1.5 $

    $Log: htmlDashboardHelper.php,v $
    Revision 1.5  2018/03/21 11:14:03  cvs
    no message

    Revision 1.4  2017/07/21 13:55:20  cvs
    call 5933

    Revision 1.3  2017/06/26 11:39:23  cvs
    no message

    Revision 1.2  2017/06/02 14:21:52  cvs

    Revision 1.1  2017/04/26 15:04:15  cvs
    call 5816



*/

class htmlDashboardHelper
{
  var $portefeuilleData = array();
  var $portefeuille = "";
  var $user;
  var $sort = "ASC";
  var $startDatum;
  var $stopDatum;
  var $tableName = "_htmlDashboard";
  var $reUseDataset = false;

  function htmlDashboardHelper($portefeuille)
  {
    global $USR;
    $this->user = $USR;
    $this->portefeuille = $portefeuille;
    $db = new DB();
    $query = "
    SELECT 
      *
    FROM
      Portefeuilles
    WHERE
      Portefeuille = '$portefeuille'
    ";
    $this->portefeuilleData = $db->lookupRecordByQuery($query);

    $this->startDatum = $this->portefeuilleData["Startdatum"];
//    $this->startDatum = (date("Y")-1)."-01-01";
    $this->stopDatum = date("Y-m-d");

    $query = "
    SELECT 
      * 
    FROM
      `".$this->tableName."` 
    WHERE
      `add_user`  = '".$this->user."' AND
      `portefeuille` = '".$this->portefeuille."'
    ";
    $testRec = $db->lookupRecordByQuery($query);

    //$this->reUseDataset = (db2jul($testRec["add_date"])+7200 > time()); // is dataset jonger dan 2 uur

  }

  function setSort($sort)
  {
     $this->sort = $sort;
  }

  function getRecords($soort="maand", $startdatum="", $stopdatum="")
  {
    $out = array();
    $db = new DB();

    $dateSearch = "";
    if ($startdatum != "")
    {
      $dateSearch .= " AND datum >= '".$startdatum."' ";
    }
    if ($stopdatum != "")
    {
      $dateSearch .= " AND datum <= '".$stopdatum."' ";
    }
    $query = "
    SELECT 
      * 
    FROM
      `".$this->tableName."` 
    WHERE
      `add_user`  = '".$this->user."' AND
      `soort` = '".$soort."' AND
      `portefeuille` = '".$this->portefeuille."' $dateSearch
    ORDER BY  
      datum ".$this->sort." ";
//adebug($query);

    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $out[] = $rec;
    }
    return $out;
  }

  function getData($force=false, $startDatum="", $stopDatum="")
  {
    global $__appvar;
//    debug(array($startDatum, $stopDatum));
    if (!$force AND $this->reUseDataset)  // reuse existing dataset
    {
//      return true;
    }
    $this->clearData();
    if ($startDatum != "")
    {
      $rapportStart = $startDatum;
    }
    else
    {
      $rapportStart = $this->portefeuilleData["Startdatum"];
    }
    if ($stopDatum != "")
    {
      $rapportDatum = $stopDatum;
    }
    else
    {
      $rapportDatum = date("Y-m-d");
    }


    include_once($__appvar["basedir"]."/html/rapport/rapportRekenClass.php");
    include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
    include_once($__appvar["basedir"]."/html/indexBerekening.php");
    $portefeuille = $this->portefeuille;
    $portRec = $this->portefeuilleData;

    verwijderTijdelijkeTabel($portefeuille);
    $index = new indexHerberekening();
    $julstart = db2jul($rapportStart);
    $julstop  = db2jul($rapportDatum);
    $jaren     = $this->_mkDatums($index->getJaren($julstart,$julstop));
//debug(array(
//        $rapportStart,$rapportDatum ,$portefeuille,$portRec["SpecifiekeIndex"]
//      ));
    $indexData = $index->getWaarden( $rapportStart,$rapportDatum ,$portefeuille,$portRec["SpecifiekeIndex"]);
//    debug($indexData);

    $cumPerfArray = array();
    $jaarCum = array();     // array met tussenwaarden voor de jaarberekening
    $yPerfArray = array();

    $specifiekeIndexVorige = 0;
    $started = false;


    foreach($indexData as $row)
    {

      $row["soort"] = "maand";
      $row["portefeuille"] = $portefeuille;
      $row["perfCumulatief"] = $row["index"] - 100;
      if ($row["perfCumulatief"] <> 0 OR $started)
      {
        $started = true;
        $row["specifiekeIndexVorige"] = $row["specifiekeIndexPerformance"]; //- $specifiekeIndexVorige;
        $specifiekeIndexVorige = $row["specifiekeIndexPerformance"];
      }
      else
      {
        $row["specifiekeIndexVorige"] = 0;
      }

      $cumPerfArray[$row["datum"]] = $row["perfCumulatief"];

      if (in_array($row["datum"], $jaren))   // leg jaar perf en Cum perf vast
      {
        $jaardatums[] = $row["datum"];
        $jaarCum[] = $row["performance"];
        $cum = 1;
        foreach($jaarCum as $item)
        {
          $cum *= (1 + ($item/100));
        }
        $cum = ($cum - 1) * 100;
        $jaarCum = array();
        $yPerfArray[$row["datum"]] = $cum;
      }
      else
      {
        $jaarCum[] = $row["performance"];
      }

      $cumPerfArray[$row["datum"]] = $row["perfCumulatief"];
      $this->addRecord($row);
    }

    $firstItem = true;
    foreach($jaardatums as $item)
    {
      $row["datum"] = $item;
      $row["soort"] = "jaar";
      $row["performance"]    = $yPerfArray[$row["datum"]];
      $row["perfCumulatief"] = $cumPerfArray[$row["datum"]];
      $this->addRecord($row);
    }

  }

  function addRecord($data)
  {
    $db = new DB();
    $query = "
    INSERT INTO
      `".$this->tableName."`
    SET
        `add_user`  = '".$this->user."' 
      , `add_date`  = NOW()
      , `change_user`  = '".$this->user."' 
      , `change_date`  = NOW()
      , `portefeuille` = '".$this->portefeuille."'
      , `soort` = '".$data["soort"]."'
      , `datum` = '".$data["datum"]."'
      , `periodeForm` = '".substr($data["periode"],0,10)."'
      , `waardeHuidige` = '".$data["waardeHuidige"]."'
      , `waardeBegin` = '".$data["waardeBegin"]."'
      , `performance` = '".$data["performance"]."'
      , `perfCumulatief` = '".$data["perfCumulatief"]."'
    ";

    $db->executeQuery($query);
  }
  
  function clearData()
  {
    $db = new DB();
    $query = "DELETE FROM `".$this->tableName."` WHERE `portefeuille` = '".$this->portefeuille."' AND `add_user` = '".$this->user."'";
    $db->executeQuery($query);
  }

  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"portefeuille",array("Type"=>"varchar(40)","Null"=>false));
    $tst->changeField($this->tableName,"soort",array("Type"=>"varchar(25)","Null"=>false));
    $tst->changeField($this->tableName,"datum",array("Type"=>"date","Null"=>false));
    $tst->changeField($this->tableName,"periodeForm",array("Type"=>"date","Null"=>false));
    $tst->changeField($this->tableName,'waardeHuidige',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'waardeBegin',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'performance',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'perfCumulatief',array("Type"=>"double","Null"=>false));

  }

  function _mkDatums($in)
  {
    foreach($in as $item)
    {
      $out[] = $item["stop"];
    }
    return $out;
  }


}









