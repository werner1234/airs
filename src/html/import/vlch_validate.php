<?php
/*
    AE-ICT sourcemodule created 01 nov. 2019
    Author              : Chris van Santen
    Filename            : caw_validate.php

21-10 naar RVV
*/

function validateCvsFile($filename)
{

	global $data,$error, $csvRegels,$prb,$rekeningAddArray, $row;

	$error = array();
  $DB = new DB();
  $query = "SELECT bankCode,doActie FROM vlchTransactieCodes";
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
  while ($data = fgetcsv($handle, 4096, ","))
  {
    $row++;
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);



    mapVlChData();
    if ($row == 1)  // header check
    {
      if ($data[1] != "Transaction Nr" OR $data[3] != "Dealing Date")
      {
        $error[] = "Bestandsindeling onjuist ";
      }
      continue;
    }


    $_code = trim($data["transactieCode"]);
    if (!in_array($_code, $_transactiecodes))
    {
      $error[] = "$row :onbekende transactiecode ($_code)";
    }


// check bestaat rekeningnummer
//
    if (!getRekening())
    {
      $error[] = "$row :Rekeningnummer komt niet voor ({$data["rekening"]}{$data["afrekenValuta"]} icm depotbank) ";
    }

// bestaat fonds
//
//

  if ($data["isin"] != "" OR $data["bankCode"] != "")
  {
    getFonds();
  }


  }
  
//  if (count($rekeningAddArray) > 0)
//  {
//    $_SESSION["rekeningAddArray"] = $rekeningAddArray;
//  }
//  else
//  {
//    $_SESSION["rekeningAddArray"] = array();
//  }
  
  fclose($handle);
  if (Count($error) == 0)
  	return true;
  else
  {
  	//return true;
  	return false;
  }

}


