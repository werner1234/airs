<?php
/*
    AE-ICT sourcemodule created 19 feb. 2020
    Author              : Chris van Santen
    Filename            : ubslux_reconFuncties.php

*/

function recon_readBank($filename, $filetype)
{
  global $prb, $batch, $recon, $airsOnly,$error;
  $ontdubbelArray = array();

  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }
  $csvRegels = Count(file($filename));
//  $prb->max = 100;
//  $prb->addLabel('text','txt1','Inlezen bankbestand, '.count($rawData).' regels');	// add Text as Label 'txt1' and value 'Please wait'
//  $prb->moveMin();
  $pro_multiplier = 100/$csvRegels;
  //$prb->show();
  $teller = 0;

  $tmpGeldArray = array();



  if ($filetype == "FND")
  {
    while ($data = fgetcsv($handle, 8192, ";"))
    {

      $teller++;
      if ($teller < 2)                             continue;  // headers overslaan

      $pro_step += $pro_multiplier;

      $record = array("depot" => "UBSL","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel

      $record["type"]         = "sec";
      $record["portefeuille"] = (int)$data[0];
      $record["datum1"]       = cnvDate($data[20]);
      $record["datum2"]       = cnvDate($data[20]);
      $record["soort"]        = "";
      $record["aantalRaw"]    = $data[4];
      $record["aantal"]       = $data[4];
      $record["ISIN"]         = trim($data[1]);
      $record["valuta"]       = $data[3];
      $record["bankCode"]     = $data[18];
      $record["fonds"]        = $data[2];
//      $record["PE"]           = 1;
      $record["koersDatum"]   = cnvDate($data[19]);
      $record["koersRaw"]     = $data[6];
      $record["koers"]        = $data[6];

      $record["batch"] = $batch;
      $output[] = $record;
      $recon->addRecord($record);
    }

  }
  else
  {
    $teller = 0;
    $dataSet = array();
    while ($data = fgetcsv($handle, 8192, ";"))
    {

      $data = trimFields($data);
      if ($data[0] == "acc. no.")  // headers overslaan
      {
        continue;
      }

      if ($data[9] != "")  // movementdate mag niet gevuld
      {
        continue;
      }

      $days = _julDag($data[1]);

      if (_julDag($dataSet[$data[0].$data[3]]["datum"]) < $days)
      {
        $dataSet[$data[0].$data[3]] = array(
          "datum" => $data[1],
          "rekening" => (int)$data[0],
          "saldo" => (float) $data[2],
          "currency" => $data[3]
        );
      }

    }


    foreach ($dataSet as $data)
    {

      $teller++;
      $d = explode(".",$data["datum"]);
      $record = array("depot" => "UBSL","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
      $record["type"]      = "cash";
      $record["rekening"]  = $data["rekening"];
      $record["datum1"]    = $d[2]."-".$d[1]."-".$d[0];
      $record["valuta"]    = $data["currency"];
      $record["bedragRaw"] = $data["saldo"];
      $record["DC"]        = ($data["saldo"] < 0)?"D":"C";
      $record["datum2"]    = $d[2]."-".$d[1]."-".$d[0];
      $record["iban"]      = "";
      $record["page"]      = "";
      $record["bedrag"]    = $data["saldo"];;
      $record["batch"]     = $batch;

      $output[] = $record;

      $recon->addRecord($record);
    }


  }



  if ($filetype == "FND")
  {
    echo "<li>AIRS data ophalen";
    ob_flush();flush();
    $recon->fillTableFormAIRS();


    echo "<li>AIRS portefeuilles ophalen";
    ob_flush();flush();
    $airsOnly = $recon->getAirsPortefeuilles();

  }
  else  // GLD
  {
    echo "<li>AIRS rekeningnummers ophalen";
    ob_flush();flush();
    foreach ($tmpGeldArray as $key => $values)  // extra loop om hoogste pagina te selecteren voor saldo call 3529
    {
      $tmpRecord = $values[0];                                       // vul temp rec met eerst gevonden record
      if (count($values) > 1 )                                       // als er meer dan 1 pagina is gevonden dan hoogste pagina gaan zoeken
      {
        for ($x=1; $x < count($values); $x++)
        {
          if ( (int) $values[$x]["page"] > (int) $tmpRecord["page"])
          {
            $tmpRecord = $values[$x];                               // als huidige pagenr > dan opgeslagen dan overschrijven
          }
        }
      }
      unset($tmpRecord["page"]);                                    // verwijder paginanr om SQL error te voorkomen
      $output[] = $tmpRecord;
      $recon->addRecord($tmpRecord);
    }

    $airsOnly = $recon->getAirsCashRekeningen();
  }

  $recon->fillVB();

  //$prb->hide();

  unlink($filename);
  return $teller;
}


function trimFields($in)
{
  $out = array();
  foreach ($in as $item)
  {
    $out[] = trim($item);
  }
  return $out;
}

function _julDag($datum)
{
  $d = explode(".",$datum);
  $julian = mktime(1,1,1,(int)$d[1], (int) $d[0], (int) $d[2]);
  return floor($julian / 86400);
}

function validateFile($filename)
{
  global $error, $cronRun;
  $error = array();

  echo "<li>start validatie bestanden";
  ob_flush();flush();


  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 4096, ";");

  $data[0] = stripBOM($data[0]);
  $validateStr1 = ($data[0] == "Acc. Nr.");
  $validateStr2 = ($data[1] == "ISIN-Code");

  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen UBSLUX bestand";
  }



  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

function cnvBedrag($txt)
{
	return str_replace(',','.',$txt);
}

function cnvDate($in)
{
  $p = explode(".", $in);
  return $p[2]."-".$p[1]."-".$p[0];
}


function getRekeningNr($port,$valuta)
{
  $DB = new DB();
  $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '$port' AND Memoriaal = 0 AND Valuta='$valuta'";
  $DB->SQL($query);
  $record = $DB->lookupRecord();
  return $record["Rekening"];

}

