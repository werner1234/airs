<?php
include_once("wwwvars.php");
global $__appvar;

$db=new DB();
$query="SHOW tables like 'CRM%'";
$db->SQL($query);
$db->Query();
$tables=array();
while($data=$db->nextRecord('num'))
  $tables[]=$data[0];

$query="SHOW tables like 'dd_%'";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord('num'))
  $tables[]=$data[0];

$tofile=$__appvar['tempdir']."CRMexport".$__appvar["bedrijf"].".sql";
$db=new DB();
$fp = fopen($tofile, 'w');
foreach ($tables as $table)
{
  echo "Exporteren `$table` <br>\n";
  $query="SELECT * FROM $table";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $query="INSERT INTO $table SET ";
    $n=0;
    foreach ($data as $key=>$value)
    {
      $komma='';
      if($n!=0)
        $komma=",";
      $n++;
      $query.="$komma $key=unhex('".bin2hex($value)."')";
    }
    fwrite($fp, $query.";\n");
  }
}
fclose($fp);
$dest=$tofile.'.gz';
if($fp_out=gzopen($dest,'wb'))
{
  if($fp_in=fopen($tofile,'r'))
  {
	  while(!feof($fp_in))
	    gzwrite($fp_out,fread($fp_in,1024*512));
	  fclose($fp_in);
	}
  gzclose($fp_out);
}

$ftpSettings['server'] = "toploader.adm.aeict.net";
$ftpSettings['path'] = "updates";
$ftpSettings['user'] = "airs";
$ftpSettings['password'] = "05airs!05";
echo 'Versturen van '.filesize($dest).' bytes naar FTP server.';
$outgoingFileName=basename($dest);
if($conn_id = ftp_connect($ftpSettings['server']))
{
  // login with username and password
	if($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
	{
		if (ftp_put($conn_id, $outgoingFileName, $dest, FTP_BINARY))
		{
			echo "<br>\n successfully uploaded $outgoingFileName\n";
		}
		else
		{
  		echo "<br>\n <b>There was a problem while uploading</b> $outgoingFileName\n";
		}
	}
	ftp_close($conn_id);
}
echo 'Done.';
?>