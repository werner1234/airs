<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/05/03 10:42:32 $
 		File Versie					: $Revision: 1.28 $

 		$Log: stroeve_validate.php,v $
 		Revision 1.28  2019/05/03 10:42:32  cvs
 		call 7770
 		
 		Revision 1.27  2018/10/04 06:14:14  cvs
 		no message
 		
 		Revision 1.26  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2018/07/17 12:27:17  cvs
 		call 6734
 		
 		Revision 1.24  2018/06/20 06:58:25  cvs
 		call 6734
 		
 		Revision 1.23  2018/06/18 09:05:25  cvs
 		call 6734
 		
 		Revision 1.22  2017/09/20 06:17:33  cvs
 		megaupdate 2722
 		
 		Revision 1.21  2015/05/06 09:42:35  cvs
 		*** empty log message ***

 		Revision 1.20  2014/12/16 07:30:30  cvs
 		*** empty log message ***

 		Revision 1.19  2014/07/08 12:43:24  cvs
 		*** empty log message ***

 		Revision 1.18  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008

 		Revision 1.17  2008/05/29 15:31:19  cvs
 		diverse tweaks op aanwijzing van Theo

 		Revision 1.16  2008/04/23 10:56:33  cvs
 		diverse tweaks op aanwijzing van Theo

 		Revision 1.15  2008/01/18 07:36:36  cvs
 		Stroevecode uit veld 22 ipv van veld 21 halen

 		Revision 1.14  2008/01/18 07:32:21  cvs
 		Dep vergelijking aangepast van "34" naar "34 "

 		Revision 1.13  2007/12/07 10:14:05  cvs
 		diverse kleine aanpassingen transactie import

 		Revision 1.12  2007/07/11 07:33:51  cvs
 		ST-isin en OP-isin controle op bankrekening aangepast

 		Revision 1.11  2007/06/27 08:28:42  cvs
 		aanpassen rekeningcheck

 		Revision 1.10  2007/06/20 14:45:14  cvs
 		- aanpassingen ST en OP opgelopen rente
 		- dep rekeningcheck in validate

 		Revision 1.9  2006/06/13 07:25:43  cvs
 		portefeuille check eruit gehaald

 		Revision 1.8  2005/11/07 10:32:05  cvs
 		*** empty log message ***

 		Revision 1.7  2005/11/07 08:09:00  cvs
 		isincode validatie als veld 3 <> ""

 		Revision 1.6  2005/09/21 07:53:48  cvs
 		nieuwe commit 21-9-2005




*/

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb,$rekeningAddArray;
	$error = array();
	$DB = new DB();



	$_transactiecodes = Array("A","V","OA","OV","SA","SV","TS",
	                          "TL","E","R","L","DV","DO","DT",
	                          "RM","KO","KD","DU","OU","ST","OP","VM");


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
        $error[] = "$row :ongeldige transactiecode ($_code) ($subLine : $sf) ";
      }
      else
      {
        $error[] = "$row :ongeldige transactiecode ($_code)";
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


    if ($VB == "")
    {
      if ($pRec = $DB->lookupRecordByQuery("SELECT Vermogensbeheerder FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$data[1]."' "))
      {
        $VB = $pRec["Vermogensbeheerder"];
      }
    }

//
// check of ISIN code voorkomt in fondsen tabel
//
/*
  	if ($data[3] )
    {
      $isinNotFound = true;
      $fonds = array();
      if (stristr($data[3],"ISIN"))
      {
   	    $_isin = explode(":",$data[3]);
   	    $isinCode = $_isin[1];
      }
   	  else
   	    $isinCode = $data[3];

   	  if (trim($isinCode) <> "" )
      {
        $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$isinCode."' ";
        $DB->SQL($query);
        if ($fonds = $DB->lookupRecord())  $isinNotFound = false;
      }

      if ($isinNotFound)  // als isincode geen waarde dan stroeve lookup forceren
      {
         $_stroeveCode = substr("00000000".trim($data[22]),-7);
         $query = "SELECT * FROM Fondsen WHERE stroeveCode = '".$_stroeveCode."' ";

         $DB->SQL($query);
         $fonds = $DB->lookupRecord();
         if (empty($fonds))
           $error[] = "$row :ISIN en Stroeve code komt niet voor fonds tabel (".$isinCode."/".$_stroeveCode.")";

      }

    }
    */
   $StroeveNotFound = true;

   if ($data[22] <> "" AND $data[3] <> "" AND trim($data[4]) <> "VM")
   {
     
     $fonds = array();

     $_stroeveCode = substr("00000000".trim($data[22]),-7);
     $query = "SELECT * FROM Fondsen WHERE stroeveCode = '".$_stroeveCode."' ";
     $DB->SQL($query);
     if (!$fonds = $DB->lookupRecord())  
     {
       if ($sf)
       {
         $error[] = "$row :Stroeve code komt niet voor fonds tabel (".$_stroeveCode.") ($subLine : $sf) ";
       }
       else
       {
         $error[] = "$row :Stroeve code komt niet voor fonds tabel (".$_stroeveCode.")";
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
  $_SESSION["importFoutFile"] = implode("",$_foutFile);
  unset($_foutFile);


  if (Count($error) == 0)
  	return true;
  else
  {
  	return false;
  }

}


?>