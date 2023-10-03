<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2006/07/26 07:42:38 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20060630_PREinstall.php,v $
 		Revision 1.1  2006/07/26 07:42:38  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2006/06/28 12:20:30  jwellner
 		*** empty log message ***
 		
 		
 		ALTER TABLE `Portefeuilles` ADD `PortefeuilleVoorzet` VARCHAR( 8 ) NOT NULL AFTER `Portefeuille` ;
*/
include("wwwvars.php");

$queries[] = " ALTER TABLE `Portefeuilles` ADD `PortefeuilleVoorzet` VARCHAR(8) NOT NULL AFTER `Portefeuille`; ";

$db = new DB;
for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20060630 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
		//exit;
	}  
}
?>