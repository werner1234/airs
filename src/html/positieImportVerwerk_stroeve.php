<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2006/05/01 14:31:04 $
 		File Versie					: $Revision: 1.2 $

 		$Log: positieImportVerwerk_stroeve.php,v $
 		Revision 1.2  2006/05/01 14:31:04  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2006/04/28 14:00:25  cvs
 		*** empty log message ***


*/

/**
 *   Inlezen van stroeve bestand
 */

if (!$handle = @fopen($file, "r"))
{
	$error[] = "FOUT bestand $file is niet leesbaar";
	return false;
}

$csvRegels = Count(file($file));
$pro_multiplier = 100/$csvRegels;

while ($data = fgetcsv($handle, 1000, ";"))
{
  if (!is_numeric($data[0])) continue;  // sla lege regels over
  $output[$ndx]["valutakoers"]  = $data[11]/$data[8];
  $output[$ndx]["aantal"]       = trim($data[7]);
  if (trim($data[3]) == "")
  {
    $output[$ndx]["soort"]        = "liq";
    $output[$ndx]["portefeuille"] = trim($data[0]).trim($data[2]);
    $output[$ndx]["waarde"]       = trim($data[8]);
    $output[$ndx]["valuta"]       = trim($data[2]);
  }
  else
  {
    $output[$ndx]["soort"]        = "fonds";
    $output[$ndx]["valuta"]       = trim($data[2]);
    $output[$ndx]["portefeuille"] = trim($data[0]);
    $output[$ndx]["isin"]         = trim($data[16]);
    $output[$ndx]["waarde"]       = trim($data[8]);
    $output[$ndx]["koers"]        = (($output[$ndx]["waarde"] / $output[$ndx]["aantal"]) / $data[14]);

  }


	$ndx++;
	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);

}
fclose($handle);
unlink($file);
?>