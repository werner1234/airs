<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/22 15:39:13 $
 		File Versie					: $Revision: 1.75 $

 		$Log: queueImportData.php,v $
 		Revision 1.75  2020/04/22 15:39:13  rvv
 		*** empty log message ***
 		
 		Revision 1.74  2020/02/26 16:10:59  rvv
 		*** empty log message ***
 		
 		Revision 1.73  2019/08/07 12:57:36  rvv
 		*** empty log message ***
 		
 		Revision 1.72  2019/08/07 12:24:15  rvv
 		*** empty log message ***
 		
 		Revision 1.71  2019/04/28 06:17:50  rvv
 		*** empty log message ***
 		
 		Revision 1.70  2018/11/17 17:25:16  rvv
 		*** empty log message ***
 		
 		Revision 1.69  2018/07/18 15:50:47  rvv
 		*** empty log message ***
 		
 		Revision 1.68  2018/02/14 16:50:16  rvv
 		*** empty log message ***
 		
 		Revision 1.67  2017/12/09 17:53:01  rvv
 		*** empty log message ***
 		
 		Revision 1.66  2017/10/21 17:28:59  rvv
 		*** empty log message ***
 		
 		Revision 1.65  2017/10/11 14:52:38  rvv
 		*** empty log message ***
 		
 		Revision 1.64  2017/09/24 10:03:47  rvv
 		*** empty log message ***
 		
 		Revision 1.63  2017/08/24 05:43:44  rvv
 		*** empty log message ***
 		
 		Revision 1.62  2017/06/14 16:09:18  rvv
 		*** empty log message ***
 		
 		Revision 1.61  2017/05/31 16:14:10  rvv
 		*** empty log message ***
 		
 		Revision 1.60  2017/04/22 16:42:29  rvv
 		*** empty log message ***
 		
 		Revision 1.59  2017/01/07 16:21:02  rvv
 		*** empty log message ***
 		
 		Revision 1.58  2015/03/11 16:53:42  rvv
 		*** empty log message ***
 		
 		Revision 1.57  2015/01/03 16:08:27  rvv
 		*** empty log message ***
 		
 		Revision 1.56  2014/12/20 13:28:52  rvv
 		*** empty log message ***
 		
 		Revision 1.55  2014/12/20 13:16:33  rvv
 		*** empty log message ***
 		
 		Revision 1.54  2014/11/08 18:35:29  rvv
 		*** empty log message ***
 		
 		Revision 1.53  2013/11/09 13:38:53  rvv
 		*** empty log message ***
 		
 		Revision 1.52  2013/10/26 15:39:54  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2013/10/12 15:49:59  rvv
 		*** empty log message ***



*/
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
include_once('../classes/pclzip.lib.php');
include_once('../classes/AIRS_consolidatie.php');

include_once('queueExportQuery.php');

function checkTables ($exportId, $controle)
{
	$DB = new DB();
	foreach($controle as $key=>$val)
	{
		$DB->SQL("SELECT COUNT(id) AS aantal FROM ".$key);
		$DB->Query();
		$records = $DB->NextRecord();

		echo $key;
		echo " master: ".$controle[$key];
		echo " client: ".$records[aantal];
		echo "<br>";
	}
}

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

function rmdirr($dir) {
   if($objs = glob($dir."/*")){
       foreach($objs as $obj) {
           is_dir($obj)? rmdirr($obj) : unlink($obj);
       }
   }
   rmdir($dir);
}


//copy many files
function copyFiles($source,$dest)
{
   $folder = opendir($source);
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
        	copyFiles($source.'/'.$file,$dest.'/'.$file);
       }
       else
       {
				if(!copy($source.'/'.$file,$dest.'/'.$file))
					$_error .= "Fout tijdens kopieren van".$dest."/".$file;
       }

   }
   closedir($folder);

   if(!empty($_error))
   	return $_error;

   return true;
}


$prb = new ProgressBar();	// create new ProgressBar
$prb->pedding = 2;	// Bar Pedding
$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
$prb->setFrame();          	                // set ProgressBar Frame
$prb->frame['left'] = 50;	                  // Frame position from left
$prb->frame['top'] = 	80;	                  // Frame position from top
$prb->addLabel('text','txt1','bezig met het downloaden van de update...');	// add Text as Label 'txt1' and value 'Please wait'
$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
$prb->show();

session_start();
$_SESSION[NAV] = "";
session_write_close();

$content = array();
echo template($__appvar["templateContentHeader"],$content);

if($__appvar["multipleDB"])
{
  $dbnaam=explode('_',$_SESSION["dbName"]);
  $Bedrijf = $dbnaam[1];
}
else
  $Bedrijf = $__appvar['bedrijf'];

