<?php
/*
    AE-ICT CODEX source module versie 1.3, 21 januari 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/10/18 15:28:00 $
    File Versie         : $Revision: 1.9 $

    $Log: tmpManager.php,v $
    Revision 1.9  2017/10/18 15:28:00  rvv
    *** empty log message ***

    Revision 1.8  2017/10/14 17:22:29  rvv
    *** empty log message ***

    Revision 1.7  2015/11/25 17:13:08  rvv
    *** empty log message ***

    Revision 1.6  2015/10/26 14:43:41  rvv
    *** empty log message ***

    Revision 1.5  2015/10/26 14:39:25  rvv
    *** empty log message ***

    Revision 1.4  2015/10/26 14:33:05  rvv
    *** empty log message ***

    Revision 1.3  2015/10/25 15:11:22  rvv
    *** empty log message ***

    Revision 1.2  2009/11/25 16:08:30  rvv
    *** empty log message ***

    Revision 1.1  2006/08/01 15:28:43  cvs
    *** empty log message ***



*/

//

include_once("wwwvars.php");
$days = 30;
$theDir = realpath(dirname(__FILE__)."/../temp/")."/";
$HiddenFiles = array(".","..","filemanage.php");
if (isset($_GET['action']) && $_GET['action'] == "delete")
{
  $deleteFile=str_replace(array('\\','/',':','?','"','<','>','|'),array('','','','','','','',''),$_GET["file"]);
  if (!unlink($theDir.$deleteFile))
  {
    echo "kon $deleteFile niet verwijderen";
  }
  else
  {
    echo "$deleteFile is verwijderd.";
  }
  echo "<hr>";
}
elseif (isset($_GET['action']) && $_GET['action'] == "download")
{
  $downloadFile=str_replace(array('\\','/',':','?','"','<','>','|'),array('','','','','','','',''),$_GET["file"]);
  $data=file_get_contents($theDir.$downloadFile);
  if (!$data)
  {
    echo "kon $downloadFile niet vinden.";
  }
  else
  {
		if(isset($HTTP_SERVER_VARS['HTTP_USER_AGENT']) and strpos($HTTP_SERVER_VARS['HTTP_USER_AGENT'],'MSIE'))
			Header('Content-Type: application/force-download');
		else
			Header('Content-Type: application/octet-stream');
		if(headers_sent())
			$this->Error('Some data has already been output to browser, can\'t send PDF file');
		Header('Content-Length: '.strlen($data));
		Header('Content-disposition: attachment; filename='.$downloadFile);
    echo $data;exit;
  }
  echo "<hr>";
}

if ((isset($argv[1]) &&  is_numeric($argv[1]) and $argv[1] > 1) or     // aanroep via commandline
    (isset($_GET['action']) && $_GET['action'] == "purge")              )   // aanroep via html
{
  if ($_GET['action'] == "purge")
    $deleteDate   = time() - ($_GET['days']*86400);
  else
    $deleteDate   = time() - ($argv[1]*86400);

  $dir = @opendir($theDir); // open the directory
	while($file = readdir($dir)) // loop once for each name in the directory
	{
		// if the name is not a directory and the name is not the name of this program file
		if(is_file($theDir.$file) )
		{
			$match = 0;
			foreach($HiddenFiles as $name) // for each value in the hidden files array
			{
				if($file == $name) // check the name is not the same as the hidden file name
				{
					$match = 1;	 // set a flag if this name is supposed to be hidden
				}
			}

			if(!$match) // if there were no matches the file should not be hidden
			{
					$filedata = stat($theDir.$file); // get some info about the file
					if ($filedata[9] < $deleteDate) unlink($theDir.$file);
			}
		}
	}

}

session_start();
$cnt = 0;

