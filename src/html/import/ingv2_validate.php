<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/11/20 13:41:09 $
 		File Versie					: $Revision: 1.2 $

 		$Log: ingv2_validate.php,v $
 		Revision 1.2  2019/11/20 13:41:09  cvs
 		call 8236
 		
 		Revision 1.1  2018/05/07 08:32:38  cvs
 		call 6620
 		
 		Revision 1.3  2017/04/03 12:14:31  cvs
 		call 5174
 		
 		Revision 1.2  2016/07/01 14:36:48  cvs
 		call 5005
 		
 		Revision 1.1  2016/03/25 10:41:08  cvs
 		call 3691
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***
 		





*/

function validateCvsFile($filename)
{

	global $data,$error, $csvRegels,$prb,$rekeningAddArray, $row;
	$error = array();
  $DB = new DB();
  
  $query = "SELECT INGcode FROM ingTransactieCodes";
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactiecodes[] = $row["INGcode"];
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
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);


// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
    $data = array_reverse($data);
    $data[] = "leeg";
    $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

    if ($row == 1)
    {
      if ($data[1] <> "portfolioID")
      {
        $error[] = "Bestandsindeling onjuist ";
      }
      if (count($data) < 10)
      {
        $error[] = "$row :te weinig velden ";
      }
      continue;

    }

//
// check transactie code bestaat
//
    $_code = trim($data[11]);
    if (!in_array($_code, $_transactiecodes))
    {
      $error[] = "$row :onbekende transactiecode ($_code)";
    }

    if ($data[10] == "J")
    {
      $error[] = "$row :is STORNO/correctie regel --> overgeslagen";
    }


// check bestaat rekeningnummer
//
    if (!getRekening())
    {
      $error[] = "$row :rekening ".trim($data[1]).trim($data[18])." bestaat niet";
    }


// bestaat fonds
//
//
    $chk = trim(strtoupper($data[11]));  // transactie type

  if ($data[6] != "CASH")
  {
    getFonds();
  }


  }
  
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
  	//return true;
  	return false;
  }

}


?>