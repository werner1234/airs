<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$db = new  DB();



$countTables = array(
  'CRM_naw',
  'agenda',
  'taken',
  'CRM_evenementen',
  'CRM_naw_adressen',
  'CRM_naw_kontaktpersoon',
  'CRM_naw_rekeningen',
  'CRM_naw_dossier',
  'CRM_naw_templates',
  'dd_reference',
);

$countArray = array();
foreach ( $countTables as $countTable ) {
  $query = "
    SELECT
      SUM(
        DATE(" . $countTable . ".add_date) = '0000-00-00' 
        OR " . $countTable . ".add_date = '' 
        OR " . $countTable . ".add_date IS NULL 
      ) AS `zero`,
      SUM(
        DATE(" . $countTable . ".add_date) >= '2021-01-01'
      ) AS `after2021`,
      SUM(
        DATE(" . $countTable . ".add_date) < '2021-01-01' 
        AND DATE(" . $countTable . ".add_date) <> '0000-00-00' 
        AND " . $countTable . ".add_date <> '' 
        AND " . $countTable . ".add_date IS NOT NULL
      ) AS `before2021`,
      count(id) as total
    FROM
	    " . $countTable . "";

  if ( $countTable === 'dd_reference' ) {
    $query .= " WHERE `categorie` <> 'rapportage'";
  }
  $db->SQL($query);
  $rec = $db->lookupRecord();
  $countArray[$countTable] = $rec;
}


$countColumnTables = array(
  'CRM_naw',
  'CRM_naw_templates',
);

$columnCountArray = array();
foreach ( $countColumnTables as $countColumnTable ) {

  $query = "SHOW COLUMNS FROM ".$countColumnTable."";
  $db->SQL($query);
  $db->executeQuery($query);

  $countQuery = "SELECT ";
  $counter = 0;
  while ($row = $db->nextRecord())
  {
    $countQuery .= ($counter >= 1?",":"") . " 
      SUM(
        ".$row['Field']." IS NOT NULL 
        AND ".$row['Field']." <> '' 
        AND ".$row['Field']." <> '0' 
        " . ($row['Type'] === 'date'?'AND DATE('.$row['Field'].') <> "0000-00-00"':'') . "
      ) AS `".$row['Field']."` \n";
    $counter++;
  }

  $countQuery .= " FROM ".$countColumnTable."";

  $db->SQL($countQuery);
  $rec = $db->lookupRecord();

  $columnCountArray[$countColumnTable] = $rec;
}


$log = new  DB(2);

$query = '';
$query = "INSERT INTO terugmelding SET ";
$query  .= "  datum = NOW()";
$query  .= ", bedrijf = '".$__appvar['bedrijf']."'";
$query  .= ", txt = 'Records telstand'";
$query  .= ", dbinfo = ''";
$query  .= ", dbfields = ''";
$query  .= ", fileinfo = '".mysql_escape_string(serialize($countArray))."'";
$query  .= ", local_vars = ''";

$log->SQL($query);
$log->query();

$query = "INSERT INTO terugmelding SET ";
$query  .= "  datum = NOW()";
$query  .= ", bedrijf = '".$__appvar['bedrijf']."'";
$query  .= ", txt = 'Kolom telstand'";
$query  .= ", dbinfo = ''";
$query  .= ", dbfields = ''";
$query  .= ", fileinfo = '".mysql_escape_string(serialize($columnCountArray))."'";
$query  .= ", local_vars = ''";

$log->SQL($query);
$log->query();


