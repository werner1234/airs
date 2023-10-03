<?
/* 	
    AE-ICT source module
    Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2006/06/28 12:20:30 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: 20060309_PREinstall.php,v $
 		Revision 1.2  2006/06/28 12:20:30  jwellner
 		*** empty log message ***
 		
 		Revision 1.1  2006/03/10 12:44:53  jwellner
 		*** empty log message ***
 		
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
		
ALTER TABLE `Querydata` ADD `Type` VARCHAR( 15 ) NOT NULL ;

*/
$queries[] = "ALTER TABLE `Querydata` ADD `Type` VARCHAR( 15 ) NOT NULL ;";



$db = new DB;

$select = "SHOW TABLES LIKE '".$this->tmp_table."'";
$DB->SQL($select);
if (!$DB->lookupRecord())
{
	
}

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20060309 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
		//exit;
	}  
}
?>