<?php
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
include_once('../classes/pclzip.lib.php');
include_once('queueExportQuery.php');

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

	$cdata = serialize($consistentie);

	$query = "INSERT INTO updates SET ".
				 "  exportId = '".$exportId."' ".
				 ", Bedrijf = '".$Bedrijf."' ".
				 ", type = '".$_POST['updateSoort']."' ".
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
   	if($_POST['updateSoort'] == 'dagelijks' || $_POST['updateSoort'] == 'vanafLaatste')
    {
        if($_POST['updateSoort'] == 'dagelijks')
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


	if ($_POST['updateSoort'] == "tabel")
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
  elseif($_POST['updateSoort']=="correctie")
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


	switch ($_POST['updateSoort'])
	{
		case "dataqueue" :
		case "userqueue" :
    case "dagelijks" :
    case "vanafLaatste" :
    case "correctie" :
		case "zaterdag" :
    case "tabel" :
		if($toqueue == true)
		{
			$data = serialize($tableData);

			// insert Into Queue
      if($_POST['updateSoort']=="correctie")
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


$content = array();
echo template($__appvar["templateContentHeader"],$content);
$bedrijven=array();
foreach($_POST as $key=>$value)
{
	if(substr($key,0,8)=='bedrijf_')
	{
		$bedrijven[]=substr($key,8);
	}
}
//listarray($bedrijven);
//listarray($_POST);

?>
zaterdag export voor bedrijven: <?echo implode(',',$bedrijven)?>
<?
$DB = new DB();
$query = "SELECT NOW() as tijd";
$DB->SQL($query);
$DB->Query();
$data = $DB->NextRecord();
$updateTimeStamp = $data['tijd'];
echo " gestart om $updateTimeStamp";
//eerst constistentie controle

$exportQueryBackup=$exportQuery;
$exportQuery=array('Valutakoersen'=>$exportQueryBackup['Valutakoersen'],'Fondskoersen'=>$exportQueryBackup['Fondskoersen']);


foreach($bedrijven as $Bedrijf)
{
	$consistentie=array();
	$_error=array();
	$DB = new DB();
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

	//if($_POST['updateSoort'] == "vanafLaatste")
	//	$lastUpdate = $data['LaatsteUpdate'];
  //elseif($_POST['updateSoort'] == 'dagelijks')
  $lastUpdate=$data['laatsteDagelijkeUpdate'];
	//else
	//	$lastUpdate = 0;

	echo  "<br>\n Bijwerken tmpFondsenPerBedrijf $Bedrijf overgeslagen voor ".$_POST['updateSoort']." update.\n";

 // include_once('queueExportQueryKoers.php');

 	$query="SELECT * FROM Bedrijfsgegevens WHERE Bedrijf = '".$Bedrijf."' ";
	$DB->SQL($query);
  $oldUpdateStatus=$DB->lookupRecord(); 
  unset($oldUpdateStatus['id']);
  unset($oldUpdateStatus['Bedrijf']);

	$query = " SELECT DISTINCT(Rekeningmutaties.Valuta) ".
		"FROM Rekeningmutaties, Rekeningen, Portefeuilles, VermogensbeheerdersPerBedrijf ".
		"WHERE  ".
		"VermogensbeheerdersPerBedrijf.Bedrijf = '".$Bedrijf."' AND ".
		"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningen.Rekening = Rekeningmutaties.Rekening ";
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
	$nieuweFondsen=array();
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
	$fondsen=array();
	while($fondsData = $DB->NextRecord())
		$fondsen[] = $fondsData['Fonds'];

	$fondsenQuery = " IN('".implode("','",$fondsen)."')";
	if(count($fondsen)>0)
		$queryValues['fondsenQuery'] = $fondsenQuery;
	else
		$queryValues['fondsenQuery'] = " IN('') ";

	$stamp = $exportId;
	$file = "export_".$Bedrijf."_".$stamp.".sql";
	$queryValues['lastUpdate'] = $lastUpdate;
	$queryValues['Bedrijf'] = $Bedrijf;
	
	$totaalRecords=0;
  foreach($exportQuery as $key=>$val)
	{
	  $query  = buildQuery($key,$val,$queryValues);//	echo "<br> $query <br>\n";
		$aantal = exportTable($key,$query,$__appvar['tempdir'].$file, true);
		// voor consistentie controle
		$queryValues2 = 	$queryValues;
		$queryValues2['lastUpdate'] = 0;

		$query  = buildQuery($key,$val,$queryValues2,'count');
		$DBx = new DB();
		$DBx->SQL($query); //	echo "<br> $query <br>\n";
		$DBx->Query();
		$aantalDb=$DBx->nextRecord();
		$consistentie[$key] = $aantalDb['aantal'];//$DBx->records();

		echo "<br>start exporteren ".$key. " ".$aantal." records (van ".$aantalDb['aantal']." records)";
		$totaalRecords +=$aantal;
	}

	if($totaalRecords > 0 || ($_POST['updateSoort'] == 'tabel' || $_POST['updateSoort'] == 'correctie'))
	{
		if(!gzcompressfile($__appvar['tempdir'].$file))
		{
			$_error[] = "Fout: zippen van bestand mislukt!";
		}

		if($crypted>0)
		{
		  include_once('../classes/AE_cls_AES.php');
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

		unlink($__appvar['tempdir'].$file);


		if($_POST['exportType'] == "queue" && count($_error)<1)
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

			if(count($_error)<1)
			{
				if(!exportHeader($outgoingFileName))
				  $_error[] = "Could not insert update.";
			}
			else
			{
				// fout
				listarray($_error);
			}

			unlink($outgoingFile);
		}
		elseif(count($_error)<1)
		{
			$melding .= "<br><br><a href=\"pushFile.php?file=".$outgoingFileName."&filetype=gzip&action=attachment\"><b>download ".$outgoingFileName."</b></a>";
			echo "<br><br><a href=\"pushFile.php?file=".$outgoingFileName."&filetype=gzip&action=attachment\"><b>download ".$outgoingFileName."</b></a>";
		}

	// extra controle of update in queue staat
    $updateinQueue=false;
    if($_POST['exportType'] == "queue")
    {
	    $DB = new DB(2);
	    $query = "SELECT id FROM updates WHERE filename = '".$outgoingFileName."'";
	    $DB->SQL($query);
	    $DB->Query();
	    if($DB->records() == 1)
	    {
	     echo "<br>Update voor $Bedrijf staat in de queue.";
       $updateinQueue=true;
	    }
	    else
	    {
	     echo "<br><b>Update $outgoingFileName niet in de queue gevonden? </b>";
	    }
    }
	}
	else
	{
		 // geen update nodig, 0 records
		 unlink($outgoingFile);
		 echo "<br><br>0 Records gevonden voor $Bedrijf, geen update nodig.";
 	}
	$prb->hide();
}
echo "<br>Export klaar om ".date("Y-m-d H:i:s");

echo template($__appvar["templateRefreshFooter"],$content);
?>