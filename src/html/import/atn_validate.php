<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.4 $

 		$Log: atn_validate.php,v $
 		Revision 1.4  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2009/06/02 12:02:28  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008

 		Revision 1.1  2008/07/24 06:22:57  cvs
 		ATN import toevoegen




*/

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb;
	$error = array();
	$DB = new DB();



	$_transactiecodes   = Array("0","1","2","3","5","6","11","19","20");
	$_transactiecodesNY = Array("11");  // deze worden wel herkend maar niet verwerkt

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
  	if (count($data) < 20)
  		$error[] = "$row :te weinig velden ";

//
// check transactie code bestaat
//
  	$_code = trim($data[1]);
  	if (!in_array($_code,$_transactiecodes))
   		$error[] = "$row :ongeldige transactiecode ($_code)";
    if (in_array($_code,$_transactiecodesNY) )
    	$error[] = "$row :transactiecode  wordt niet verwerkt ($_code)";

//
//
// check bestaat rekeningnummer
//


    $_rekNr = trim($data[20])."MEM";
    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
	  $DB->SQL($query);
    if (!$rekening = $DB->lookupRecord())
       $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";

//
// check of ISIN code voorkomt in fondsen tabel
//
    $isinCode = trim($data[13]);
  	if ($isinCode <> "")
    {
      $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$isinCode."' ";
      $DB->SQL($query);
      if ($fonds = $DB->lookupRecord())  $isinNotFound = false;
      if (empty($fonds))
        $error[] = "$row :ISIN code komt niet voor fonds tabel (".$isinCode.")";

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