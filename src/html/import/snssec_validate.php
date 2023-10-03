<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.6 $

 		$Log: snssec_validate.php,v $
 		Revision 1.6  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/09/21 08:30:05  cvs
 		call 5200
 		
 		Revision 1.4  2015/12/01 09:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2013/01/30 10:17:18  cvs
 		check op depotbank SNS
 		getrekening via port + mem methode
 		
 		Revision 1.2  2011/03/04 07:15:18  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2010/06/09 15:20:23  cvs
 		*** empty log message ***

 		Revision 1.2  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008

 		Revision 1.1  2008/05/27 15:21:07  cvs
 		*** empty log message ***

 		Revision 1.5  2007/08/15 07:14:42  cvs
 		omzetten naar nieuwe indeling van CSV bestand

 		Revision 1.4  2005/09/21 07:53:48  cvs
 		nieuwe commit 21-9-2005



*/
//
// check bestaat $portefeuille
//

foreach ($transactieCodes as $k => $v)
{
  if ($v == "D" OR $v == "LO" OR $v == "L" OR $v == "DS")
    $memRekening[] = $k;
}

function checkPortefeuille($portefeuille)
{
    global $row,$DB,$error;
	  $query = "SELECT id FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$portefeuille."' ";
		$DB->SQL($query);
    if (!$p = $DB->lookupRecord())
       $error[] = "$row :Geen Portefeuille ($portefeuille) gevonden ";
}
//
// check bestaat er een rekening
//

function checkRekening($rekening,$valuta)
{
    global $row,$DB,$error,$_rekNr;

   	$_rekNr = trim($rekening).trim($valuta);

		$query = "SELECT id FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
		$DB->SQL($query);
    if (!$rekening = $DB->lookupRecord())
      $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";
}

function checkRekeningTra($rekening,$valuta)
{
  // rekeningnr bijzoeken via portnr+mem methode (tnt 30-1-2013)
  global $row,$DB,$error,$_rekNr;

  $_rekNr = trim($rekening).trim($valuta);

	$query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '$rekening' AND Memoriaal = 0 AND Valuta='$valuta' AND Inactief = 0 AND Depotbank IN ('SNS','NIBC') ";
  $DB->SQL($query);
  if (!$rekeningRec = $DB->lookupRecord())
  {
    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".trim($rekening)."MEM' ";
    $DB->SQL($query);
    if (!$tempRec = $DB->lookupRecord())
    { 
      $error[] = "$row :Geen rekening gevonden bij $rekening ($valuta)";
    }
    else
    {
      $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '".$tempRec["Portefeuille"]."' AND Memoriaal = 0 AND Valuta='$valuta' AND Inactief = 0 AND Depotbank IN ('SNS','NIBC') ";
      $DB->SQL($query);
      if (!$tempRec = $DB->lookupRecord())
      {
        $error[] = "$row :Geen rekening gevonden bij $rekening ($valuta)";
      }
    }
  }  
}
//
// check of ISIN code voorkomt in fondsen tabel
//
function checkSNScode($code)
{
  global $row,$DB,$error;
 	$query = "SELECT * FROM Fondsen WHERE snsSecCode = '".$code."' ";
 	$DB->SQL($query);
 	$DB->query();

 	if ($DB->records() > 1)
 	  $error[] = "$row :SNS/NIBC code ($code) komt meer dan eens voor.";

 	if (!$fonds = $DB->nextRecord())
  	$error[] = "$row :SNS/NIBC code ($code) komt niet voor in fonds tabel";
}

function validateCvsFile($filename, $soort)
{
	global $error, $csvRegels,$prb,$row,$memRekening;
	
	$DB = new DB();

	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
  
  $row1 = fgets($handle, 4096);
  $data = convertFixedLine($row1);
  if ($soort == "single")
  {
    if ($data[1] <> "CASHTRANS" AND $data[1] <> "SECURITYTRANS")
    {
      $error[] = "geen geldig SNS/NIBC bestand";
      return;
    }  
    else
    {
      $soort = ($data[1] <> "CASHTRANS")?"STRA":"CTRA";
    }
  }  
  elseif ($soort == "CTRA")
  {
    if ($data[1] <> "CASHTRANS" AND trim($data[1]) <> "" )
    {
      $error[] = "geldbestand geen CTRA bestand";
      return;
    }  
  }
  else
  {
    if ($data[1] <> "SECURITYTRANS" AND trim($data[1]) <> "")
    {
      $error[] = "stukkenbestand geen STRA bestand";
      return;
    }  
  }
  
  $handle = @fopen($filename, "r");
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $prb->hide();
  $row = 0;
  while (!feof($handle))
  {
    $dataRaw = fgets($handle, 4096);
    if (trim($dataRaw) == "") continue;

    $data = convertFixedLine($dataRaw);

    $row++;
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    if($data[1] == "SECURITYTRANS") //Transacties
    {
      if(isNumeric($data[6]))
        $portefeuille = intval($data[6]);

      //checkPortefeuille($portefeuille);
      checkSNScode($data[19]);
      
      
      $tc = $data[8];
      if (in_array($tc,$memRekening))
        checkRekening($data[6],"MEM");
      else
        checkRekeningTra($data[6],$data[22]);  

    }

    elseif  ($data[1] == "CASHTRANS") //Mutaties
    {
      checkRekening($data[40],$data[43]);
    }
    else
    {
      $error[] = "$row : <b> Eerste kollom bevat geen SECURITYTRANS of CASHTRANS. (Verkeerde bestandsindeling?) </b>";
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