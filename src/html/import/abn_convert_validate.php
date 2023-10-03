<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2011/12/06 14:07:41 $
 		File Versie					: $Revision: 1.2 $

 		$Log: abn_convert_validate.php,v $
 		Revision 1.2  2011/12/06 14:07:41  cvs
 		eerste spatie verwijderen indien aanwezig
 		
 		Revision 1.1  2011/07/16 09:52:45  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008
 		
 		Revision 1.2  2005/09/21 07:53:48  cvs
 		nieuwe commit 21-9-2005

 		Revision 1.1  2005/05/30 17:09:46  cvs
 		einde dag 30-5-2005

 		Revision 1.5  2005/05/17 12:28:36  cvs
 		2?

 		Revision 1.4  2005/05/17 12:26:26  cvs
 		no message

 		Revision 1.3  2005/05/09 17:04:40  cvs
 		Klaar voor debuggen door Theo




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
	$prb->setLabelValue('txt1','Validatie van ABNAMRO bestand ('.$csvRegels.' regels)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $fileValid = false;
  while ($data =  fgets($handle, 4096))
  {
    if ($data[0] == " ") $data = substr($data,1);  // als eerste char een spatie deze wegknippen
    $row++;
		if (trim($data) == "ABNANL2A")
		{
			$fileValid = true;
			break;
		}
		if ($row > 15) break;
  }
  fclose($handle);

  if ($fileValid == false)
 		$error[] = "Geen ABNANL2A regels gevonden ";

  if (Count($error) == 0)
  	return true;
  else
  {
  	return false;
  }

}


?>