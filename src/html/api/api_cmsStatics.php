<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/07/10 07:30:34 $
    File Versie         : $Revision: 1.4 $

    $Log: api_portStatics.php,v $
    Revision 1.4  2020/07/10 07:30:34  cvs
    call 8707

    Revision 1.3  2020/07/08 11:20:08  cvs
    call 8707

    Revision 1.2  2019/09/30 08:27:04  cvs
    call 8136

    Revision 1.1  2018/09/26 09:30:07  cvs
    update naar DEMO



*/



$vb = $__ses["data"]["vb"];
$rc = array();
$ov = array();

$db = new DB();
$query = "
  SELECT 
    `Risicoklasse` 
  FROM 
    `Risicoklassen` 
  WHERE 
    `Vermogensbeheerder` = '{$vb}'
  ORDER BY
    `Risicoklasse`
 ";
$db-> executeQuery($query);
while ($rec = $db->nextRecord())
{
  $rc[$rec["Risicoklasse"]] = $rec["Risicoklasse"];
}

$query = "
  SELECT 
    `waarde` 
  FROM 
    `KeuzePerVermogensbeheerder` 
  WHERE 
    `categorie` = 'SoortOvereenkomsten' AND 
    `vermogensbeheerder` = '{$vb}'
";
$db-> executeQuery($query);
while ($rec = $db->nextRecord())
{
  $ov[$rec["waarde"]] = $rec["waarde"];
}


$output = array();

$output["statics"] = array(
  "vb" => $vb,
  "stamp" => date("Y-m-d H:i:s")
);

$output["soortOvereenkomsten"] = $ov;
$output["risicoklasse"] = $rc;


