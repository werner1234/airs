<?php
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
include_once('../classes/pclzip.lib.php');
include_once('queueExportQuery.php');
include_once('../classes/BedrijfconsistentieControleClass.php');

function consistentieCheck($Bedrijf)
{
	flush();
	echo "<br>- start consistentie controle<br>";
	flush();

	$return = true;


	$query1 = "SELECT Portefeuilles.Vermogensbeheerder, ".
	"Rekeningen.Portefeuille, ".
	"Rekeningmutaties.Afschriftnummer ".
	"FROM Rekeningmutaties, Rekeningen, Portefeuilles, VermogensbeheerdersPerBedrijf   ".
	"WHERE  ".
	"VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."' AND ".
	"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
	"Portefeuilles.Portefeuille = Rekeningen.Portefeuille AND ".
	"Rekeningen.Rekening = Rekeningmutaties.Rekening AND ".
	"(Rekeningmutaties.Verwerkt = 0 ) GROUP BY Rekeningmutaties.Afschriftnummer ";

	// check rekeningmutaties
	$DB = new DB();
	$DB->SQL($query1);
	$DB->Query();
	while($data = $DB->nextRecord())
	{
		echo "<br>Fout: Rekening ".$data['Rekening']." , afschrift ".$data['Afschriftnummer']." bij portefeuille ".$data['Portefeuille']." heeft onverwerkte mutaties.";
		$return = false;
	}
  logExport("Rekeningmutaties check klaar");

	$query1 = "SELECT
sum(EigendomPerPortefeuille.percentage) as totaal,
VermogensbeheerdersPerBedrijf.Bedrijf,
EigendomPerPortefeuille.Portefeuille
FROM
VermogensbeheerdersPerBedrijf
Inner Join Portefeuilles ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
Inner Join EigendomPerPortefeuille ON Portefeuilles.Portefeuille = EigendomPerPortefeuille.Portefeuille
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='".$Bedrijf."'
GROUP BY EigendomPerPortefeuille.Portefeuille
HAVING  totaal <> 100";

		$DB->SQL($query1);
	$DB->Query();
	while($data = $DB->nextRecord())
	{
		echo "<br>Fout: Bij portefeuille ".$data['Portefeuille']." is het eigendoms percentage (".$data['totaal']."% ).";
		//$return = false;
	}

	// check 2
  logExport("EigendomPerPortefeuille check klaar");

      
  $query2="SELECT 
	Rekeningafschriften.Rekening,Rekeningafschriften.Afschriftnummer ,VermogensbeheerdersPerBedrijf.Bedrijf,Portefeuilles.Portefeuille
FROM
 Rekeningafschriften  
JOIN Rekeningen ON Rekeningafschriften.Rekening=Rekeningen.Rekening 
JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille
JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND VermogensbeheerdersPerBedrijf.Bedrijf='".$Bedrijf."'
WHERE
Rekeningafschriften.Verwerkt = 0";
	// check rekeningmutaties
	$DB = new DB();
	$DB->SQL($query2);
	$DB->Query();
	while($data = $DB->nextRecord())
	{
		echo "<br>Fout: Rekening ".$data['Rekening']." , afschrift ".$data['Afschriftnummer']." bij portefeuille ".$data['Portefeuille']." is niet verwerkt.";
		$return = false;
	}
  logExport("Rekeningafschriften check klaar");
  
  $query="SELECT
Portefeuilles.Portefeuille,
Portefeuilles.Startdatum,
Portefeuilles.Einddatum,
MIN(Rekeningmutaties.Boekdatum) as eersteBoekdatum,
MAX(Rekeningmutaties.Boekdatum) as laatsteBoekdatum
FROM
Portefeuilles
INNER JOIN Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
INNER JOIN Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
INNER JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE Portefeuilles.Startdatum='0000-00-00' AND VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."' 
GROUP BY Portefeuilles.Portefeuille";
	$DB->SQL($query);
	$DB->Query();
	while($data = $DB->nextRecord())
	{
	  echo '<br>';
	  echo vtb('Fout: Portefeuille %s, heeft rekeningmutaties tussen %s en %s heeft geen startdatum (%s) en (einddatum %s)',
      array($data['Portefeuille'], $data['eersteBoekdatum'], $data['laatsteBoekdatum'], $data['Startdatum'], $data['Einddatum']));
//		echo "<br>Fout: Portefeuille ".$data['Portefeuille']." , heeft rekeningmutaties tussen ".$data['eersteBoekdatum']." en ".$data['laatsteBoekdatum']."
//		 heeft geen startdatum (".$data['Startdatum'].") en (einddatum ".$data['Einddatum'].")";
		//$return = false;
	}
  logExport("startdatum/einddatum check klaar");
  
  if($return==true)
  {
    $controle = new BedrijfConsistentieControle($Bedrijf);
    if($_POST)
      $controle->updateRecords($_POST);
    $controle->getChecks();
    $result = $controle->doChecks();
   
    if($_GET['force'] == '1' && $controle->block == false)
    {
      echo "<br/>Export geforceerd, niet blokkerende consistentie resultaten worden genegeerd.";
    }
    elseif($result == false)
    {
      $controle->getFixForm($_SERVER['REQUEST_URI']);
    	?>
			<script type="text/javascript">
			function hideStatus()
			{
			  javascript:document.getElementById("status").style.visibility="hidden";
			}
			</script>
			<div id=status STYLE="position:absolute;top:10px;left:20px;background:white;border:1px dashed #000000;padding:30px;margin:30px;">
			Melding: <br/>
		  <?
      
      if($controle->block==true)
        echo "<br/><a class=\"letterButton\" style=\"width:350px\" href=\"".$_SERVER['REQUEST_URI']."\"><b> Problemen die eerst gecorrigeerd moeten worden gevonden.</b> </a> ";
      else
		    echo "<br/><a class=\"letterButton\" style=\"width:350px\" href=\"".$_SERVER['REQUEST_URI']."&force=1\"><b> Problemen gevonden. Toch doorgaan? </b> </a> ";
		  ?>
		  <br/><br/>
			<a href="javascript:hideStatus();" class="letterButton" style="width:60px" > verbergen. </a>
      </div>
			<?
      exit;
    }
  }
	echo "<br>- einde consistentie controle<br>";
	flush();
	return $return;
}


