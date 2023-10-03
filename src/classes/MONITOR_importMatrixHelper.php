<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/06/05 13:57:39 $
    File Versie         : $Revision: 1.6 $

    $Log: MONITOR_importMatrixHelper.php,v $
    Revision 1.6  2020/06/05 13:57:39  cvs
    call 8579

    Revision 1.5  2020/05/13 13:50:48  cvs
    call 8579

    Revision 1.4  2020/01/24 12:30:08  cvs
    call 8353

    Revision 1.3  2018/11/19 15:14:15  cvs
    call 7245

    Revision 1.2  2018/11/07 13:30:50  cvs
    call 7245

    Revision 1.1  2018/10/29 15:36:32  cvs
    call 7245



*/


class MONITOR_importMatrixHelper
{
  var $lastDateDb = "";
  var $lastDateForm = "";
//  var $prio1Banken = array(
//    "BIN",
//    "TGB",
//    "AAB",
//    "AABAIR",
//    "TGBAIR",
//    "GIRO",
//    "BINAIR",
//
//  );
  var $prio1Banken = array();

  function MONITOR_importMatrixHelper()
  {
     $this->lastDate();
  }

  function lastDate()
  {
    $db    = new DB();
    $query = "SELECT add_date FROM `MONITOR_importMatrix` ORDER BY id DESC";
    $rec   = $db->lookupRecordByQuery($query);
    $this->lastDateDb   = substr($rec["add_date"],0,10);
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
    $query = "SELECT DISTINCT `add_date` FROM `MONITOR_importMatrix` ORDER BY `add_date` DESC LIMIT $days";
    $db->executeQuery($query);
    $options = "";
    while ($rec = $db->nextRecord())
    {
      $addDate  = substr($rec["add_date"],0,10);
      $selected = ($current == $addDate)?"SELECTED":"";
      $options .= "\n<option value='{$addDate}' $selected>".$this->dateDbToForm($addDate)."</option>";
    }
    return $options."\n";
  }

  function getBedrijven()
  {
    $db = new DB();
    $query = "
        SELECT 
            DISTINCT `MONITOR_importMatrix`.`bedrijf` 
        FROM 
            `MONITOR_importMatrix`
        INNER JOIN `Bedrijfsgegevens` ON 
            `Bedrijfsgegevens`.`Bedrijf` =  `MONITOR_importMatrix`.`bedrijf`
        ORDER BY 
            `MONITOR_importMatrix`.`bedrijf` ";
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
    $query = "SELECT DISTINCT `depotbank` FROM `MONITOR_importMatrix` ORDER BY `depotbank` ";
    $db->executeQuery($query);
    $out = array();
    $prio1 = array();
    $prio2 = array();
    while ($rec = $db->nextRecord())
    {
      if (in_array($rec["depotbank"], $this->prio1Banken))
      {
        $prio1[] = $rec["depotbank"];
      }
      else
      {
        $prio2[] = $rec["depotbank"];
      }
      
    }
    $out = array_merge($prio1, $prio2);
    return $out;
  }

  function checkMatrix($date)
  {
    $db = new DB();
    $query = "SELECT COUNT(id) AS aantal FROM `MONITOR_importMatrix` WHERE DATE(add_date) = '$date' ";
    $rec = $db->lookupRecordByQuery($query);
    return $rec["aantal"];
  }

  function setKlaargezet($bedrijf, $datum, $prio1)
  {
    global $USR;
    $db = new DB();

    $prio1Where = ($prio1 == 1)?"AND verwerkPrio = 1":"";

    $query = "
      UPDATE `MONITOR_importMatrix` SET
      `change_date` = NOW(),
      `change_user` = '{$USR}',
      `klaargezet` = 1
      WHERE 
        `bedrijf` = '{$bedrijf}' AND 
        `verwerkt` = 1 AND
        DATE(add_date) = '$datum'
        {$prio1Where}
      ";
      return $db->executeQuery($query);
  }

  function getKlaargezet($bedrijf, $datum)
  {
    global $USR;
    $db = new DB();

    $query = "SELECT count(id) AS aantal FROM `MONITOR_importMatrix` WHERE `bedrijf` = '{$bedrijf}' AND DATE(add_date) = '$datum' AND `klaargezet` = 1";
    $rec = $db->lookupRecordByQuery($query);

    return ($rec['aantal']);
  }

  function populateToday()
  {
    global $USR;
    $db = new DB();
    $db2 = new DB();
//    $query = "
//    SELECT
//      MONITOR_bedrijfDepot.*,
//      Vermogensbeheerders.autoPortaalVulling
//    FROM
//      `MONITOR_bedrijfDepot`
//    INNER JOIN
//      Bedrijfsgegevens ON Bedrijfsgegevens.Bedrijf = MONITOR_bedrijfDepot.bedrijf
//    INNER JOIN
//      Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = MONITOR_bedrijfDepot.bedrijf
//    ORDER BY
//      MONITOR_bedrijfDepot.bedrijf,
//      MONITOR_bedrijfDepot.depotbank
//    ";


    $query = "
    SELECT
      MONITOR_bedrijfDepot.*,
      pt.autoPortaalVulling
    FROM
      `MONITOR_bedrijfDepot` 
    INNER JOIN
      Bedrijfsgegevens ON Bedrijfsgegevens.Bedrijf = MONITOR_bedrijfDepot.bedrijf
    INNER JOIN 
      ( SELECT
	        VermogensbeheerdersPerBedrijf.Bedrijf,
	        MAX( Vermogensbeheerders.autoPortaalVulling ) as 'autoPortaalVulling'
        FROM
	        VermogensbeheerdersPerBedrijf
	      INNER JOIN Vermogensbeheerders ON 
	        VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder 
        GROUP BY
	      (
	          VermogensbeheerdersPerBedrijf.Bedrijf
	      )
	    ) pt on MONITOR_bedrijfDepot.bedrijf = pt.Bedrijf
    ORDER BY
      MONITOR_bedrijfDepot.bedrijf,
      MONITOR_bedrijfDepot.depotbank
    ";

    $db->executeQuery($query);
    $count = 0;

    while ($rec = $db->nextRecord())
    {
//      $verwerkPrio = (in_array($rec["depotbank"], $this->prio1Banken))?1:2;
      $verwerkPrio = 1;
      $query = "
      INSERT INTO `MONITOR_importMatrix` SET
      `add_date` = NOW(),
      `add_user` = '{$USR}',
      `change_date` = NOW(),
      `change_user` = '{$USR}',
      `datum`       = DATE(NOW()),
      `bedrijf` =  '{$rec["bedrijf"]}',
      `depotbank` =  '{$rec["depotbank"]}',
      `autoPortaalVulling` =  '{$rec["autoPortaalVulling"]}',
      `bestanden` =  '{$rec["bestanden"]}',
      `verwerkPrio` = {$verwerkPrio}
      ";

      $db2->executeQuery($query);
      $count++;
    }
    return $count;
  }

}