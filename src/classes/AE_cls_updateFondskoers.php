<?php
/*
    AE-ICT sourcemodule created 21 jul. 2021
    Author              : Chris van Santen
    Filename            : AE_cls_updateFondskoers.php


*/
class AE_cls_updateFondskoers
{
  var $user;

  var $fonds;
  var $datum;
  var $koers;
  var $db;
  var $fondsArray = array();
  var $fondsData = array();
  var $f;
  var $jv;
  var $ouderdomDagen = 35;  // koersen tonen waarvan de AIRS koers ouder is dan xx dagen

  function AE_cls_updateFondskoers($import=true)
  {
    global $USR, $_SESSION;
	  $this->user = $USR;
	  $this->db = new DB();
	  $this->f = new AE_cls_formatter();
	  $this->jv = $this->julDag(date("Y-m-d"));
	  if ($import)
    {
      $_SESSION["importFondsKoersen"] = array();
    }
	  else
    {
      $this->loadFromSession();
    }

  }

  function getFondsKoersen()
  {
    $query = "
      SELECT 
        `Fondsen`.`Fonds`,
        `Fondskoersen`.`Datum`,
        `Fondskoersen`.`Koers`
      FROM
        `Fondsen`
      LEFT JOIN 
        `Fondskoersen` on `Fondsen`.`Fonds` = `Fondskoersen`.`Fonds`
      WHERE
        `Fondsen`.`Fonds` IN ('".implode("','",$this->fondsArray)."')
    ";

  }

  function parseResults()
  {
    $koersDatumArray  = array();
    $tmpArray         = array();

    $db = new DB();
    // laatst bekende fondsdatum ophalen
    $query = "
    SELECT 
      `Fondskoersen`.Fonds, 
      DATE(MAX(Datum)) AS  Datum,
      `Fondsen`.`koersmethodiek`
    FROM 
      `Fondskoersen` 
    INNER JOIN `Fondsen` ON
      `Fondskoersen`.`Fonds` = `Fondsen`.`Fonds`
    WHERE 
      `Fondskoersen`.`Fonds` IN ('".implode("','",$this->fondsArray)."') 
    GROUP BY `Fondskoersen`.`Fonds`
    ORDER BY `Datum` DESC  
      ";
    //debug($query);

    $dbl = new DB();
    $db->executeQuery($query);
    while ( $rec = $db->nextRecord())
    {
      $query = "SELECT Koers FROM `Fondskoersen` WHERE Fonds = '{$rec["Fonds"]}' AND Datum = '{$rec["Datum"]}'";
      $lKoers = $dbl->lookupRecordByQuery($query);
      $tmpArray[$rec["Fonds"]] = array(
        "jd"   => $this->julDag($rec["Datum"]),
        "date" => $rec["Datum"],
        "airsKoers" => $lKoers["Koers"]);
    }

    //debug($tmpArray);
    // merge de koersdata uit het bankbestand
    foreach ($this->fondsArray as $fnd)
    {

      if (count($tmpArray[$fnd]) > 0)
      {
        $koersDatumArray[$fnd] = $tmpArray[$fnd];
      }
      else
      {
        $koersDatumArray[$fnd] = array(
          "jd"   => 0
        );
      }

    }

    // bouw een array op de HTML pagina te kunnen parsen
    $resultArray = array();
    $resId       = 0;
    foreach($this->fondsData as $fnd=>$data)
    {
      $resId++;
      $data           = array_shift($data);
      $data["id"]     = $resId;
      $data["fonds"]  = $fnd;
      $airsPos        = $koersDatumArray[$fnd];
      $addRecord      = ($airsPos["jd"] == 0);
      if ($addRecord)
      {
        $data["new"]   = 1;
        $data["dagen"] = 0;
        $resultArray[] = $data;
      }
      else
      {
        if ($data["juldag"] > $airsPos["jd"] + $this->ouderdomDagen)
        {
          $data["new"]   = 0;
          $data["laatsteDatum"] = $this->juldag2Datum($airsPos["jd"], false);
          $data["laatsteJul"]   = $airsPos["jd"];
          $data["airsKoers"]    = $airsPos["airsKoers"];
          $data["dagen"] = $data["juldag"] - $airsPos["jd"];

          $resultArray[] = $data;
        }
      }
    }
    return $resultArray;
  }

