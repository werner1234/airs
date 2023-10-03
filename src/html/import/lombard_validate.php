<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/10/16 12:27:15 $
 		File Versie					: $Revision: 1.5 $

 		$Log: lombard_validate.php,v $
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
  
  $query = "SELECT LOMcode FROM lombardTransactieCodes";
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactiecodes[] = $row["LOMcode"];
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
  while ($data = fgetcsv($handle, 2000, "\t"))
  {
    if ($row == 0)
    {
      if ($data[0] == "Accn nb")
      {
        $row++;
        continue;  // goede file, skip header
      }
      else
      {
        $error[] = "$row :header kolom 1 is geen 'Accn nb' ";
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
  		$error[] = "$row :te weinig velden ";

//
// check transactie code bestaat
//
  	$_code = trim($data[9])."-".trim($data[26]);
  	if ( !in_array($_code,$_transactiecodes) AND
         (!in_array($data[4], $accTypeSkipArray))  // deze worden later overgeslagen dus hoeven niet gemeldt
       )
    {
      $error[] = "$row :onbekende transactiecode ($_code)";
    }


//
// check bestaat rekeningnummer
//

    $chk    = trim(strtoupper($data[4]));  // transactie type
    $IsIsin = (trim($data[3]) == "")?false:true;  // is ISIN gevuld



     
    if (trim($data[2]) == "")
    {
      $_rekNr = substr($data[1],0,8)."MEM";
      $val = "MEM";

    }
    else
    {
      $_rekNr = substr($data[1],0,8).$data[2];
      $val = $data[2];
    }

    if ($__appvar["bedrijf"] == "WMP" AND $chk == "MA")
    {
       $_rekNr   = substr($data[1],0,8)."MAR".$data[2];
      $val = $data[2];
    }
    $rek = substr($data[1],0,8);

    if (getRekening($_rekNr))        
    {
      $VB =  $rekening["Vermogensbeheerder"];
    }
    else
    {
      //$mr["Rekening"]   = ($data[4] == "MA")?substr($data[1],0,8)."MAR":substr($data[1],0,8).$data[2];
      if (!in_array($data[4], $accTypeSkipArray))  // deze worden later overgeslagen dus hoeven niet gemeldt)
      {
        //debug($data);
        $error[] = "$row :Rekeningnummer komt niet voor ($_rekNr icm depotbank)";
        addToRekeningAdd($rek,$val);

      }


    }
    


//
// check of ISIN code voorkomt in fondsen tabel
//

    
   $isinValuta = ($data[24] <> "")?$data[24]:$data[25];
   if (!getFonds())
   {
     $error[] = "$row :Fonds niet gevonden (".$data[27].$isinValuta."/".$data[28].")";
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
  	return true;
  else
  {
  	return false;
  }

}


?>