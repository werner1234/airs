<?php
/*
    AE-ICT sourcemodule created 30 apr. 2021
    Author              : Chris van Santen
    Filename            : airsRM_validate.php


*/

function validateCvsFile($filename)
{

	global $stopImport, $data,$error, $csvRegels,$prb,$kolomIndeling, $row, $set;
  global $transactieCodes;
	$error      = array();
  $pro_step   = 0;
  $db         = new DB();

	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
//  debug($set);
  while ($data = fgetcsv($handle, 4096, $set["fileDelimit"]))
  {
    $row++;
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    if ($data[0] == "")
    {
      continue;  // lege regels overslaan
    }



    if ($row == 1 )  // headerchecks
    {
//      debug($data);
      $failed = false;
      for ($x=0; $x < count($data); $x++)
      {
        if ($data[$x] != $kolomIndeling[$x])
        {
          $failed = true;
          break;
        }
      }

      if ($failed)
      {
        $error[] = "Bestandsindeling onjuist ";
        $stopImport = true;
        break;
      }
      continue;
    }

//    debug($data);

//
// check bestaat rekeningnummer
//
    if (!getRekening($data[10]))
    {
      $error[] = "{$row} :Rekeningnummer komt niet voor ({$data[10]}) ";
    }

//
// bestaat fonds
//
    if ($data[16] != "" AND $data[15] != "")
    {
      getFonds($data[16], $data[15]);
    }

  }
  

  
  fclose($handle);
	return (Count($error) == 0);

}


