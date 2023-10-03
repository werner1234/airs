<?php
/*
    AE-ICT sourcemodule created 30 aug. 2022
    Author              : Chris van Santen
    Filename            : saxo_validate.php


*/

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
  $stornoIds = array();
  while ($data = fgetcsv($handle, 4096, $set["fileDelimit"]))
  {
    $row++;
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    if ($data[0] == "")
    {
      continue;  // lege regels overslaan
    }
    if ($data[0] == "CounterpartID")
    {
      continue;
    }

    array_unshift($data,"leeg");
    mapDataFields();

    if ($row == 1 AND $set["headerRow"])  // headerchecks
    {
      if ($data[1] != "CounterpartID")
      {
        $error[] = "Bestandsindeling onjuist (geen header) ";
      }
      if (count($data) < 10)
      {
        $error[] = "$row :te weinig velden ";
      }
      continue;

    }

    if ((int)$data["stornoId"] != 0 )
    {
      $stornoIds[] = $data["stornoId"];
      $stornoArray[$data["stornoId"]][$data["storno"]][] = $data;
      //$error[] = "$row :is STORNO/correctie regel (<b>{$data["stornoId"]}</b>) --> overgeslagen";
      continue;
    }

//    if ($data["transactieCodeDetail"] == 0)
//    {
//      $error[] = "{$row} :missende transactiecode ";
//      continue;
//    }



// check transactie code bestaat
//
//    $_code = trim($data["transactieCode"]);
//    if (!in_array($_code, $transactieCodes))
//    {
//      $error[] = "{$row} :onbekende transactiecode ({$_code})";
//    }



//
// check bestaat rekeningnummer
//
//    getRekening();


//
// bestaat fonds
//
    getFonds();

  }

  if (count($stornoIds) > 0) // reloop to exclude storno related rows
  {
    fclose($handle);
    $handle = @fopen($filename, "r");
    $row    = 0;
    while ($data = fgetcsv($handle, 4096, $set["fileDelimit"]))
    {
      $row++;
      array_unshift($data,"leeg");
      mapDataFields();
      if (in_array($data[23],$stornoIds))
      {
        $stornoArray[$data[23]][$data["storno"]][] = $data;
        //$error[] = "$row :is STORNO gerelateerde regel --> overgeslagen";
        continue;
      }
    }
  }
