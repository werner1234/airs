<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/22 15:39:13 $
 		File Versie					: $Revision: 1.91 $

 		$Log: queueAutoupdate.php,v $
 		Revision 1.91  2020/04/22 15:39:13  rvv
 		*** empty log message ***
 		
 		Revision 1.90  2020/01/11 19:42:11  rvv
 		*** empty log message ***
 		
 		Revision 1.89  2019/09/07 15:47:31  rvv
 		*** empty log message ***
 		
 		Revision 1.88  2019/09/04 15:29:38  rvv
 		*** empty log message ***
 		
*/
if(!isset($USR) || $USR=='')
{
  $USR = 'sys';
}

$disable_auth = true;
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
include_once('../classes/pclzip.lib.php');
include_once('../classes/AIRS_consolidatie.php');
include_once('queueExportQuery.php');

$_error = array();
$dataqueueOke=false;
function createTempTable($tabledef)
{
  $tempDB = new db();
  $tempDB->SQL($tabledef);
  if ($tempDB->Query())
    return true;
  else
    return false;
}

function deleteTempTable($tablename)
{
  $tempDB = new db();
  $query = "DROP TABLE IF EXISTS $tablename";
  $tempDB->SQL($query);

  if ($tempDB->Query())
    return true;
  else
    return "fout tijdens verwijderen tijdelijke tabel: $tablename";
}


//copy many files
function copyFiles($source,$dest)
{
   if(!$folder = opendir($source))
     return "Bronbestanden bestaan niet";

   while($file = readdir($folder))
   {
       if ($file == '.' || $file == '..') {
           continue;
       }

       if(is_dir($source.'/'.$file))
       {
       		// check if file exists
       		if(!file_exists($dest.'/'.$file))
        		if(!mkdir($dest.'/'.$file,0777))
							$_error .= "Fout bij aanmaken $dest/$file";
        	$terugmelding=copyFiles($source.'/'.$file,$dest.'/'.$file);
        	if($terugmelding != "1")
          	$_error .= $terugmelding;
       }
       else
       {
				if(copy($source.'/'.$file,$dest.'/'.$file) != true)
					$_error .= "Fout bij het schrijven naar ".$dest."/".$file."\n";
       }
   }
   closedir($folder);

   if(!empty($_error))
   	return $_error;

   return true;
}

function rmdirr($dir) 
{
   if($objs = glob($dir."/*")){
       foreach($objs as $obj) {
           is_dir($obj)? rmdirr($obj) : unlink($obj);
       }
   }
   rmdir($dir);
}

if($__appvar["multipleDB"])
{
   $bedrijven[$__appvar['bedrijf']]=$_DB_resources[1];
   $query="SHOW DATABASES LIKE 'airs_%'";
   $DB2 = new DB();
   $DB2->SQL($query);
   $DB2->Query();
   $DBsettings=$_DB_resources[1];
   while($database=$DB2->nextRecord('num'))
   {
      $dbNaam=explode("_",$database[0]);
      $DBsettings['db']=$database[0];
      $bedrijven[$dbNaam[1]]=$DBsettings;
   }
}
else
  $bedrijven[$__appvar['bedrijf']]=$_DB_resources[1];

// FixQueue ook automatisch verwerken als er niemand is ingelogd.
verwerkFixQueue();

