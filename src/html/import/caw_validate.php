<?php
/*
    AE-ICT sourcemodule created 01 nov. 2019
    Author              : Chris van Santen
    Filename            : caw_validate.php

*/

function validateCvsFile($filename)
{

	global $data,$error, $csvRegels,$prb,$rekeningAddArray, $row;

	$error = array();
  $DB = new DB();
  $query = "SELECT bankCode,doActie FROM cawTransactieCodes";
  $_transactiecodes = array();
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactiecodes[] = $row["bankCode"];
  }
  
	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  while ($data = fgetcsv($handle, 4096, ";"))
  {
    $row++;
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    if(count($data) < 10)
    {
      continue; // skip lege regels
    }

// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
    $data = array_reverse($data);
    $data[] = "leeg";
    $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

    if ($row == 1)
    {
      if ($data[1] <> "PortfolioNumber")
      {
        $error[] = "Bestandsindeling onjuist ";
      }
      if (count($data) < 10)
      {
        $error[] = "$row :te weinig velden ";
      }
      continue;

    }

//
// check transactie code bestaat
//
    $_code = trim($data[4]);
    if (!in_array($_code, $_transactiecodes))
    {
      $error[] = "$row :onbekende transactiecode ($_code)";
    }


    if (strtolower($data[67]) == "true")
    {
      $error[] = "$row :storno overgeslagen ($_code)";
    }


// check bestaat rekeningnummer
//
    $data["rekening"]      = $data[1];
    $data["afrekenValuta"] = $data[9];
    if (!getRekening())
    {
      $error[] = "$row :Rekeningnummer komt niet voor ({$data["rekening"]}{$data["afrekenValuta"]} icm depotbank) ";
    }

// bestaat fonds
//
//

  $data["fondsValuta"]  = $data[64];
  $data["isin"]  = $data[3];

  if (strtolower(substr($data["isin"],0,4)) == "isin")
  {
    $data["isin"]         = substr($data[3],5);
  }

  if (strtolower(substr($data["isin"],0,8)) == "telekurs")
  {
    $data["bankCode"] = substr($data[3], 9);
  }


  if ($data["isin"] != "" OR $data["bankCode"] != "")
  {
    getFonds();
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
  $rawFileArray = file($filename);
  $_foutFile    = array();
  // neem de header over uit het originele bestand
  $_foutFile[]  = $rawFileArray[0];

  foreach ($error as $item)
  {
    $ind = explode(":", $item);
    $r = $ind[0]-1;
    $_foutFile[] = $rawFileArray[$r];
  }
  $_SESSION["importFoutFile"] = implode("",$_foutFile);
  unset($_foutFile);

  if (Count($error) == 0)
  	return true;
  else
  {
  	//return true;
  	return false;
  }

}