function gzcompressfile($source,$level=false)
{
	$dest=$source.'.gz';
	$mode='wb'.$level;
	$error=false;
	if($fp_out=gzopen($dest,$mode))
  {
	   if($fp_in=fopen($source,'r'))
     {
	       while(!feof($fp_in))
	           gzwrite($fp_out,fread($fp_in,1024*512));
	       fclose($fp_in);
	   }
	   else 
       $error=true;
	   gzclose($fp_out);
	 }
	 else 
     $error=true;
	if($error) 
    return false;
	else 
    return $dest;
}

function cryptFile($input,$output,$pass,$crypted)
{
	if($crypted==2)
	{
		include_once('../classes/AE_cls_AES.php');
		$crypt = new AE_AES($pass);
		$crypt->encrypt_file($input, $output);
	}
	else
	{
		include_once('../classes/AE_cls_RC4.php');
		$rc4 = new AE_RC4($pass);
		$rc4->RC4_file($input, $output);
	}
  return $output;
}

function updateFondsenPerBedrijf($Bedrijf,$table="FondsenPerBedrijf")
{
	global $USR;

	$query = "SELECT Rekeningmutaties.Fonds
FROM
Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."'
INNER JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds ";

	$DB = new DB();
	// eerst leeg maken? yeps
	$DB->SQL("DELETE FROM ".$table." WHERE Bedrijf = '".$Bedrijf."' ");
	$DB->Query();

	// doe de volgende query .
	$DB->SQL($query);
	$DB->Query();

	$DB2 = new DB();
	while($data = $DB->NextRecord())
	{
		$query = "INSERT INTO ".$table." SET Bedrijf = '".$Bedrijf."', Fonds = '".mysql_escape_string($data['Fonds'])."' , add_date = NOW(), add_user = '".$USR."', change_date = NOW(), change_user = '".$USR."' ";
		$DB2->SQL($query);
		$DB2->Query();
	}

	// Doe ook de indices erbij!
	$query = "SELECT * FROM (
(SELECT Indices.Beursindex as Fonds 
	FROM Indices 
JOIN VermogensbeheerdersPerBedrijf ON Indices.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder 
	WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."' ) 
	UNION
	(SELECT
IndexPerBeleggingscategorie.Fonds
FROM
IndexPerBeleggingscategorie
INNER JOIN VermogensbeheerdersPerBedrijf ON IndexPerBeleggingscategorie.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
AND VermogensbeheerdersPerBedrijf.Bedrijf='".$Bedrijf."' GROUP BY Fonds)
)  as indextabel GROUP BY Fonds";
	$DB->SQL($query);
	$DB->Query();

	$DB2 = new DB();
	while($data = $DB->NextRecord())
	{
	  $query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['Fonds'])."' AND  Bedrijf = '".$Bedrijf."'";
	  if($DB2->QRecords($query) < 1)
	  {
		  $query = "INSERT INTO ".$table." SET Bedrijf = '".$Bedrijf."', Fonds = '".mysql_escape_string($data['Fonds'])."', add_date = NOW(), add_user = '".$USR."', change_date = NOW(), change_user = '".$USR."' ";
  		$DB2->SQL($query);
	  	$DB2->Query();
	  }
	}

	//Voeg ook Portefeuille Specifieke Index toe
	$query = "
SELECT DISTINCT Portefeuilles.SpecifiekeIndex as Fonds 
FROM Portefeuilles 
JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."' 
JOIN Fondsen ON Fondsen.Fonds=Portefeuilles.SpecifiekeIndex 
WHERE 
Portefeuilles.SpecifiekeIndex <> '' ";
	$DB->SQL($query);
	$DB->Query();

	$DB2 = new DB();
	while($data = $DB->NextRecord())
	{
	  $query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['Fonds'])."' AND  Bedrijf = '".$Bedrijf."'";
	  if($DB2->QRecords($query) < 1)
	  {
	    $query = "INSERT INTO ".$table." SET Bedrijf = '".$Bedrijf."' , Fonds = '".mysql_escape_string($data['Fonds'])."' , add_date = NOW() , add_user = '".$USR."' , change_date = NOW() , change_user = '".$USR."' ";
	  	$DB2->SQL($query);
		  $DB2->Query();
	  }
	}

	// Voeg ook de nog missende Fondsen toe die aan een beleggingscategorie zijn gekoppeld.
	$query="SELECT BeleggingscategoriePerFonds.Fonds,VermogensbeheerdersPerBedrijf.Bedrijf
          FROM
          BeleggingscategoriePerFonds
          Join VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder
          LEFT Join $table ON VermogensbeheerdersPerBedrijf.Bedrijf = $table.Bedrijf AND BeleggingscategoriePerFonds.Fonds = $table.Fonds
          JOIN Fondsen ON Fondsen.Fonds=BeleggingscategoriePerFonds.Fonds 
          WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."' AND $table.Fonds IS NULL";
	$DB->SQL($query);
	$DB->Query();
	while($data = $DB->NextRecord())
	{
	  $query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['Fonds'])."' AND  Bedrijf = '".$Bedrijf."'";
	  if($DB2->QRecords($query) < 1)
	  {
		  $query = "INSERT INTO ".$table." SET Bedrijf = '".$Bedrijf."', Fonds = '".mysql_escape_string($data['Fonds'])."', add_date = NOW() , add_user = '".$USR."', change_date = NOW() , change_user = '".$USR."' ";
		  $DB2->SQL($query);
		  $DB2->Query();
	  }
	}
  
  $query="SELECT bench.Fonds FROM ( SELECT benchmarkverdeling.fonds,$table.Bedrijf,benchmarkverdeling.benchmark FROM benchmarkverdeling
INNER JOIN $table ON benchmarkverdeling.benchmark = $table.Fonds AND $table.Bedrijf='$Bedrijf') bench 
left JOIN $table ON bench.fonds=$table.fonds AND $table.Bedrijf='$Bedrijf' WHERE $table.fonds  is null GROUP BY bench.fonds";
	$DB->SQL($query);
	$DB->Query();
	while($data = $DB->NextRecord())
	{
	  $query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['Fonds'])."' AND  Bedrijf = '".$Bedrijf."'";
	  if($DB2->QRecords($query) < 1)
	  {
		  $query = "INSERT INTO ".$table." SET  Bedrijf = '".$Bedrijf."', Fonds = '".mysql_escape_string($data['Fonds'])."' , add_date = NOW() , add_user = '".$USR."' , change_date = NOW() , change_user = '".$USR."' ";
		  $DB2->SQL($query);
		  $DB2->Query();
	  }
	}
  
  $query="SELECT bench.Fonds FROM ( SELECT benchmarkverdelingVanaf.fonds,$table.Bedrijf,benchmarkverdelingVanaf.benchmark FROM benchmarkverdelingVanaf
