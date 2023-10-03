<?php
/*
    AE-ICT sourcemodule created 21 apr. 2021
    Author              : Chris van Santen
    Filename            : _template_validate.php


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
  //aetodo:moet nog gemaakt worden
  return true; // moet nog gemaakt worden
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
      if ($data[1] != "PortfolioNumber")
      {
        $error[] = "Bestandsindeling onjuist ";
      }
      if (count($data) < 10)
      {
        $error[] = "$row :te weinig velden ";
      }
      continue;

    }

// check transactie code bestaat
//
    $_code = trim($data["transactieCode"]);
    if (!in_array($_code, $transactieCodes))
    {
      $error[] = "{$row} :onbekende transactiecode ({$_code})";
      continue;
    }

    if ($data["storno"] == "C" or (int)$data["storno"] > 0)
    {
      $error[]       = "$row :is STORNO/correctie regel --> overgeslagen";
      $stornoTrans[] = $data["gestorneerdId"];
      continue; 
    }


//
// check bestaat rekeningnummer
//
    if (!getRekening())
    {
      $error[] = "{$row} :Rekeningnummer komt niet voor ({$data["rekening"]}{$data["afrekenValuta"]} icm depotbank) ";
      continue;
    }

//
// bestaat fonds
//
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
	return (Count($error) == 0);

}


