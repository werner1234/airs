<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/04/08 07:39:50 $
 		File Versie					: $Revision: 1.4 $

 		$Log: hsbc_validate.php,v $
 		Revision 1.4  2020/04/08 07:39:50  cvs
 		call 6991
 		
 		Revision 1.3  2019/03/06 14:14:55  cvs
 		call 6991
 		
 		Revision 1.2  2019/01/23 13:23:30  cvs
 		call 6991
 		
 		Revision 1.1  2018/11/23 13:34:45  cvs
 		call 6991
 		
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
  
  $query = "SELECT HSBCcode,doActie FROM hsbcTransactieCodes";
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactiecodes[] = $row["HSBCcode"];
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
    $row++;

    if ($row > 3)
    {
      break;
    }
    if ($row == 1)
    {
      if (!strstr($data[0], "HSBCDE"))
      {
        echo "ongeldig HSBC bestand";
        exit;
      }
      continue;
    }
    if ($row == 2)
    {
      if (strstr($data[0], "Bestand") OR strstr($data[0], "Saldo") )
      {
        echo "ongeldig HSBC bestand (= positiebestand)";
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


//
// check transactie code bestaat
//

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
  $rawFileArray = file($filename);
  $_foutFile    = array();
  foreach ($error as $item)
  {
    $ind = explode(":", $item);
    $r = $ind[0]-1;
    $_foutFile[] = $rawFileArray[$r];
  }
  $_SESSION["importFoutFile"] = implode("",$_foutFile);
  unset($_foutFile);

  if (Count($error) == 0)
  {
    return true;
  }
  else
  {
  	return false;
  }

}
