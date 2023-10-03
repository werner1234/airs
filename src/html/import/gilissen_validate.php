<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.5 $

 		$Log: gilissen_validate.php,v $
 		Revision 1.5  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008
 		
 		Revision 1.3  2005/09/22 10:20:47  cvs
 		diverse bugfixes en aanpassing nav CRM todo's

 		Revision 1.2  2005/09/21 07:53:48  cvs
 		nieuwe commit 21-9-2005

 		Revision 1.1  2005/07/12 15:03:03  cvs
 		einde dag 12-7-2005

 		Revision 1.5  2005/05/17 12:28:36  cvs
 		2?




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
  	if (count($data) < 2)  continue;  // lege regels overslaan
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
     $_rekNr = trim($data[1]).trim($data[9]);
		 $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
		 $DB->SQL($query);
     if (!$rekening = $DB->lookupRecord())
       $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";

// check bestaat portefeuille voor dit rekeningnummer
//
//     $_code = trim($data[1]);
//	   $query = "SELECT * FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$_code."' ";
//		 $DB->SQL($query);
//     if (!$_bla = $DB->lookupRecord())
//       $error[] = "$row :Geen Portefeuille bij Rekeningnummer ($_code) ";

//
// check of ISIN code voorkomt in fondsen tabel
//
  	if ($data[3] <> "")
    {
   	 $_tgb = $data[3];
     $query = "SELECT * FROM Fondsen WHERE TGBCode = '".$_tgb."' ";
     $DB->SQL($query);
     if (!$fonds = $DB->lookupRecord())
       $error[] = "$row :TGB code komt niet voor fonds tabel ($_tgb)";
    }


  }
  fclose($handle);
  if (Count($error) == 0)
  	return true;
  else
  {
  	// targetbestand verwijderen omdat validatie niet goed was

  	return false;
  }

}


?>