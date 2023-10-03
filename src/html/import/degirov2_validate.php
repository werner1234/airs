<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/02/05 14:54:56 $
 		File Versie					: $Revision: 1.6 $

 		$Log: degirov2_validate.php,v $
 		Revision 1.6  2020/02/05 14:54:56  cvs
 		call 8397
 		
 		Revision 1.5  2019/09/18 10:31:09  cvs
 		call 8103
 		
 		Revision 1.4  2019/09/18 09:39:31  cvs
 		call 8103
 		
 		Revision 1.3  2019/03/04 13:14:02  cvs
 		call 7243
 		
 		Revision 1.2  2018/10/17 15:38:19  cvs
 		call 7243
 		
 		Revision 1.1  2018/10/15 15:11:01  cvs
 		call 7243
 		

*/

function validateCvsFile($file)
{

	global $error, $csvRegels,$prb,$row,$memRekening,$rekeningAddArray, $transactieMapping;
  $DB = new DB();
  $row = -1;
  $handle = fopen($file, "r");
  $csvRegels = Count(file($file));

  $pro_multiplier = (100/$csvRegels);
  $_tfile = explode("/",$file);
  $_file = $_tfile[count($_tfile)-1];
  $skipped = "";
  while ($data = fgetcsv($handle, 4096, ";"))
  {
    $row++;
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
    if ($row == 0)   // header check
    {

      $test1 = ($data[0] == "account");
      $test2 = ($data[1] == "id");
      $test3 = ($data[2] == "cashId");

      if (!$test1 OR !$test2 OR !$test3)
      {
        return false;
      }
      continue;
    }

    if ($data[0] == "account"  AND $data[1] == "id")
    {
      continue;  // header regels in samengevoegde bestanden
    }

    $data = array_reverse($data);
    $data[] = "leeg";
    $data = array_reverse($data);


    if (!array_key_exists($data[9], $transactieMapping))
    {
      $error[] = "$row : geen transactie mapping voor: {$data[9]}";
    }

    // rekeningcheck
    if ($data[4] != "" AND $data[12] == 0)
    {
      $_rekNr = $data[1] . "MEM";
      $valuta = "MEM";
    }
    else
    {
      $_rekNr = $data[1] . $data[14];
      $valuta = $data[14];
    }
    $_rekNr = substr($_rekNr,2);
    if (!getRekening($_rekNr))
    {
      $error[] = "$row :Rekeningnummer komt niet voor ($_rekNr icm depotbank)";
      addToRekeningAdd(substr($data[1],2), $valuta);
    }

    // fondscheck
    if ($data[9] != "CA3046" AND $data[9] != "CA3026")
    {
      if ($data[4] != "" OR $data[22] != "")
      {
        $fndVal = ($data[30] == "GBX")?"GBP":$data[30];
        if (!giroCheckFonds($data[4], $data[22], $data[30]))
        {
          $error[] = "$row :Fondscode komt niet voor fonds tabel (" . $data[4] . " / " . $data[22] . " icm " .$fndVal. ")";
        }

        if ($VB == "")
        {
          if ($pRec = $DB->lookupRecordByQuery("SELECT `Vermogensbeheerder` FROM `Portefeuilles` WHERE `Portefeuille` = '" . $data[1] . "' "))
          {
            $VB = $pRec["Vermogensbeheerder"];
          }
        }

        $_SESSION["VB"] = $VB;
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


  }

  fclose($handle);
  $rawFileArray = file($file);
  $_foutFile    = array();
  foreach ($error as $item)
  {
    $ind = explode(":", $item);
    $r = $ind[0]-1;
    $_foutFile[] = $rawFileArray[$r];
  }

  $_SESSION["importFoutFile"] = implode("", $_foutFile);

  unset($_foutFile);

  return true;
}