  function saveToSession()
  {
    global $_SESSION;
    $_SESSION["importFondsKoersen"]["fondsen"] = $this->fondsArray;
    $_SESSION["importFondsKoersen"]["data"] = $this->fondsData;
  }

  function loadFromSession()
  {
    global $_SESSION;
    $this->fondsArray = $_SESSION["importFondsKoersen"]["fondsen"] ;
    $this->fondsData  = $_SESSION["importFondsKoersen"]["data"];
  }

  function loadFromTRM()
  {
    global $USR;
    $this->fondsData  = array();
    $db  = new DB();
    $db2 = new DB();
    $query = "
    SELECT 
      Fonds, 
      Fondskoers, 
      Boekdatum 
    FROM 
      TijdelijkeRekeningmutaties 
    WHERE
      add_user = '{$USR}' AND 
      SUBSTRING(transactieType,1,1) IN ('A','V') AND 
      Fondskoers > 0 AND 
      Fonds != ''
    GROUP BY Fonds ";
//    debug($query);
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $jd                 = $this->julDag($rec["Boekdatum"]);
      $query = "
      SELECT 
        `Fondsen`.`Fonds`
      FROM
        `Fondsen`
      WHERE
        `Fondsen`.`Fonds` = '{$rec["Fonds"]}' AND
        `Fondsen`.`koersmethodiek` NOT IN (5,6)
      ";
      // skip fondsen met koersmethodiek 5 of 6
      if ($db2->lookupRecordByQuery($query))
      {
        $this->fondsArray[] = $rec["Fonds"];

        $this->fondsData[$rec["Fonds"]][$jd] = array(
          "datum" => $this->f->format("@D{form}", $rec["Boekdatum"]),
          "koers" => $this->f->format("@N{.2}",$rec["Fondskoers"]),
          "juldag" => $jd,
          "dagen"  => $this->jv - $jd
        );
      }

    }

    $this->fondsArray = array_unique($this->fondsArray);
  }

  function showData()
  {
    debug($this->fondsArray);
    debug($this->fondsData);
  }


  function fondsExist($fonds)
  {
    $query = "
      SELECT 
        `Fondskoersen`.*
      FROM 
        `Fondskoersen` 
      WHERE 
        `Fondskoersen`.`Fonds` = '$fonds'  
    ";
    return ($rec = $this->db->lookupRecordByQuery($query));
  }

  function checkDatum($fonds, $datum)
  {
     $query = "SELECT * FROM `Fondskoersen` WHERE `Fonds` = '$fonds' AND DATE(Datum) = '$datum' ";
     return ($rec = $this->db->lookupRecordByQuery($query));
  }

  function addToArray($fonds, $datum, $koers)
  {

    if (!in_array($fonds, $this->fondsArray) AND trim($fonds) != "")
    {
      $this->fondsArray[] = $fonds;
      $jd                 = $this->julDag($datum);
      $this->fondsData[$fonds][$jd] = array(
        "datum" => $this->f->format("@D{form}", $datum),
        "koers" => $this->f->format("@N{.2}",$koers),
        "juldag" => $jd,
        "dagen"  => $this->jv - $jd
      );
    }
  }


  function addFondsKoers($fonds, $datum, $koers)
  {
      if ($this->fondsExist($fonds) AND (float) $koers > 0)
      {
        $query = "INSERT INTO  `Fondskoersen` SET 
        `Fonds`       = '$fonds',
        `Datum`       = '$datum',
        `Koers`       = '$koers',
        `add_date`    = NOW(),
        `add_user`    = '{$this->user}',
        `change_date` = NOW(),
        `change_user` = '{$this->user}',
        `oorspKrsDt`  = '$datum'
        ";
//        debug($query);
        $this->db->executeQuery($query);
      }
  }

  function julDag($dbDatum)
  {
    $parts = explode("-",$dbDatum);
    $julian = mktime(1,1,1,$parts[1],$parts[2],$parts[0]);
    return floor($julian / 86400);
  }

  function juldag2Datum($juldag, $dbFormat=true)
  {
    $outFormat = ($dbFormat)?"Y-m-d":"d-m-Y";
    return date($outFormat, ($juldag+1) * 86400);
  }

}