INNER JOIN $table ON benchmarkverdelingVanaf.benchmark = $table.Fonds AND $table.Bedrijf='$Bedrijf') bench
left JOIN $table ON bench.fonds=$table.fonds AND $table.Bedrijf='$Bedrijf' WHERE $table.fonds is null GROUP BY bench.fonds";
  $DB->SQL($query);
  $DB->Query();
  while($data = $DB->NextRecord())
  {
    $query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['Fonds'])."' AND  Bedrijf = '".$Bedrijf."'";
    if($DB2->QRecords($query) < 1)
    {
      $query = "INSERT INTO ".$table." SET  Bedrijf = '".$Bedrijf."', Fonds = '".mysql_escape_string($data['Fonds'])."' , add_date = NOW() , add_user = '".$USR."' , change_date = NOW() , change_user = '".$USR."' ";
      $DB2->SQL($query);
      $DB2->Query();
    }
  }

	$query="SELECT OptieFondsen.OptieFonds FROM  
(SELECT Fondsen.OptieBovenliggendFonds as OptieFonds,$table.Fonds as optie FROM $table 
JOIN Fondsen ON $table.Fonds=Fondsen.Fonds AND $table.Bedrijf='".$Bedrijf."' GROUP BY Fondsen.OptieBovenliggendFonds ) OptieFondsen
LEFT JOIN $table ON OptieFondsen.OptieFonds=$table.Fonds AND $table.Bedrijf='".$Bedrijf."'
WHERE $table.Fonds is null AND OptieFondsen.OptieFonds <> ''";
	$DB->SQL($query);
	$DB->Query();
	while($data = $DB->NextRecord())
	{
		$query = "SELECT id FROM $table WHERE  Fonds = '".mysql_escape_string($data['OptieFonds'])."' AND  Bedrijf = '".$Bedrijf."'";
		if($DB2->QRecords($query) < 1)
		{
			$query = "INSERT INTO ".$table." SET  Bedrijf = '".$Bedrijf."', Fonds = '".mysql_escape_string($data['OptieFonds'])."' , add_date = NOW() , add_user = '".$USR."' , change_date = NOW() , change_user = '".$USR."' ";
			$DB2->SQL($query);
			$DB2->Query();
		}
	}

}

function updateValutasPerBedrijf($Bedrijf)
{
	global $USR;

	$query="SELECT Rekeningmutaties.Valuta 
		FROM (Rekeningmutaties, Rekeningen, Portefeuilles, VermogensbeheerdersPerBedrijf) 
		LEFT JOIN ValutasPerBedrijf ON ValutasPerBedrijf.Bedrijf = '".$Bedrijf."' AND ValutasPerBedrijf.Valuta = Rekeningmutaties.Valuta 
		WHERE  
		VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."' AND 
		VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND 
		Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND 
		Rekeningmutaties.Rekening = Rekeningen.Rekening AND 
		ValutasPerBedrijf.Bedrijf IS NULL 
		GROUP BY Rekeningmutaties.Valuta 
		ORDER BY Rekeningmutaties.Valuta";

	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();

	$DB2 = new DB();
	while($data = $DB->NextRecord())
	{
		$query = "INSERT INTO ValutasPerBedrijf SET ".
			"  Bedrijf = '".$Bedrijf."' ".
			", Valuta = '".$data['Valuta']."' ".
			", add_date = NOW() ".
			", add_user = '".$USR."' ".
			", change_date = NOW() ".
			", change_user = '".$USR."' ";

		$DB2->SQL($query);
		$DB2->Query();
	}

	$query="SELECT Fondsen.Valuta
FROM Fondsen 
JOIN tmpFondsenPerBedrijf ON tmpFondsenPerBedrijf.Fonds = Fondsen.Fonds AND tmpFondsenPerBedrijf.Bedrijf = '".$Bedrijf."' 
LEFT JOIN ValutasPerBedrijf ON ValutasPerBedrijf.Bedrijf = '".$Bedrijf."'  AND ValutasPerBedrijf.Valuta = Fondsen.Valuta 
WHERE  ValutasPerBedrijf.Bedrijf IS NULL 
GROUP BY Fondsen.Valuta";
	$DB->SQL($query);
	$DB->Query();

	while($data = $DB->NextRecord())
	{
		$query = "INSERT INTO ValutasPerBedrijf SET ".
			"  Bedrijf = '".$Bedrijf."' ".
			", Valuta = '".$data['Valuta']."' ".
			", add_date = NOW() ".
			", add_user = '".$USR."' ".
			", change_date = NOW() ".
			", change_user = '".$USR."' ";

		$DB2->SQL($query);
		$DB2->Query();
	}

}

function clearQueue($exportId)
{
  logIt('Aanroep clearQueue '.$exportId);
  echo "Export afgebroken.<br>\n";

	return true;
}

function exportHeader($filename)
{
	global $exportId, $Bedrijf, $USR, $ftpSettings, $__appvar, $consistentie, $jaar, $updateTimeStamp;

	$filesize = filesize($__appvar['tempdir'].$filename);

	$DB2 = new DB();
  $query = "SELECT koersExport,fondskostenDoorkijkExport FROM Vermogensbeheerders , VermogensbeheerdersPerBedrijf WHERE VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerBedrijf.Bedrijf = '$Bedrijf'";
  $DB2->SQL($query);
 	$koersExport=$DB2->lookupRecord();
 	if($koersExport['koersExport'] > 0)
 	  $consistentie=array();

	if(isset($consistentie['klantMutaties']))
	  unset($consistentie['klantMutaties']);

	$cdata = serialize($consistentie);

	$query = "INSERT INTO updates SET ".
				 "  exportId = '".$exportId."' ".
				 ", Bedrijf = '".$Bedrijf."' ".
				 ", type = '".$_GET['updateSoort']."' ".
				 ", jaar = '".$jaar."' ".
				 ", filename = '".$filename."' ".
				 ", filesize = '".$filesize."' ".
				 ", server = '".$ftpSettings['server']."' ".
				 ", username = '".$ftpSettings['user']."' ".
				 ", password = '".$ftpSettings['password']."' ".
				 ", consistentie = '".mysql_escape_string($cdata)."' ".
				 ", add_date = NOW() ".
				 ", add_user = '".$USR."' ".
				 ", change_date = NOW() ".
				 ", change_user = '".$USR."' ";

	$DB = new DB(2);
	$DB->SQL($query);
	if($res = $DB->Query())
	{
   	if($_GET['updateSoort'] == 'dagelijks' || $_GET['updateSoort'] == 'vanafLaatste')
    {
        if($_GET['updateSoort'] == 'dagelijks')
          $updateVeld="laatsteDagelijkeUpdate='$updateTimeStamp',";
        else
          $updateVeld='';  
          
		  $query = "UPDATE Bedrijfsgegevens SET LaatsteUpdate='$updateTimeStamp', $updateVeld change_date=now(), change_user='exp ".$USR."' WHERE Bedrijf = '".$Bedrijf."'; ";
			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();
    }
	}
	return $res;
}

