<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2011/04/27 14:55:23 $
 		File Versie					: $Revision: 1.1 $

 		$Log: raboswift_validate.php,v $
 		Revision 1.1  2011/04/27 14:55:23  cvs
 		*** empty log message ***
 		


*/

function validateFile($filename)
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
	$prb->setLabelValue('txt1','Validatie van Rabo swift bestand ('.$csvRegels.' regels)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $fileValid = false;
  while ($data =  fgets($handle, 4096))
  {
    $row++;
		if (trim($data) == ":940:")
		{
			$fileValid = true;
			break;
		}
		if ($row > 15) break;
  }
  fclose($handle);

  if ($fileValid == false)
 		$error[] = "Geen :940: regel gevonden ";

  if (Count($error) == 0)
  	return true;
  else
  {
  	return false;
  }

}


?>