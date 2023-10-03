<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/10/23 06:13:02 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20061016_PREinstall.php,v $
 		Revision 1.1  2006/10/23 06:13:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/06/28 12:20:30  jwellner
 		*** empty log message ***
 		
*/
include("wwwvars.php");

$queries[] = "ALTER table Vermogensbeheerders ADD order_controle  text AFTER  Export_dag_pad; ";
$queries[] = "ALTER table OrderRegels ADD controle tinyint AFTER status; ";
$queries[] = "ALTER table OrderRegels ADD controle_regels text AFTER status; ";
$queries[] = "ALTER table Orders ADD controle_datum datetime NOT NULL default '0000-00-00 00:00:00' AFTER memo; ";

$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20060621 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
	}  
}
?>