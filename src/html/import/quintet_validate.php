<?php

/*

    Author              : Lennart Poot
    Filename            : quintet_validate.php


*/

///////////////////////////////////////////////////////////////////////////////
///
/// TEMPLATE file voor bankimport, dit bestand niet aanpassen
/// maar opslaan als html/import/{fileprefix}_validate.php
///
///////////////////////////////////////////////////////////////////////////////


function validateCvsFile($filename)
{

	global $data,$error, $csvRegels,$prb,$rekeningAddArray, $row, $set;
  global $transactieCodes;
	$error      = array();
  $pro_step   = 0;
  $db         = new DB();
  getTransactieMapping();

	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;


  while ($data = fgetcsv($handle, 4096, $set["fileDelimit"]))
  {
    $row++;

    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    if ($data[0] == "")
    {
      continue;  // lege regels overslaan
    }
    array_unshift($data,"leeg");
    mapDataFields();

    if ($row == 1 AND $set["headerRow"])  // headerchecks
    {

      if(!($data[1] == "Filename" and
         $data[2] == "Referencenumber" and
         $data[3] == "Portfolionumber"))
      {
        $error[] = "Bestandsindeling onjuist ";
        break;
      }
      if (count($data) < 10)
      {
        $error[] = "$row :te weinig velden ";
        break;
      }
      continue;
    }

// check transactie code bestaat
//
    $_code = trim($data["transactieCode"]);
    if (!in_array($_code, $transactieCodes))
    {
      $error[] = "{$row} :onbekende transactiecode ({$_code})";
    }

    if (strtoupper(trim($data["storno"])) == "Y")
    {
      $error[] = "$row :is STORNO/correctie regel --> overgeslagen";
      continue;
    }


    // EPC Subscription	Saldo regels worden overgeslagen:
    // EPC	Subscription	Saldo Ins 14705.88200 OAK CONST TELES
    if (strtoupper(trim($data["transactieCode"])) == "EPC-SUBSCRIPTION" AND
        substr(strtoupper(trim($data["omschrijving"])), 0, 5) == "SALDO"
    )
    {
      $error[] = "$row :is EPC-Subscription Saldo regel --> overgeslagen";
      continue;
    }

//
// check bestaat rekeningnummer
//
    if (!getRekening())
    {
      $error[] = "{$row} :Rekeningnummer komt niet voor ({$data["rekening"]}{$data["afrekenValuta"]} icm depotbank) ";
    }

//
// bestaat fonds
//
    if ($data["isin"] != "" AND $data["bankCode"] != "")
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
	return (Count($error) == 0);

}
