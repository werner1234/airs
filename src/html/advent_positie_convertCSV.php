<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2019/08/23 11:33:59 $
File Versie					: $Revision: 1.3 $
*/
include_once("wwwvars.php");
include_once ("../classes/AE_cls_fileUpload.php");

$data = array_merge($_GET,$_POST);
$bank = $data["bank"];
$version = $data["version"];
$upl = new AE_cls_fileUpload();

if($_POST['posted'])
{
  if ($upl->checkExtension($_FILES['csvFile']['name']))
  {
    // check filetype
    if($_FILES['csvFile']['type'] != "text/comma-separated-values" &&
      $_FILES['csvFile']['type'] != "text/x-csv" &&
      $_FILES['csvFile']['type'] != "text/csv" &&
      $_FILES['csvFile']['type'] != "application/octet-stream" &&
      $_FILES['csvFile']['type'] != "application/vnd.ms-excel" &&
      $_FILES['csvFile']['type'] != "text/plain")
    {
      $_error[] = "" . vt('FOUT: verkeerd bestandstype') . "(".$_FILES['csvFile']['type']."), " . vt('alleen text bestanden zijn toegestaan.') . "";
    }
    // check error

    if($_FILES['csvFile']['error'] != 0)
    {
      $_error[] = "" . vt('Fout: CSV bestand niet ingevuld of bestaat niet') . " (".$_FILES['csvFile']['name'].")";
    }


    $fileCopieOk = false;
    $file1CopieOk = false;
    if (count($_error) == 0)
    {
      $importcode = date("YmdHi");  //datum als JJJJMMDDUUMM
      $importfile = $__appvar["basedir"]."/temp/{$bank}_".$importcode.".csv";
      if ( move_uploaded_file($_FILES['csvFile']['tmp_name'],$importfile) )
      {
        $fileCopieOk = true;
      }
      else
        $_error[] = "" . vt('Fout') . " : $bank " . vt('csvFile upload error.') . "";

    }

    if (count($_error) > 0)
    {
      echo "" . vt('Foutmelding') . ": ";
      for ($x=0; $x < count($_error); $x++)
      {
        echo "<br />$x: ".$_error[$x];
      }
      exit();
    }


    if (count(file($importfile)) > 0)
    {
      $exe = 'Location: advent_positie_convertVerwerkCSV.php?bank='.$bank.'&version='.$version.'&file='.urlencode($importfile);
      header($exe);
      exit();
    }
    $foutmelding = vt("Fout : bronbestand verkeerd geselecteerd of leeg .");
  }
  else
  {
    $foutmelding = vt("Fout : verboden bestandsformaat.");
  }


  echo template($__appvar["templateContentHeader"],$content);
  echo $foutmelding;
  exit;
}


session_start();
$_SESSION['NAV'] = "";
session_write_close();

$content = array();
echo template($__appvar["templateContentHeader"],$content);

?>
<form action="<?=$PHP_SELF?>" enctype="multipart/form-data" method="POST"   name="controleForm" target="convertFrame">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="bank" value="<?=$bank?>" />
<input type="hidden" name="version" value="<?=$_GET["version"]?>" />

<!-- Name of input element determines name in $_FILES array -->



<?php

echo "<b>" . vt('Conversie starten') . "</b><br><br>";
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";



?>

<div class="form">
  <div class="formblock">
  <div class="formlinks"><?=$bank?> <?= vt('bestand'); ?> </div>
  <div class="formrechts">
    <input type="file" name="csvFile" size="70" />
  </div>
</div>
<div class="formblock">
  <div class="formlinks"> &nbsp;</div>
  <div class="formrechts">
    <input type="button" value="Start conversie" onClick="document.controleForm.submit();">
  </div>
</div>
</form>

<div class="formblock">
  <div class="formlinks"> &nbsp;</div>
  <div class="formrechts">
    <iframe width="800" height="400" name="convertFrame" ><?= vt('meldingen'); ?>..</iframe>
  </div>
</div>

</div>
<?
echo template($__appvar["templateRefreshFooter"],$content);
?>