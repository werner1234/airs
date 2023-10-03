<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/02/11 15:57:20 $
 		File Versie					: $Revision: 1.11 $

 		$Log: pictet_validate.php,v $
 		Revision 1.11  2020/02/11 15:57:20  cvs
 		call 8411
 		
 		Revision 1.10  2019/10/09 09:57:31  cvs
 		call 8061
 		
 		Revision 1.9  2019/07/08 14:25:46  cvs
 		call 7927
 		
 		Revision 1.8  2019/06/14 11:22:59  cvs
 		call 7882
 		
 		Revision 1.7  2019/03/22 12:35:44  cvs
 		call 6686
 		
 		Revision 1.6  2018/10/15 13:21:25  cvs
 		call 7227
 		
 		Revision 1.5  2018/10/03 15:32:40  cvs
 		no message
 		
 		Revision 1.4  2018/05/16 13:32:19  cvs
 		call 6888
 		
 		Revision 1.3  2018/03/14 16:41:09  cvs
 		call 6686
 		
 		Revision 1.2  2018/01/22 11:06:45  cvs
 		call 4125
 		
 		Revision 1.1  2015/12/01 09:01:53  cvs
 		update 2540, call 4352
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***
 		





*/

function validateCvsFile($filename)
{
  
	global $error, $csvRegels,$prb,$rekeningAddArray, $row;

  $data4Array = array("ETDDE");
	$error = array();
  $DB = new DB();
  
  $query = "SELECT PICcode FROM pictetTransactieCodes";
  $DB->executeQuery($query);
  while ($row = $DB->nextRecord())
  {
    $_transactiecodes[] = $row["PICcode"];
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
  $validateSkipArray = array("SECURITIES EVENTS", "INTEREST");
  while ($data = fgetcsv($handle, 1000, "\t"))
  {
    $row++;
    if ($row == 1)
    {
      if (substr($data[0],0,16) <> "HEADER12.PL3L951")
      {
        $error[] = "$row :header HEADER12.PL3L951 niet gevonden";
        break;
      }
    }
    if ($row < 4)
    {
      continue; // skip headers
    }
   if (trim($data[1]) == "" )
   {
     continue;  // lege regels overslaan
   }

    $data = convertRecord($data);
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);


// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie


//
// check transactie code bestaat
//
  	$_code = trim($data[3]);
  	if (!in_array($_code,$_transactiecodes))
    {
      $error[] = "$row :onbekende transactiecode ($_code)";
    }


//

  $PICcodeNotFound = true;
  $fonds = array();

  $_rekNr = str_replace(".","-",$data[41]).$data[18];

  if ($data[3] == "EVTIT" AND in_array($data[4], $data4Array ))
  {
    $_rekNr = str_replace(".","-",$data[41])."MEM";
  }


  if (getRekening($_rekNr) == false)
  {
    $error[] = "$row :Rekeningnummer komt niet voor ($_rekNr icm depotbank)";
  }
  else
  {
    $VB =  $rekening["Vermogensbeheerder"];
  }


  if (  (trim($data[6]) == "" AND  trim($data[7]) == "" ) OR
        (
          $data[3] == "ICAC" AND
          ($data[4] == "DCV"  OR $data[4] == "DCA")
        )
    )
  {
    // geen fonds info  
  }
  else
  {
    if (trim($data[6]) <> "")
    {
      $PICcode = trim($data[6]);
      $query = "SELECT * FROM Fondsen WHERE PICcode = '".$PICcode."' ";
      $DB->SQL($query);
      if ($fonds = $DB->lookupRecord()) { $PICcodeNotFound = false;  }
    }

    if ($data[7] <> "" AND $data[15] <> "" AND $PICcodeNotFound)
    {
      $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$data[7]."' AND Valuta = '".$data[15]."' ";
      $DB->SQL($query);
      if ($fonds = $DB->lookupRecord()) { $PICcodeNotFound = false;  }
    }

    if ($PICcodeNotFound AND $data["6"] != "VA57LU")
    {
      $error[] = "$row :Fonds niet gevonden (".$data[7]." ".$data[15]."/".$data[6].")";
    }




  }
    $d = array();
    $d["bankTransactieId"] = $data[1]."-".$data[40];
    $d["Boekdatum"]        = substr($data[13],0,4)."-".substr($data[13],4,2)."-".substr($data[13],6,2);
    $d["Rekening"]         = $_rekNr;

    checkVoorDubbelInRM($d, "validate");

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