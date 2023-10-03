<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/05/09 11:38:20 $
 		File Versie					: $Revision: 1.1 $

 		$Log: optimix_validate.php,v $
 		Revision 1.1  2018/05/09 11:38:20  cvs
 		call 6878
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***
 		





*/

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb,$rekeningAddArray, $getFondsArray;


	$error = array();
  $DB = new DB();
  
  $query = "SELECT OPTcode FROM optTransactieCodes";
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactiecodes[] = $row["OPTcode"];
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
    if ($row == 1)
    {
      if ($data[0] != "IP Counter" OR $data[1] != "Portfolio ID")
      {
        echo "ongeldig Optimix bestand";
        exit;
      }
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
  	if (!in_array($_code,$_transactiecodes))
    {
      $error[] = "$row :onbekende transactiecode ($_code)";
    }

    $_rekNr = trim($data[2])."EUR";
    if (getRekening($_rekNr))
    {
      $VB =  $rekening["Vermogensbeheerder"];
    }
    else
    {
      $error[] = "$row :Rekeningnummer komt niet voor ($_rekNr icm depotbank)";
    //         addToRekeningAdd($data[1],"EUR");
    }


    if (!getFonds($data[5],$data[10]) AND in_array($data[3], $getFondsArray) AND $data[5] != "")
    {
       $error[] = "$row :Fonds bestaat niet ".$data[5]."/".$data[10];
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
  {
    return true;
  }
  else
  {
  	return false;
  }

}
