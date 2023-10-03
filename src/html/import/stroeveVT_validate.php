<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.7 $

 		$Log: stroeveVT_validate.php,v $
 		Revision 1.7  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008
 		
 		Revision 1.5  2008/05/29 15:31:19  cvs
 		diverse tweaks op aanwijzing van Theo

 		Revision 1.4  2008/05/27 15:19:15  cvs
 		- SNS import do_V en do_DV
 		- StroeveVT import datum selecteerbaar

 		Revision 1.3  2007/11/02 14:59:22  cvs
 		VT contracten import, poging 3

 		Revision 1.2  2007/11/02 14:56:56  cvs
 		VT contracten import, poging 2

 		Revision 1.1  2007/11/02 14:49:24  cvs
 		VT contracten import



*/

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb,$datum;
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
  $row = 1;
  $data = fgetcsv($handle, 1000, ";"); // lees eerste regel en sla die over (= header)

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

//
// check minimaal 16 velden
//
  	if (count($data) < 9)
  		$error[] = "$row :te weinig velden ";

//
// check veld 5 numeriek en >= 0
//

    if ($datum <> "")
    {
      $datumInCSV = trim($data[3]);
      $datumVergelijk = substr($datumInCSV,0,4)."-".substr($datumInCSV,4,2)."-".substr($datumInCSV,6,2);
      if ($datum <> $datumVergelijk )
      {
        $error[] = "$row :begindatum ongelijk aan opgegeven datum ($datum / $datumVergelijk)";
      }
    }

//
// check veld 5 numeriek en >= 0
//

  	$_code = trim($data[7]);
  	if (!(is_numeric($_code) AND $_code >= 0))
  		$error[] = "$row :veld tegenwaarde bevat een ongeldige waarde ($_code)";

//
// check bestaat rekeningnummer
//


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
    $valuta = (trim($data[5]) == "U$")?"USDF":"EUR";
    $PortNr  = trim($data[1]);

    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '".$PortNr."' AND Valuta = '".$valuta."' AND Termijnrekening <> 0";
    $DB->SQL($query);
    if (!$rekening = $DB->lookupRecord())
      $error[] = "$row :Termijn rekeningnummer komt niet voor Rekeningen tabel ($PortNr / $valuta)";

    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '".$PortNr."' AND Valuta = 'EUR' AND Termijnrekening <> 0";
    $DB->SQL($query);
    if (!$rekening = $DB->lookupRecord())
      $error[] = "$row :Termijn rekeningnummer komt niet voor Rekeningen tabel ($PortNr / EUR)";



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