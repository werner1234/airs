<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/09/20 06:15:59 $
 		File Versie					: $Revision: 1.1 $

 		$Log: bil_validate.php,v $
 		Revision 1.1  2017/09/20 06:15:59  cvs
 		megaupdate
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***
 		





*/

function validateCvsFile($filename)
{
  global $error, $csvRegels,$prb,$rekeningAddArray;
	$error = array();
  $DB = new DB();


  $query = "SELECT BILcode,doActie FROM bilTransactieCodes";
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactieMapping[] = $row["BILcode"];
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
  while ($data = fgetcsv($handle, 4096, ","))
  {
    $row++;
    if ($row == 1 )
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
    {
      $error[] = "$row :te weinig velden ";
    }

//
// check transactie code bestaat
//
    $_code = trim($data[3]);
    if (!in_array($_code, $_transactieMapping))
    {
      $error[] = "$row :onbekende transactiecode ($_code)";
    }

//
// check bestaat rekeningnummer
//


    $_rekNr = trim($data[13]) . trim($data[14]);
    if (getRekening($_rekNr))
    {
      $VB = $rekening["Vermogensbeheerder"];
    }
    else
    {
      $error[] = "$row :Rekeningnummer komt niet voor ($_rekNr icm depotbank)";
      //addToRekeningAdd(trim($data[13]),trim($data[14]));
    }


//
// check of ISIN code voorkomt in fondsen tabel
//
    $bankCodeNotFound = true;
    if ($data[4] <> "")
    {
      $fonds = array();
      $query = "SELECT * FROM Fondsen WHERE BILcode = '" . $data[4] . "' ";
      if ($fonds = $DB->lookupRecordByQuery($query))
      {
        $bankCodeNotFound = false;
      }
    }
    if ($bankCodeNotFound)
    {
      $query = "SELECT * FROM Fondsen WHERE ISINCode = '" . $data[23] . "' AND Valuta = '" . $data[12] . "'";
      if (!$fonds = $DB->lookupRecordByQuery($query))
      {
        $error[] = "$row :BILcode en ISIN komt niet voor fonds tabel (" . $BILCode . "/" . $data[23] . $data[12] . ")";
      }
    }

  }



  $_SESSION["VB"] = $VB;
  
//  if (count($rekeningAddArray) > 0)
//  {
//    $_SESSION["rekeningAddArray"] = $rekeningAddArray;
//  }
//  else
//  {
//    $_SESSION["rekeningAddArray"] = array();
//  }
  
  fclose($handle);
  if (Count($error) == 0)
  	return true;
  else
  {
  	return false;
  }

}


?>