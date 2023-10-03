<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/08 13:38:28 $
 		File Versie					: $Revision: 1.2 $

 		$Log: ubslux_validate.php,v $
 		Revision 1.2  2020/07/08 13:38:28  cvs
 		call 7606

naar RVV 20201113

*/
//
// check bestaat $portefeuille
//

foreach ($transactieCodes as $k => $v)
{
  if ($v == "D" OR $v == "LO" OR $v == "L" OR $v == "DS")
    $memRekening[] = $k;
}



function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb,$row,$data;

  $DB = new DB();

  $DB->executeQuery("SELECT * FROM ubsluxTransactieCodes ORDER BY bankCode");

  $transactieCodes  = array();
  $_transactieArray = array();

  $content = array();

  while ($codeRec = $DB->nextRecord())
  {
    $transactieCodes[$codeRec["bankCode"]] = $codeRec["doActie"];
  }

	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		debug("FOUT bestand $filename is niet leesbaar");
		return false;
	}
  

  $handle = @fopen($filename, "r");
	$csvRegels = Count(file($filename));

	$prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $prb->hide();
  $row = 0;

  while ($rowInput = fgetcsv($handle, 4096, ";"))
  {
    $fileType = "";
    $rowInput = trimFields($rowInput);
    $row++;

    if ($rowInput[0] == 'acc. no.' OR
      $rowInput[0] == 'Product Type' OR
      $rowInput[0] == 'event type' OR
      $rowInput[0] == 'Acc. Nr.' OR
      $rowInput[0] == 'Account Nr.')
    {
      continue;
    }
    array_unshift($rowInput, "leeg");


    if (!$fileType = checkRowType($rowInput, $row))
    {
      continue;
    }
//debug($rowInput);
    $fonds = array();
    $data = array("row" => $row);
    $skipRest = false;
    switch ($fileType)
    {
      case "cashmov":
//      debug($rowInput);
        break;
      case "sectrans3":
      case "sectrans2":
      case "sectrans":
        $data["rekening"]       = (int)$rowInput[2];
        $data["rekValuta"]      = $rowInput[17];

        $data["isin"]           = $rowInput[9];
        $data["fondsValuta"]    = $rowInput[18];
        if (!$fonds = getFonds())
        {
          //$error[] = "$row :fonds niet gevonden: ".$data["isin"]."/".$data["fondsValuta"];
        }
        if (!ubslCheckRekening($data["rekening"], $data["rekValuta"]))
        {
          $error[] = "$row :rekening niet gevonden:".$data["rekening"].$data["rekValuta"];
        }
        break;
      case "fxtrans":
        $data["rekening"] = (int)$rowInput[1];

        if ($rowInput[11] == "EUR")
        {
          $data["rekValutaEUR"] = $rowInput[11];
          $data["rekValutaVV"]  = $rowInput[13];
        }
        else
        {
          $data["valutaEUR"]    = $rowInput[13];
          $data["valutaVV"]     = $rowInput[11];
        }


        if ($data["valutaEUR"]  == $data["valutaVV"])
        {
          $meldArray[] = "$row: FX boeking overgeslagen: 2x zelfde valuta";
          $skipRest =true;;
          continue;
        }

        if ($data["valutaEUR"] != "EUR" AND $data["valutaVV"] != "EUR")
        {
          $meldArray[] = "$row: FX boeking overgeslagen: geen EUR rekening";
          $skipRest =true;;
          continue;
        }
        if (!ubslCheckRekening($data["rekening"], $data["valutaEUR"]))
        {
          $error[] = "$row :rekening niet gevonden:".$data["rekening"].$data["valutaEUR"];
        }
        if (!ubslCheckRekening($data["rekening"], $data["valutaVV"]))
        {
          $error[] = "$row :rekening niet gevonden:".$data["rekening"].$data["valutaVV"];
        }
        $data["transactiecode"] = "fx";
        break;
      default:
        $data["transactiecode"] = $fileType;
        $meldArray[] = "{$row}: {$fileType} niet ingeregeld";
        $skipRest =true;

    }

    if ($skipRest)
    {
      continue;
    }



    $tc = $transactieCodes[$data["transactiecode"]];
//  debug($data, "tc=".$tc);
    if ($tc != "")
    {
      $do_func = "do_" . $tc;

      if (function_exists($do_func))
      {
//        call_user_func($do_func);
      }
      else
      {
        $skipped .= "- regel {$data["row"]} functie $do_func bestaat niet <br>";
      }
    }
    else
    {
      $skipped .= "- regel {$data["row"]} onbekende transactiecode {$rowInput["transactiecode"]}<br>";
    }

    //debug($rowInput,$fileType);
    continue;
    if ($row == 1)  // skip header regel
    {
      continue;
    }

    if ($rowInput[0] == null)  // skip lege regels
    {
      continue;
    }


  }

  fclose($handle);
  if (Count($error) == 0)
  	return true;
  else
  {
  	return false;
  }

}


?>