//debug($stornoArray);

  foreach ($stornoArray as $transId => $set)
  {
    $idx = array();
    foreach ($set as $k=>$v)
    {
      $idx[] = strtolower($k);
    }
    $done = false;

    // optie 1 //
    if (!in_array("rebook", $idx) AND
         in_array("", $idx)       AND
         (in_array("reverse", $idx) OR in_array("cancel", $idx)))
    {
      foreach($set as $state=>$dat)
      {

        foreach ($dat as $item)
        {
          switch (strtolower($state))
          {
            case "reverse":
            case "cancel":
              $error[] = "{$item["row"]} :Storno in bestand, overgeslagen (transId: {$item["transactieId"]})";
              break;
            default:
              $error[] = "{$item["row"]} :Gestorneerd in bestand, overgeslagen (transId: {$item["transactieId"]})";
              break;
          }
        }


      }
      $done = true;
    }


    // optie 2 //
    if (!in_array("rebook", $idx) AND
        !in_array("", $idx)       AND
        (in_array("reverse", $idx) OR in_array("cancel", $idx)))
    {

      foreach($set as $state=>$dat)
      {

        foreach ($dat as $item)
        {
          switch (strtolower($state))
          {
            case "reverse":
            case "cancel":
              $error[] = "{$item["row"]} :Storno overgeslagen,<span style='color:red'>verwijder oorspronkelijk transactie (org. transId: {$transId})</span>";
              break;
            default:
              $error[] = "{$item["row"]} : (transId: {$item["transactieId"]})";
              break;
          }
        }
      }

        $done = true;
    }

    // optie 3 //
    if (in_array("rebook", $idx) AND
        in_array("", $idx)       AND
        (in_array("reverse", $idx) OR in_array("cancel", $idx)))
    {
      foreach($set as $state=>$dat)
      {
        foreach ($dat as $item)
        {
          switch (strtolower($state))
          {
            case "rebook":
              // deze regel boeken
              break;
            case "reverse":
            case "cancel":
              $error[] = "{$item["row"]} :Storno in bestand, overgeslagen (transId: {$item["transactieId"]})";
              break;
            default:
              $error[] = "{$item["row"]} :Gestorneerd in bestand, overgeslagen (transId: {$item["transactieId"]})";
              break;
          }
        }

      }

      $done = true;
    }

    // optie 4 //
    if (in_array("rebook", $idx) AND
        !in_array("", $idx)       AND
        (in_array("reverse", $idx) OR in_array("cancel", $idx)))
    {

      $cancelSet   = array();
      $rebookSet   = array();
      $matchFields = array(
        "portefeuille",
        "afrekenValuta",
        "bankCode",
        "boekdatum",
        "valutakoers",
        "koers",
      );
      foreach($set as $state=>$dat)
      {
        $setRaw = array();

        foreach ($dat as $item)
        {
          $setRaw["portefeuille"]   = $item["portefeuille"];
          $setRaw["afrekenValuta"]  = $item["afrekenValuta"];
          $setRaw["bankCode"]       = $item["bankCode"];
          $setRaw["boekdatum"]      = $item["boekdatum"];
          $setRaw["aantal"]        += $item["aantal"];
          $setRaw["nettoBedrag"]   += $item["nettoBedrag"];
          $setRaw["nettoControle"] += $item["nettoControle"];
          $setRaw["valutakoers"]   += $item["valutakoers"];
          $setRaw["koers"]         += $item["koers"];
          if (strtolower($state) == "cancel")
          {
            $cancelSet = $setRaw;
          }
          else
          {
            $rebookSet = $setRaw;
          }
        }

      }
//      debug($cancelSet, "CancelSet");
//      debug($rebookSet, "ReboolSet");
      $match = true;
      foreach ($cancelSet as $fld => $value)
      {
         if (in_array($fld, $matchFields))
         {
           if ($cancelSet[$fld] != $rebookSet[$fld])     { $match = false; }
         }
         else
         {
           if ($cancelSet[$fld] + $rebookSet[$fld] != 0) { $match = false; }
         }
      }

      foreach($set as $state=>$dat)
      {
        if ($match)
        {
          foreach($dat as $item)
          {
            $error[] = "{$item["row"]} :Storno en Rebook gelijk regel overgeslagen (transId: {$item["transactieId"]})";
          }

        }
        else
        {
          foreach($dat as $item)
          {
            if (strtolower($state) != "rebook")
            {
              $error[] = "{$item["row"]} :Storno overgeslagen,<span style='color:red'>verwijder oorspronkelijk transactie (org. transId: {$transId}), Rebook-regel is geboekt</span>[4]";
            }
          }
        }
      }


      $done = true;
    }

    // overige //
    if (!$done)
    {
//      debug($dat, "overig");
      foreach($set as $state=>$dat)
      {
        foreach ($dat as $item)
        {
          $error[] = "{$item["row"]} :STORNO gerelateerde, overgeslagen (transId: {$item["transactieId"]})[ov]";
        }
      }

    }

  }

  $rawFileArray = file($filename);
  $_foutFile    = array();
  $_foutFile[] = $rawFileArray[0];

  foreach ($error as $item)
  {
    $ind = explode(":", $item);
    $r = $ind[0]-1;
    $_foutFile[] = $rawFileArray[$r];
//    debug($rawFileArray[$r]);
  }
  $_SESSION["importFoutFile"] = $_foutFile;
  unset($_foutFile);

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


