<?php
/*
    AE-ICT CODEX source module versie 1.3, 21 januari 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/02/26 16:00:39 $
    File Versie         : $Revision: 1.5 $

    $Log: CRM_TemplateUpload.php,v $
    Revision 1.5  2011/02/26 16:00:39  rvv
    *** empty log message ***

    Revision 1.4  2010/12/01 18:07:03  rvv
    *** empty log message ***

    Revision 1.3  2010/10/09 14:50:08  rvv
    CRM in frame

    Revision 1.2  2009/10/25 08:37:21  rvv
    *** empty log message ***

    Revision 1.1  2009/01/20 17:47:35  rvv
    *** empty log message ***

    Revision 1.1  2006/08/01 15:28:43  cvs
    *** empty log message ***



*/

//
include_once("wwwvars.php");

$theDir = realpath(dirname(__FILE__))."/RTF_templates/";
$HiddenFiles = array(".","..");

$cfg=new AE_config();

if ($_GET["file"] <> "" && $_GET['action'] <> 'delete')
{
  $cfg->addItem('standaardbrief',$_GET["file"]);
  $standaardbrief=$_GET["file"];
}
else
  $standaardbrief = $cfg->getData('standaardbrief');


if($_GET['rtfDateFormat'])
$cfg->addItem('rtfDateFormat',addslashes($_GET['rtfDateFormat']));

$data['rtfDateFormat']=$cfg->getData('rtfDateFormat');

  if($_POST['posted'])
{
	if(empty($_FILES['importfile']['name']))
		$_error[] = "Fout: no Filename?";
	if($_FILES['importfile']['error'] != 0)
		$_error[] = "ErrorNumber: ".$_FILES['importfile']['error'];

	$importfile = $theDir.$_FILES['importfile']['name'];
	if(count($_error) == 0 && move_uploaded_file($_FILES['importfile']['tmp_name'],$importfile))
	{
	  $_error[] = "Upload voltooid.";

	}
	else
	 $_error[] = "Upload mislukt.";
}
elseif ($_GET['action'] == "delete")
{
  $deleteFile = $_GET["file"];
  if (!unlink($theDir.$deleteFile))
  {
    $_delete = "Kan bestand $deleteFile niet verwijderen.";
  }
  else
  {
    $_delete = "$deleteFile verwijderd.";
  }

}

session_start();
$cnt = 0;

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
					$files[$cnt]["date"] = date("d-m-Y",$filedata[9]);
					$cnt++;
			}

		}
	}

	closedir($dir); // now that all the rows have been built close the directory

$subHeader     = "";
$mainHeader    = "RTF Template beheer:";

$editcontent['javascript']="

function verwijderen(file,url)
{
   if(confirm ('Weet u het zeker om ' + file +' te verwijderen?'))
   {
     this.location=url;
   }
}
";


$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>$mainHeader</b> $subHeader </div><br><br>";
echo template($__appvar["templateContentHeader"],$editcontent);
if($_delete)
  echo "<b style=\"color:red;\">".$_delete."</b><br>";
?>


<form method="GET" >
<?
echo "<table >";
for ($x=0 ;$x < count($files);$x++)
{
  if($standaardbrief==$files[$x]["name"])
    $selected="checked";
  else
    $selected='';
  echo "<tr>";
  echo "  <td><input type=\"radio\" name=\"file\" value=\"".$files[$x]["name"]."\" $selected></td>";
  echo "  <td >".$files[$x]["name"]."&nbsp;&nbsp;&nbsp;&nbsp;</td>";
  echo "  <td align=\"right\" >".$files[$x]["size"]."&nbsp;&nbsp;</td>";
  echo "  <td>".$files[$x]["date"]."</td>";
  echo "  <td> &nbsp;->&nbsp;| <a class=\"link\" href=\"javascript:verwijderen('".$files[$x]["name"]."','".$PHP_SELF."?action=delete&file=".urlencode($files[$x]["name"])."')\" >remove</a>
  | <a class=\"link\" href=\"RTF_templates/".$files[$x]["name"]."\" >download</a> | </td>";

  echo "</tr>";
}
echo "<tr>";
echo "  <td>&nbsp;&nbsp;<br><br><br><b></b>&nbsp;&nbsp;</td>";
echo "  <td ><b>Totale mapgrootte&nbsp;&nbsp;</b></td>";
echo "  <td align=\"right\" ><b>".number_format(($totalSize/1024)/1024,3)."Mb&nbsp;&nbsp;</b></td>";
echo "  <td></td>";
echo "  <td> </td>";
echo "</tr>";

echo "</table>";
?>

  Datum weergave in sjablonen:
  <select name="rtfDateFormat">
    <OPTION VALUE="%d-%m-%Y" <?if($data['rtfDateFormat']=='%d-%m-%Y')echo "SELECTED";?>>01-01-2011
    <OPTION VALUE="%d %M %Y" <?if($data['rtfDateFormat']=='%d %M %Y')echo "SELECTED";?>>01 januari 2011
  </select>
<br><br>
<input type="button" name="standaard" value="Instellen opslaan" onclick="javascript:submit();">
</form>


<hr>

<div class="form">

<form enctype="multipart/form-data"  method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
<input type="hidden" name="posted" value="true" />
<b>Upload File</b><br><br>
<?php
if($_error)
{
foreach ($_error as $error)
	echo "<b style=\"color:red;\">".$error."</b><br>";
}
?>
<div class="form">
<div class="formblock">
<div class="formlinks">Bestand </div>
<div class="formrechts">
<input type="file" name="importfile" size="50">
</div>
</div>
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="submit" value="Verstuur">
</div>
</div>
</form>
</div>
<?
echo template($__appvar["templateRefreshFooter"],$editcontent);


?>