function exportTable($table, $query, $tofile, $toqueue = false)
{
	global $exportId, $Bedrijf,$USR, $prb;

	$prb->moveStep(0);
	$prb->setLabelValue('txt1','Export tabel '.$table.' (selectie maken)' );
	$pro_step = 0;

	$DB1 = new DB(1);
	$DB1->SQL($query);
//	echo $query ."<br><br>\n\n";
	if(!$DB1->Query())
	{
		echo "<br>\n FOUT in query : ".$query;
  	clearQueue($exportId);
		exit;
		return false;
	}


	$aantal = $DB1->Records();

	$pro_multiplier = (100 / ($aantal+1));
	$prb->setLabelValue('txt1','Export tabel '.$table.' ( '.$aantal.' records) ');

	if($fp = fopen($tofile, 'a'))
	{
	}
	else
	{
		echo "<br>\n FOUT: openen van ".$tofile." mislukt.";
		exit;
	}


	if ($_GET['updateSoort'] == "tabel")
	{
    $data = serialize("TRUNCATE TABLE $table ; \n");
    $q2 = "INSERT INTO importdata SET ";
		$q2 .= " Bedrijf = '".$Bedrijf."', ";
		$q2 .= " tableName = 'systeem', ";
		$q2 .= " tableId =   '0', ";
		$q2 .= " tableData = '".mysql_escape_string($data)."', ";
		$q2 .= " exportId = '".$exportId."', ";
		$q2 .= " add_user = '".$USR."', ";
		$q2 .= " add_date = NOW() , ";
		$q2 .= " change_user = '".$USR."', ";
		$q2 .= " change_date = NOW() ;\n";
		fwrite($fp, $q2);
	}
  elseif($_GET['updateSoort']=="correctie")
  {
		$correctieSQL="('$Bedrijf','$table','0')";
  }
	$normalUpdate='';

  $n=0;
	while($tableData = $DB1->NextRecord())
	{
		$n++;
		$pro_step += $pro_multiplier;
		$prb->moveStep($pro_step);


	switch ($_GET['updateSoort'])
	{
		case "dataqueue" :
		case "userqueue" :
    case "dagelijks" :
    case "vanafLaatste" :
    case "correctie" :
    case "tabel" :
		if($toqueue == true)
		{
			$data = serialize($tableData);

			// insert Into Queue
      if($_GET['updateSoort']=="correctie")
      {
				$correctieSQLlen=strlen($correctieSQL);
				if($correctieSQLlen==0)
					$correctieSQL.="('$Bedrijf','$table','" . $tableData['id'] . "')";
				else
				  $correctieSQL.=",('$Bedrijf','$table','" . $tableData['id'] . "')";
				if($correctieSQLlen > 1000000 || $n==$aantal)
				{
					$q2 = "INSERT INTO importdata (Bedrijf,tableName,tableId) VALUES $correctieSQL ;\n";
					fwrite($fp, $q2);
					$correctieSQL='';
				}

      }
      else
      {
				$normalSQLlen=strlen($normalUpdate);
				if($normalSQLlen==0)
					$normalUpdate.="('".$Bedrijf."','".$table."','".$tableData['id']."','".mysql_escape_string($data)."',  '".$exportId."',  '".$USR."',NOW() ,'".$USR."',NOW()) ";
				else
					$normalUpdate.=",('".$Bedrijf."','".$table."','".$tableData['id']."','".mysql_escape_string($data)."',  '".$exportId."',  '".$USR."',NOW() ,'".$USR."',NOW()) ";
				if($normalSQLlen > 1000000 || $n==$aantal)
				{
					$q2 = "INSERT INTO importdata (Bedrijf,tableName,tableId,tableData,exportId,add_user,add_date,change_user,change_date) VALUES $normalUpdate ;\n";
					//echo $q2."<br>\n<br>\n";
					fwrite($fp, $q2);
					$normalUpdate='';
				}
      }

		}
    break;
		default:
			$exportArray = array();
      foreach($tableData as $key=>$val)
			{
				$exportArray[] = " `".$key."` = '".mysql_escape_string($val)."' ";
			}
			// maak een INSERT Query
			$export = "";
			$export .= "INSERT INTO ".$table." SET ";
			$export .= implode(",",$exportArray).";\n";
			fwrite($fp, $export);
    break;  
	}
  }

		fclose($fp);
		return $aantal;
 }

function countTable($query)
{
	global $prb, $exportId;
	$prb->moveStep(0);
	$prb->setLabelValue('txt1','Controle');
	$pro_step = 0;

	$DB1 = new DB(1);
	$DB1->SQL($query);

	if(!$DB1->Query())
	{
		echo "<br>\n FOUT in query : ".$query;
		clearQueue($exportId);
		exit;
		return false;
	}
	else
	{
		$aantal= $DB1->Records();
	}
	return $aantal;
}

function logExport($txt)
{
  global $exportStart,$exportLogLaatste;
  if($exportStart==0)
  {
    $exportStart = time();
    $exportLogLaatste = $exportStart;
  }
  $nu=time();
  logIt($_GET['Bedrijf']." | ".$_GET['updateSoort']." | ". $_GET['tabel']." | ".($nu-$exportLogLaatste)."s | ".($nu-$exportStart)."s | $txt ",1);
  $exportLogLaatste =$nu;
}

$content = array();
echo template($__appvar["templateContentHeader"],$content);
$Bedrijf=$_GET['Bedrijf'];
?>
exporteren Bedrijf: <?=$Bedrijf?>
<?
$DB = new DB();
$query = "SELECT NOW() as tijd";
$DB->SQL($query);
$DB->Query();
$data = $DB->NextRecord();
$updateTimeStamp = $data['tijd'];
echo " $updateTimeStamp";
logExport("Start Export");

