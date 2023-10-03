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
  $db         = new DB();
//  getTransactieMapping();

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

    if (count($data) < 2)
    {
      continue;  // lege regels overslaan
    }
    array_unshift($data,"leeg");
    mapDataFields();
    
    if ($row == 1 AND $set["headerRow"])  // headerchecks
    {
      if ($data[1] != "account" and $data[2] != "fonds")
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
//    $_code = trim($data["transactieCode"]);
//    if (!in_array($_code, $transactieCodes))
//    {
//      $error[] = "{$row} :onbekende transactiecode ({$_code})";
//      continue;
//    }
    
// check bestaat rekeningnummer
//
    if (!getRekening()) { continue; }

// bestaat fonds
//
    if ($data["isin"] != "")
    {
      if(!getFonds()) { continue; }
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


