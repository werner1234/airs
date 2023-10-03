<?
/* 	
    AE-ICT source module
    Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2006/01/18 11:58:28 $
 		File Versie					: $Revision: 1.4 $
 		
 		$Log: 20060116_PREinstall.php,v $
 		Revision 1.4  2006/01/18 11:58:28  jwellner
 		no message
 		
 		Revision 1.3  2006/01/16 16:17:38  jwellner
 		no message
 		
 		Revision 1.2  2006/01/16 12:37:27  jwellner
 		PRE Install scripts voor Vermogensbeheerder tabel .
 		
 		Revision 1.1  2006/01/16 12:34:48  jwellner
 		PRE Install scripts voor Vermogensbeheerder tabel .
 		
 		PRE installatie module, hier staan o.a. table wijzigingen in, bestand wordt eenmalig 
		uitgevoerd door vars.php en daarna automatisch gewist.
		
ALTER TABLE `Vermogensbeheerders` ADD `Export_data_dag` TEXT NOT NULL AFTER `csvSeperator` ;
ALTER TABLE `Vermogensbeheerders` ADD `Export_data_maand` TEXT NOT NULL AFTER `csvSeperator` ;
ALTER TABLE `Vermogensbeheerders` ADD `Export_data_kwartaal` TEXT NOT NULL AFTER `csvSeperator` ;

ALTER TABLE `Vermogensbeheerders` ADD `Export_dag_pad` VARCHAR( 255 ) NOT NULL AFTER `csvSeperator` ;
ALTER TABLE `Vermogensbeheerders` ADD `Export_maand_pad` VARCHAR( 255 ) NOT NULL AFTER `csvSeperator` ;
ALTER TABLE `Vermogensbeheerders` ADD `Export_kwartaal_pad` VARCHAR( 255 ) NOT NULL AFTER `csvSeperator` ;
 
ALTER TABLE `Portefeuilles` ADD `BeheerfeeBasisberekening` TINYINT NOT NULL AFTER `Taal` ;
ALTER TABLE `Portefeuilles` ADD `BeheerfeeBTW` DECIMAL( 8, 1 ) DEFAULT '19' NOT NULL AFTER `BeheerfeeStaffelPercentage5` ;

*/
$queries[] = "ALTER TABLE `Vermogensbeheerders` ADD `Export_data_dag` TEXT NOT NULL AFTER `csvSeperator` ;";
$queries[] = "ALTER TABLE `Vermogensbeheerders` ADD `Export_data_maand` TEXT NOT NULL AFTER `csvSeperator` ;";
$queries[] = "ALTER TABLE `Vermogensbeheerders` ADD `Export_data_kwartaal` TEXT NOT NULL AFTER `csvSeperator` ;";

$queries[] = "ALTER TABLE `Vermogensbeheerders` ADD `Export_dag_pad` VARCHAR( 255 ) NOT NULL AFTER `csvSeperator` ;";
$queries[] = "ALTER TABLE `Vermogensbeheerders` ADD `Export_maand_pad` VARCHAR( 255 ) NOT NULL AFTER `csvSeperator` ;";
$queries[] = "ALTER TABLE `Vermogensbeheerders` ADD `Export_kwartaal_pad` VARCHAR( 255 ) NOT NULL AFTER `csvSeperator` ;";

$queries[] = "ALTER TABLE `Portefeuilles` ADD `BeheerfeeBasisberekening` TINYINT NOT NULL AFTER `Taal` ;";
$queries[] = "ALTER TABLE `Portefeuilles` ADD `BeheerfeeBTW` DECIMAL( 8, 1 ) DEFAULT '19' NOT NULL AFTER `BeheerfeeStaffelPercentage5` ;";

$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20060116 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
		//exit;
	}  
}
?>