<?php
/* 	
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/10/31 11:55:12 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: updateImportDatabase.php,v $
 		Revision 1.3  2006/10/31 11:55:12  rvv
 		Voor user update
 		
 		Revision 1.2  2006/03/21 10:13:26  jwellner
 		*** empty log message ***
 		
 		Revision 1.1  2006/03/06 09:40:26  jwellner
 		*** empty log message ***
 		
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
$disable_auth = true;
include_once("AE_lib2.php3");
include_once("wwwvars.php");

// zet hier de speciale database instellingen.
// tables die niet worden geimporteerd.
$skipTables = array("AABTransaktieCodes","Export","Mutatievoorstel", "Querydata", "ManagementOverzicht","UpdateHistory", "FondsParticipatieVerloop", "Accountmanagers","Bedrijfsgegevens","Beleggingscategorien","BeleggingscategorienPerWegingscategorie","CategorienPerHoofdcategorie","Controles","Depotbanken","Gebruikers","Grootboekrekeningen","Indices","KortingenPerDepotbank","FondsenPerBedrijf","Risicoklassen","SectorenPerHoofdsector","TijdelijkePerformance","TijdelijkeRapportage","TijdelijkeRekeningmutaties","Transactieoverzicht","Transactietypes","Valutarisico","Valutas","ValutasPerBedrijf","Vermogensbeheerders","VermogensbeheerdersPerBedrijf","VermogensbeheerdersPerGebruiker","Vertalingen","Zorgplichtcategorien","Zorgplichtcontrole");

$importTables = array("BeleggingscategoriePerFonds","BeleggingssectorPerFonds","Beleggingssectoren","Clienten","Fondsen","Fondskoersen","Portefeuilles","Rekeningafschriften","Rekeningen","Rekeningmutaties","Rentepercentages","Valutakoersen","ZorgplichtPerFonds","ZorgplichtPerPortefeuille");

echo template($__appvar["templateContentHeader"],$content);
if(!$_POST['jaar'])
{
?>
<b>Importeren Access database </b><br><br>
* <b>Maak een backup van de originele Airs database!</b><br>
* Maak een database "airs_import" aan of maak deze leeg. <br>
* Importeer via navicat de .mdb naar de database "airs_import" <br>
* selecteer hier het juiste jaar<br><br>

<form action="updateImportDatabase.php" method="POST" >
<input type="hidden" name="posted" value="true" />

<div class="form">
<div class="formblock">
<div class="formlinks"> Jaar </div>
<div class="formrechts">
<select name="jaar">
<?
echo "<option value=\"\">--</options>\n";
$jaar = date("Y");
for($a=0; $a < 6; $a++)
{
	echo "<option value=\"".($jaar-$a)."\">".($jaar-$a)."</options>\n";
}
?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<br><Br>
<input type="submit" value="Importeer data">
</div>
</div>

</form>
<?
}
else 
{
	$jaar = $_POST[jaar];
	$dbResource   = new DB(4);
	// Voeg in elke table een ID  + datum / tijd veld toe!
	
	$dbResource->SQL("SHOW TABLES");
	$dbResource->Query();
	$dbResource2 = new DB(4);
	
	echo "<br>updating tables [";
	while ($data = $dbResource->nextRecord())
	{
	  echo ".";
	  flush();
	  
	  $data[0] = $data['Tables_in_'.$_DB_resources[4]['db']];
	  $SQL1 = "ALTER TABLE ".$data[0]." ADD id INT AUTO_INCREMENT PRIMARY KEY FIRST ;";
		$SQL2 = "ALTER TABLE ".$data[0]." ADD add_date DATETIME default 'NOW()', 
	                                    ADD add_user VARCHAR( 10 ),
	                                    ADD change_date DATETIME default 'NOW()', 
	                                    ADD change_user VARCHAR( 10 );";
		
	  $dbResource2->SQL($SQL1);
	  $dbResource2->Query($SQL1);
	  
	  $dbResource2->SQL($SQL2);
	  $dbResource2->Query($SQL2);
	  
	  // update addUser & date
	  $SQL3 = "UPDATE ".$data[0]." SET add_user = 'imp".$jaar."', add_date = NOW(), change_user = 'imp".$jaar."', change_date = NOW() ";
	  $dbResource2->SQL($SQL3);
	  $dbResource2->Query($SQL3);
	}
	echo " ] OK";
	flush();
	
	// lees nu de update file in
	echo "<br>converting tables [";
	flush();
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
					echo ".";
				}
			}
		}
	}
	echo " ] OK ";
	
	echo "<br>remove tables [";
	
	for($a=0; $a < count($skipTables); $a++)
	{
		$dbResource->SQL("DROP TABLE `".$skipTables[$a]."`");
		$dbResource->Query();
		echo ".";
	}
	echo " ] OK ";
	
	echo "<br>converting Rekeningmutaties / Rekeningafschriften [";
	
	$query1 = "UPDATE Rekeningmutaties SET Afschriftnummer = (Afschriftnummer + ".$jaar."000)";
	$dbResource->SQL($query1);
	$dbResource->Query();
	
	$query2 = "UPDATE Rekeningafschriften SET Afschriftnummer = (Afschriftnummer + ".$jaar."000)";
	$dbResource->SQL($query2);
	$dbResource->Query();
	echo ".. ] OK";
	flush();
	// importeren data - loopje over tables.
	
	echo "<br>reading data (may take a while)<br>";
	for($a=0; $a < count($importTables); $a++)
	{
		$tel = 0;
		$teltrue = 0;
		
		echo "<br>".$importTables[$a]." : ";
		// query select in import database.
		$select = "SELECT * FROM `".$importTables[$a]."`";
		$dbResource = new DB(4);
		$dbResource->SQL($select);
		$dbResource->Query();
		// loop over records.
		while($selectData = $dbResource->nextRecord())
		{
			$tel++;
			$doInsert = false;
			// doe checks!
			$db = new DB();
			
			
			switch($importTables[$a])
			{
				case "BeleggingscategoriePerFonds" :
					// key : Beleggingscategorie  Vermogensbeheerder Fonds
					$test = " SELECT COUNT(id) AS aantal " .
									" FROM BeleggingscategoriePerFonds ".
									" WHERE ".
									" Beleggingscategorie = '".$selectData['Beleggingscategorie']."' AND ".
									" Vermogensbeheerder = '".$selectData['Vermogensbeheerder']."' AND ".
									" Fonds = '".$selectData['Fonds']."' ";
					$db->SQL($test);
					$db->Query();
					$tdata = $db->nextRecord();
					if($tdata[aantal] < 1)				
						$doInsert = true;
				break;
				case "BeleggingssectorPerFonds" :
					// key : Beleggingssector Vermogensbeheerder Fonds
					$test = " SELECT COUNT(id) AS aantal " .
									" FROM BeleggingssectorPerFonds ".
									" WHERE ".
									" Beleggingssector = '".$selectData['Beleggingssector']."' AND ".
									" Vermogensbeheerder = '".$selectData['Vermogensbeheerder']."' AND ".
									" Fonds = '".$selectData['Fonds']."' ";
					$db->SQL($test);
					$db->Query();
					$tdata = $db->nextRecord();
					if($tdata[aantal] < 1)				
						$doInsert = true;
				break;
				case "Beleggingssectoren" :
					// key : Beleggingssector
					$test = " SELECT COUNT(id) AS aantal " .
									" FROM Beleggingssectoren ".
									" WHERE ".
									" Beleggingssector = '".$selectData['Beleggingssector']."' ";
					$db->SQL($test);
					$db->Query();
					$tdata = $db->nextRecord();
					if($tdata[aantal] < 1)
						$doInsert = true;
				break;
				case "Clienten" :
					// key : Client 
					$test = " SELECT COUNT(id) AS aantal " .
									" FROM Clienten ".
									" WHERE ".
									" Client = '".$selectData['Client']."' ";
					$db->SQL($test);
					$db->Query();
					$tdata = $db->nextRecord();
					if($tdata[aantal] < 1)				
						$doInsert = true;
				break;
				case "Fondsen" :
					// key : Fonds
					$test = " SELECT COUNT(id) AS aantal " .
									" FROM Fondsen ".
									" WHERE ".
									" Fonds = '".$selectData['Fonds']."' ";
					$db->SQL($test);
					$db->Query();
					$tdata = $db->nextRecord();
					if($tdata[aantal] < 1)				
						$doInsert = true;
				break;
				case "Fondskoersen" :
					// niet nodig altijd true
					$doInsert = true;
				break;
				case "Portefeuilles" :
					// key : Portefeuille 
					$test = " SELECT COUNT(id) AS aantal " .
									" FROM Portefeuilles ".
									" WHERE ".
									" Portefeuille = '".$selectData['Portefeuille']."' ";
	
					$db->SQL($test);
					$db->Query();
					$tdata = $db->nextRecord();
					if($tdata[aantal] < 1)				
						$doInsert = true;
				break;
				case "Rekeningafschriften" :
					// key : niet nodig altijd true
					$doInsert = true;
				break;
				case "Rekeningen" :
					// key : Rekening
					$test = " SELECT COUNT(id) AS aantal " .
									" FROM Rekeningen ".
									" WHERE ".
									" Rekening = '".$selectData['Rekening']."' ";
					$db->SQL($test);
					$db->Query();
					$tdata = $db->nextRecord();
					if($tdata[aantal] < 1)				
						$doInsert = true;
				break;
				case "Rekeningmutaties" :
					// key : niet nodig altijd true.
					$doInsert = true;
				break;
				case "Rentepercentages" :
					// key : Fonds Datum
					$test = " SELECT COUNT(id) AS aantal " .
									" FROM Rentepercentages ".
									" WHERE ".
									" Fonds = '".$selectData['Fonds']."' AND ".
									" Datum = '".$selectData['Datum']."' ";
					$db->SQL($test);
					$db->Query();
					$tdata = $db->nextRecord();
					if($tdata[aantal] < 1)				
						$doInsert = true;
				break;
				case "Valutakoersen" :
					// niet nodig altijd true.
					$doInsert = true;
				break;
				case "ZorgplichtPerFonds" :
					// key : Zorgplicht Vermogensbeheerder Fonds
					$test = " SELECT COUNT(id) AS aantal " .
									" FROM ZorgplichtPerFonds ".
									" WHERE ".
									" Zorgplicht = '".$selectData['Zorgplicht']."' AND ".
									" Vermogensbeheerder = '".$selectData['Vermogensbeheerder']."' AND ".
									" Fonds = '".$selectData['Fonds']."' ";
					$db->SQL($test);
					$db->Query();
					$tdata = $db->nextRecord();
					if($tdata[aantal] < 1)				
						$doInsert = true;
				break;
				case "ZorgplichtPerPortefeuille" :
					// key : Portefeuille Zorgplicht
					$test = " SELECT COUNT(id) AS aantal " .
									" FROM ZorgplichtPerPortefeuille ".
									" WHERE ".
									" Portefeuille = '".$selectData['Portefeuille']."' AND ".
									" Zorgplicht = '".$selectData['Zorgplicht']."' ";
					$db->SQL($test);
					$db->Query();
					$tdata = $db->nextRecord();
					if($tdata[aantal] < 1)				
						$doInsert = true;
				break;
			}
			
			if($doInsert == true)
			{
				$setArray = array();
				
				$teltrue ++;
				// insert records.
				$insertQuery = "INSERT INTO `".$importTables[$a]."` SET ";
				reset($selectData);
				while (list($key, $value) = each($selectData))
				{
					if($key <> "id")
					{
						$setArray[] = " `".$key."` = '".mysql_escape_string($value)."' ";
					}
				}
	
				$insertQuery .= implode(", ",$setArray);
				$db->SQL($insertQuery);
				$db->Query();
				flush();
			}
		}
		echo $teltrue." van ".$tel." records toegevoegd.\n<br>";
	}
	echo "<br>done!";
	flush();
}
echo template($__appvar["templateContentFooter"],$content);
?>