<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/25 13:12:59 $
 		File Versie					: $Revision: 1.15 $

 		$Log: rabo_validate.php,v $
 		Revision 1.15  2020/05/25 13:12:59  cvs
 		call 8431/8208
 		
 		Revision 1.14  2020/04/06 08:43:07  cvs
 		call 8208
 		
 		Revision 1.13  2019/11/29 13:53:08  cvs
 		call 8208
 		
 		Revision 1.12  2019/11/20 15:30:50  cvs
 		call 8208
 		
 		Revision 1.11  2019/09/23 09:30:37  cvs
 		call 7649
 		
 		Revision 1.10  2019/06/19 11:50:41  cvs
 		call 7649
 		
 		Revision 1.6  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/09/21 08:30:05  cvs
 		call 5200
 		
 		Revision 1.4  2015/12/01 09:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2013/01/30 10:17:18  cvs
 		check op depotbank SNS
 		getrekening via port + mem methode
 		
 		Revision 1.2  2011/03/04 07:15:18  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2010/06/09 15:20:23  cvs
 		*** empty log message ***

 		Revision 1.2  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008

 		Revision 1.1  2008/05/27 15:21:07  cvs
 		*** empty log message ***

 		Revision 1.5  2007/08/15 07:14:42  cvs
 		omzetten naar nieuwe indeling van CSV bestand

 		Revision 1.4  2005/09/21 07:53:48  cvs
 		nieuwe commit 21-9-2005



*/
//
// check bestaat $portefeuille
//

foreach ($transactieCodes as $k => $v)
{
  if ($v == "D" OR $v == "LO" OR $v == "L" OR $v == "DS")
    $memRekening[] = $k;
}



function validateCvsFile($filename, $soort)
{
	global $error, $csvRegels,$prb,$row,$memRekening;


  $DB = new DB();

  $DB->executeQuery("SELECT * FROM raboTransactieCodes ORDER BY bankCode");

  $transactieCodes  = array();
  $_transactieArray = array();

  $content = array();

  while ($codeRec = $DB->nextRecord())
  {

    $tc = explode("_",$codeRec["bankCode"]);

    $transactieCodes[$tc[0]][$tc[1]] = $codeRec["doActie"];
    $_transactieArray[$tc[0]][$tc[1]] = $codeRec["bankCode"];
  }

	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		debug("FOUT bestand $filename is niet leesbaar");
		return false;
	}
  
  $data = fgets($handle, 4096);
//debug($data);
  $chkStr = strtoupper(substr($data,0,9));
  if ($chkStr <> "CASHTRANS" AND $chkStr <> "SECURITYT" AND $chkStr <> "TRANSACTI")
  {
    $error[] = "geen geldig Rabo bestand";
    return true;
  }


  
  $handle = @fopen($filename, "r");
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $prb->hide();
  $row = 0;

  while ($data = fgetcsv($handle, 4096, "|"))
  {

    if ($data[0] == null )  // skip lege regels
    {
      continue;
    }
    array_unshift($data, "leeg");

    $row++;
    if ($data[12] == "0002")
    {
      $error[] = "$row :Storno overgeslagen";
      continue;
    }

    $tcParts = explode("-",$data[2]);
    $transCode = $tcParts[0]."-".$tcParts[1]."-".$tcParts[2];

    switch (strtoupper(substr($data[1],0,9)))
    {
      case "CASHTRANS":
        $recType = "geld";
        if (!raboCheckRekening($data[3].$data[16]))
        {
          $error[] = "$transCode :Rekening ".$data[3].$data[16]." onbekend";
        }

        if (!array_key_exists($data[8], $transactieCodes["C"]))
        {
        $error[] = "$transCode :Transactiecode (C)  ".$data[8]." onbekend";
        }
        break;
      case "SECURITYT":
        $recType = "stukken";
        if (!array_key_exists($data[7], $transactieCodes["S"]))
        {
          $error[] = "$transCode :Transactiecode (S)  ".$data[7]." onbekend";
        }
        if (!raboCheckFonds($data[19]))
        {
          $error[] = "$transCode :Fondscode ".$data[19]." onbekend";
        }
        break;
      case "TRANSACTI":
        $recType = "kosten";
        if (!array_key_exists($data[9], $transactieCodes["T"]))
        {
        $error[] = "$transCode :Transactiecode (T)  ".$data[9]." onbekend";
        }
        break;
      default:
        $error[] = "$row : <b> Eerste kolom bevat ongeldig ID trans/sec/cash) </b>";
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