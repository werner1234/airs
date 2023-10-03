<?php
/*
    AE-ICT sourcemodule created 31 mrt. 2021
    Author              : Chris van Santen
    Filename            : caceis_reconFuncties.php


*/

function recon_readBank($filename, $filetype, $skipGetAirs=false)
{
  global $prb, $batch, $recon, $airsOnly,$dubbelPos;
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }
  $csvRegels = Count(file($filename));

  $prb = new ProgressBar();	// create new ProgressBar

  echo "<li>inlezen bankbestand";
  ob_flush();flush();
  $teller = 0;
  $prevRow = "";
  $dubbelPos = 0;
  $delimter = ($filetype == "POS")?";":",";
  while ($data = fgetcsv($handle, 4096, $delimter))
  {

//    debug($data, $filetype);
    $teller++;
    $pro_step += $pro_multiplier;
    //  $prb->moveStep($pro_step);
    //  $prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".count($rawData).' regels');

    //   $prb->moveNext();
    $row = $rawData[$x];
    $record = array("depot" => "kas","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
    if ($filetype == "FND")
    {

      $record["type"]         = "sec";
      $record["portefeuille"] = $data[0];
      $record["datum"]       = convertDate($data[3]);

      $record["aantal"]       = convertNumber($data[15]);
      //$record["aantal"]       = (int)textPart($row,213,233)/1000000;
      $record["ISIN"]         = $data[4];
      $record["bankCode"]     = $data[5];
      $record["fonds"]        = $data[6];
      $record["valuta"]       = $data[11];
      $record["koers"]        = convertNumber($data[10]);

    }
    elseif ($filetype == "POS")  // call 9393 POS bestand
    {

//      debug($data);
//      if ($row == $prevRow)
//      {
//        $dubbelPos++;
//        continue;  // dubbele regels negeren
//      }



      $fnd = optFonds($data);
      $d = explode("/", $data[7]);
      $record["type"]         = "sec";
      $record["portefeuille"] = $data[0];
      $record["datum1"]       = "{$d[2]}-{$d[1]}-{$d[0]}";
      //$record["soort"]        = $data[8]; //todo: wat is soort??
      $record["aantal"]       = convertNumber($data[12]);
      $record["airsCode"]     = $fnd;
      $record["fonds"]        = $fnd;
      $record["bankCode"]     = $fnd;
      $record["fileBankCode"] = $fnd;
      $record["valuta"]       = $data[16];
      $record["koers"]        = convertNumber($data[14]);


      $prevRow = $row;

    }
    else  // GLD
    {
      $record["type"]      = "cash";
      $record["rekening"]  = $data[3];
      $record["datum"]    = convertDateGLD($data[2]);
      $record["valuta"]    = $data[6];

      $record["iban"]      = $data[3];

      $record["bedrag"]    = $data[5];
    }

    $record["batch"] = $batch;

    $output[] = $record;

    $recon->addRecord($record);


  }
  if ($filetype <> "GLD")
  {
    echo "<li>AIRS data ophalen";
    ob_flush();flush();
    if (!$skipGetAirs)
    {
      $recon->fillTableFormAIRS();
      echo "<li>AIRS portefeuilles ophalen ($filetype)";
      ob_flush();flush();
      $airsOnly = $recon->getAirsPortefeuilles();
    }

  }
  else  // GLD
  {
    echo "<li>AIRS rekeningnummers ophalen";
    ob_flush();flush();
    $airsOnly = $recon->getAirsCashRekeningen();
  }

  $recon->fillVB();

  //$prb->hide();
  unlink($filename);
  return $teller;
}

function convertDateGLD($in)
{
  return substr($in,0,4)."-".substr($in,4,2)."-".substr(6,2);
}

function convertDate($in)
{
  $p = explode("/", $in);
  return $p[2]."-".$p[0]."-".$p[1];
}

function convertNumber($in)
{
  return str_replace(",", "", $in);
}

