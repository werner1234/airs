<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/22 14:20:49 $
    File Versie         : $Revision: 1.2 $

    $Log: api_info_fondsKoers.php,v $
    Revision 1.2  2018/10/22 14:20:49  cvs
    call 7228

    Revision 1.1  2018/09/26 09:30:07  cvs
    update naar DEMO



*/

$fonds = rawurldecode($__ses["data"]["fonds"]);

/////////////////////////////////////
$dates    = array();
$jaren    = array();
$yearData = array();
$out      = array();

$db = new DB();

$query = "SELECT * FROM Fondsen WHERE Fonds = '$fonds' ";
$fondsRec = $db->lookupRecordByQuery($query);



$query = "SELECT Datum, Koers FROM Fondskoersen WHERE Fonds = '$fonds' ORDER BY Datum DESC";
$koersRec = $db->lookupRecordByQuery($query);

$dates["db"] = $_DB_resources[1]['db'];
$dates["last"] = $koersRec;

$thisYear = date("Y");
$query = "SELECT Datum, Koers FROM Fondskoersen WHERE fonds = '$fonds' AND  Datum >= '".($thisYear-1)."-01-01' ORDER BY Datum ";
$title = "Koersverloop afgelopen 1 jaar";

$db->executeQuery($query);
$firstRec = true;
while ($rec = $db->nextRecord())
{
  if ($firstRec)
  {
    $firstRec = false;
    $dates["yearFirst"] = $rec;
  }
  $out[] = $rec;
  $dates["yearLast"] = $rec;
}

$query="SELECT year(Datum) as jaar FROM Fondskoersen WHERE Fonds='$fonds' AND Datum >= '2004-01-01' GROUP BY jaar ORDER By jaar";
$db->executeQuery($query);

while($data= $db->nextRecord())
{
  $jaren[] = $data['jaar'];
}

foreach ($jaren as $jaar)
{
  $query="SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='$fonds' AND year(Datum)='$jaar' ORDER BY Datum DESC";
  $data= $db->lookupRecordByQuery($query);
  $yearData[] = array("datum" => $data['Datum'], "koers" => $data['Koers']);
}


$output = array();
$output["statics"]   = $dates;
$output["graphData"] = $out;
$output["yearData"]  = $yearData;




