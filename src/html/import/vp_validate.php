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
	global $data,$error, $csvRegels,$prb,$rekeningAddArray, $row, $set, $gbMap;
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
  $validRowType = array("fts","exp","zav","sxt","aat");



  while ($data = fgetcsv($handle, 4096, $set["fileDelimit"]))
  {
    $row++;
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    if ($data[0] == "")
    {
      continue;  // lege regels overslaan
    }
    $fileType = checkRowType($data, $row);
    array_unshift($data,"leeg");
    mapDataFields($fileType);

    if (!in_array($fileType, $validRowType))
    {
      continue;
    }


    // storno controle
    //

    if ($data["storno"] == 1)
    {
      $error[] = "{$row} :Stornoboeking, overgeslagen in {$fileType}";
      continue;
    }

    // check transactie code bestaat
//
    $_code = trim($data["transactieCode"]);
    if (!in_array($_code, $transactieCodes))
    {

      if (!array_key_exists($_code, $gbMap))
      {
        $error[] = "{$row} :onbekende transactiecode ({$_code}) in {$fileType}";
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


