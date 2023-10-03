<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2006/05/01 14:31:04 $
 		File Versie					: $Revision: 1.2 $

 		$Log: positieImportVerwerk_gilissen.php,v $
 		Revision 1.2  2006/05/01 14:31:04  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2006/04/28 14:00:25  cvs
 		*** empty log message ***


*/

/**
 *   Inlezen van gilissen bestanden
 */

if (!$handle = @fopen($file, "r"))
{
	$error[] = "FOUT bestand $file is niet leesbaar";
	return false;
}

if (!$handle1 = @fopen($file1, "r"))
{
	$error[] = "FOUT bestand $file1 is niet leesbaar";
	return false;
}


$csvRegels = Count(file($file))+Count(file($file1));
$pro_multiplier = 100/$csvRegels;

while ($data = fgetcsv($handle1, 1000, ";"))
{
  if (!is_numeric($data[1])) continue;  // sla lege regels over
  $output[$ndx]["valutakoers"]  = $data[25]/$data[24];
  $isin = trim($data[12]).trim($data[13]).trim($data[14]);
  $output[$ndx]["soort"]        = "fonds";
  $output[$ndx]["valuta"]       = trim($data[21]);
  $output[$ndx]["portefeuille"] = trim($data[10]);
  $output[$ndx]["isin"]         = $isin;
  $output[$ndx]["waarde"]       = str_replace(",",".",trim($data[19])*trim($data[18]));
  $output[$ndx]["aantal"]       = str_replace(",",".",trim($data[18]));
  $output[$ndx]["koers"]        = str_replace(",",".",trim($data[19]));

	$ndx++;
	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);
}

while ($data = fgetcsv($handle, 1000, ";"))
{
  if (!is_numeric($data[1])) continue;  // sla lege regels over
  $output[$ndx]["soort"]        = "liq";
  $output[$ndx]["portefeuille"] = trim($data[5]).trim($data[6]);
  $output[$ndx]["waarde"]       = str_replace(",",".",trim($data[8]));
  $output[$ndx]["valuta"]       = trim($data[6]);
  $output[$ndx]["aantal"]       = str_replace(",",".",trim($data[8]));

	$ndx++;
  $pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);

}

fclose($handle);
fclose($handle1);

unlink($file);
unlink($file1);
?>