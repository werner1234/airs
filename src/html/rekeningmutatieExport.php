<?php
/*
    AE-ICT sourcemodule created 28 okt. 2019
    Author              : Chris van Santen
    Filename            : rekeningmutatieExport.php

*/

include_once("wwwvars.php");
session_start();
//debug($_REQUEST);
global $__rekmutGlobe;
//debug($_SESSION["rekmutQuery"]);
$s = explode ("(Rekeningmutaties)", $_SESSION["rekmutQuery"]);

$s2 = explode ("LIMIT", $s[1]);

$db  = new DB();
$db2 = new DB();

if ($_GET["type"] == "RM") // call 9324
{

  $query = "
  SELECT 
    Rekeningmutaties.id AS transId,
    date(Rekeningmutaties.Boekdatum) as Boekdatum,
    Rekeningmutaties.Fonds ,
    Rekeningmutaties.Omschrijving ,
    Rekeningmutaties.Grootboekrekening,
    Rekeningmutaties.Memoriaalboeking,
    Rekeningmutaties.Valuta ,
    Rekeningmutaties.Valutakoers ,
    Rekeningmutaties.Aantal ,
    Rekeningmutaties.Fondskoers ,
    Rekeningmutaties.Rekening ,
    Rekeningmutaties.Debet ,
    Rekeningmutaties.Credit ,
    Rekeningmutaties.Bedrag ,
    Rekeningmutaties.Transactietype ,
    Fondsen.Valuta as fondsValuta,
    Fondsen.ISINCode ,
    Rekeningmutaties.settlementDatum ,
  	Rekeningmutaties.orderId
  FROM 
    (Rekeningmutaties)	
";
  if (!stristr($s2[0], "fondsen"))
  {
    $query .= "
    LEFT JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
    ";
  }
  $query .= $s2[0];
//  debug($query);
  $db->executeQuery($query);
  $firstRec = true;
  while ($rec = $db->nextRecord())
  {

    if ($firstRec)
    {
      $outRows[] = array(
        "transId",
        "Boekdatum",
        "Fonds",
        "Omschrijving",
        "Grootboekrekening",
        "Memoriaalboeking",
        "Valuta",
        "Valutakoers",
        "Aantal",
        "Fondskoers",
        "Rekening",
        "Debet",
        "Credit",
        "Bedrag",
        "Transactietype",
        "fondsValuta",
        "ISINCode",
        "settlementDatum",
        "orderId"
      );
      $firstRec = false;

    }
    $rowRec = array();
    foreach($rec as $value)
    {
      $rowRec[] = $value;
    }
    $outRows[] = $rowRec;
  }

  $output = array();
  foreach($outRows as $row)
  {
    $output[] = '"'.implode('","', $row).'"';
  }
  $filename = "AIRS_RM_".date("Ymd-His").".csv";
}
else
{
  $query = "SELECT Rekeningmutaties.*  FROM (Rekeningmutaties) ".$s2[0];

  $db->executeQuery($query);
  $firstRec = true;
  while ($rec = $db->nextRecord())
  {
    if ($firstRec)
    {
      $vb = $rec["Vermogensbeheerder"];
      $query = "SELECT * FROM `grootboeknummers` WHERE `vermogensbeheerder` = '$vb' ";
      $gbMap = array();
      $db2->executeQuery($query);
      while ($gbRec = $db2->nextRecord())
      {
        $gbMap[$gbRec["grootboekrekening"]] = $gbRec["rekeningnummer"];
      }
      $firstRec = false;
//    debug($gbMap);
    }

    $gb = $rec["Grootboekrekening"];
    $rekRows[] = array(
      "Boekdatum" => $rec["Boekdatum"],
      "Rekening" => $rec["Rekening"],
      "Grootboekrekening" => $gb,
      "vbGb" => ($gbMap[$gb] != "")?$gbMap[$gb]:$gb,
      "Bedrag" => $rec["Bedrag"],
      "Portefeuille" => $rec["Portefeuille"],
      "Omschrijving" => $rec["Omschrijving"],
      "id" => $rec["id"],
    );

  }



  $filename = "AIRS_rekeningmutaties_$vb_".date("Ymd-His").".csv";
  $output = array();
  foreach ($rekRows as $item)
  {
    $item["Omschrijving"] = str_replace(",", " ", $item["Omschrijving"]); // verwijder event. komma's
    $bDat = substr($item["Boekdatum"],8,2).substr($item["Boekdatum"],5,2).substr($item["Boekdatum"],0,4);
    $dataRow = array(
      0,                                      //1
      '"M"',                                  //2
      '"9"',                                  //3
      '"'.substr($item["Boekdatum"],5,2).'"',      //4
      '""',                                   //5
      '"'.$item["id"].'"',                    //6
      '"'.$item["Omschrijving"].'"',          //7
      '"'.$bDat.'"',                          //8
      '""',                                   //9
      '""',                                   //10
      '""',                                   //11
      '""',                                   //12
      0.00,                                   //13
      '" "',                                  //14
      '" "',                                  //15
      1.00,                                   //16
      '" "',                                  //17
      0.00,                                   //18
      '" "',                                  //19
      '" "',                                  //20
      '" "',                                  //21
      0.00,                                   //22
      '" "',                                  //23
      '" "',                                  //24
      '" "',                                  //25
      0.00,                                   //26
      '" "',                                  //27
      '" "',                                  //28
      0.00,                                   //29
      '" "',                                  //30
      '" "',                                  //31
      '" "',                                  //32
      '" "',                                  //33
      0.00,                                   //34
      0.00,                                   //35
      0.00,                                   //36
      0.00,                                   //37
      '" "',                                  //38
      '" "',                                  //39
      '" "'                                   //40
    );
    $output[] = implode(",", $dataRow);

    $dataRow = array(
      1,                                        //1
      '"M"',                                    //2
      '" 9"',                                   //3
      '" "',                                    //4
      '""',                                     //5
      '"'.$item["id"].'"',                      //6
      '"'.$item["Omschrijving"].'"',            //7
      '"'.$bDat.'"',                            //8
      '"'.$__rekmutGlobe["tegenRekening"].'"',  //9
      '""',                                     //10
      '""',                                     //11
      '""',                                     //12
      $item["Bedrag"],                          //13
      '"N"',                                    //14
      '""',                                     //15
      1,                                        //16
      '" "',                                    //17
      '""',                                     //18
      '" "',                                    //19
      '" "',                                    //20
      '" "',                                    //21
      0.00,                                     //22
      '" "',                                    //23
      '" "',                                    //24
      '" "',                                    //25
      0.00,                                     //26
      '" "',                                    //27
      '" "',                                    //28
      0.00,                                     //29
      '" "',                                    //30
      '" "',                                    //31
      '" "',                                    //32
      '" "',                                    //33
      0.00,                                     //34
      0.00,                                     //35
      0.00,                                     //36
      0.00,                                     //37
      '" "',                                    //38
      '" "',                                    //39
      '" "'                                     //40

    );
    $output[] = implode(",", $dataRow);

    $dataRow = array(
      2,                                    //1
      '"M"',                                //2
      '" 9"',                               //3
      '" "',                                //4
      '""',                                 //5
      '"'.$item["id"].'"',                  //6
      '"'.$item["Omschrijving"].'"',        //7
      '"'.$bDat.'"',                        //8
      '"'.$item["vbGb"].'"',                //9
      '""',                                 //10
      '""',                                 //11
      '""',                                 //12
      $item["Bedrag"] * -1,                 //13
      '"N"',                                //14
      '""',                                 //15
      1,                                    //16
      '" "',                                //17
      '""',                                 //18
      '" "',                                //19
      '" "',                                //20
      '" "',                                //21
      0.00,                                 //22
      '" "',                                //23
      '" "',                                //24
      '" "',                                //25
      0.00,                                 //26
      '" "',                                //27
      '" "',                                //28
      0.00,                                 //29
      '" "',                                //30
      '" "',                                //31
      '" "',                                //32
      '" "',                                //33
      0.00,                                 //34
      0.00,                                 //35
      0.00,                                 //36
      0.00,                                 //37
      '" "',                                //38
      '" "',                                //39
      '" "'                                 //40
    );
    $output[] = implode(",", $dataRow);
  }
}






header("Content-type: text/csv");
header("Content-Disposition: attachment; filename={$filename}");
header("Pragma: no-cache");
header("Expires: 0");
echo implode("\r\n", $output);



