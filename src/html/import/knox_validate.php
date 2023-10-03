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

	global $data,$error, $csvRegels,$prb,$rekeningAddArray, $row, $set, $stornoTrans;
  global $transactieCodes;
	$error        = array();
  $stonoTrans   = array();
  $pro_step     = 0;
  $transReeks   = array();
  $db           = new DB();
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


  while ($data = fgetcsv($handle, 4096, $set["fileDelimit"], $set["fileEnclosure"]))
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

    $transReeks[$data["transactieId"]] = $row;

    if ($row == 1 AND $set["headerRow"])  // headerchecks
    {
      if ($data[1] != "ReferenceNumber")
      {
        $or[] = "Bestandsindeling onjuist ";
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
    }

    if ($data["storno"] == "Y")
    {
      $error[]      = "$row :is STORNO, gestorneerd transactie ID = {$data["gestorneerdId"]}--> overgeslagen";
      $stornoTrans[] = $data["gestorneerdId"];
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
    if ($data["isin"] != "" OR $data["bankCode"] != "")
    {
      getFonds();
    }

  }

  // voeg storno regelnummers toe en sorteer direct error reeks
  array_unique($stornoTrans);
  foreach($stornoTrans as $storeTransId)
  {
    $stornoRegel = $transReeks[$storeTransId];
    for($ei=0;$ei<=count($error);$ei++)
    {
      $foutRegel   = $error[$ei];
      $parts       = explode(":", $foutRegel);
      $foutRegelNr = (int)$parts[0];

      if($stornoRegel<=$foutRegelNr or $ei==count($error))
      {
        array_splice($error, $ei, 0, "$stornoRegel :is STORNO, gestorneerd transactie ID = {$storeTransId}--> overgeslagen");
        break;
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
	return (Count($error) == 0);

}