//eerst constistentie controle
if(consistentieCheck($Bedrijf) == true)
{
  logExport("consistentieCheck klaar");
	$prb = new ProgressBar();	// create new ProgressBar
	$prb->pedding = 2;	// Bar Pedding
	$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
	$prb->setFrame();          	                // set ProgressBar Frame
	$prb->frame['left'] = 50;	                  // Frame position from left
	$prb->frame['top'] = 	80;	                  // Frame position from top
	$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
	$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
	$prb->show();

	// generate Export ID
	$exportId = date("YmdHis");
	$DBa = new DB();
  $DBa->SQL("SELECT LaatsteUpdate,laatsteDagelijkeUpdate,crypted FROM Bedrijfsgegevens WHERE Bedrijf = '".$Bedrijf."'");
	$DBa->Query();
	$data = $DBa->NextRecord();
	$crypted = $data['crypted'];

	if($_GET['updateSoort'] == "vanafLaatste")
		$lastUpdate = $data['LaatsteUpdate'];
  elseif($_GET['updateSoort'] == 'dagelijks')
    $lastUpdate=$data['laatsteDagelijkeUpdate'];
	else
		$lastUpdate = 0;

	// build tmp Fondsen table voor bedrijf.
		if(in_array($_GET['updateSoort'],array("correctie","tabel")))
		{
			echo  "<br>\n Bijwerken tmpFondsenPerBedrijf overgeslagen voor ".$_GET['updateSoort']." update.\n";
		}
  	else
		{
			updateFondsenPerBedrijf($Bedrijf, $table = "tmpFondsenPerBedrijf");
      logExport("Stop updateFondsenPerBedrijf");
		}
	// haal verschil op ?
	$DB = new DB();
	$query="SELECT tmpFondsenPerBedrijf.Fonds
  FROM
  tmpFondsenPerBedrijf
  LEFT Join FondsenPerBedrijf ON tmpFondsenPerBedrijf.Fonds = FondsenPerBedrijf.Fonds AND  FondsenPerBedrijf.Bedrijf='$Bedrijf'
  WHERE tmpFondsenPerBedrijf.Bedrijf='$Bedrijf'
  AND FondsenPerBedrijf.Fonds is null";
	$DB->SQL($query);
	$DB->Query();
	while($fondsData = $DB->NextRecord())
	  $nieuweFondsen[] = $fondsData['Fonds'];

	$newFondsenQuery = " IN('".implode("',\n'",$nieuweFondsen)."')";
	if(count($nieuweFondsen)>0)
		$queryValues['newFondsenQuery'] = $newFondsenQuery;
	else
	  $queryValues['newFondsenQuery'] = " IN('') ";

	$query="SELECT tmpFondsenPerBedrijf.Fonds FROM tmpFondsenPerBedrijf WHERE tmpFondsenPerBedrijf.Bedrijf='$Bedrijf'";
	$DB->SQL($query);
	$DB->Query();
	while($fondsData = $DB->NextRecord())
	  $fondsen[] = $fondsData['Fonds'];

	$fondsenQuery = " IN('".implode("','",$fondsen)."')";
	if(count($fondsen)>0)
		$queryValues['fondsenQuery'] = $fondsenQuery;
	else
	  $queryValues['fondsenQuery'] = " IN('') ";


	  $depotbanken=array();
	$vermogensbeheerders=array();
	 $query="SELECT Depotbanken.Depotbank,Portefeuilles.Vermogensbeheerder FROM
Depotbanken, Portefeuilles, VermogensbeheerdersPerBedrijf
WHERE
Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND VermogensbeheerdersPerBedrijf.Bedrijf = '$Bedrijf' AND Portefeuilles.Depotbank = Depotbanken.Depotbank
GROUP By Depotbanken.Depotbank,Portefeuilles.Vermogensbeheerder 
";
	$DB->SQL($query);
	$DB->Query();
	while($depotbankData = $DB->NextRecord())
	{
		$depotbanken[$depotbankData['Depotbank']] = $depotbankData['Depotbank'];
		$vermogensbeheerders[$depotbankData['Vermogensbeheerder']] = $depotbankData['Vermogensbeheerder'];
	}

 $query="SELECT Rekeningen.Depotbank FROM Rekeningen
JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '$Bedrijf' AND Rekeningen.Depotbank <> ''
GROUP By Rekeningen.Depotbank
";
	$DB->SQL($query);
	$DB->Query();
	while($depotbankData = $DB->NextRecord())
	  $depotbanken[$depotbankData['Depotbank']] = $depotbankData['Depotbank'];

	$depotbankQuery = " IN('".implode("','",$depotbanken)."')";
	if(count($depotbanken)>0)
		$queryValues['depotbankQuery'] = $depotbankQuery;
	else
	  $queryValues['depotbankQuery'] = " IN('') ";

	if(count($vermogensbeheerders)>0)
		$queryValues['vermogensbeheerderQuery'] = " IN('".implode("','",$vermogensbeheerders)."')";
	else
		$queryValues['vermogensbeheerderQuery'] = " IN('') ";

	// ---------------- Accountmanagers
	$queryValues['lastUpdate'] = $lastUpdate;
	$queryValues['Bedrijf'] = $Bedrijf;

	// ---------------- Valutakoersen
	$query = "SELECT Rekeningmutaties.Valuta
		FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
		WHERE
		VermogensbeheerdersPerBedrijf.Bedrijf = '$Bedrijf'
GROUP BY Rekeningmutaties.Valuta";
	$DB->SQL($query);
	$DB->Query();
	$ValArray = array();
	while($Valdata = $DB->NextRecord())
		$ValArray[$Valdata['Valuta']] = $Valdata['Valuta'];
	$query="SELECT DISTINCT(Fondsen.Valuta) FROM Fondsen JOIN tmpFondsenPerBedrijf ON tmpFondsenPerBedrijf.Fonds = Fondsen.Fonds AND tmpFondsenPerBedrijf.Bedrijf = '".$Bedrijf."'
  WHERE  tmpFondsenPerBedrijf.change_date >= '$lastUpdate'";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$ValArray[$Valdata['Valuta']] = $Valdata['Valuta'];
	$valutaQuery = " IN('".implode("','",$ValArray)."')";

	if(count($ValArray) > 0)
		$queryValues['valutaQuery'] = $valutaQuery;
	else
		$queryValues['valutaQuery'] = " IN('')  "; //fondsvaluta toeveogen

	$query="SELECT Rekeningmutaties.Bewaarder
	 FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
	 WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '$Bedrijf'
	 GROUP BY Rekeningmutaties.Bewaarder";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$bewaarders[$Valdata['Bewaarder']] = $Valdata['Bewaarder'];
	$bewaarderQuery = " IN('".implode("','",$bewaarders)."')";

	if(count($bewaarders) > 0)
		$queryValues['bewaarderQuery'] = $bewaarderQuery;
	else
		$queryValues['bewaarderQuery'] = " IN('')  ";

	$query="SELECT Portefeuilles.Client
	  FROM Portefeuilles
	  JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
	  WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '$Bedrijf'
GROUP BY Portefeuilles.Client";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$clienten[$Valdata['Client']] = mysql_real_escape_string($Valdata['Client']);
	$clientenQuery = " IN('".implode("','",$clienten)."')";

	if(count($clienten) > 0)
		$queryValues['clientenQuery'] = $clientenQuery;
	else
		$queryValues['clientenQuery'] = " IN('')  ";

			$query="SELECT VermogensbeheerdersPerGebruiker.Gebruiker
FROM VermogensbeheerdersPerGebruiker
JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='$Bedrijf'
GROUP BY VermogensbeheerdersPerGebruiker.Gebruiker";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$gebruikers[$Valdata['Gebruiker']] = mysql_real_escape_string($Valdata['Gebruiker']);
	$gebruikerQuery = " IN('".implode("','",$gebruikers)."')";

	if(count($gebruikers) > 0)
		$queryValues['gebruikerQuery'] = $gebruikerQuery;
	else
		$queryValues['gebruikerQuery'] = " IN('')  ";

			$query="SELECT Eigenaars.Eigenaar FROM Eigenaars
Inner Join EigendomPerPortefeuille ON EigendomPerPortefeuille.Eigenaar = Eigenaars.Eigenaar
Inner Join Portefeuilles ON EigendomPerPortefeuille.Portefeuille = Portefeuilles.Portefeuille
Inner Join VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  VermogensbeheerdersPerBedrijf.Bedrijf = '$Bedrijf' GROUP BY Eigenaars.id";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$eigenaar[$Valdata['Eigenaar']] = mysql_real_escape_string($Valdata['Eigenaar']);
	$eigenaarsQuery = " IN('".implode("','",$eigenaar)."')";

	if(count($eigenaar) > 0)
		$queryValues['eigenaarsQuery'] = $eigenaarsQuery;
	else
		$queryValues['eigenaarsQuery'] = " IN('')  ";


	$fondsKoppelingen=array();
	$query="SELECT Portefeuilles.Portefeuille FROM Portefeuilles 
INNER JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder 
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='$Bedrijf' ";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$fondsKoppelingen['Portefeuilles'][$Valdata['Portefeuille']] = $Valdata['Portefeuille'];
	$query="SELECT GeconsolideerdePortefeuilles.VirtuelePortefeuille FROM GeconsolideerdePortefeuilles 
INNER JOIN VermogensbeheerdersPerBedrijf ON GeconsolideerdePortefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder 
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='$Bedrijf'";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$fondsKoppelingen['Portefeuilles'][$Valdata['VirtuelePortefeuille']] = $Valdata['VirtuelePortefeuille'];

	$query="SELECT BeleggingscategoriePerFonds.Beleggingscategorie
FROM BeleggingscategoriePerFonds
INNER JOIN VermogensbeheerdersPerBedrijf ON BeleggingscategoriePerFonds.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='$Bedrijf' AND BeleggingscategoriePerFonds.Beleggingscategorie <> ''
GROUP by BeleggingscategoriePerFonds.Beleggingscategorie";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$fondsKoppelingen['Beleggingscategorien'][$Valdata['Beleggingscategorie']] = $Valdata['Beleggingscategorie'];

	$query="SELECT CategorienPerVermogensbeheerder.Beleggingscategorie
FROM VermogensbeheerdersPerBedrijf INNER JOIN CategorienPerVermogensbeheerder ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = CategorienPerVermogensbeheerder.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='$Bedrijf'";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$fondsKoppelingen['Beleggingscategorien'][$Valdata['Beleggingscategorie']] = $Valdata['Beleggingscategorie'];

	$query="SELECT
BeleggingssectorPerFonds.Beleggingssector,
BeleggingssectorPerFonds.AttributieCategorie,
BeleggingssectorPerFonds.Regio,
BeleggingssectorPerFonds.DuurzaamCategorie
FROM
BeleggingssectorPerFonds
INNER JOIN VermogensbeheerdersPerBedrijf ON BeleggingssectorPerFonds.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='$Bedrijf' AND BeleggingssectorPerFonds.Beleggingssector <> ''
GROUP by BeleggingssectorPerFonds.Beleggingssector,BeleggingssectorPerFonds.AttributieCategorie,BeleggingssectorPerFonds.Regio,BeleggingssectorPerFonds.DuurzaamCategorie";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
	{
		$fondsKoppelingen['Beleggingssectoren'][$Valdata['Beleggingssector']] = $Valdata['Beleggingssector'];
		$fondsKoppelingen['AttributieCategorien'][$Valdata['AttributieCategorie']] = $Valdata['AttributieCategorie'];
		$fondsKoppelingen['Regios'][$Valdata['Regio']] = $Valdata['Regio'];
		$fondsKoppelingen['DuurzaamCategorien'][$Valdata['DuurzaamCategorie']] = $Valdata['DuurzaamCategorie'];
	}
	$query="SELECT KeuzePerVermogensbeheerder.categorie, KeuzePerVermogensbeheerder.waarde
FROM KeuzePerVermogensbeheerder INNER JOIN VermogensbeheerdersPerBedrijf ON KeuzePerVermogensbeheerder.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='$Bedrijf'";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
	{
		$fondsKoppelingen[$Valdata['categorie']][$Valdata['waarde']] = $Valdata['waarde'];
	}

		$query="SELECT CategorienPerVermogensbeheerder.Beleggingscategorie
FROM CategorienPerVermogensbeheerder
INNER JOIN VermogensbeheerdersPerBedrijf ON CategorienPerVermogensbeheerder.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='$Bedrijf' AND CategorienPerVermogensbeheerder.Beleggingscategorie <> ''
GROUP by CategorienPerVermogensbeheerder.Beleggingscategorie";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$fondsKoppelingen['Beleggingscategorien'][$Valdata['Beleggingscategorie']] = $Valdata['Beleggingscategorie'];

	$query="SELECT AttributiePerGrootboekrekening.AttributieCategorie
FROM AttributiePerGrootboekrekening
INNER JOIN VermogensbeheerdersPerBedrijf ON AttributiePerGrootboekrekening.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='$Bedrijf' AND AttributiePerGrootboekrekening.AttributieCategorie <> ''
GROUP by AttributiePerGrootboekrekening.AttributieCategorie";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$fondsKoppelingen['Beleggingscategorien'][$Valdata['Beleggingscategorie']] = $Valdata['Beleggingscategorie'];

	$query="SELECT
CategorienPerHoofdcategorie.Hoofdcategorie as Beleggingscategorie,
VermogensbeheerdersPerBedrijf.Bedrijf
FROM
CategorienPerHoofdcategorie
JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = CategorienPerHoofdcategorie.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='$Bedrijf'
GROUP BY Hoofdcategorie";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$fondsKoppelingen['Beleggingscategorien'][$Valdata['Beleggingscategorie']] = $Valdata['Beleggingscategorie'];

	$query="SELECT
SectorenPerHoofdsector.Hoofdsector as Beleggingssector,
VermogensbeheerdersPerBedrijf.Bedrijf
FROM
SectorenPerHoofdsector
JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = SectorenPerHoofdsector.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='$Bedrijf'
GROUP BY Hoofdsector";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$fondsKoppelingen['Beleggingssectoren'][$Valdata['Beleggingssector']] = $Valdata['Beleggingssector'];

	$query="SELECT Portefeuilles.SoortOvereenkomst FROM Portefeuilles 
  JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND VermogensbeheerdersPerBedrijf.Bedrijf = '$Bedrijf'
  WHERE Portefeuilles.SoortOvereenkomst <> ''";
	$DB->SQL($query);
	$DB->Query();
	while($Valdata = $DB->NextRecord())
		$fondsKoppelingen['SoortOvereenkomsten'][$Valdata['SoortOvereenkomst']] = $Valdata['SoortOvereenkomst'];

	$queryFilters=array('Beleggingssectoren'=>'Beleggingssectoren.Beleggingssector','AttributieCategorien'=>'AttributieCategorien.AttributieCategorie',
											'Regios'=>'Regios.Regio','Beleggingscategorien'=>'Beleggingscategorien.Beleggingscategorie','DuurzaamCategorien'=>'DuurzaamCategorien.DuurzaamCategorie',
											'SoortOvereenkomsten'=>'SoortOvereenkomsten.SoortOvereenkomst','Portefeuilles'=>'Portefeuille');
	foreach($queryFilters as $filter=>$veld)
	{
		if (count($fondsKoppelingen[$filter]) > 0)
		{
			$queryValues[$filter.'Query'] =  "AND $veld IN('".implode("','",$fondsKoppelingen[$filter])."')";
		}
		else
		{
			$queryValues[$filter.'Query'] = "AND $veld IN('')";
		}
	}
  logExport("Query filters gemaakt");

	$stamp = $exportId;
	$file = "export_".$Bedrijf."_".$stamp.".sql";

	if($_GET['exportType'] == "queue")
		$toqueue = true;

	$query="SELECT max(koersExport) as koersExport, max(fondskostenDoorkijkExport) as fondskostenDoorkijkExport FROM Vermogensbeheerders, VermogensbeheerdersPerBedrijf WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."' AND VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";
	$DB->SQL($query);
  $koersExport=$DB->lookupRecord();
  if($koersExport['koersExport'] > 0)
    include_once('queueExportQueryKoers.php');

	if($_GET['updateSoort'] == 'tabel' || ($_GET['updateSoort'] == 'correctie' && $_GET['tabel'] <> 'alles') )
	{
    $exportQueryNew[$_GET['tabel']] = $exportQuery[$_GET['tabel']];
	  $exportQuery = $exportQueryNew;
	}



 	$query="SELECT * FROM Bedrijfsgegevens WHERE Bedrijf = '".$Bedrijf."' ";
	$DB->SQL($query);
  $oldUpdateStatus=$DB->lookupRecord(); 
  unset($oldUpdateStatus['id']);
  unset($oldUpdateStatus['Bedrijf']);
  
  if($_GET['updateSoort'] == 'dagelijks')
    $updateVeld="laatsteDagelijkeUpdate='$updateTimeStamp',";
  else
    $updateVeld='';

	if(in_array($_GET['updateSoort'],array('dagelijks','vanafLaatste')))
	{
		$query = "UPDATE Bedrijfsgegevens SET LaatsteUpdate='$updateTimeStamp', $updateVeld change_date=now(), change_user='exp " . $USR . "' WHERE Bedrijf = '" . $Bedrijf . "'; ";
		$DB->SQL($query);
		$DB->Query();
	}

  $querySet='';
  foreach($oldUpdateStatus as $key=>$value)
  { 
    if($querySet<>'')
      $querySet.=",";
    $querySet.=" $key='".mysql_real_escape_string($value)."'";
  }
  $queryRollBack="UPDATE Bedrijfsgegevens SET $querySet WHERE Bedrijf = '".$Bedrijf."'";
  
  logExport('Begin export tabellen');
  foreach($exportQuery as $key=>$val)
	{
    if($_GET['updateSoort'] == 'correctie')
    {
      if($koersExport['koersExport'] > 0)
      {
        echo "Correctie naar koersonly export niet mogelijk.";
        exit;
      }
      $query  = buildQuery($key,$val,$queryValues,'ids');	

    }  
    else
		  $query  = buildQuery($key,$val,$queryValues);

    // hoort bij call 3205

    BepaalRecordIdsForIntCheck(buildQuery($key,$val,$queryValues,'ids'),$Bedrijf, $key, $queryValues, $exportId);
//    echo "<br> $query <br>\n";

		$aantal = exportTable($key,$query,$__appvar['tempdir'].$file, $toqueue);
    $totaalRecords += $aantal;
		// voor consistentie controle
		$queryValues2 = 	$queryValues;
		$queryValues2['lastUpdate'] = 0;

    echo "<br>start exporteren " . $key . " " . $aantal . " records";
    if($_GET['telTotaal'])
    {
      $query = buildQuery($key, $val, $queryValues2, 'count');
      $DBx = new DB();
      $DBx->SQL($query);// 	echo "<br> $query <br>\n";
      $DBx->Query();
      $aantalDb = $DBx->nextRecord();
      $consistentie[$key] = $aantalDb['aantal'];//$DBx->records();
      echo "(van " . $aantalDb['aantal'] . " records)";
      logExport("exportTable " . $key . " " . $aantal . " records (van " . $aantalDb['aantal'] . " records)");
    }
    else
    {
      logExport("exportTable " . $key . " " . $aantal . " records.");
    }
	}
  logExport('Voor zip update.');

  if($totaalRecords > 0 || ($_GET['updateSoort'] == 'tabel' || $_GET['updateSoort'] == 'correctie'))
	{
		if(!gzcompressfile($__appvar['tempdir'].$file))
		{
			$_error[] = "Fout: zippen van bestand mislukt!";
		}

		if($crypted>0)
		{
	    $outgoingFile=$__appvar['tempdir'].$file.".gz".'.crypt'.$crypted;
	    $outgoingFileName=$file.".gz".'.crypt'.$crypted;
	    cryptFile($__appvar['tempdir'].$file.".gz",$outgoingFile,$Bedrijf,$crypted);
	    if(filesize($outgoingFile) < 1)
	      $_error[] = "Fout: encryptie van bestand mislukt!";
	    unlink($__appvar['tempdir'].$file.".gz");
		}
		else
		{
	    $outgoingFile=$__appvar['tempdir'].$file.".gz";
	    $outgoingFileName=$file.".gz";
		}
    logExport('Zip update klaar');
		unlink($__appvar['tempdir'].$file);

		if($_GET['exportType'] == "queue" && empty($_error))
		{
  		$ftp->progress = &$prb;
			$ftp->progress->setLabelValue('txt1','Versturen van '.filesize($outgoingFile).' bytes naar FTP server.' );
			if($conn_id = ftp_connect($ftpSettings['server']))
			{
				// login with username and password
				if($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
				{
					if (ftp_put($conn_id, $outgoingFileName, $outgoingFile, FTP_BINARY))
					{
						echo "<br>\n successfully uploaded $outgoingFileName\n";
					}
					else
					{
						echo "<br>\n <b>There was a problem while uploading</b> $outgoingFileName\n";
						$_error[] = "There was a problem while uploading";
					}
				}
				ftp_close($conn_id);
			}
			else
			{
				$_error[] = "Could not connect";
			}
      logExport('ftp klaar');
			if(empty($_error))
			{
				if(!exportHeader($outgoingFileName))
				  $_error[] = "Could not insert update.";

				if(in_array($_GET['updateSoort'],array("correctie","tabel")))
				{
					echo  "<br>\nBijwerken updateFondsenPerBedrijf overgeslagen voor ".$_GET['updateSoort']." update.\n";
				}
				else
				{
					updateFondsenPerBedrijf($Bedrijf);
					updateValutasPerBedrijf($Bedrijf);
				}
			}
			else
			{
			 	$DB->SQL($queryRollBack);
	      $DB->Query();
				// fout
				listarray($_error);
			}

			unlink($outgoingFile);
		}
		elseif ($_GET['exportType'] == "file" && empty($_error))
		{
			echo "<br><br><a href=\"pushFile.php?file=".$outgoingFileName."&filetype=gzip&action=attachment\"><b>download ".$outgoingFileName."</b></a>";
			$melding .= "<br><br><a href=\"pushFile.php?file=".$outgoingFileName."&filetype=gzip&action=attachment\"><b>download ".$outgoingFileName."</b></a>";
		}
		elseif(empty($_error))
		{
		  if($_GET['updateSoort'] == 'dagelijks' || $_GET['updateSoort'] == 'vanafLaatste')
      {
        if($_GET['updateSoort'] == 'dagelijks')
          $updateVeld="laatsteDagelijkeUpdate='$updateTimeStamp',";
        else
          $updateVeld='';  

			$DB2 = new DB();          
		  $query = "UPDATE Bedrijfsgegevens SET LaatsteUpdate='$updateTimeStamp', $updateVeld change_date=now(), change_user='exp ".$USR."' WHERE Bedrijf = '".$Bedrijf."'; ";
			$DB2->SQL($query);
			$DB2->Query();
      }
			$melding .= "<br><br><a href=\"pushFile.php?file=".$outgoingFileName."&filetype=gzip&action=attachment\"><b>download ".$outgoingFileName."</b></a>";
			echo "<br><br><a href=\"pushFile.php?file=".$outgoingFileName."&filetype=gzip&action=attachment\"><b>download ".$outgoingFileName."</b></a>";
		}
		else
		{
		  $DB->SQL($queryRollBack);
	    $DB->Query();
		  listarray($_error);
		}
	// extra controle of update in queue staat
    $updateinQueue=false;
    if($_GET['exportType'] == "queue")
    {
	    $DB = new DB(2);
	    $query = "SELECT id FROM updates WHERE filename = '".$outgoingFileName."'";
	    $DB->SQL($query);
	    $DB->Query();
	    if($DB->records() == 1)
	    {
	     echo "<br>Update staat in de queue.";
	     $melding .= "<br>Update staat in de queue.";
       $updateinQueue=true;
	    }
	    else
	    {
	     echo "<br><b>Update $outgoingFileName niet in de queue gevonden? </b>";
	     $melding .= "<br><b>Update $outgoingFileName niet in de queue gevonden? </b>";
       $DB->SQL($queryRollBack);
	     $DB->Query();
	    }
    }
    logExport('Update klaar');
	}
	else
	{
	 	 $DB->SQL($queryRollBack);
	   $DB->Query();
		 // geen update nodig, 0 records
		 unlink($outgoingFile);
		 echo "<br><br>0 Records gevonden, geen update nodig.";
		 $melding .= "<br><br>0 Records gevonden, geen update nodig.";
	}
	$prb->hide();
}

			?>
				<script type="text/javascript">
				function hideStatus()
				{
				  javascript:document.getElementById("status").style.visibility="hidden";
				}
				</script>

			<div id=status STYLE="position:absolute;top:10px;left:20px;background:white;border:1px dashed #000000;padding:30px;margin:30px;">
			Melding: <br>
		  <?=$melding?>
			<br/>
      <?
      if($updateinQueue==true)
      {
        echo '<br/><br/><a href="queueExport.php" class="letterButton" style="width:100px" > Volgende export. </a><br/>';
        echo '<br/><br/><a href="MONITOR_voortgang.php" class="letterButton" style="width:100px" > Importvoortgang. </a><br/>';
      }


      ?>
			<br/><br/><a href="javascript:hideStatus();" class="letterButton" style="width:60px"> verbergen. </a>
      </div>
			<?
echo template($__appvar["templateRefreshFooter"],$content);
?>