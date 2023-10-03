<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/04/29 11:14:09 $
 		File Versie					: $Revision: 1.3 $

 		$Log: jblux_validate.php,v $
 		Revision 1.3  2020/04/29 11:14:09  cvs
 		call 7829
 		
 		Revision 1.2  2020/02/24 15:26:51  cvs
 		call 7829
 		
 		Revision 1.1  2019/08/23 12:28:56  cvs
 		call 7829
 		


*/

function validateCvsFile($filename)
{
  
	global $error, $csvRegels,$prb,$rekeningAddArray, $accTypeSkipArray, $__appvar;

//return true;
	$error = array();
  $DB = new DB();
  
  $query = "SELECT bankCode FROM jbluxTransactieCodes";
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactiecodes[] = $row["bankCode"];
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
  while ($data = fgetcsv($handle, 8192, ";"))
  {
    $row++;
    if ($row == 1)
    {
      if ($data[0] != "INPUT_DATE")
      {
        $error[] = "Geen JBLUX bestand ";
        break;
      }
      if (count($data) < 16)
      {
        $error[] = "$row :te weinig velden ";
        break;
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

  if (!in_array($data[22], $_transactiecodes))
  {
    $error[] = "$row :transactie code onbekend ({$data[22]}) ";
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


