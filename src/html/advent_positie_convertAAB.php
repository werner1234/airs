<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2019/08/23 11:33:59 $
File Versie					: $Revision: 1.2 $
*/
include_once("wwwvars.php");
include_once ("../classes/AE_cls_fileUpload.php");
$upl = new AE_cls_fileUpload();

if($_POST['posted'])
{

  if (!$upl->checkExtension($_FILES['MT5XX']['name']))
  {
     $_error[] = vt("Fout: MT5XX veboden bestandsformaat");
  }
    // check filetype
  if($_FILES['MT5XX']['type'] != "text/comma-separated-values" &&
     $_FILES['MT5XX']['type'] != "text/x-csv" &&
     $_FILES['MT5XX']['type'] != "text/csv" &&
     $_FILES['MT5XX']['type'] != "application/octet-stream" &&
     $_FILES['MT5XX']['type'] != "application/vnd.ms-excel" &&
     $_FILES['MT5XX']['type'] != "text/plain")
  {
    $_error[] = "" . vt('FOUT: verkeerd bestandstype') . "(".$_FILES['MT5XX']['type']."), " . vt('alleen text bestanden zijn toegestaan.') . "";
  }
  // check error

  if($_FILES['MT5XX']['error'] != 0)
  {
    $_error[] = "" . vt('Fout: MT5XX bestand niet ingevuld of bestaat niet') . " (".$_FILES['MT5XX']['name'].")";
  }

  if (!$upl->checkExtension($_FILES['MT940']['name']))
  {
    $_error[] = vt("Fout: MT940 veboden bestandsformaat");
  }
    // check filetype
  if($_FILES['MT940']['type'] != "text/comma-separated-values" &&
     $_FILES['MT940']['type'] != "text/x-csv" &&
     $_FILES['MT940']['type'] != "text/csv" &&
     $_FILES['MT940']['type'] != "application/octet-stream" &&
     $_FILES['MT940']['type'] != "text/plain")
  {
    $_error[] = "" . vt('FOUT: verkeerd bestandstype') . "(".$_FILES['MT940']['type']."), " . vt('alleen text bestanden zijn toegestaan.') . "";
  }
    // check error

  if($_FILES['MT940']['error'] != 0)
  {
    $_error[] = "" . vt('Fout: MT940 bestand niet ingevuld of bestaat niet') . " (".$_FILES['MT940']['name'].")";
  }


   

  $fileCopieOk = false;
  $file1CopieOk = false;
  if (count($_error) == 0)
  {
    $importcode = date("YmdHi");  //datum als JJJJMMDDUUMM
    $importfile = $__appvar["basedir"]."/temp/MT5XX_".$importcode.".csv";
    if ( move_uploaded_file($_FILES['MT5XX']['tmp_name'],$importfile) )
    {
      $fileCopieOk = true;
    }
    else
      $_error[] = vt("Fout : MT5XX upload error.");

    $importfile1 = $__appvar["basedir"]."/temp/MT940_".$importcode.".csv";
    if ( move_uploaded_file($_FILES['MT940']['tmp_name'],$importfile1) )
    {
      $file1CopieOk = true;
    }
    else
      $_error[] = vt("Fout : MT940 upload error.");
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


  if (count(file($importfile)) > 0 AND count(file($importfile1)) > 0)
  {
    $exe = 'Location: advent_positie_convertVerwerk.php?MT5XX='.urlencode($importfile).'&MT940='.urlencode($importfile1);
    header($exe);
    exit();
  }
  
  echo template($__appvar["templateContentHeader"],$content);
  echo vt("Fout : bronbestand verkeerd geselecteerd of leeg (MT5XX/MT940).");
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
<input type="hidden" name="aanvullen" value="0" />

<!-- Name of input element determines name in $_FILES array -->



<?php

echo "<b>" . vt('Conversie starten') . "</b><br><br>";
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";



?>

<div class="form">
  <div class="formblock">
  <div class="formlinks"><?= vt('MT5XX bestand'); ?> </div>
  <div class="formrechts">
    <input type="file" name="MT5XX" size="70" />
  </div>
</div>

<div class="form">

<div class="formblock">
  <div class="formlinks"><?= vt('MT940 bestand'); ?> </div>
  <div class="formrechts">
    <input type="file" name="MT940" size="70" />
  </div>
</div>

<div class="formblock">
  <div class="formlinks"> &nbsp;</div>
  <div class="formrechts">
    <input type="button" value="Start conversie" onClick="document.controleForm.submit();">
  </div>
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