<?
/* 	
    AE-ICT source module
    Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2005/11/09 10:20:47 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: 20051011_PREinstall.php,v $
 		Revision 1.2  2005/11/09 10:20:47  jwellner
 		no message
 		
 		Revision 1.1  2005/10/11 09:49:20  cvs
 		*** empty log message ***
*/
//
//  PRE installatie module, hier staan o.a. table wijzigingen in, bestand wordt eenmalig 
//  uitgevoerd door vars.php en daarna automatisch gewist.
//
//

$db = new DB;
$db->SQL("show fields from Fondsen like 'stroeveCode'");
if (!$db->lookupRecord())
{
  $db->SQL("ALTER TABLE Fondsen ADD COLUMN `stroeveCode` varchar(25) NULL");
  if (!$db->Query())
  {
    echo "FOUTMELDING: 20051011 kolom toevoegen in Fondsen mislukt, neem aub contact op met AIRS ";
    exit;
  }  
}
?>