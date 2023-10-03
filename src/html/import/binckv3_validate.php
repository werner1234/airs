<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/01/14 11:56:19 $
 		File Versie					: $Revision: 1.14 $

 		$Log: binckv3_validate.php,v $
 		Revision 1.14  2020/01/14 11:56:19  cvs
 		call 6223
 		
 		Revision 1.13  2019/05/03 10:42:32  cvs
 		call 7770
 		
 		Revision 1.12  2018/10/02 10:21:52  cvs
 		call 7202
 		
 		Revision 1.11  2018/09/28 06:09:58  cvs
 		call 7193
 		
 		Revision 1.10  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/06/20 06:58:55  cvs
 		call 6734
 		
 		Revision 1.8  2018/06/18 09:05:03  cvs
 		call 6734
 		
 		Revision 1.7  2017/09/29 12:18:21  cvs
 		call 6223
 		
 		Revision 1.6  2017/09/20 06:16:18  cvs
 		megaupdate
 		
 		Revision 1.5  2015/05/06 09:42:14  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2014/12/16 07:30:30  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/07/10 06:53:14  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/02/08 09:04:33  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2013/10/10 14:13:04  cvs
 		*** empty log message ***



*/
function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb, $VB, $rekeningAddArray, $optieArray, $uitkArray, $toekArray;
	$error = array();
	$DB = new DB();


	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  
  $row = 0;
  $VB  = "";
  $subFile = "";
  $subLine = 0;
  $columns = 0;
  while ($data = fgetcsv($handle, 1000, ";"))
  {
    $row++;
    $data = str_replace("\xEF\xBB\xBF", "", $data);  // remove BOM
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

   if (trim($data[0]) == "")
   {
     continue;  // regel overslaan als eerste kolom is leeg
   }

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

/*
** 15 aug 2007 cvs
** hieronder aanpassingen aan de array om de nieuwe structuur van de cvs file aan te passen naar de oude standaard
*/
    $t7 = $data[7];
    $data[7]  = $data[6];
    $data[6]  = $t7;
  $data[19] = $data[18];          // isin code verhuisd naar veld 18
//  $data[32] = $data[31];          // Binckcode verhuisd naar veld 30

  $sf = (trim($data[$fileColumn]) != "")?$data[$fileColumn]:false;
/*
** 15 aug 2007 cvs
** einde aanpassing
*/


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
// check minimaal 20 velden
//
		if (count($data) < 20)
    {
      $error[] = "$row :te weinig velden ";
    }

//
// check veld 4 is nummeriek
//
  	$_code = trim($data[2]);  // == rekeningsoort

  	if (!(is_numeric($_code) AND $_code >= 0))
    {
      $error[] = "$row :veld 4 bevat een ongeldige waarde ($_code)";
    }

//
// check bestaat rekeningnummer
//
    $val = str_replace(" ","_",$data[6]); // vervang spatie door underscore
    $val = str_replace("-","_",$data[6]); // vervang - door underscore
    $_isin = trim($data[19]);

    
    if (  ($val == "D"  AND $_isin <> "")  OR
          ($val == "L"  AND $_isin <> "")     )
    {
       $_rekNr = trim($data[1])."MEM";
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

         addToRekeningAdd($data[1],"MEM");
       }  
       else
       {
         $VB =  $rekening["Vermogensbeheerder"]; 
       }
    }
    else
    {
      $_rekNr = trim($data[1]).trim($data[3]);

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

        addToRekeningAdd($data[1],$data[3]);
      }
      else
      {
        $VB =  $rekening["Vermogensbeheerder"];
      }
    }
    

   // $VB =  $rekening["Vermogensbeheerder"];


// check bestaat stornering melden
//
    $_code = trim(strtolower($data[7]));

    if ($_code == "s")
    {
      if ($sf)
      {
        $error[] = "$row :Deze regel bevat een stornering ($subLine : $sf) ";
      }
      else
      {
        $error[] = "$row :Deze regel bevat een stornering ";
      }
    }


//
// check of ISIN code voorkomt in fondsen tabel
//

    $skipTransArray = array(
      "O-G",
      "O-G1",
      "RTDB",
      "RTCR",
      "TOEK",
      "UITK"


    );

  if ($data[6] == "UITK")
  {

    $uitkArray["portefeuille"][]  = $data[1];
    $uitkArray["isin"][]          = $data[19];
  }

  if ($data[6] == "TOEK")
  {
    $toekArray["isin"][]          = $data[19];
  }

  if (!in_array($data[6], $skipTransArray ))
  {

    if (!$fnd = getFonds($data))
    {
      if (in_array($data[20], $optieArray))
      {
        $codeOms = "optiecode: {$data[31]} / {$data[9]} ";
      }
      else
      {
        $codeOms = "bankcode: {$data[30]}, ISIN {$data[19]} / {$data[9]}";
      }

      if ($sf)
      {
        $error[] = "$row :Fonds komt niet voor in fonds tabel {$codeOms} ($subLine : $sf) ";
      }
      else
      {
        $error[] = "$row :Fonds komt niet voor in fonds tabel  {$codeOms} / {$data[9]}";
      }
    }

//     else
//     {
//       $_binckCode = trim($data[30]);
//       $query = "SELECT * FROM Fondsen WHERE binckCode = '".$_binckCode."' ";
//       $DB->SQL($query);
//       if (!$fonds = $DB->lookupRecord())
//       {
//         if ($sf)
//         {
//           $error[] = "$row :binck code komt niet voor fonds tabel ($_binckCode) ($subLine : $sf) ";
//         }
//         else
//         {
//           $error[] = "$row :binck code komt niet voor fonds tabel ($_binckCode)";
//         }
//       }
//
//     }
  }
//  else
//  {
//    if ($data[19] != "" AND $data[6] == "TOEK")
//    {
//   	   $_isin = trim($data[19]);
//       $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$_isin."' ";
//
//       $DB->SQL($query);
//       if (!$fonds = $DB->lookupRecord())
//       {
//         $error[] = "$row :ISIN komt niet voor fonds tabel ({$_isin})  (TOEK)";
//       }
//
//     }
//  }

  if ($VB == "")
  {
    if ($pRec = $DB->lookupRecordByQuery("SELECT Vermogensbeheerder FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$data[1]."' "))
    {
      $VB = $pRec["Vermogensbeheerder"];
    }
  }

  }

  $_SESSION["uitK"] = $uitkArray;
  $_SESSION["toeK"] = $toekArray;

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
//    debug($rawFileArray[$r]);
  }
  $_SESSION["importFoutFile"] = $_foutFile;
  unset($_foutFile);


  if (Count($error) == 0)
  	return true;
  else
  {
  	return false;
  }

}


?>