function optFonds($data)
{
  $maanden = array("", "Jan", "Feb", "Mrt", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec");
//  $m =  substr($data[7],4,2);
  $m = $data[35];
  $y = substr($data[6],2,2);

  $timeCode = $maanden[ (int)$m ].$y;
  $price = convertNumber($data[9]);
  $price = (substr($price,-3) == ".00")?(int)$price:$price;

  $prefix = substr($data[3],0,3);
  if ($prefix == "AEO" OR $prefix== "OCO")
  {
    $data[3] = substr($data[3],3);

  }

  $fonds = $data[3]." ".substr($data[8],0,1)." ".$timeCode." ".$price;
  return $fonds;
}

function convertRow($rawData)
{
  $maanden = array("", "Jan", "Feb", "Mrt", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec");
  //$data 7/8/9/10 herleiden tot AIRS fondscode
  // bv AP C jun16 35

  $data = array();
  $data[1]   = textPart($rawData,1,8);    // datum
  $data[2]   = textPart($rawData,9,11);
  $data[3]   = textPart($rawData,12,13);
  $data[4]   = textPart($rawData,14,22);  // portefeuille
  $data[5]   = textPart($rawData,23,28);
  $data[6]   = textPart($rawData,29,30);
  $data[7]   = textPart($rawData,31,36);  // symbool  fonds.fonds eerste deel tot spatie
  $data[8]   = textPart($rawData,37,37);  // call/put
  $data[9]   = textPart($rawData,38,45);  // exp datum
  $data[10]  = textPart($rawData,46,52);  // strike  delen door 100
  $data[11]  = textPart($rawData,53,53);
  $data[12]  = textPart($rawData,54,56);  // valuta
  $data[13]  = textPart($rawData,57,64);
  $data[14]  = textPart($rawData,65,69);  // long
  $data[15]  = textPart($rawData,70,70);
  $data[16]  = textPart($rawData,71,75);
  $data[17]  = textPart($rawData,76,76);
  $data[18]  = textPart($rawData,77,81);  // short
  $data[19]  = textPart($rawData,82,82);
  $data[20]  = textPart($rawData,83,87);
  $data[21]  = textPart($rawData,88,88);
  $data[22]  = textPart($rawData,89,96);  // prijs
  $data[23]  = textPart($rawData,97,97);
  $data[24]  = textPart($rawData,98,105); // settlement koers
  $data[25]  = textPart($rawData,106,106);
  $data[26]  = textPart($rawData,107,114);
  $data[27]  = textPart($rawData,115,121);
  $data[28]  = textPart($rawData,122,127);
  $data[29]  = textPart($rawData,128,128);
  $data[30]  = textPart($rawData,129,133);
  $data[31]  = textPart($rawData,134,141);
  $data[32]  = textPart($rawData,142,142);
  $data[33]  = textPart($rawData,143,150);
  $m =  substr($data[9],4,2);
  $y = substr($data[9],2,2);

  $timeCode = $maanden[ (int)$m ].$y;
  $price = (int)$data[10];
  $price = $price/100;
  $price = strstr($price,".")?number_format($price,2):$price;

  $data["fonds"] = $data[7]." ".$data[8]." ".$timeCode." ".$price;
  $data["aantal"] = $data[14] - $data[18];


  return $data;
}


function validateFile($filename,$filename2)
{
//aetodo: moet nog gemaakt worden
  return true;
  global $error, $filetype;
  $error = array();
  echo "<li>start validatie bestanden";
  ob_flush();flush();

  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $dataRaw = fgets($handle, 4096);
  $validateStr1 = textPart($dataRaw,21,21);

  if ( !$validateStr1 == "A")
  {
    $error[] = "Bestand validatie mislukt, geen Kasbank FND bestand";
    return false;
  }
  fclose($handle);

  if (!$handle = @fopen($filename2, "r"))
  {
    $error[] = "FOUT bestand $filename2 is niet leesbaar";
    return false;
  }

  $dataRaw = fgets($handle, 4096);
  $validateStr2 = textPart($dataRaw,16,16);

  if ( !$validateStr2 == "A")
  {
    $error[] = "Bestand validatie mislukt, geen Kasbank GLD bestand";
    return false;
  }
  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}


?>