unset($_SESSION['submenu']);
unset($_SESSION['NAV']);


	$dir = @opendir($theDir); // open the directory
  $fileList=array();
  $fileListDatum=array();
  $fileListSize=array();
	while($file = readdir($dir)) // loop once for each name in the directory
	{
	  $fileList[]=$file;
		$filedata = stat($theDir.$file);
		$fileListDatum[$filedata[9] . $file]=$file;
		$fileListSize[1000000000+($filedata[7]). $file]=$file;
	}
  if($_GET['sort']=='naam')
	{
		sort($fileList);
	}
  elseif ($_GET['sort']=='datum')
	{
		ksort($fileListDatum);
		$fileList=$fileListDatum;
	}
  elseif($_GET['sort']=='grootte')
	{
		ksort($fileListSize);
		$fileList=$fileListSize;
	}

  $cnt=0;
  foreach($fileList as $file)
  {
		// if the name is not a directory and the name is not the name of this program file
		if(is_file($theDir.$file) )
		{
			$match = 0;
			foreach($HiddenFiles as $name) // for each value in the hidden files array
			{
				if($file == $name) // check the name is not the same as the hidden file name
				{
					$match = 1;	 // set a flag if this name is supposed to be hidden
				}
			}

			if(!$match) // if there were no matches the file should not be hidden
			{
					$filedata = stat($theDir.$file); // get some info about the file
					// find out if the file is one that can be edited
					if (strtolower(substr($file,-4)) == ".zip")
					  $files[$cnt]["zip"] = true;
					else
					  $files[$cnt]["zip"] = false;
					$files[$cnt]["name"] = $file;
					$size = (($filedata[7]/1024)/1024);
					$totalSize += $filedata[7];
					$unit = "Mb";
					if ($size < 1)
					{
					  $size = $size * 1024;
					  $unit = "Kb";
					}
					$files[$cnt]["size"] = number_format($size,3). " ".$unit;
					$files[$cnt]["date"] = date("d-m-Y H:i:s",$filedata[9]);
					$cnt++;
			}

		}
	}

	closedir($dir); // now that all the rows have been built close the directory

$subHeader     = "";
$mainHeader    = "";


echo template($__appvar["templateContentHeader"],$content);
?>

<div class="kop">
<h4>&nbsp;&nbsp; Filemanager: </h4>
&nbsp;&nbsp;<b><?=$_GET["msg"]?></b><br>
<br>

</div>
<br>
<br>
<?
echo "<table >";
echo "<tr>";
echo "  <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
echo "  <td ><a href='tmpManager.php?sort=naam'><b>Bestandnaam &nbsp;&nbsp;&nbsp;&nbsp;</a></b></td>";
echo "  <td align=\"right\" ><a href='tmpManager.php?sort=grootte'><b>Bestandsgrootte &nbsp;&nbsp;</b></a></td>";
echo "  <td><a href='tmpManager.php?sort=datum'><b>Bestandsdatum</b></a></td>";
echo "  <td> &nbsp;&nbsp;</td>";
echo "  <td> &nbsp;&nbsp;</td>";
echo "</tr>";
for ($x=0 ;$x < count($files);$x++)
{
  echo "<tr>";
  echo "  <td>&nbsp;&nbsp;<b>*</b>&nbsp;&nbsp;</td>";
  echo "  <td >".$files[$x]["name"]."&nbsp;&nbsp;&nbsp;&nbsp;</td>";
  echo "  <td align=\"right\" >".$files[$x]["size"]."&nbsp;&nbsp;</td>";
  echo "  <td>".$files[$x]["date"]."</td>";
  echo "  <td> &nbsp;->&nbsp;| <a class=\"link\" href=\"".$PHP_SELF."?action=delete&file=".urlencode($files[$x]["name"])."\" >verwijder</a> |</td>";
  echo "  <td> &nbsp;->&nbsp;| <a class=\"link\" href=\"".$PHP_SELF."?action=download&file=".urlencode($files[$x]["name"])."\" >download</a> |</td>";
  echo "</tr>";
}
echo "<tr>";
echo "  <td>&nbsp;&nbsp;<br><br><br><b></b>&nbsp;&nbsp;</td>";
echo "  <td ><b>totale mapgrootte&nbsp;&nbsp;</b></td>";
echo "  <td align=\"right\" ><b>".number_format(($totalSize/1024)/1024,3)."Mb&nbsp;&nbsp;</b></td>";
echo "  <td></td>";
echo "  <td> </td>";

  echo "</tr>";

echo "</table>";
?>
<hr>
<a class="link" href="<?=$PHP_SELF?>?action=purge&days=<?=$days?>" >verwijder alle bestanden ouder dan <?=$days?> dagen</a>
<hr>
<?
echo template($__appvar["templateRefreshFooter"],$content);
?>