<?php
/* 	
 		Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2005/08/05 15:05:20 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: updateDatabase.php,v $
 		Revision 1.1  2005/08/05 15:05:20  jwellner
 		vakantie!
 		
 		Revision 1.12  2005/08/04 11:12:01  jwellner
 		- in/export functionaliteiten toegevoegd.
 		
 		Revision 1.11  2005/08/01 07:01:30  jwellner
 		no message
 		
 		Revision 1.10  2005/06/30 08:23:32  jwellner
 		Rapportage toegevoegd
 		
 		Revision 1.9  2005/05/30 11:36:31  jwellner
 		no message
 		
 		Revision 1.7  2005/05/18 11:46:47  jwellner
 		consistentie Check
 		
 		Revision 1.6  2005/05/17 09:58:07  jwellner
 		index op velden
 		
 		Revision 1.5  2005/05/06 10:08:54  cvs
 		no message
 		
 		Revision 1.4  2005/05/06 09:21:44  cvs
 		table wijzigingen TijdelijkeRekeningmutaties
 		
 		
 		
*/
include_once("AE_lib2.php3");
include_once("wwwvars.php");

$dbResource   = new DB();

// Voeg in elke table een ID  + datum / tijd veld toe!

$dbResource->SQL("SHOW TABLES");
$dbResource->Query();
$dbResource2 = new DB();

while ($data = $dbResource->nextRecord())
{
  $data[0] = $data['Tables_in_'.$_DB_resources[1]['db']];
  echo "Updating table: ".$data[0];
  $SQL1 = "ALTER TABLE ".$data[0]." ADD id INT AUTO_INCREMENT PRIMARY KEY FIRST ;";
	$SQL2 = "ALTER TABLE ".$data[0]." ADD add_date DATETIME default 'NOW()', 
                                    ADD add_user VARCHAR( 10 ),
                                    ADD change_date DATETIME default 'NOW()', 
                                    ADD change_user VARCHAR( 10 );";
	
  $dbResource2->SQL($SQL1);
  $dbResource2->Query($SQL1);
  
  $dbResource2->SQL($SQL2);
  $dbResource2->Query($SQL2);
  echo "<br>";
	flush();
}

// lees nu de update file in
if(!$fp = @fopen($__appvar[basedir]."/database/mdb2sql.sql","r"))
{
	echo $__appvar[basedir]."/database/mdb2sql.sql openen : FOUT";
}
else 
{
	$sql = fread($fp,filesize($__appvar[basedir]."/database/mdb2sql.sql"));

	$sqlRegel = explode(";",$sql);
	for($tel=0;$tel <count($sqlRegel); $tel++)
	{
		$sqlRegel[$tel] = chop($sqlRegel[$tel]);
		if(!empty($sqlRegel[$tel]))
		{
			$dbResource2->SQL($sqlRegel[$tel]);
			if(!$dbResource2->Query())
			{
				echo "FOUT in ".$sqlRegel[$tel];
			}
			else 
			{
				echo "OK: ".$sqlRegel[$tel]."<br>";
			}
		}
	}
}
echo "<br>Tabellen aanpassen : OK ";

flush();
?>