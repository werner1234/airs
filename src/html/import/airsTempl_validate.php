<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/07 07:57:03 $
 		File Versie					: $Revision: 1.2 $

 		$Log: airsTempl_validate.php,v $
 		Revision 1.2  2020/07/07 07:57:03  cvs
 		call 8728
 		


*/

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb, $data, $row;
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
  while ($data = fgetcsv($handle, 4096, ";"))
  {
    $row++;
    if ($data[0] == "Rekening") continue;
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);


// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

   //debug($data);
   
//
// check minimaal 16 velden
//
  	if (count($data) < 10)
  		$error[] = "$row :te weinig velden ";



    $data["rekening"]               = $data[1];
    $data["isin"]                   = $data[3];
    $data["valuta"]                 = $data[4];
    $data["fonds"]                  = $data[5];

// check bestaat rekeningnummer
//
    if (!getRekening())
    {
      $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ({$data["rekening"]})";
    }


// check bestaat fonds
//

   	if ( "fonds" != "" OR $data["isin"] )
    {
      getFonds();
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

