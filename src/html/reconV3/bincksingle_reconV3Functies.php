<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function binck_recon_readBank($filename)
{
  global $prb, $batch, $recon, $airsOnly, $cronRun;

  $verbose = !$cronRun;
  $count   = 0;
  $db = new DB();

  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }


  while ($data = fgetcsv($handle, 4096, ";"))
  {
    $count++;
    $data[0] = stripBOM($data[0]);
    if (!is_numeric(trim($data[0])))
    {
      continue;
    }  // sla lege regels over
    if (trim($data[4]) == "DIV")
    {
      continue;
    }  // DIV boekingen overslaan  2008-09-26

    ////////////////////////////////////////////

    $record = array();  // reset $record per ingelezen regel

    $portefeuille   = trim($data[0]);
    $rekeninnr      = trim($data[0]);

    $isin           = trim($data[3]);
    $binck          = $data[16];
    $aantal         = makeNumber($data[9]);
    $valuta         = trim($data[2]);

    if (trim($data[15]) == "")  // cash
    {
      if (trim($data[2]) == "PNC")
      {
        $valuta         = "GBP";
        $aantal         = makeNumber($data[9])/100;
      }

      $record["isPositie"]    = false;
      $record["portefeuille"] = $rekeninnr;
      $record["valuta"]       = $valuta;
      $record["bedrag"]       = $aantal;
    }
    else
    {

      $record["memo"] = $data[17];
      if ($data[4] == "CALL" OR $data[4] == "PUT" OR $data[4] == "FUT")  // opties
      {
        //debug($data);
        if ($data[4] == "FUT")
        {
          $split = explode(" ", $data[17]);

          $end = count($split);
          $binck = $split[0] . " %" . $split[$end - 2] . " " . $split[$end - 1];
          $isin = $binck;
        }
        else
        {
          $binck = trim($data[17]);
          $split = explode(" ", $data[17]);

          $end = count($split);

          $bParts = explode(".", $split[$end-1]);
          if ($bParts[1] == 0)
          {
            $bedrag = $bParts[0];
          }
          else
          {
            $bedrag = $split[$end-1];
          }
//          $binck = $split[0] . " %" . $split[$end - 4] . " " . $split[$end - 3] . substr($split[$end - 2],-2) . " ".$bedrag;
          $isin = "isOptie|".$split[0] . " %" . $split[$end - 4] . " " . $split[$end - 3] . substr($split[$end - 2],-2) . " ".$bedrag;
//          debug($split);
//          debug($data[17], $isin);

        }

      }

      $record["bankCode"] = $binck;
      $record["isPositie"] = true;
      $record["portefeuille"] = $portefeuille;
      $record["aantal"] = $aantal;
      $record["ISIN"] = $isin;


      $record["PE"] = "";
      $record["valuta"] = (trim($data[7]) == "PNC")?"GBP":trim($data[7]);
      $record["koers"] = makeNumber($data[8]);
//      debug($record);
    }
    $recon->addToBankPile($record);
  }

  unlink($filename);
  return $count;
}

function binck_validateFile($filename)
{
  global $error, $filetype;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 2048, ";");
  $data[0] = stripBOM($data[0]);

  $validateStr1 = is_numeric($data[0]);     // portefeuille veld is numeriek
  $validateStr2 = (substr($data[1],0,2) );  // datumveld begin met 20
  $validateStr3 = (strlen($data[2]) >= 3);  // valutaveld gevuld

  if ( $validateStr1 AND $validateStr2 AND $validateStr3)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen Binck bestand";
  }



  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

