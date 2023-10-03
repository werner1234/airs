<?
/* 	
    AE-ICT source module
    Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2005/11/21 08:40:08 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20051111_PREinstall.php,v $
 		Revision 1.1  2005/11/21 08:40:08  jwellner
 		layout
 		
 		Revision 1.1  2005/11/09 10:20:47  jwellner
 		no message
 		

 		PRE installatie module, hier staan o.a. table wijzigingen in, bestand wordt eenmalig 
		uitgevoerd door vars.php en daarna automatisch gewist.
*/

$queries[] = "ALTER TABLE `Vermogensbeheerders` ADD `AfdrukSortering` VARCHAR( 20 ) NOT NULL AFTER `BasisVoorRisicoMeting`;";

$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20051111 Toevoegen veld in regel ".$a." mislukt, neem aub contact op met AIRS.";
		exit;
	}  
}
?>