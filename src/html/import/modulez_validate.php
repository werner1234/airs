<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/06/18 06:56:32 $
 		File Versie					: $Revision: 1.1 $

 		$Log: modulez_validate.php,v $
 		Revision 1.1  2018/06/18 06:56:32  cvs
 		update naar VRY omgeving
 		
 		Revision 1.5  2017/11/24 16:28:10  cvs
 		call 6224
 		
 		Revision 1.4  2017/11/15 09:28:46  cvs
 		aanpassing fonds zoeken
 		
 		Revision 1.3  2017/10/25 13:59:18  cvs
 		call 6224 Lynx import
 		
 		Revision 1.2  2017/10/20 10:15:10  cvs
 		call 6224
 		
 		Revision 1.1  2017/09/29 12:15:48  cvs
 		call 6224
 		


*/

function validateCvsFile($filename)
{


	global $error, $csvRegels,$prb,$rekeningAddArray,$i,$transactieMapping;
	debug($transactieMapping);
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
  while ($data = fgetcsv($handle, 8192, ","))
  {
    trimRecord($data);
    $row++;
    if ($row == 1)
    {
      continue;
    }
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

//
// check minimaal 16 velden
//
  	if (count($data) < 15)
  		$error[] = "$row :te weinig velden ";

//
// check transactie code bestaat
//


    $_code = trim($data[$i["transaction_type"]]);
    if (!array_key_exists($_code,$transactieMapping))
    {
      $error[] = "$row :onbekende transactiecode ($_code)";
    }



//
// check bestaat rekeningnummer
//

    $rekeningNr = trim($data[$i["account_number"]]).trim($data[$i["currency"]]);
    if (!getRekening($rekeningNr))
    {
      $error[] = "$row :onbekend rekeningnummer ($rekeningNr)";
    }

//    if ($data[29] == "CASH")
//    {
//      $rekeningNr = trim($data[2]).trim($data[5]);
//      if (!getRekening($rekeningNr))
//      {
//        $error[] = "$row :onbekend rekeningnummer ($rekeningNr)";
//      }
//    }



//
// check of ISIN code voorkomt in fondsen tabel
//


    $fonds = array();
    if (trim($data[$i["isin"]]) == "" )
    {
        // cashmutatie
    }
    else
    {
      if (trim($data[$i["isin"]]) != "" )
      {
         $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$data[$i["isin"]]."' AND Valuta ='".$data[$i["instrument_currency"]]."' ";

          if (!$fonds = $DB->lookupRecordByQuery($query))
          {
            $error[] = "$row :ISIN komt niet voor in fondsentabel (".$data[$i["isin"]]."-".$data[$i["instrument_currency"]].")";
          }
      }
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
