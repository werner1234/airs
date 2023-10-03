<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/12/24 11:31:56 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20081008_PREinstall_FIN.php,v $
 		Revision 1.1  2008/12/24 11:31:56  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/06 10:18:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/10/09 06:23:57  cvs
 		gebruikerstabel ivm CRM
 		
 		Revision 1.1  2007/09/27 13:35:24  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/08/24 11:26:49  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2007/08/24 11:25:17  cvs
 		*** empty log message ***
 		
 
 		
 	
*/
include("wwwvars.php");

$query[] = "UPDATE Rekeningmutaties SET Boekdatum = '2008-09-30' ,change_user = 'upd' , change_date = now() WHERE Boekdatum = '0000-00-00 00:00:00' OR Boekdatum IS NULL OR Boekdatum = '' ";
$query[] = "UPDATE Rekeningafschriften SET Datum = '2008-09-30' ,change_user = 'upd' , change_date = now() WHERE Datum = '0000-00-00 00:00:00' OR Datum IS NULL OR Datum = ''";

$db = new DB();
for ($a=0; $a < count($query); $a++)
{
	$db->SQL($query[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20081008 $query ".$a." mislukt, neem aub contact op met AIRS.";
	}
}

?>