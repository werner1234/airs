<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/04 11:10:14 $
 		File Versie					: $Revision: 1.5 $

 		$Log: kbc_validate.php,v $
 		Revision 1.5  2020/05/04 11:10:14  cvs
 		call 7598
 		
 		Revision 1.4  2020/04/01 07:29:15  cvs
 		call 7598
 		
 		Revision 1.3  2020/02/05 11:49:10  cvs
 		call 7598
 		
 		Revision 1.2  2020/01/15 14:34:45  cvs
 		call 7598
 		
 		Revision 1.1  2019/10/04 07:44:49  cvs
 		call 7598
 		
 		Revision 1.5  2018/10/04 06:14:14  cvs
 		no message
 		
 		Revision 1.4  2018/10/03 15:28:49  cvs
 		no message
 		
 		Revision 1.3  2018/06/20 06:57:48  cvs
 		call 6734
 		
 		Revision 1.2  2018/06/18 09:04:24  cvs
 		call 6734
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***
 		





*/

function validateCvsFile($filename)
{

  global $error, $csvRegels,$prb, $skipTransactieCodeArray, $transactieMapping;
//return true;

	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;


  $fileType = "";
  $dateFields = array(
    "CPNS" => array(8,9),
    "TRNS_BS" => array(24,26),
    "FMVT" => array(3,4)
  );
  while ($data = fgetcsv($handle, 4096, ","))
  {
    $row++;

    // BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
    $data = array_reverse($data);
    $data[] = "leeg";
    $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

    if (kbcCheckDate($data[8]) AND kbcCheckDate($data[9]))
    {
      $fileType = "CPNS";

    }
    elseif (kbcCheckDate($data[24]) AND kbcCheckDate($data[26]) )
    {
      $fileType = "TRNS";
    }
    elseif (kbcCheckDate($data[4]) AND kbcCheckDate($data[5]))
    {
      $fileType = "FMVT";
    }
    else
    {
      $fileType = "xx";
    }


    switch ($fileType)
    {
      case "CPNS";

        $bankCode = "xxx";
        $ISIN = $data[5];
        $valuta = $data[12];
        if (!$rec = kbcGetFonds($bankCode ,$ISIN ,$valuta))
        {
          $error[] = "$row: fonds niet gevonden bankcode: {$bankCode}, ISIN: {$ISIN}/{$valuta} ";
        }
        $rekening = $data[1].$data[19];
        if (!$rec = getRekening($rekening))
        {
          $error[] = "$row: rekening niet gevonden voor {$rekening} ";
        }

        if ($data[3] == "Y")
        {
          $error[] = "$row: storno overgeslagen ";
        }

        break;
      case "TRNS";
        $bankCode = $data[11];
        $ISIN = $data[9];
        $valuta = $data[13];

        if (!$rec = kbcGetFonds($bankCode, $ISIN, $valuta))
        {
          $error[] = "$row: fonds niet gevonden bankcode: {$bankCode}, ISIN: {$ISIN}/{$valuta} ";
        }
        $rekening = $data[1].$data[30];
        if (!$rec = getRekening($rekening))
        {
          $error[] = "$row: rekening niet gevonden voor {$rekening} ";
        }
        if ($data[6] == "Y")
        {
          $error[] = "$row: storno overgeslagen (oorspronkelijk transId {$data[7]}-{$data[8]})";
        }
        break;
      case "FMVT";
        if (substr($data[2],0,3) == "404")
        {
          $error[] = "$row: 404 mutatie voor {$rekening} overgeslagen";
          continue;
        }
        $rekening = $data[1].$data[8];
        if (!getRekening($rekening))
        {
          $error[] = "$row: rekening niet gevonden voor {$rekening} ";
        }
        break;
      default:

    }

  }

  
  fclose($handle);

  $rawFileArray = file($filename);
  $_foutFile    = array();
  foreach ($error as $item)
  {
    $ind = explode(":", $item);
    $r = $ind[0]-1;
    $_foutFile[] = $rawFileArray[$r];
  }

  $_SESSION["importFoutFile"] = implode("", $_foutFile);
  unset($_foutFile);

  if (Count($error) == 0)
  	return true;
  else
  {
  	return false;
  }

}
