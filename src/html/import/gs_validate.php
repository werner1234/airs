<?php
/*
    AE-ICT sourcemodule created 08 nov. 2021
    Author              : Chris van Santen
    Filename            : gs_validate.php


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
  global $transactieCodes, $transactieMapping, $meldArray;
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
    if ($data[0] == "Header" OR $data[0] == "Trailer")
    {
      continue;
    }

    array_unshift($data,"leeg");
    mapDataFields();
//debug($data);
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

//    if (empty($data["transactieCodeA"]) OR empty($data["transactieCodeB"]))
//    {
//      $error[] = "$row :formattering regel onjuist / onvolledige transactiecode";
//      continue;
//    }

// check transactie code bestaat
//
    $_code = trim($data["transactieCode"]);
    if (!in_array($_code, $transactieCodes))
    {
      $error[] = "{$row} :onbekende transactiecode ({$_code})";
    }

    if ($data["storno"] == "C")
    {
      $error[] = "$row :is STORNO/correctie regel --> overgeslagen";
    }

    // zet het regel nr
    $data["regelnr"] = $row;
//
// check bestaat rekeningnummer
//
    getRekening();


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


