<?php
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
include_once('../classes/pclzip.lib.php');
include_once('queueExportQuery.php');

if($__appvar['master'] == false)
  exit;


$bedrijven = array("AEI","RCN",'WWO','BCS','GDE','FCM');
$updateSoort=array('dagelijks'=>'Laatste twee weken',
                   'tabel'=>'Complete tabel');

if($_GET['posted']=='true')
{
  if($_GET['updateSoort']=='')
  {
    echo "Geen soort update gekozen.";exit;
  }
  

  $exportId = date("YmdHis");
  $stamp=$exportId;
	$file = "export_".$Bedrijf."_".$stamp.".sql"; 
  $exportQueryNew[$_GET['tabel']] = $exportQuery[$_GET['tabel']];
  $exportQuery = $exportQueryNew;
  if(count($exportQuery)<>1)
  {
    echo vt("Aantal tabellen te exporteren <> 1?");
    exit;
  }

  if($_GET['updateSoort'] == 'dagelijks')
    $queryValues['lastUpdate']=date('Y-m-d',time()-14*24*3600);
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

		echo "<br>" . vt('start exporteren') . " ".$key. " ".$aantal." " . vt('records') . " (" . vt('van') . " ".$aantalDb['aantal']." " . vt('records') . ")";
		$totaalRecords +=$aantal;
	}
  
  if($totaalRecords > 0 || ($_GET['updateSoort'] == 'tabel'))
	{
		if(!gzcompressfile($__appvar['tempdir'].$file))
		{
			$_error[] = "Fout: zippen van bestand mislukt!";
		}

    $outgoingFile=$__appvar['tempdir'].$file.".gz";
    $outgoingFileName=$file.".gz";
		unlink($__appvar['tempdir'].$file);

		//ftpdatabase($ftpSettings, $__appvar[tempdir].$file.".gz", $file.".gz");
		if(empty($_error))
		{
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
				$_error[] = "Could not connect";
		
    
    	if(!exportHeader($outgoingFileName))
				  $_error[] = "Could not insert update.";
          
			if(!empty($_error))
				listarray($_error);
		
			unlink($outgoingFile);
		}
		else
		{
		  listarray($_error);
		}
	// extra controle of update in queue staat
    $updateinQueue=false;
	  $DB = new DB(2);
	  $query = "SELECT id FROM updates WHERE filename = '".$outgoingFileName."'";
	  $DB->SQL($query);
	  $DB->Query();
	  if($DB->records() == 1)
	  {
	   echo "<br>" . vt('Update staat in de queue') . ".";
	   $melding .= "<br>Update staat in de queue.";
     $updateinQueue=true;
	  }
	  else
	  {
	   echo "<br><b>Update $outgoingFileName niet in de queue gevonden? </b>";
	   $melding .= "<br><b>Update $outgoingFileName niet in de queue gevonden? </b>";
	  }
  }
	else
	{
		 // geen update nodig, 0 records
		 unlink($outgoingFile);
		 echo "<br><br>0 Records gevonden, geen update nodig.<br><br>";
		 $melding .= "<br><br>0 Records gevonden, geen update nodig.";
	}
  
} 
  
session_start();
$_SESSION[NAV] = "";
session_write_close();

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

function exportHeader($filename)
{
	global $exportId, $Bedrijf, $USR, $ftpSettings, $__appvar, $consistentie, $jaar, $updateTimeStamp;

	$filesize = filesize($__appvar['tempdir'].$filename);
  $consistentie=array();
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
  $res = $DB->Query();

	return $res;
}

function exportTable($table, $query, $tofile, $toqueue = false)
{
	global $exportId, $Bedrijf,$USR;

	$DB1 = new DB(1);
	$DB1->SQL($query);
	//echo $query ."<br><br>\n\n";
	if(!$DB1->Query())
	{
		echo "<br>\n FOUT in query : ".$query;
		exit;
	}
	$aantal = $DB1->Records();

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


	while($tableData = $DB1->NextRecord())
	{
  	$data = serialize($tableData);
	  $q2 = "INSERT INTO importdata SET ";
	 	$q2 .= " Bedrijf = '".$Bedrijf."', ";
	  $q2 .= " tableName = '".$table."', ";
	  $q2 .= " tableId = '".$tableData['id']."', ";
	  $q2 .= " tableData = '".mysql_escape_string($data)."', ";
	  $q2 .= " exportId = '".$exportId."', ";
	  $q2 .= " add_user = '".$USR."', ";
	  $q2 .= " add_date = NOW() , ";
	  $q2 .= " change_user = '".$USR."', ";
	  $q2 .= " change_date = NOW() ;\n"; 
		fwrite($fp, $q2);
  }

	fclose($fp);
	return $aantal;
 }






echo template($__appvar["templateContentHeader"],$content);
?>

<form  method="GET" name='selectForm' id='selectForm' >
<input type="hidden" name="posted" value="true" />

<b><?= vt('Export data'); ?></b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>
<div class="form">
<div class="formblock">
<div class="formlinks"> <?= vt('Bedrijf'); ?></div>
<div class="formrechts">
<select name="Bedrijf" id="Bedrijf">
<?=SelectArray("",$bedrijven)?>
</select>
</div>
</div>


<div class="form">
<div class="formblock">
<div class="formlinks"> <?= vt('Soort update'); ?></div>
<div class="formrechts">
<select id="updateSoort" name="updateSoort">
<?=SelectArray("",$updateSoort,true)?>
</select>
</div>
</div>

<div class="form" id="tabelSelectie">
<div class="formblock">
<div class="formlinks"> <?= vt('Tabel'); ?></div>
<div class="formrechts">
<select name="tabel">
<option value="updateInformatie"><?= vt('updateInformatie'); ?></option>
<option value="handleidingenAIRS"><?= vt('handleidingenAIRS'); ?></option>
</select>
</div>
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="submit" value="Exporteren" >
</div>
</div>
</form>

</div>

<?


echo template($__appvar["templateRefreshFooter"],$content);
?>