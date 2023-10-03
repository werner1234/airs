<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/13 14:53:11 $
 		File Versie					: $Revision: 1.7 $

 		$Log: hhb_validate.php,v $
 		Revision 1.7  2020/07/13 14:53:11  cvs
 		call 8518
 		
 		Revision 1.6  2020/03/04 09:35:15  cvs
 		call 8025
 		
 		Revision 1.5  2020/01/24 11:25:47  cvs
 		call 8025
 		
 		Revision 1.4  2019/12/09 10:16:14  cvs
 		call 8025
 		
 		Revision 1.3  2019/11/20 15:51:21  cvs
 		call 8025
 		
 		Revision 1.2  2019/10/09 12:45:43  cvs
 		call 8025
 		
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
//aetodo: moet nog af gemaakt worden
return;
	$error = array();
  $DB = new DB();
  $query = "SELECT bankCode,doActie FROM sarTransactieCodes";

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
  while ($data = fgetcsv($handle, 4096, "|"))
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
      if ($data[1] <> "recordType")
      {
        $error[] = "Bestandsindeling onjuist ";
      }
      if (count($data) < 10)
      {
        $error[] = "$row :te weinig velden ";
      }
      continue;

    }

// uitgezet tbv 8518 (kan later verwijdert)
//    if (trim($data[10]) == "FW-FX")
//    {
//      $error[] = "regel $row: regel overgeslagen FORWARD FX boeking";
//      continue;
//    }
//


// check transactie code bestaat
//
//    $_code = trim($data[20]);
//    if (!in_array($_code, $_transactiecodes))
//    {
//      $error[] = "$row :onbekende transactiecode ($_code)";
//    }
//
//    if ($data[19] != "N")
//    {
//      $error[] = "$row :is STORNO/correctie regel --> overgeslagen";
//    }
//
//
//// check bestaat rekeningnummer
////
//    $data["rekening"]      = $data[39];
//    $data["afrekenValuta"] = $data[29];
//    if (!getRekening())
//    {
//      $error[] = "$row :Rekeningnummer komt niet voor ({$data["rekening"]}{$data["afrekenValuta"]} icm depotbank) ";
//    }
//
//// bestaat fonds
////
////
//    $chk = trim(strtoupper($data[11]));  // transactie type
//
//  $data["fondsValuta"]  = $data[16];
//  $data["isin"]         = $data[7];
//  if ($data[4] == "HHB_ID" AND $data[5] != "" )
//  {
//    $data["bankCode"]     = $data[5];
//  }
//
//
//  if ($data["isin"] != "" OR $data["bankCode"] != "")
//  {
//    getFonds();
//  }


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