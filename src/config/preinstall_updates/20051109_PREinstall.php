<?
/* 	
    AE-ICT source module
    Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2005/11/09 10:20:47 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20051109_PREinstall.php,v $
 		Revision 1.1  2005/11/09 10:20:47  jwellner
 		no message
 		

 		PRE installatie module, hier staan o.a. table wijzigingen in, bestand wordt eenmalig 
		uitgevoerd door vars.php en daarna automatisch gewist.
		
		
ALTER TABLE `Valutas` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;
ALTER TABLE `Valutakoersen` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;
ALTER TABLE `Valutarisico` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;
ALTER TABLE `ValutasPerBedrijf` CHANGE `Valuta` `Valuta` CHAR( 4 ) NOT NULL ;
ALTER TABLE `Fondsen` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;
ALTER TABLE `Mutatievoorstel` CHANGE `Fondsvaluta` `Fondsvaluta` CHAR( 4 ) NULL DEFAULT NULL ;
ALTER TABLE `Portefeuilles` CHANGE `RapportageValuta` `RapportageValuta` CHAR( 4 ) NULL DEFAULT NULL ;
ALTER TABLE `Rekeningen` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;
ALTER TABLE `Rekeningmutaties` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;
ALTER TABLE `TijdelijkeRekeningmutaties` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;
ALTER TABLE `TijdelijkeRapportage` CHANGE `valuta` `valuta` VARCHAR( 4 )  NOT NULL ;
*/

$queries[] = "ALTER TABLE `Valutas` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;";
$queries[] = "ALTER TABLE `Valutakoersen` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;";
$queries[] = "ALTER TABLE `Valutarisico` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;";
$queries[] = "ALTER TABLE `ValutasPerBedrijf` CHANGE `Valuta` `Valuta` CHAR( 4 ) NOT NULL ;";
$queries[] = "ALTER TABLE `Fondsen` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;";
$queries[] = "ALTER TABLE `Mutatievoorstel` CHANGE `Fondsvaluta` `Fondsvaluta` CHAR( 4 ) NULL DEFAULT NULL ;";
$queries[] = "ALTER TABLE `Portefeuilles` CHANGE `RapportageValuta` `RapportageValuta` CHAR( 4 ) NULL DEFAULT NULL ;";
$queries[] = "ALTER TABLE `Rekeningen` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;";
$queries[] = "ALTER TABLE `Rekeningmutaties` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;";
$queries[] = "ALTER TABLE `TijdelijkeRekeningmutaties` CHANGE `Valuta` `Valuta` CHAR( 4 ) NULL DEFAULT NULL ;";
$queries[] = "ALTER TABLE `TijdelijkeRapportage` CHANGE `valuta` `valuta` VARCHAR( 4 )  NOT NULL ;";

$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20051109 aanpassen Valuta naar 4 char regel ".$a." mislukt, neem aub contact op met AIRS.";
		exit;
	}  
	
}
?>