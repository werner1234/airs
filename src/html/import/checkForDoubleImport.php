<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.6 $

 		$Log: checkForDoubleImport.php,v $
 		Revision 1.6  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.5  2015/12/01 09:01:53  cvs
 		update 2540, call 4352
 		
 		Revision 1.4  2013/12/16 08:21:00  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008
 		
 		Revision 1.2  2005/10/13 07:18:59  cvs
 		doublecheck rekeninnr toegevoegd




*/

function checkForDoubleImport($mutatiedata)
{
  global $__develop;
  if ($__develop)
  {
    return false;
  }
  $Tdb = new DB();

 $query = "
SELECT
 *
FROM
  Rekeningmutaties
WHERE
  Rekening          = '$mutatiedata[Rekening]'          AND
  Boekdatum         = '$mutatiedata[Boekdatum]'         AND
  Grootboekrekening = '$mutatiedata[Grootboekrekening]' AND
  Valuta            = '$mutatiedata[Valuta]'            AND
  Valutakoers       = '$mutatiedata[Valutakoers]'       AND
	Fonds             = '$mutatiedata[Fonds]'             AND
	Aantal            = '$mutatiedata[Aantal]'            AND
	Fondskoers        = '$mutatiedata[Fondskoers]'        AND
	ROUND(Debet,2)    = '".round($mutatiedata[Debet],2)."'  AND
	ROUND(Credit,2)   = '".round($mutatiedata[Credit],2)."' AND
	ROUND(Bedrag,2)   = '".round($mutatiedata[Bedrag],2)."'
";

  $Tdb->SQL($query);
  if ($Tdb->lookupRecord())
    return true;   // dubbele gevonden
  else
    return false;  // geen dubbele gevonden
}
?>