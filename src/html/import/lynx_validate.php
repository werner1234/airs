<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/11/24 16:28:10 $
 		File Versie					: $Revision: 1.5 $

 		$Log: lynx_validate.php,v $
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

	global $error, $csvRegels,$prb,$rekeningAddArray;
	$error = array();
  $DB = new DB();
  
  $query = "SELECT LYNXcode FROM lynxTransactieCodes";
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactiecodes[] = $row["LYNXcode"];
  }
  
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


// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

//
// check minimaal 16 velden
//
  	if (count($data) < 16)
  		$error[] = "$row :te weinig velden ";

//
// check transactie code bestaat
//


    $_code = trim($data[1])."-".trim($data[6]);
    if (!in_array($_code,$_transactiecodes))
    {
      $error[] = "$row :onbekende transactiecode ($_code)";
    }



//
// check bestaat rekeningnummer
//

    $rekeningNr = trim($data[2]).trim($data[10]);
    if (!getRekening($rekeningNr))
    {
      $error[] = "$row :onbekend rekeningnummer ($rekeningNr)";
    }

    if ($data[29] == "CASH")
    {
      $rekeningNr = trim($data[2]).trim($data[5]);
      if (!getRekening($rekeningNr))
      {
        $error[] = "$row :onbekend rekeningnummer ($rekeningNr)";
      }
    }



//
// check of ISIN code voorkomt in fondsen tabel
//

    $LYNXcode = "";
    $LynxCodeNotFound = true;
    $fonds = array();
    if (trim($data[3]) == "N/A" AND trim($data[4]) == "N/A" )
    {
        // cashmutatie
    }
    else
    {
      if (trim($data[3]) != "N/A" AND  $data[29] != "CASH")
      {
        $LYNXcode = $data[3];
        $query = "SELECT * FROM Fondsen WHERE LYNXcode = '".trim($LYNXcode)."' ";

        if (!$fonds = $DB->lookupRecordByQuery($query))
        {
          if(trim($data[4]) != "N/A")
          {
            $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$data[4]."' AND Valuta ='".$data[5]."' ";

            if (!$fonds = $DB->lookupRecordByQuery($query))
            {
              $error[] = "$row :Lynx code en ISIN komen niet voor in fondsentabel (".$data[4]."-".$data[5].", LC: ".$LYNXcode.")";
            }
          }
          else
          {
            if ($data[29] != "CASH")
            {
              $error[] = "$row :Lynx-code komt niet voor fonds tabel (Lynx-code: ".$data[3].")";
            }
          }
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
