<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/09/04 10:00:58 $
    File Versie         : $Revision: 1.2 $

    $Log: reconMonitor_importMatrixHelper.php,v $
    Revision 1.2  2019/09/04 10:00:58  cvs
    call 7934

    Revision 1.3  2018/11/19 15:14:15  cvs
    call 7245

    Revision 1.2  2018/11/07 13:30:50  cvs
    call 7245

    Revision 1.1  2018/10/29 15:36:32  cvs
    call 7245



*/


class reconMonitor_importMatrixHelper
{
  var $lastDateDb = "";
  var $lastDateForm = "";

  var $statusArray = array(
    1 => "matrix",      // lichtblauw, alleen de matrix is klaargezet
    2 => "klaargezet",      // lichtgeel, iemand heeft de recons gedraaid maar zijn nog niet bekeken
    3 => "in behandeling",  // oranje, iemand heeft deze in behandeling
    4 => "bevindingen",     // oranje met vetrode letters, behandeld, maar open met bevindingen
    5 => "afgerond",        // groen, recon is afgewikkeld
  );
  var $statusColors = array(
    1 => "#AED6F1|#333333",   // lichtblauw, alleen de matrix is klaargezet
    2 => "#FCF3CF|#333333",   // lichtgeel, iemand heeft de recons gedraaid maar zijn nog niet bekeken
    3 => "#F8C471|#333333",   // oranje, iemand heeft deze in behandeling
    4 => "#F8C471|#FD0228",   // oranje met vetrode letters, behandeld, maar open met bevindingen
    5 => "#06B72B|#FFFFFF",   // groen, recon is afgewikkeld
  );
  var $statusBG = array();
  var $statusFG = array();

  function reconMonitor_importMatrixHelper()
  {
     $this->lastDate();
     foreach ($this->statusColors as $key=>$value)
     {
       $c = explode("|", $value);
       $this->statusBG[$key] = $c[0];
       $this->statusFG[$key] = $c[1];
     }
  }

  function lastDate()
  {
    $db    = new DB();
    $query = "SELECT DATE(datum) as datum FROM `reconMonitor_matrix` ORDER BY datum DESC";
    $rec   = $db->lookupRecordByQuery($query);
    $this->lastDateDb   = substr($rec["datum"],0,10);
    $this->lastDateForm = $this->dateDbToForm($this->lastDateDb);
  }

  function dateDbToForm($dbDate)
  {
    $p = explode("-", $dbDate);
    return $p[2]."-".$p[1]."-".$p[0];
  }

  function createDateOptions($current, $days=30 )
  {
    $db = new DB();
    $query = "SELECT DISTINCT `datum` FROM `reconMonitor_matrix` ORDER BY `datum` DESC LIMIT $days";
    $query = "
      SELECT 
        DISTINCT DATE(`datum`) as `datum` 
      FROM 
        `reconMonitor_matrix` 
      WHERE 
        DATE(`datum`) > '2019-01-01' 
      ORDER BY 
        `datum` DESC  
      LIMIT 30
      ";
//    debug($query);
    $db->executeQuery($query);
    $options = "";
    while ($rec = $db->nextRecord())
    {
      $addDate  = substr($rec["datum"],0,10);
      $selected = ($current == $addDate)?"SELECTED":"";
      $options .= "\n<option value='{$addDate}' $selected>".$this->dateDbToForm($addDate)."</option>";
    }
    return $options."\n";
  }

  function getBedrijven()
  {
    $db = new DB();
    $query = "SELECT DISTINCT `bedrijf` FROM `reconMonitor_matrix` ORDER BY `bedrijf` ";
    $db->executeQuery($query);
    $out = array();
    while ($rec = $db->nextRecord())
    {
      $out[] = $rec["bedrijf"];
    }
    return $out;
  }

  function getDepotBanken()
  {
    $db = new DB();
    $query = "SELECT DISTINCT `depotbank` FROM `reconMonitor_matrix` ORDER BY `depotbank` ";
    $db->executeQuery($query);
    $out = array();
    while ($rec = $db->nextRecord())
    {
      $out[] = $rec["depotbank"];
    }
    return $out;
  }

  function checkMatrix($date)
  {
    $db = new DB();
    $query = "SELECT COUNT(id) AS aantal FROM `reconMonitor_matrix` WHERE DATE(datum) = '$date' ";
    $rec = $db->lookupRecordByQuery($query);
    return $rec["aantal"];
  }

  function ajaxUpdate($data)
  {
    global $USR;
    $db = new DB();

    $query = "
      UPDATE `reconMonitor_matrix` SET
      `change_date` = NOW(),
      `change_user` = '{$USR}',
      `door` = '{$USR}',
      `status` = '{$data["status"]}',
      `memo` = '".mysql_real_escape_string($data["memo"])."'
      WHERE 
        `id` = '{$data["recId"]}'
      ";
      return $db->executeQuery($query);
  }

  function getKlaargezet($bedrijf, $datum)
  {
    global $USR;
    $db = new DB();

    $query = "SELECT count(id) AS aantal FROM `reconMonitor_matrix` WHERE `bedrijf` = '{$bedrijf}' AND DATE(datum) = '$datum' AND `klaargezet` = 1";
    $rec = $db->lookupRecordByQuery($query);

    return ($rec['aantal'] != 0);
  }

  function populateToday($date="")
  {
    global $USR;
    if ($date == "")
    {
      $dbDate = date("Y-m-d");
    }
    else
    {
      $d = explode("-", $date);
      $dbDate = $d[2]."-".$d[1]."-".$d[0];
    }

    $db = new DB();
    $db2 = new DB();

    $query = "SELECT id FROM `reconMonitor_matrix` WHERE datum = '{$dbDate}'";
    if (!$db->lookupRecordByQuery($query))
    {
      $query = "SELECT * FROM `MONITOR_bedrijfDepot` ORDER BY bedrijf, depotbank ";
      $db->executeQuery($query);
      $count = 0;

      while ($rec = $db->nextRecord())
      {

        $query = "
      INSERT INTO `reconMonitor_matrix` SET
      `add_date` = NOW(),
      `add_user` = '{$USR}',
      `change_date` = NOW(),
      `datum` = '{$dbDate}',
      `change_user` = '{$USR}',
      `door`        = '{$USR}',
      `bedrijf` =  '{$rec["bedrijf"]}',
      `depotbank` =  '{$rec["depotbank"]}',
      `bestanden` =  '{$rec["bestanden"]}',
      `status`    = 1                                            
      ";

        $db2->executeQuery($query);
        $count++;
      }
    }
    else
    {
      return "geen";
    }

    return $count;
  }

}