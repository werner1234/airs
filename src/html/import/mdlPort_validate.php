<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.3 $

 		$Log: mdlPort_validate.php,v $
 		Revision 1.3  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/07/18 12:25:09  cvs
 		update 20160718
 		
 		Revision 1.1  2016/06/24 08:13:55  cvs
 		validatie ingebouwd
 		

*/

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb,$rekeningAddArray;
	$error = array();
	$db = new DB();

	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
  
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  while ($data = fgetcsv($handle, 1000, ";"))
  {
    $row++;
    if ($row == 1 ) continue; //header overslaan
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);


// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
    $data = array_reverse($data);
    $data[] = "leeg";
    $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
    if (count($data) < 6)
    {
      $error[] = "Foute bestandsindeling";
      return;
    }
//
// check bestaat rekeningnummer
//
//debug($data,$row);
    $rekRec = false;
    if ($data[2] == "MEM")
    {
      $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Memoriaal = 1 AND Portefeuille = '" . $data[1] . "' ";
      if (!$rekRec = $db->lookupRecordByQuery($query))
      {
        $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Memoriaal = 1 AND Inactief = 0 AND Rekening = '{$data[1]}MEM'";
        $rekRec = $db->lookupRecordByQuery($query);
      }
    }
    else
    {
      $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Memoriaal = 0 AND Portefeuille = '" . $data[1] . "' AND Valuta = '" . $data[2] . "'";
      $rekRec = $db->lookupRecordByQuery($query);
    }

    if (!$rekRec )
    {
      $error[] = "$row :Rekeningnummer komt niet voor portefeuille " . $data[1] . " (" . $data[2] . ")";
    }
    else
    {
      $VB = $rekening["Vermogensbeheerder"];
    }

    $query = "SELECT * FROM Fondsen WHERE ISINCode = '" . $data[3] . "' AND Valuta = '" . $data[4] . "'";
    if (!$fondsRec = $db->lookupRecordByQuery($query))
    {
      $error[] = "$row :ISIN code komt niet voor fonds tabel (" . $data[3] . "/" . $data[4] . ")";
    }


  }


  $_SESSION["VB"] = $VB;
  
  if (count($rekeningAddArray) > 0)
  {
    $_SESSION["rekeningAddArray"] = $rekeningAddArray;
  }
  else
  {
    $_SESSION["rekeningAddArray"] = array();
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