<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.4 $

 		$Log: sns_validate.php,v $
 		Revision 1.4  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/03/09 16:05:01  cvs
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
function checkPortefeuille($portefeuille)
{
/*
    global $row,$DB,$error;
	  $query = "SELECT id FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$portefeuille."' ";
		$DB->SQL($query);
    if (!$p = $DB->lookupRecord())
       $error[] = "$row :Geen Portefeuille ($portefeuille) gevonden ";
*/
}
//
// check bestaat er een rekening
//
function checkRekening($portefeuille,$valuta)
{
    global $row,$DB,$error,$_rekNr;

   	$_rekNr = trim($portefeuille).trim($valuta);

		$query = "SELECT id FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
		$DB->SQL($query);
    if (!$rekening = $DB->lookupRecord())
      $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";
}
//
// check of ISIN code voorkomt in fondsen tabel
//
function checkSNScode($code)
{
  global $row,$DB,$error;
 	$query = "SELECT * FROM Fondsen WHERE SNSCode = '".$code."' ";
 	$DB->SQL($query);
 	$DB->query();

 	if ($DB->records() > 1)
 	  $error[] = "$row :SNS code ($code) komt meer dan eens voor.";

 	if (!$fonds = $DB->nextRecord())
  	$error[] = "$row :SNS code ($code) komt niet voor in fonds tabel";
}

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb,$row;
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
  while ($data = fgetcsv($handle, 1000, ";"))
  {
    $row++;
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    $data = cleanRow($data);

// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

if(strlen($data[1])== 16) //Transacties
{
 if(isNumeric($data[3]))
  $portefeuille = intval($data[3]);

 checkPortefeuille($portefeuille);
 checkRekening($portefeuille,$data[11]);
 checkSNScode($data[9]);

}
elseif (strlen($data[1]) == 8) //Mutaties
{
 if(isNumeric($data[2]))
  $portefeuille = intval($data[2]);
 checkPortefeuille($portefeuille);
 checkRekening($portefeuille,$data[7]);
}
else
{
 if(strlen($data[1]) != 0)
  $error[] = "$row : <b> Eerste kollom bevat geen 16 of 6 tekens. (Verkeerde bestandsindeling?) </b>";
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