<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2017/04/13 13:05:18 $
File Versie					: $Revision: 1.1 $

$Log: advent_positie_convertUBS.php,v $
Revision 1.1  2017/04/13 13:05:18  cvs
no message





*/
include_once("wwwvars.php");

if($_POST['posted'])
{

  if($_FILES['MT535']['error'] != 0)
  {
    $_error[] = "Fout: MT535 bestand niet ingevuld of bestaat niet (".$_FILES['MT535']['name'].")";
  }

  if($_FILES['MT941']['error'] != 0)
  {
    $_error[] = "Fout: MT941 bestand niet ingevuld of bestaat niet (".$_FILES['MT941']['name'].")";
  }


  $fileCopieOk = false;
  $file1CopieOk = false;
  if (count($_error) == 0)
  {
    $importcode = date("YmdHi");  //datum als JJJJMMDDUUMM
    $importfile = $__appvar["basedir"]."/temp/MT535_".$importcode.".csv";
    if ( move_uploaded_file($_FILES['MT535']['tmp_name'],$importfile) )
    {
      $fileCopieOk = true;
    }
    else
      $_error[] = "Fout : MT5XX upload error.";

    $importfile1 = $__appvar["basedir"]."/temp/MT941_".$importcode.".csv";
    if ( move_uploaded_file($_FILES['MT941']['tmp_name'],$importfile1) )
    {
      $file1CopieOk = true;
    }
    else
      $_error[] = "Fout : MT940 upload error.";
  }

  if (count($_error) > 0)
  {
    echo "Foutmelding: ";
    for ($x=0; $x < count($_error); $x++)
    {
      echo "<br />$x: ".$_error[$x];
    }
    exit();
  }


  if (count(file($importfile)) > 0 AND count(file($importfile1)) > 0)
  {
    $exe = 'Location: advent_positie_convertVerwerkUBS.php?MT535='.urlencode($importfile).'&MT941='.urlencode($importfile1);
    header($exe);
    exit();
  }
  
  echo template($__appvar["templateContentHeader"],$content);
  echo "Fout : bronbestand verkeerd geselecteerd of leeg (MT535/MT941).";
  exit;
}


session_start();
$_SESSION[NAV] = "";
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

echo "<b>UBS Conversie starten</b><br><br>";
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";



?>

<div class="form">
  <div class="formblock">
  <div class="formlinks">MT535 bestand </div>
  <div class="formrechts">
    <input type="file" name="MT535" size="70" />
  </div>
</div>

<div class="form">

<div class="formblock">
  <div class="formlinks">MT941 bestand </div>
  <div class="formrechts">
    <input type="file" name="MT941" size="70" />
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
    <iframe width="800" height="400" name="convertFrame" >meldingen..</iframe>
  </div>
</div>

</div>
<?
echo template($__appvar["templateRefreshFooter"],$content);
?>