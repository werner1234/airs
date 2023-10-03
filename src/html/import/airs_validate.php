<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.5 $

 		$Log: airs_validate.php,v $
 		Revision 1.5  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.4  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/05/12 08:20:23  cvs
 		airs import
 		
 		Revision 1.2  2017/04/13 13:52:30  cvs
 		no message
 		
 		Revision 1.1  2014/07/10 06:53:14  cvs
 		*** empty log message ***
 		


*/

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb;
	$error = array();
	$DB = new DB();



	$_transactiecodes = Array("UITK","A","V","OA","OV","SA","SV","R",
	                          "DV","KNBA","BEW","BEH","ST","OP","KRUIS",
                            "MOA","MOV","MSA","MSV");
	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
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

//
// check minimaal 16 velden
//
  	if (count($data) < 16)
  		$error[] = "$row :te weinig velden ";

//
// check transactie code bestaat
//
  	$_code = trim($data[4]);
  	if (!in_array($_code,$_transactiecodes))
   		$error[] = "$row :ongeldige transactiecode ($_code)";

//
// check veld 5 numeriek en >= 0
//
  	$_code = trim($data[5]);
  	if (!(is_numeric($_code) AND $_code >= 0))
  		$error[] = "$row :veld 5 bevat een ongeldige waarde ($_code)";

//
// check bestaat rekeningnummer
//

    $chk    = trim(strtoupper($data[4]));  // transactie type
    $IsIsin = (trim($data[22]) == "")?false:true;  // is ISIN gevuld

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
          ($chk == "OP"  AND $IsIsin)  OR
          ($chk == "MOA"  AND $IsIsin)  OR
          ($chk == "MOV"  AND $IsIsin)  OR
          ($chk == "MSA"  AND $IsIsin)  OR
          ($chk == "MSV"  AND $IsIsin)    )
    {
       $_rekNr = trim($data[1])."MEM";
		   $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
		   $DB->SQL($query);
       if (!$rekening = $DB->lookupRecord())
         $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";


    }
    else
    {

      if (substr($data[22],0,3) == "34 ")
      {
         $_rekNr = trim($data[1])."DEP";
		     $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
		     $DB->SQL($query);
         if (!$rekening = $DB->lookupRecord())
           $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";



      }
      else
      {
       $_rekNr = trim($data[1]).trim($data[9]);
		   $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
		   $DB->SQL($query);
       if (!$rekening = $DB->lookupRecord())
         $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";
      }
    }


//
// check of ISIN code voorkomt in fondsen tabel
//

   if ($data[22])
   {
     $query = "SELECT * FROM Fondsen WHERE Fonds = '".$data[22]."' ";
     $DB->SQL($query);
     if (!$fonds = $DB->lookupRecord())
     {
       $error[] = "$row :AIRS fondscode komt niet voor fonds tabel (".$data[22].")";
     }
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