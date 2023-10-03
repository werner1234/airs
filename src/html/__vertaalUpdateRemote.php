<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2020/07/08 07:00:13 $
  File Versie					: $Revision: 1.2 $

  $Log: __ubslux.php,v $
  Revision 1.2  2020/07/08 07:00:13  cvs
  call 8715

  Revision 1.1  2020/06/26 06:50:20  cvs
  call 8715

  Revision 1.1  2020/06/26 04:42:47  cvs
  test


 */

include_once("wwwvars.php");

$_DB_resources[22]['server'] = "appie10.airshost.nl";
$_DB_resources[22]['user']   = "update_vertaal";
$_DB_resources[22]['passwd'] = "4191vj(U4)";
$_DB_resources[22]['db']     = "airs_vertaal";

$tel["totaal"]   = 0;
$tel["nieuw"]    = 0;
$tel["skipped"]  = 0;

$dbRemote = new DB(22);
$dbLocal  = new DB();

if ($_GET["days"] > 0)
{
  $day = $_GET["days"];
}
else
{
  $day = 3;
}

$query = "SELECT * FROM appVertaling WHERE DATE(add_date) > (NOW() - INTERVAL {$day} DAY) ORDER BY id ";

echo "<h3>$query</h3>";
$dbLocal->executeQuery($query);
while ($rec = $dbLocal->nextRecord())
{

  $tel["totaal"]++;
  $query = "SELECT id FROM appVertaling WHERE veld = '{$rec["veld"]}'";

  if ($remoteRec = $dbRemote->lookupRecordByQuery($query))
  {
    $tel["skipped"]++;
    $skipArray[] = $rec["veld"];
  }
  else
  {
    $tel["nieuw"]++;
    $f =array();
    $q = "INSERT INTO appVertaling SET \n";
    unset($rec["id"]);
    foreach ($rec as $k=>$v)
    {
      $f[] = "`$k` = '$v'";
    }
    $q .= implode(",\n", $f);

    $dbRemote->executeQuery($q);
  }

}

debug($tel);
debug($skipArray);

