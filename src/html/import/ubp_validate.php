<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/08 12:21:48 $
 		File Versie					: $Revision: 1.3 $

 		$Log: ubp_validate.php,v $
 		Revision 1.3  2020/06/08 12:21:48  cvs
 		call 8668
 		
 		Revision 1.2  2017/03/09 08:02:40  cvs
 		call 5639
 		
 		Revision 1.1  2016/12/05 12:46:35  cvs
 		call 5294
 		



*/

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb,$rekeningAddArray,$fondsLookupResults;
	$error = array();

  $DB = new DB();

	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  while ($data = fgetcsv($handle, 8192, ";"))
  {
    $data = trimRecord($data);
    $row++;
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
    if ($row == 1)
    {
      if ($data[0] != "COMMON_NBR_CO")
      {
        $error[] = "Geen geldige UBP bestand";
      }
      continue;
    }

    if (count($data) < 16)
    {
      $error[] = "$row :te weinig velden ";
    }
    $data = array_reverse($data);
    $data[] = "leeg";
    $data = array_reverse($data);

// check bestaat rekeningnummer
//
//    $port = (trim($data[3]) <> "")?trim($data[3]):trim($data[14]);
//
//    $val = trim($data[48]);
//    if (!getRekening($port, $val))
//    {
//      $error[] = $row . ": rekeningnr " . $port . "/" . $val . " bestaat niet";
//    }
//    if (!UBP_getfonds($data))
//    {
//      $error[] = $row . ": geen Fonds gevonden ";
//    }



//    UBP_getfondsOld($data);
//
//    if ( !$fondsLookupResults["noCodes"] AND $fondsLookupResults["notFound"])
//    {
//      $error[] = $row.": geen Fonds bij ".$fondsLookupResults["fonds"]."/".$fondsLookupResults["valutra"];
//    }


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