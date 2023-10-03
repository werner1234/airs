<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.2 $

 		$Log: binckv2_validate_convert.php,v $
 		Revision 1.2  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/07/16 09:52:45  cvs
 		*** empty log message ***
 		



*/
function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb;
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
  $data = fgetcsv($handle, 1000, ";");  // eerste regel overslaan veldnamen
  $row = 1;
  while ($data = fgetcsv($handle, 1000, ";"))
  {
    $row++;
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);


// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie


/*
** 15 aug 2007 cvs
** hieronder aanpassingen aan de array om de nieuwe structuur van de cvs file aan te passen naar de oude standaard
*/
  $t7 = $data[7];
  $data[7]  = $data[6];
  $data[6]  = $t7;
  $data[16] = str_replace("/","",$data[16]);

/*
** 15 aug 2007 cvs
** einde aanpassing
*/

//
// check minimaal 20 velden
//
		if (count($data) < 20)
  		$error[] = "$row :te weinig velden ";

//
// check veld 4 is nummeriek
//
  	$_code = trim($data[2]);  // == rekeningsoort

  	if (!(is_numeric($_code) AND $_code >= 0))
  		$error[] = "$row :veld 4 bevat een ongeldige waarde ($_code)";

//
// check bestaat rekeningnummer
//

    $_rekNr = trim($data[1]).trim($data[3]);

		$query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
		$DB->SQL($query);
    if (!$rekening = $DB->lookupRecord())
      $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";

// check bestaat portefeuille voor dit rekeningnummer
//
/*  validatie op verzoek Theo uitgezet per 7 dec 2007
    $_code = trim($data[1]);
	  $query = "SELECT * FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$_code."' ";
		$DB->SQL($query);
    if (!$_bla = $DB->lookupRecord())
       $error[] = "$row :Geen Portefeuille bij Rekeningnummer ($_code) ";
*/


// check bestaat stornering melden
//
    $_code = trim(strtolower($data[7]));

    if ($_code == "s")
       $error[] = "$row :Deze regel bevat een stornering ";

//
// check of ISIN code voorkomt in fondsen tabel
//
  if ($data[6] <> "O-G" AND $data[6] <> "O-G1" AND $data[6] <> "RTDB" AND $data[6] <> "RTCR")
  {
    if ($data[19] <> "")
    {
   	   $_isin = trim($data[19]);
       $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$_isin."' AND Valuta = '".$data[9]."'";
       $DB->SQL($query);
       if (!$fonds = $DB->lookupRecord())
    	  $error[] = "$row :ISIN icm Valutacode komt niet voor fonds tabel ($_isin / ".$data[9]." )";
     }
     else
     {
       $_binckCode = trim($data[32]);
       $query = "SELECT * FROM Fondsen WHERE binckCode = '".$_binckCode."' ";
       $DB->SQL($query);
       if (!$fonds = $DB->lookupRecord())
      	$error[] = "$row :binck code komt niet voor fonds tabel ($_binckCode)";
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