$lastQueueType=0;
if(!empty($_POST))
{
	// select data from queue
	$DB = new DB(2);
	$q = "SELECT * FROM updates WHERE Bedrijf = '$Bedrijf' AND complete = '0' ORDER BY add_date ASC";
	$DB->SQL($q);
	$DB->Query();
	while($queue = $DB->nextRecord())
	{
	  $messages='';
	  $exportId = $queue['exportId'];
	  $DB2 = new DB(2);
    $DB2->SQL("UPDATE updates SET terugmelding = '', complete = '2', change_date = NOW() ,change_user='$USR' WHERE id = '".$queue['id']."'");
    $DB2->Query();
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
          // FTP pasv aanzetten indien nodig (plaats de var in local_vars.php
          if ($__appvar["ftpPasv"])
          {
            $messages .= logTxt("Ftp pasv set.");
            ftp_pasv($conn_id, true);
          }
          echo "\nDownloaden bestand gestart";
          if (ftp_get($conn_id, $localPath, $queue['filename'], FTP_BINARY))
          {
            // download OK
            $messages .= logTxt("Downloaden bestand gelukt.");
          }
          else
          {
            $_error[] = "FTP: download " . $queue['filename'] . " mislukt\n";
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
		    echo $_error[] = "<br>\n Unzip error : ".$archive->errorInfo(true);
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
		  	if(! $fout = copyFiles($__appvar['tempdir']."softwareUpdate/AIRS/",$__appvar["basedir"]."/"))
		  	{
			  	$_error[] = "Fout : kopieeren update mislukt ".$fout;
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
			//$messages .= "Zoeken naar ".$__appvar["basedir"]."/config/PREinstall.php\n";
      if (file_exists($preInstallScript))  // er bestaat een installatie updatescript dus uitvoeren
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
            $dbNaam=explode("_",$database[0]);
            $_DB_resources[1]['db']=$dbNaam[1];
            include($preInstallScript);
          }
          $_DB_resources[1]=$DBsettings;
        }
        else
        {
          include($preInstallScript);
          $messages .= logTxt("PREinstall uitgevoerd.\n");
        }
        unlink($preInstallScript);
      }
      $allTableDef=mysql_escape_string(serialize(getTableDef()));
  break;
		default :
			echo $_error[]	 = "<br>\n Fout: onbekend update type ".$queue[type];
		break;
	}
	// if type is data

	// schrijft data naar lokale queue
	switch ($queue['type'])
	{
		case "dataqueue" :
		case "userqueue" :
    case "dagelijks" :
    case "vanafLaatste" :
    case "correctie" :
    case "tabel" :
		case "zaterdag" :
    case "documenten" :

	if(empty($_error))
	{
		echo "<br>\n wegschrijven update naar tijdelijke tabel\n";
		//if($lines = file($tofile))
		//{
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
			$DB2 = new DB();
			$handle = @fopen($tofile, "r");
			if ($handle)
			{
			   while (!feof($handle))
			   {
						$buffer = fgets($handle, 16777216);
						if(!empty($buffer))
						{
							$DB2->SQL($buffer);
							if(!$DB2->Query())
							{
								echo $_error[] = "<br>\n FOUT: in query ".$buffer." ".$DB2->errorstr;
							}
						}
					}
          $messages .= logTxt("Importdata tabel gevuld.");
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
 


		unlink($tofile);
		// update from queue
		// verwijder file van ftp
		// update queue server

		if(empty($_error))
		{
		  $messages .= logTxt("Data import gestart.");
		  $status=updateDataFromQueue($Bedrijf,$exportId,$queue);
      $updateDataFromQueueOke=$status[0];
      $portefeuilles=$status[1];
      $message=$status[2];
      $messages .= logTxt($message);
			if($updateDataFromQueueOke)
      {
        $messages .= logTxt("updateDataFromQueue klaar.");
        if($queue['type'] <> 'documenten')
        {
          if (file_exists("uitgebreideAutoupdate.php"))
          {
            logIt("uitgebreideAutoupdate gestart.");
            $messages .= logTxt("uitgebreideAutoupdate gestart.");
            include_once("uitgebreideAutoupdate.php");
            uitgebreideAutoUpdateRapportage($Bedrijf);
            $messages .= logTxt("uitgebreideAutoupdate klaar.");
          }
          if ($__appvar["extraDataUpdateScript"] <> "")
          {
            include_once($__appvar["extraDataUpdateScript"]);
          }
        }

			}
			else
			{
				$_error[] = "Lokale queue update mislukt";
			}
		}

		// remove temp table
		deleteTempTable("importdata");
	}
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
			//$DB->SQL("SHOW TABLES");
			//$DB->Query();
			for($x=0; $x < count($tables); $x++)
			{
  			$data[0] = $tables[$x];
  			$query = "TRUNCATE TABLE `".$data[0]."` ";
				$DB2->SQL($query);
				if(!$DB2->Query())
					$_error[] = "Error empty table: ".$data[0];
			}
      $messages .= logTxt("Tabellen leeg gemaakt.");

			$handle = @fopen($tofile, "r");
			if ($handle)
			{
			   $messages .= logTxt("Data import gestart.");
			   while (!feof($handle))
			   {
			      $buffer = fgets($handle, 16777216); // sneller php5 $line = stream_get_line($handle, 16777216, "\n");
            if(!empty($buffer))
						{
							$DB->SQL($buffer);
							if(!$DB->Query())
							{
								echo $_error[] = "<br>\n FOUT: in query ".$buffer." ".$DB->errorstr;
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

		}

		fclose($fplock);
		unlink($lockfile);
		unlink($tofile);
    break;
    case "softwareupdate":
      $messages .= logTxt("softwareupdate klaar.");
    break;
    case "smsStatus":
      $messages .= logTxt("smsStatus update klaar.");
      break;
		default :
			echo $_error[]	 = "<br>\n Fout: onbekend update type ".$queue[type];
		break;
	}

	if(!count($_error) > 0 )
	{
		// verwijder bestand van FTP
		if($conn_id = ftp_connect($ftpSettings['server']))
		{
		  $messages .= logTxt("Ftp verbinding gemaakt.");
			if($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
			{
		    $messages .= logTxt("Ftp login gelukt.");
				if (ftp_delete($conn_id, $queue['filename']))
				{
          $messages .= logTxt("Update van Ftp server verwijderd.");
				}
				else
				{
					$_error[] = "FTP: fout bij verwijderen ".$queue['filename'];
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
	  if($queue['type'] <> 'softwareupdate')
    {
      $con = new AIRS_consolidatie();
      $con->bijwerkenConsolidaties();
    }
  	// tel tabel totalen
    foreach($exportQuery as $key=>$val)
  	{
  		$query  = "SELECT COUNT(id) AS totaal FROM ".$key;
  		$DBx = new DB();
  		$DBx->SQL($query);
  		$DBx->Query();
  		$aantal = $DBx->nextRecord();
  		$consistentie[$key] = $aantal['totaal'];
  	}
  	// loopje over consistentie array
  	$remote = unserialize($queue['consistentie']);
    foreach($remote as $key=>$val)
  	{
  		if(intval($consistentie[$key]) <> intval($val))
  		{
  			$verschil[$key] = $val - $consistentie[$key];
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
			$messages .= $_error[$a]."\n";
			echo $_error[$a]."<br>";
		}
    
    if(db2jul($queue['change_date'])-db2jul($queue['add_date']) > 43200)
      $newStatus=3;
    else
      $newStatus=0;  

		$DB2 = new DB(2);
		$DB2->SQL("UPDATE updates SET complete = '$newStatus', terugmelding = '".mysql_escape_string($messages)."', tableDef='".mysql_escape_string(serialize(getTableDef()))."', change_date = NOW(),change_user='$USR' WHERE id = '".$queue['id']."' ");
		$DB2->Query();
		//mail()
		echo "<br>\n Fout in update ".$exportId;
	}
	else
	{
		$consistentieVerschil = serialize($verschil);

		// update is OK!
		$DB2 = new DB(2);
 		$query = "UPDATE updates SET terugmelding = 'klaar',  tableDef='', complete = '1', change_date = NOW(),change_user='$USR' WHERE id = '".$queue['id']."' ";
		$DB2->SQL($query);
       
		$query = "UPDATE updates SET terugmelding = '".mysql_escape_string($messages)."',  tableDef='$allTableDef', ".
						 " complete = '1', ".
						 " change_date = NOW() ,change_user='$USR'".
						 " WHERE id = '".$queue['id']."' ";
		$DB2->SQL($query);
		$DB2->Query();
    $DB2->SQL("UPDATE bedrijven SET change_date=now(),change_user='upd',queuedate=now(),versie='$PRG_VERSION',extraInfo='Versie ".$PRG_VERSION." van ".$PRG_RELEASE." draaiende php ".PHP_VERSION." op server ".$_SERVER['SERVER_NAME']."' WHERE Bedrijf = '$Bedrijf'");
		$DB2->Query();
		echo "<br>\n Klaar met update ".$exportId;
    
    if($queue['type']=='dagelijks' || $queue['type']=='zaterdag')
      $lastQueueType=2;
    if($queue['type']=='vanafLaatste' && $lastQueueType < 2 )
      $lastQueueType=1;  
	}
	// update done!
}
	if($lastQueueType==2)
	{
		$portefeuilles='';
	}
}

//check op uitgebreide autoupdate
logIt('queue type '.$lastQueueType." $updateDataFromQueueOke");
if($updateDataFromQueueOke)
{
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
    
  }
}

echo template($__appvar["templateContentFooter"],$content);
$prb->hide();
?>