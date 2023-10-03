<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/11/29 13:14:50 $
 		File Versie					: $Revision: 1.1 $

 		$Log: rabobank_reconV3Functies.php,v $
 		Revision 1.1  2019/11/29 13:14:50  cvs
 		call 7937
 		

 		
*/


function rabo_recon_readBank($filename,$useISIN=false,$skipAirsRekening=false)
{
  global $prb, $batch, $recon, $airsOnly;
  
  
  $db = new DB();
  
  if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
    
  $pro_multiplier = 100/$csvRegels;
  $teller   = 0;

  while ($data = fgetcsv($handle, 4096, "|"))
  {
    $data[0] = stripBOM($data[0]);
    $teller++;
    if ($data[0] == null)  // skip lege regels
    {
      continue;
    }

    $record = array();

    $recType = (stristr($data[0],"CASHPOS"))?"cash":"sec";

    if ($recType == "sec")
    {
     
      $db = new DB();
      if ($data[5] <> "")
      {
        $q = "SELECT * FROM Fondsen WHERE raboCode='$data[5]' ";
        if (!$fondsRec = $db->lookupRecordByQuery($q))
        {
          $fondsRec = null;
        }
      }

      $record["isPositie"]    = true;
      $record["portefeuille"] = $data[1];
      $record["datum1"]       = "";
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantal"]       = $data[6];
      $record["ISIN"]         = "";
      $record["bankCode"]     = $data[4];
      $record["fonds"]        = $fondsRec["Fonds"];
      $record["PE"]           = "";
      $record["valuta"]       = $data[9];
      //$record["koersRaw"]     = $data[11]/$data[8];
      //$record["koers"]        = $data[11]/$data[8];

    }
    else
    {
      $rek = explode("-", $data[1]);
      $record["isPositie"]    = false;
      $record["portefeuille"] = $rek[1];
      $record["valuta"]       = $data[4];
      $record["bedrag"]       = $data[3];
      
    }

    $recon->addToBankPile($record);

  }

  unlink($filename);
  return $teller;
}

function rabo_validateFile($filename)
{

  global $error;
  $error = array();
  echo "<li>start validatie bestanden";
  ob_flush();flush();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }
  $csvRegels = Count(file($filename));

  while ($data = fgetcsv($handle, 4096, "|"))
  {
    $data[0] = stripBOM($data[0]);

    if (!strstr($data[0], "SECURITYPOS") AND !strstr($data[0], "CASHPOS"))
    {
        $error[] = "Bestand validatie mislukt, geen Rabo recon bestand";
        return false;
    }
  }  
  fclose($handle);

  return (Count($error) == 0);
}

//function convertFixedLine($rawData,$debug=false)
//{
//
//  $data[1] = textPart($rawData,1,15);
//  if ($data[1] == "SECURITYPOS")
//  {
//    $data[3] = textPart($rawData,21,55);                // portefeuille
//    $data[5] = ontnullen(textPart($rawData,60,79));     // Fondscode
//    $data[7] = textPart($rawData,84,101);               // aantal stukken
//    $data[7] = str_replace(",",".",$data[7]);
//    $data[8] = textPart($rawData,422,433);               // ISIN
//    $data[9] = textPart($rawData,109,112);               // valuta
//  }
//  else
//  {
//    $data[5]  = textPart($rawData,60,77);               // saldo
//    $data[5] = str_replace(",",".",$data[5]);
//    $data[6]  = textPart($rawData,78,80);               // valuta
//    $data[11] = textPart($rawData,21,29);               // RekeneningNr
//    $data[20] = textPart($rawData,196,213);               // aantal stukken
//    $data[20] = str_replace(",",".",$data[20]);
//    $data[21] = textPart($rawData,214,231);               // aantal stukken
//    $data[21] = str_replace(",",".",$data[21]);
//
//    $data[5] = $data[5] + $data[21] - $data[20];
//
//  }
//  if ($debug)
//    listarray($data);
//  return $data;
//}

function getRekeningNr($port,$valuta)
{
  $DB = new DB();
  $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '$port' AND Memoriaal = 0 AND Valuta='$valuta'";
  $DB->SQL($query);
  $record = $DB->lookupRecord();
  return $record["Rekening"];

}

?>