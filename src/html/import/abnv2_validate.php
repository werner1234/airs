<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/10/30 14:37:26 $
 		File Versie					: $Revision: 1.8 $

 		$Log: abnv2_validate.php,v $
 		Revision 1.8  2019/10/30 14:37:26  cvs
 		call 8217
 		
 		Revision 1.7  2019/08/19 14:25:02  cvs
 		no message
 		
 		Revision 1.6  2019/04/29 14:00:43  cvs
 		call 7746
 		
 		Revision 1.5  2019/04/12 11:56:19  cvs
 		call 7047
 		
 		Revision 1.4  2019/04/12 11:52:59  cvs
 		call 7047
 		
 		Revision 1.3  2019/04/03 15:11:22  cvs
 		call 7047
 		
 		Revision 1.2  2019/03/22 12:32:54  cvs
 		call 7047
 		
 		Revision 1.1  2018/11/23 13:34:06  cvs
 		call 7047
 		

*/

function validateCvsFile($filename)
{

	global $error, $csvRegels,$prb,$rekeningAddArray;

	$error = array();
  $DB = new DB();
  $query = "SELECT bankCode,doActie FROM abnV2TransactieCodes";
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $transactieMapping[] = $row["bankCode"];
    if ($row["doActie"] == "NVT")
    {
      $skipNVT[] = $row["bankCode"];
    }

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
  while ($data = fgetcsv($handle, 8192, ";"))
  {

    $row++;
    if ($row == 1)  // header overslaan
    {
      if ($data[0] != "PortfolioID")
      {
        $error[] = "Headerregel ontbreekt";
        break;
      }
      continue;

    }
    if ($data[0] == "PortfolioID")
    {
      continue;  //header regels overslaan
    }
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);


//// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
//	 $data = array_reverse($data);
//	 $data[] = "leeg";
//	 $data = array_reverse($data);
//// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
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
// check transactie code bestaat.
//

    $_code = trim($data[9]);
    if (!in_array($_code, $transactieMapping) AND $_code != "")
    {
      if ($sf)
      {
        $error[] = "$row :onbekende transactiecode ($_code)  ($subLine : $sf) ";
      }
      else
      {
        $error[] = "$row :onbekende transactiecode ($_code)";
      }

    }

    if (in_array($_code, $skipNVT))  // geen verdere validatie op NVT regels
    {
      continue;
    }

//
// check bestaat rekeningnummer
//

    $chk = trim(strtoupper($data[4]));  // transactie type
    $IsIsin = (trim($data[3]) == "")?false:true;  // is ISIN gevuld


// *****   getRekening($rekeningNr="-1", $depot="AAB")

//    if (  ($chk == "ST"  AND $IsIsin)  OR
//          ($chk == "OP"  AND $IsIsin)     )
//    {
//       $_rekNr = trim($data[1])."MEM";
//       if (getRekening($_rekNr))
//       {
//         $VB =  $rekening["Vermogensbeheerder"];
//       }
//       else
//       {
//         if ($sf)
//         {
//           $error[] = "$row :$_rekNr onbekend  ($subLine : $sf) ";
//         }
//         else
//         {
//           $error[] = "$row :Rekeningnummer komt niet voor ($_rekNr icm depotbank) ";
//         }
//         addToRekeningAdd($data[1],"MEM");
//       }
//    }
//    else
//    {
//
//      if (substr($data[22],0,3) == "34 ")
//      {
//         $_rekNr = trim($data[1])."DEP";
//         if (!getRekening($_rekNr))
//         {
//           if ($sf)
//           {
//             $error[] = "$row :$_rekNr onbekend  ($subLine : $sf) ";
//           }
//           else
//           {
//             $error[] = "$row :Rekeningnummer komt niet voor ($_rekNr icm depotbank) ";
//           }
//         }
//      }
//      else
//      {
//        $_rekNr = trim($data[1]).trim($data[9]);
//        if (getRekening($_rekNr))
//        {
//          $VB =  $rekening["Vermogensbeheerder"];
//        }
//        else
//        {
//          if ($sf)
//          {
//            $error[] = "$row :$_rekNr onbekend  ($subLine : $sf) ";
//          }
//          else
//          {
//            $error[] = "$row :Rekeningnummer komt niet voor ($_rekNr icm depotbank) ";
//          }
//          addToRekeningAdd(trim($data[1]),trim($data[9]));
//        }
//      }
//    }


//
// check of ISIN code voorkomt in fondsen tabel
//

    if (trim($data[1]) != "")
    {

      $f = abnV2_getFonds(trim($data[1]), "", "");
      if ($f != "fondsLoaded")
      {
        $error[] = "$row :$f";
      }

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

    $_SESSION["VB"] = $VB;

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

    return (Count($error) == 0);

}
