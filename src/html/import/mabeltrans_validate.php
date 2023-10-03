<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.3 $

 		$Log: mabeltrans_validate.php,v $
 		Revision 1.3  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/05/06 09:37:24  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2014/07/10 06:53:14  cvs
 		*** empty log message ***
 		


*/

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb;
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
  while ($data = fgetcsv($handle, 1000, ","))
  {
    $row++;
    if ($data[0] == "bankid") continue;
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);


// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

   //debug($data);
   
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
      
      $qExtra = "";
   	  if (trim($isinCode) <> "" )
      {
        if ($data[24] <> "")
        {
          $qExtra = " AND Valuta='".$data[24]."'";
        }  
        $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$isinCode."' ".$qExtra;
        $DB->SQL($query);
        if ($fonds = $DB->lookupRecord())
        {
          $aantal = $DB->QRecords($query);
          if ($aantal > 1)
          {
            $error[] = "$row :ISIN code komt meer dan eens voor in fonds tabel (".$isinCode."/aantal: ".$aantal.")";
          }
        }
        else
        {
          $extra =($data[24] <> "")? "icm Valuta ".$data[24]:"";
          $error[] = "$row :fonds met ISIN $isinCode $extra niet gevonden";
        }
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