<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/10/17 11:13:23 $
 		File Versie					: $Revision: 1.5 $

 		$Log: jb_validate.php,v $
 		Revision 1.5  2018/10/17 11:13:23  cvs
 		call 7230
 		
 		Revision 1.4  2018/10/17 11:08:31  cvs
 		call 7230
 		
 		Revision 1.3  2018/06/15 07:28:15  cvs
 		call 5912
 		
 		Revision 1.2  2018/05/01 06:13:10  cvs
 		call 5913
 		
 		Revision 1.5  2017/10/16 12:27:15  cvs
 		call 6170
 		
 		Revision 1.4  2017/09/20 06:16:53  cvs
 		call 6115
 		
 		Revision 1.3  2017/02/22 07:40:41  cvs
 		cal 5571
 		
 		Revision 1.2  2016/04/04 14:27:18  cvs
 		no message
 		
 		Revision 1.1  2015/12/01 09:01:53  cvs
 		update 2540, call 4352
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***
 		





*/

function validateCvsFile($filename)
{
  
	global $error, $csvRegels,$prb,$rekeningAddArray, $accTypeSkipArray, $__appvar;



	$error = array();
  $DB = new DB();
  
  $query = "SELECT JBcode FROM jbTransactieCodes";
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactiecodes[] = $row[""];
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
  while ($data = fgetcsv($handle, 4096, ";"))
  {
    if ($row == 0)
    {
      if ($data[0] != "KBEW" AND $data[0] != "DBEW")
      {
        $error[] = "Geen JB bestand ";
        break;
      }
    }
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
    if (count($data) < 16)
    {
      $error[] = "$row :te weinig velden ";
    }



//
// check bestaat rekeningnummer
//

//    $chk = trim(strtoupper($data[4]));  // transactie type
//    $IsIsin = (trim($data[3]) == "")?false:true;  // is ISIN gevuld
//
//
//    $rekeningNr = trim($data[3]) . trim($data[8]);
//    if (!getRekening($rekeningNr))
//    {
//      $error[] = "$row :Rekeningnummer komt niet voor ($rekeningNr icm depotbank)";
//    }
//
//    $rekeningNr = trim($data[3]) . "MEM";
//    if (!getRekening($rekeningNr))
//    {
//      $error[] = "$row :Rekeningnummer komt niet voor ($rekeningNr icm depotbank)";
//    }
//
//// check of ISIN code voorkomt in fondsen tabel
////
    if ($data[1] == "DBEW")
    {

      $BankFondscode = $data[9];
      $isin          = $data[11];
      $valuta        = $data[25];
      if (!JB_getfonds($BankFondscode ,$isin, $valuta))
      {
        $error[] = "$row :Fonds niet gevonden (".$isin.$valuta."/".$BankFondscode.")";
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