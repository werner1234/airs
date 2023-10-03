<?
/* 	
    AE-ICT source module
    Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2005/11/21 08:40:08 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20051121_PREinstall.php,v $
 		Revision 1.1  2005/11/21 08:40:08  jwellner
 		layout
 		
 		Revision 1.1  2005/11/09 10:20:47  jwellner
 		no message
 		

 		PRE installatie module, hier staan o.a. table wijzigingen in, bestand wordt eenmalig 
		uitgevoerd door vars.php en daarna automatisch gewist.
		
ALTER TABLE `Rekeningmutaties` CHANGE `Aantal` `Aantal` DECIMAL( 12, 4 ) NOT NULL DEFAULT '0.00' 
ALTER TABLE `TijdelijkeRekeningmutaties` CHANGE `Aantal` `Aantal` DECIMAL( 12, 4 ) NULL DEFAULT '0.00' 
ALTER TABLE `TijdelijkeRapportage` CHANGE `beginAantal` `beginAantal` DECIMAL( 12, 4 ) NOT NULL DEFAULT '0.00'
ALTER TABLE `TijdelijkeRapportage` CHANGE `totaalAantal` `totaalAantal` DECIMAL( 12, 4 ) NOT NULL DEFAULT '0.00'
*/

$queries[] = "ALTER TABLE `Rekeningmutaties` CHANGE `Aantal` `Aantal` DECIMAL( 12, 4 ) NOT NULL DEFAULT '0.00';";
$queries[] = "ALTER TABLE `TijdelijkeRekeningmutaties` CHANGE `Aantal` `Aantal` DECIMAL( 12, 4 ) NULL DEFAULT '0.00';";
$queries[] = "ALTER TABLE `TijdelijkeRapportage` CHANGE `beginAantal` `beginAantal` DECIMAL( 12, 4 ) NOT NULL DEFAULT '0.00';";
$queries[] = "ALTER TABLE `TijdelijkeRapportage` CHANGE `totaalAantal` `totaalAantal` DECIMAL( 12, 4 ) NOT NULL DEFAULT '0.00';";

$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20051121 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
		exit;
	}  
}
?>