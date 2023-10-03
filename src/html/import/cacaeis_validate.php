<?php
/*
    AE-ICT sourcemodule created 26 feb. 2021
    Author              : Chris van Santen
    Filename            : cacaeis_validate.php


*/

function validateCvsFile($filename)
{

	global $data,$error, $csvRegels,$prb,$rekeningAddArray, $row, $bankTransactieTable, $rowDelimiter;


	$error = array();
  $DB = new DB();
  $query = "SELECT bankCode,doActie FROM {$bankTransactieTable}";

  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactiecodes[] = $row["bankCode"];
  }
debug($_transactiecodes);
	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  while ($data = fgetcsv($handle, 4096, $rowDelimiter))
  {
    $row++;
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);


// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
    $data = array_reverse($data);
    $data[] = "leeg";
    $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

    if ($row == 1)
    {
      if ($data[1] <> "recordType")
      {
        $error[] = "Bestandsindeling onjuist ";
      }
      if (count($data) < 10)
      {
        $error[] = "$row :te weinig velden ";
      }
      continue;

    }

// uitgezet tbv 8518 (kan later verwijdert)
//    if (trim($data[10]) == "FW-FX")
//    {
//      $error[] = "regel $row: regel overgeslagen FORWARD FX boeking";
//      continue;
//    }
//


// check transactie code bestaat
//
    mapDataFields();
    if ($data["saldoRegel"] AND !$data["stukken"])
    {
      $error[] = "$row :Saldoregel overgeslagen ";
      continue;
    }

    if (!in_array($data["transactieCode"], $_transactiecodes))
    {
      $error[] = "$row :onbekende transactiecode ({$data["transactieCode"]})";
    }

//    if ($data[19] != "N")
//    {
//      $error[] = "$row :is STORNO/correctie regel --> overgeslagen";
//    }

//
//// check bestaat rekeningnummer
////

    if ($data["stukken"])
    {
      if (!getRekening($data["portefeuille"]."MEM"))
      {
        $error[] = "$row :Rekeningnummer komt niet voor ({$data["portefeuille"]}MEM) icm depotbank) ";
      }
    }
    else
    {


      if (!getRekening())
      {
        $error[] = "$row :Rekeningnummer komt niet voor ({$data["rekening"]}{$data["afrekenValuta"]} icm depotbank) ";
      }
    }



//
//// bestaat fonds
////
////
//    $chk = trim(strtoupper($data[11]));  // transactie type
//
    if ($data["stukken"])
    {
      if ($data["isin"] != "" OR $data["bankCode"] != "")
      {
        getFonds();
      }
    }


  }
  
  if (count($rekeningAddArray) > 0)
  {
    $_SESSION["rekeningAddArray"] = $rekeningAddArray;
  }
  else
  {
    $_SESSION["rekeningAddArray"] = array();
  }
  
  fclose($handle);
  if (Count($error) == 0)
  	return true;
  else
  {
  	//return true;
  	return false;
  }

}


?>