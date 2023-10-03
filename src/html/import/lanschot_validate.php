<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/05/03 10:42:32 $
 		File Versie					: $Revision: 1.6 $

 		$Log: lanschot_validate.php,v $
 		Revision 1.6  2019/05/03 10:42:32  cvs
 		call 7770
 		
 		Revision 1.5  2018/10/04 06:14:14  cvs
 		no message
 		
 		Revision 1.4  2018/10/03 15:28:49  cvs
 		no message
 		
 		Revision 1.3  2018/06/20 06:57:48  cvs
 		call 6734
 		
 		Revision 1.2  2018/06/18 09:04:24  cvs
 		call 6734
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***
 		





*/

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb,$rekeningAddArray;
	$error = array();
  $DB = new DB();
  
  $query = "SELECT FVLCode FROM lanschotTransactieCodes";
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactiecodes[] = $row["FVLCode"];
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
  $subFile = "";
  $subLine = 0;
  $columns = 0;
  while ($data = fgetcsv($handle, 1000, ","))
  {
    $row++;
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);


// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

    if ($columns == 0)
    {
      $columns = count($data);
      $fileColumn = $columns-1;
    }

    $sf = (trim($data[$fileColumn]) != "")?$data[$fileColumn]:false;

    if ($sf)
    {
      if ($subFile != $sf)
      {
        $sf = "file= ".$sf;
        $subFile = $sf;
        $subLine = 1;
      }
      else
      {
        $subLine++;
      }
    }

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
  	$_code = trim($data[4]);
  	if (!in_array($_code,$_transactiecodes))
    {
      if ($sf)
      {
        $error[] = "$row :onbekende transactiecode ($_code) ($subLine : $sf) ";
      }
      else
      {
        $error[] = "$row :onbekende transactiecode ($_code)";
      }
    }


//
// check veld 5 numeriek en >= 0
//
  	$_code = trim($data[5]);
  	if (!(is_numeric($_code) AND $_code >= 0))
    {
      $error[] = "$row :veld 5 bevat een ongeldige waarde ($_code)";
    }

//
// check bestaat rekeningnummer
//

    $chk    = trim(strtoupper($data[4]));  // transactie type
    $IsIsin = (trim($data[3]) == "")?false:true;  // is ISIN gevuld

/* flow samen met theo bepaald, 11-7-2007
*
*  als ( ST met ISIN) of (OP met ISIN) dan
*    controleer de MEM rekening
*  anders
*     als (veld 22 begint met 34)
*        controleer op DEP rekening
*     anders
*        controleer op valuta rekening
*
*/

    if (  ($chk == "ST"  AND $IsIsin)  OR
          ($chk == "OP"  AND $IsIsin)     )
    {
       $_rekNr = trim($data[1])."MEM";
       if (getRekening($_rekNr))
       {
         $VB =  $rekening["Vermogensbeheerder"]; 
       }
       else
       {
         if ($sf)
         {
           $error[] = "$row :$_rekNr onbekend  ($subLine : $sf) ";
         }
         else
         {
           $error[] = "$row :Rekeningnummer komt niet voor ($_rekNr icm depotbank) ";
         }
         addToRekeningAdd($data[1],"MEM");
       }

    }
    else
    {

      if (substr($data[22],0,3) == "34 ")
      {
         $_rekNr = trim($data[1])."DEP";
         if (!getRekening($_rekNr))
         {
           if ($sf)
           {
             $error[] = "$row :$_rekNr onbekend  ($subLine : $sf) ";
           }
           else
           {
             $error[] = "$row :Rekeningnummer komt niet voor ($_rekNr icm depotbank) ";
           }
         }  
      }
      else
      {
        $_rekNr = trim($data[1]).trim($data[9]);
        if (getRekening($_rekNr))        
        {
          $VB =  $rekening["Vermogensbeheerder"]; 
        }
        else
        {
          if ($sf)
          {
            $error[] = "$row :$_rekNr onbekend  ($subLine : $sf) ";
          }
          else
          {
            $error[] = "$row :Rekeningnummer komt niet voor ($_rekNr icm depotbank) ";
          }
          addToRekeningAdd(trim($data[1]),trim($data[9]));
        }
      }
    }


//
// check of ISIN code voorkomt in fondsen tabel
//

   $FVLNotFound = true;
   
   if ($data[22] <> "" AND $data[3] <> "" AND trim($data[4]) <> "VM")
   {
     
     $fonds = array();

     $FVLCode = substr("00000000".trim($data[22]),-7);
     $query = "SELECT * FROM Fondsen WHERE FVLCode = '".$FVLCode."' ";
     $DB->SQL($query);
     if (!$fonds = $DB->lookupRecord())  
     {
       if ($sf)
       {
         $error[] = "$row :Lanschot code komt niet voor fonds tabel (" . $FVLCode . ") ($subLine : $sf) ";
       }
       else
       {
         $error[] = "$row :Lanschot code komt niet voor fonds tabel (" . $FVLCode . ")";
       }
   
     }
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

  $rawFileArray = file($filename);
  $_foutFile    = array();
  foreach ($error as $item)
  {
    $ind = explode(":", $item);
    $r = $ind[0]-1;
    $_foutFile[] = $rawFileArray[$r];
  }

  $_SESSION["importFoutFile"] = implode("", $_foutFile);
  unset($_foutFile);

  if (Count($error) == 0)
  	return true;
  else
  {
  	return false;
  }

}


?>