$lastQueueType=0;
foreach ($bedrijven as $bedrijf=>$DBsettings)
{
  $__appvar['bedrijf']=$bedrijf;
  $_DB_resources[1]=$DBsettings;
	$dataqueueOke=false;
	$updateDataFromQueueOke=false;
// selecteer laatste update.

  $db=new DB();
  $query="DELETE FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.add_date < NOW() - INTERVAL 1 DAY";
  $db->SQL($query);
  $db->Query();

  $DB = new DB(2);
  
  $q = "SELECT * FROM updates WHERE exportId = exportId AND Bedrijf = '".$__appvar['bedrijf']."' AND complete = '2' GROUP BY exportId ORDER BY add_date ASC";
  $DB->SQL($q);
  $DB->Query();
  $aantal = $DB->Records();
  if($aantal > 0)
  {
    echo "Update $bedrijf bezig. Update process afgebroken.";
    exit();
  }
  
  $q = "SELECT * FROM updates WHERE exportId = exportId AND Bedrijf = '".$__appvar['bedrijf']."' AND complete = '0' GROUP BY exportId ORDER BY add_date ASC";
  $DB->SQL($q);
  $DB->Query();
  $aantal = $DB->Records();
// voer update uit if result == 1
if($aantal > 0)
{
	while($queue = $DB->nextRecord())
	{
	$messages='';
	$_error = array();
	echo "start update $bedrijf ".$queue['exportId']." (".$queue['filesize'].")\n";

  $DB2 = new DB(2);
  $DB2->SQL("UPDATE updates SET terugmelding = 'start update', complete = '2', change_date = NOW() ,change_user='sys' WHERE id = '".$queue['id']."'");
  $DB2->Query();

	$Bedrijf = $__appvar['bedrijf'];
	$exportId = $queue['exportId'];

	// download file
	$ftpSettings['server']		= $queue['server'];
	$ftpSettings['user'] 			= $queue['username'];
	$ftpSettings['password'] 	= $queue['password'];

	$localPath = $__appvar['tempdir'].$queue['filename'];

	if($queue['filesize']>0)
  {
    // download gzip.
    if ($conn_id = ftp_connect($ftpSettings['server']))
    {
      $messages .= logTxt("Ftp connectie gemaakt.");
      if ($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
      {
        $messages .= logTxt("Ftp login gelukt.");
        if (is_file($localPath))
        {
          unlink($localPath);
        }
        // FTP pasv aanzetten indien nodig (plaats de var in local_vars.php
        if ($__appvar["ftpPasv"])
        {
          $messages .= logTxt("Ftp pasv set.");
          ftp_pasv($conn_id, true);
        }
        $messages .= logTxt("Downloaden bestand gestart");
        if (ftp_get($conn_id, $localPath, $queue['filename'], FTP_BINARY))
        {
          // download OK
          $messages .= logTxt("Downloaden bestand gelukt.");
        }
        else
        {
          $_error[] = "FTP: download van " . $queue['filename'] . " naar $localPath mislukt\n";
        }
      }
      ftp_close($conn_id);
      $messages .= logTxt("FTP verbinding gesloten.");
    }
    else
    {
      $_error[] = "FTP: verbinding mislukt";
    }
  }

	if(substr($queue['filename'],strlen($queue['filename'])-7,6)=='.crypt')
	{
		$crypted=true;
		$newPath = substr($localPath, 0, strlen($localPath) - 7);
		if(substr($localPath,strlen($localPath)-1) == '2')
		{
			include_once('../classes/AE_cls_AES.php');
			$crypt = new AE_AES($Bedrijf);
			$crypt->decrypt_file($localPath,$newPath);
		}
		else
		{
			include_once('../classes/AE_cls_RC4.php');
			$rc4 = new AE_RC4($Bedrijf);
			$rc4->RC4_file($localPath, $newPath);
		}
		$messages .= logTxt("Bestand gedecodeerd.");
		if(filesize($newPath) < 1)
			$_error[] = "FOUT: decryptie van ".$localPath." mislukt.";
		unlink($localPath);
		$localPath=$newPath;
	}

	//unzip file (s)
	$allTableDef='';
	switch ($queue['type'])
	{
		case "dataqueue" :
		case "userqueue" :
    case "dagelijks" :
    case "vanafLaatste" :
    case "correctie" :
    case "tabel" :
    case "database" :
		case "zaterdag" :
    case "documenten" :
		// alleen data geen PATH info in zip .
		$tofile = $__appvar['tempdir']."tmp_".$queue['exportId'].".sql";
		if(empty($_error))
		{
			// get contents of a gz-file into a string
			if($fp = fopen($tofile, 'w+'))
			{
			  $messages .= logTxt("Databestand geopend.");
				if($zd = gzopen($localPath, "r"))
				{
					while($buffer = gzread($zd, 10000))
					{
						fwrite($fp, $buffer);
					}
					gzclose($zd);
          $messages .= logTxt("Databestand uitegepakt.");
				}
				else
				{
					$_error[] = "FOUT: openen van ".$localPath." mislukt.";
				}
				fclose($fp);
			}
			else
			{
				$_error[] = "FOUT: openen van ".$tofile." mislukt.";
			}
		}
		// remove zip file.
		unlink($localPath);
		break;
		case "softwareupdate" :
			// software update er staan ook paden in de zipfile.
			$archive = new PclZip($localPath);
		  if ($archive->extract(PCLZIP_OPT_PATH, $__appvar['tempdir']."softwareUpdate/") == 0)
		  {
		    echo $_error[] = "Unzip error : ".$archive->errorInfo(true);
		  }

		  // check software version
		  if(file_exists($__appvar['tempdir']."softwareUpdate/AIRS/config/version.php"))
		  {
		  	$myVersion = $PRG_VERSION;
		  	include($__appvar['tempdir']."softwareUpdate/AIRS/config/version.php");
		  	if($PRG_VERSION < $myVersion)
		  	{
		  		$_error[] = "Fout: update versienummer (".$PRG_VERSION.") is lager dan huidige versie (".$myVersion.")";
		  	}
		  }
		  else
		  {
		  	$_error[] = "Fout: geen version.php gevonden in ".$__appvar['tempdir']."softwareUpdate/AIRS/config/version.php";
		  }
      $messages .= logTxt("Update uitgepakt in tijdelijke locatie.");
		  if(empty($_error))
		  {
		  	// als er geen errors zijn kopieer dan de data!.
        $terugmelding=copyFiles($__appvar['tempdir']."softwareUpdate/AIRS/",$__appvar["basedir"]."/");
        if($terugmelding != "1")
        {
         	$_error[] = "Fout : kopieeren update mislukt.\n".$terugmelding;
        }
        else
          $messages .= logTxt("Bestanden in productie geplaatst.");
		  }
		  // remove update en directory.
		  if(file_exists($__appvar['tempdir']."softwareUpdate/AIRS/config/version.php"))
		  {
		  	rmdirr($__appvar['tempdir']."softwareUpdate/AIRS");
        $messages .= logTxt("Tijdelijke update bestanden verwijderd.");
		  }

			// remove zip file.
		unlink($localPath);
		$preInstallScript = $__appvar["basedir"]."/config/PREinstall.php";
    if(file_exists($preInstallScript))  // er bestaat een installatie updatescript dus uitvoeren
    {
      $messages .= logTxt("PREinstall gevonden.\n");
      if($__appvar["multipleDB"])
      {
        $query="SHOW DATABASES LIKE 'airs_%'";
        $DB2 = new DB();
        $DB2->SQL($query);
        $DB2->Query();
        $DBsettings=$_DB_resources[1];
        while($database=$DB2->nextRecord('num'))
        {
          $_DB_resources[1]['db']=$database[0];
          include($preInstallScript);
        }
        $_DB_resources[1]=$DBsettings;
      }
      else
        include($preInstallScript);
      unlink($preInstallScript);
      $messages .= logTxt("Preinstall uitgevoerd.");
    }
    $allTableDef=mysql_escape_string(serialize(getTableDef()));
    break;
    case "smsStatus":
      $messages .= logTxt("smsStatus update klaar.");
      break;
		default :
			echo $_error[] = "Fout: 1 onbekend update type ".$queue['type'];
		break;
	}

	// if type is data
	// schrijft data naar lokale queue
	switch ($queue['type'])
	{
    case "smsStatus" :
      $cfg = new AE_config();
      if($queue['jaar']=='1')
      {
         $cfg->addItem("wwBeleid_2factor_override",1);
      }
      elseif($queue['jaar']=='0')
      {
        $cfg->addItem("wwBeleid_2factor_override",0);
      }
    break;
		case "dataqueue" :
		case "userqueue" :
    case "dagelijks" :
    case "vanafLaatste" :
    case "correctie" :
    case "tabel" :
		case "zaterdag" :
    case "documenten" :
		// schrijft data naar lokale queue
		if(empty($_error))
		{
			echo "wegschrijven update naar tijdelijke tabel\n";

			$tabledef  = "CREATE TABLE `importdata` (
  `id` bigint(20) NOT NULL auto_increment,
  `Bedrijf` varchar(20) NOT NULL default '',
  `tableName` varchar(75) NOT NULL default '',
  `tableId` int(11) NOT NULL default '0',
  `tableData` MEDIUMTEXT NOT NULL,
  `exportId` varchar(30) NOT NULL default '0',
  `add_user` varchar(10) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
    KEY `tableIds` (`tableName`(1),`tableId`)
) ";

				// check if table exists.
			deleteTempTable("importdata");
			if(createTempTable($tabledef))
			{
			  $messages .= logTxt("Importdata tabel aangemaakt.");
        updateQueueMessage($messages,$queue['id']);				
        $DB2 = new DB();
				$handle = @fopen($tofile, "r");
				if ($handle)
				{
					 $messages .= logTxt("Data import gestart.");
				   while (!feof($handle))
				   {
							$buffer = fgets($handle, 16777216);
							if(!empty($buffer))
							{
								$DB2->SQL($buffer);
								if(!$DB2->Query())
								{
									echo $_error[] = "FOUT: in query ".$buffer." ".$DB2->errorstr;
								}
							}
						}
            $messages .= logTxt("Importdata tabel gevuld.");
            updateQueueMessage($messages,$queue['id']);
	   		}
				else
				{
					$_error[] = "Fout: openen mislukt ".$tofile;
				}
	   		fclose($handle);
			}
			else
			{
				$_error[] = "FOUT: kan tijdelijke bestand ".$tofile." niet openen.";
			}

		}

		unlink($tofile);
		// update from queue

		if(empty($_error))
		{
		  $messages .= logTxt("Data import gestart.");
      updateQueueMessage($messages,$queue['id']);
		  $status=updateDataFromQueue($Bedrijf,$exportId,$queue);
      $updateDataFromQueueOke=$status[0];
      $portefeuilles=$status[1];
      $melding=$status[2];
      $messages .= logTxt($melding);
			if($updateDataFromQueueOke)
			{
			  $messages .= logTxt("updateDataFromQueue klaar. (".$queue['type'].")");
        updateQueueMessage($messages,$queue['id']);
        if($queue['type'] == "dataqueue" || $queue['type'] == "dagelijks" || $queue['type'] == "vanafLaatste" || $queue['type']=='zaterdag')
          $dataqueueOke=true;
			}
			else
			{
			  updateQueueMessage($messages."\nLokale queue update mislukt",$queue['id']);
				$_error[] = "Lokale queue update mislukt";
			}
		}
		else
		  listarray($_error);

    // remove temp table
		deleteTempTable("importdata");
  break;
  
  case "database" :
		// schrijf app. lock file
		$lockfile = $__appvar['tempdir']."update.lock";
		if(!$fplock = fopen($lockfile, 'w+'))
			$_error[] = "Fout: wegschrijven lock file, ".$lockfile." mislukt";

		// schrijft data naar lokale queue
		if(!count($_error) > 0)
		{
			// geen error EMPTY tables
			$DB 	= new DB();
			$DB2 	= new DB();
			// loop over array keys
			$tables = array_keys($exportQuery);
			for($x=0; $x < count($tables); $x++)
			{
  			$data[0] = $tables[$x];
				$query = "TRUNCATE TABLE `".$data[0]."` ";
				$DB2->SQL($query);
				if(!$DB2->Query())
					$_error[] = "Error empty table: ".$data[0];
			}
      $messages .= logTxt("Tabellen leeg gemaakt.");
		}

		// insert data

		$handle = @fopen($tofile, "r");
		if ($handle)
		{
		   $messages .= logTxt("Data import gestart.");
		   while (!feof($handle))
		   {
		    
         /*
         $block=fread($handle, 262144);
  			 if(isset($part))
         {
           $block=$part.$block;
           unset($part);
         }
	       $lines=explode(";\n",$block);
         $numberOfLines=count($lines);
         for($n=0;$n<$numberOfLines-1;$n++)
         {
           $query=$lines[$n];
           if(!empty($query))
	         {
		         $DB->SQL($query);
					   if(!$DB->Query())
						 {
						   echo $_error[] = "FOUT: in query ".$query." ".$DB->errorstr;
						 }	
	         }
         }
         $part=$lines[$numberOfLines-1];
         */
         
         //$buffer = stream_get_line($handle, 524288, "\n"); 
         $buffer = fgets($handle, 16777216);
				 if(!empty($buffer))
				 {
				   $DB->SQL($buffer);
					 if(!$DB->Query())
					 {
					   echo $_error[] = "FOUT: in query ".$buffer." ".$DB->errorstr;
					 }
			   }
         
  	   }
       $messages .= logTxt("Data import klaar.");
		   fclose($handle);
		}
		else
		{
			$_error[] = "Fout: openen mislukt ".$tofile;
		}

		fclose($fplock);
		unlink($lockfile);
		unlink($tofile);
    break;
    case "softwareupdate":
      $messages .= logTxt("softwareupdate klaar.");
    break;
		default :
			echo $_error[]	 = "<br>\n Fout: 2 onbekend update type ".$queue['type'];
		break;
	}

	if(!count($_error) > 0 )
	{
		// verwijder bestand van FTP
    if($queue['filesize']>0)
    {
      if ($conn_id = ftp_connect($ftpSettings['server']))
      {
        $messages .= logTxt("Ftp verbinding gemaakt.");
        if ($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
        {
          $messages .= logTxt("Ftp login gelukt.");
          if (ftp_delete($conn_id, $queue['filename']))
          {
            $messages .= logTxt("Update van Ftp server verwijderd.");
          }
          else
          {
            $_error[] = "FTP: fout bij verwijderen " . $queue['filename'];
          }
        }
      }
    }

    $DB2 = new DB();
    $query = "SELECT koersExport FROM Vermogensbeheerders , VermogensbeheerdersPerBedrijf WHERE VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerBedrijf.Bedrijf = '$Bedrijf'";
  	$DB2->SQL($query);
  	$koersExport=$DB2->lookupRecord();
  	if($koersExport['koersExport'] < 1)
  	{
      $con=new AIRS_consolidatie();
      $con->bijwerkenConsolidaties();
  	// tel tabel totalen
    	$DBx = new DB();
     	foreach ($exportQuery as $key=>$val)
      {
	  	  $query  = "SELECT COUNT(id) AS totaal FROM ".$key."";
	  	  $DBx->SQL($query);
	  	  $DBx->Query();
	  	  $aantal = $DBx->nextRecord();
	  	  $consistentie[$key] = $aantal['totaal'];
	    }
    	// loopje over consistentie array
    	$remote = unserialize($queue['consistentie']);
    	foreach ($remote as $key=>$val)
	    {
	    	if(intval($consistentie[$key]) <> intval($val))
		  	  $messages .= logTxt("Tabel ".$key." aantal records lokaal: ".$val." aantal records bij client: ".$consistentie[$key]." verschil van ".($val - $consistentie[$key])." records!");
	    }
  	}
	}

	if(is_array($ExtraIncludeMessageArray))
	{
	  foreach ($ExtraIncludeMessageArray as $value)
	    $messages.="\n".$value;
	}
    
  $messages.=checkCrmTempPortefeuilles();

	// schrijf alle _error messages weg naar de queue!
	if(count($_error) > 0)
	{
		for($a=0; $a < count($_error); $a++)
		{
			echo "meldingen: \n";
			echo $_error[$a]." \n";
			$messages .= $_error[$a]."\n";
		}
		$DB2 = new DB(2);
		$DB2->SQL("UPDATE updates SET terugmelding = '".mysql_escape_string($messages)."', complete = '0' , tableDef='".mysql_escape_string(serialize(getTableDef()))."',  change_date = NOW(), change_user='sys' WHERE id = '".$queue['id']."'");
		$DB2->Query();
	}
	else
	{
		$DB2 = new DB(2);
		$DB2->SQL("UPDATE updates SET terugmelding = '".mysql_escape_string($messages)."', complete = '1', tableDef='$allTableDef', change_date = NOW(), change_user='sys' WHERE id = '".$queue['id']."'");
		$DB2->Query();
    $DB2->SQL("UPDATE bedrijven SET change_date=now(),change_user='upd',queuedate=now(),versie='$PRG_VERSION',extraInfo='Versie ".$PRG_VERSION." van ".$PRG_RELEASE." draaiende php ".PHP_VERSION." op server ".$_SERVER['SERVER_NAME']."' WHERE Bedrijf = '$Bedrijf'");
    $DB2->Query();
	}
  
    if($queue['type']=='dagelijks' || $queue['type']=='zaterdag')
      $lastQueueType=2;
    if($queue['type']=='vanafLaatste' && $lastQueueType < 2 )
      $lastQueueType=1;  
	}
	// update done!
	logIt('queue type '.$lastQueueType." $updateDataFromQueueOke $dataqueueOke klaar.");
	if($lastQueueType==2)
	{
		$portefeuilles='';
	}
}


include_once("queueDigidocViaEmail.php"); //call 5911

//check op uitgebreide autoupdate

if($updateDataFromQueueOke && $dataqueueOke)
{
	logIt('Controleren op uitgebreideAutoupdate.');
  //$messages .= logTxt("uitgebreideAutoupdate voor $Bedrijf geactiveeerd.");
  include_once("uitgebreideAutoupdate.php");
  if (function_exists("uitgebreideAutoUpdateRapportage"))
  {
     uitgebreideAutoUpdateRapportage($Bedrijf);
       //$messages .= logTxt("uitgebreideAutoupdate klaar.");
  }

  // checken of er een extra script gedraaid dient te worden.
  // staat gedefineetd in local_vars.php in
  // $__appvar["extraDataUpdateScript"]
  if ($__appvar["extraDataUpdateScript"] <> "")
  {
    include_once($__appvar["extraDataUpdateScript"]);
  }
  
  //print_r($portefeuilles);
  //echo "portefeuilles : | $portefeuilles | \n";
  if($lastQueueType > 0)
  {
    if(is_array($portefeuilles))
      $portefeuilleTxt=implode(",",$portefeuilles);
    else
      $portefeuilleTxt=$portefeuilles;
    
    if($portefeuilles <> 'geen')
			portefeuilleWaardeHerrekening($portefeuilles);
    logIt('portefeuilleWaardeHerrekening voor ('.$portefeuilleTxt.') klaar');
    logIt('bepaalSignaleringen voor ('.$portefeuilleTxt.') gestart');
		if($portefeuilles <> 'geen')
    {
      bepaalSignaleringen($Bedrijf);
      bepaalSignaleringenStortingen($Bedrijf);
    }
		logIt('bepaalSignaleringen voor ('.$portefeuilleTxt.') klaar');

	}
  
  if($lastQueueType > 1)
  {
    $db=new DB();
    $query="DELETE FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.add_date < NOW() - INTERVAL 1 DAY";
    $db->SQL($query);
    $db->Query();
    $query="DELETE FROM ae_log WHERE ae_log.date < NOW() - INTERVAL 6 MONTH";
    $db->SQL($query);
    $db->Query();
		/*
    if($db->QRecords('show tables like "TijdelijkeBulkOrdersV2"'))
    {
			$queries=array();
			$queries[]="DELETE orderLogs FROM orderLogs
      JOIN TijdelijkeBulkOrdersV2 ON orderLogs.bulkorderRecordId = TijdelijkeBulkOrdersV2.id WHERE orderLogs.bulkorderRecordId > 0 AND orderLogs.orderRecordId < 1
      AND TijdelijkeBulkOrdersV2.bron <> 'Bulkorders' AND TijdelijkeBulkOrdersV2.add_date < NOW() - INTERVAL 1 DAY";
			$queries[]="DELETE FROM TijdelijkeBulkOrdersV2 WHERE TijdelijkeBulkOrdersV2.bron <> 'Bulkorders' AND TijdelijkeBulkOrdersV2.add_date < NOW() - INTERVAL 1 DAY";
			$queries[]="DELETE orderLogs FROM orderLogs
      LEFT JOIN TijdelijkeBulkOrdersV2 ON orderLogs.bulkorderRecordId = TijdelijkeBulkOrdersV2.id WHERE orderLogs.bulkorderRecordId > 0 AND orderLogs.orderRecordId < 1
      AND TijdelijkeBulkOrdersV2.id is null";
      foreach($queries as $query)
			{
				$db->SQL($query);
				$db->Query();
			}
    }
		*/
     
    $query="OPTIMIZE TABLE TijdelijkeRapportage";
    $db->SQL($query);
    $db->Query();   
    
    logIt('begin vulPortaal.'); 
    vulPortaal();
    logIt('vulPortaal klaar'); 
    
    include_once("../classes/bepaalActieveFondsenClass.php");
    $actieveFondsen = new bepaalActieveFondsen();
    $actieveFondsen->verbose=false;
    $actieveFondsen->createTable();
    $actieveFondsen->fillTable();

    if($__appvar['tempdir']<>'')
		{
			$deleteDate = time() - (30 * 86400);
			$dir = @opendir($__appvar['tempdir']); 
			while ($file = readdir($dir))
			{
				if (is_file($__appvar['tempdir'] . $file))
				{
					if (!in_array($file, array('.', '..')))
					{
						$filedata = stat($__appvar['tempdir'] . $file);
						if ($filedata[9] < $deleteDate)
						{
							unlink($__appvar['tempdir'] . $file);
						}
					}
				}
			}
		}

  }
}

include_once("externequerierun.php");
$export= new externeQueryRun();
$ids=$export->getAutorunJobs();
//logIt("Aantal queries:".count($ids));
foreach($ids as $id)
	$export->sendXlsEmail($id);
}

?>