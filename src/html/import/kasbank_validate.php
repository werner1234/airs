<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.2 $

 		$Log: kasbank_validate.php,v $
 		Revision 1.2  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/11/05 12:52:58  cvs
 		*** empty log message ***
 		



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

	$query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '$rekening' AND Memoriaal = 0 AND Valuta='$valuta' AND Inactief = 0 AND Depotbank = 'KAS' ";
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
      $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '".$tempRec["Portefeuille"]."' AND Memoriaal = 0 AND Valuta='$valuta' AND Inactief = 0 AND Depotbank = 'KAS' ";
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
function checkKasbankcode($code)
{
  global $row,$DB,$error;
 	$query = "SELECT * FROM Fondsen WHERE kasbankCode = '".$code."' ";
 	$DB->SQL($query);
 	$DB->query();

 	if ($DB->records() > 1)
 	  $error[] = "$row :Kasbankcode ($code) komt meer dan eens voor.";

 	if (!$fonds = $DB->nextRecord())
  	$error[] = "$row :Kasbankcode ($code) komt niet voor in fonds tabel";
}

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb,$row,$memRekening, $transactieCodes;
	$error = array();
	$DB = new DB();

	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van Kasbank bestand ('.$csvRegels.' records)');
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

    if(substr($data[3],0,2) == "20" AND ($data[8] == "+" OR $data[8] == "-"))
    {
      checkRekening($data[2].$data[7]);
      
      if (!$tcRec = getTAcode($data[24]) )
      {
        $error[] = "$row : <b> transactiecode ".$data[24]." bestaat niet in codetabel </b>";
      }
      else
      {
        if ($tcRec["doActie"] == "")  
        {
          $error[] = "$row : <b> transactiecode ".$data[24]." (".$tcRec["omschrijving"].") actie niet gevuld in codetabel </b>";
        }
      }
    }
    else
    {
      $error[] = "$row : <b> Geen geldige Kasbank regel </b>";
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