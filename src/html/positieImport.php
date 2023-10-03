<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2019/08/23 11:39:23 $
File Versie					: $Revision: 1.2 $

$Log: positieImport.php,v $
Revision 1.2  2019/08/23 11:39:23  cvs
call 8024

Revision 1.1  2006/04/28 09:13:42  cvs
*** empty log message ***



*/
include_once("wwwvars.php");
include_once ("../classes/AE_cls_fileUpload.php");
$upl = new AE_cls_fileUpload();

if($_POST['posted'])
{
  if (!$upl->checkExtension($_FILES['importfile']['name']))
  {
    echo vt("Fout: veboden bestandsformaat");
    exit;
  }

  $_bank = $_POST['bank'];
  // check filetype
  if($_FILES['importfile'][type] != "text/comma-separated-values" &&
     $_FILES['importfile'][type] != "text/x-csv" &&
     $_FILES['importfile'][type] != "application/octet-stream" &&
     $_FILES['importfile'][type] != "text/plain")
  {
    $_error = "<?= vt('FOUT: verkeerd bestandstype'); ?>(".$_FILES['importfile'][type]."), " . vt('alleen text bestanden zijn toegestaan.') . "";
  }
  // check error

  if($_FILES['importfile'][error] != 0)
  {
    $_error = "" . vt('Fout: bestand niet ingevuld of bestaat niet') . " (".$_FILES['importfile']['name'].")";
  }

  
  /*
  Als gilissen dan nog een bestand inlezen
  */
  if ($_bank == "gilis")
  {
    
    if($_FILES['importfile'][error] != 0)  // als fout foutmelding aanpassen
    {
      $_error = "" . vt('Fout: EXIBAL bestand niet ingevuld of bestaat niet') . " (".$_FILES['importfile']['name'].")";
    }
    // check filetype
    if($_FILES['importfile1'][type] != "text/comma-separated-values" &&
       $_FILES['importfile1'][type] != "text/x-csv" &&
       $_FILES['importfile1'][type] != "application/octet-stream" &&
       $_FILES['importfile1'][type] != "text/plain")
    {
      $_error = "" . vt('FOUT: verkeerd bestandstype') . "(".$_FILES['importfile1'][type]."), " . vt('alleen text bestanden zijn toegestaan.') . "";
    }
    // check error

    if($_FILES['importfile1'][error] != 0)
    {
      $_error = "" . vt('Fout: EXIPOS bestand niet ingevuld of bestaat niet') . " (".$_FILES['importfile1']['name'].")";
    }

  }
 
 
  $fileCopieOk = false;
  $file1CopieOk = false;
  if (empty($_error))
  {
    $importcode = date("YmdHi");  //datum als JJJJMMDDUUMM
    $importfile = $__appvar["basedir"]."/temp/positieimport1_".$importcode.".csv";
    if(move_uploaded_file($_FILES['importfile']['tmp_name'],$importfile))
    {
      $fileCopieOk = true;
    }
    else
     $_error = "Fout : upload error.";
    
    if ($_bank == "gilis" OR $_bank == "abn")
    {
      $importfile1 = $__appvar["basedir"]."/temp/positieimport2_".$importcode.".csv";
      if(move_uploaded_file($_FILES['importfile1']['tmp_name'],$importfile1))
      {
        $file1CopieOk = true;
      }
      else
        $_error = vt("Fout : upload error.");
    }
    
    
    if ($_error)
    {
      echo "Foutmelding: ".$_error;
      exit(); 
    }
    
    if (count(file($importfile)) > 0 AND $_bank <> "gilis" )
    {
      header('Location: positieImportVerwerk.php?bank='.urlencode($_bank).'&datum='.urlencode($_POST['datum']).'&file='.urlencode($importfile));
      exit();
    }
    if (count(file($importfile)) > 0 AND count(file($importfile1)) > 0)
    {
      $exe = 'Location: positieImportVerwerk.php?bank='.urlencode($_bank).'&datum='.urlencode($_POST['datum']).'&file='.urlencode($importfile).'&file1='.urlencode($importfile1);
      header($exe);
      exit();
    }
    // als target bestand leeg is
    $_error = "" . vt('Fout : bronbestand verkeerd geselecteerd of leeg') . " ($importfile).";
    // verwijder het lege bestand
    
    
    
  }

  echo template($__appvar["templateContentHeader"],$content);
  echo $_error;
  exit;
}


session_start();
$_SESSION[NAV] = "";
session_write_close();

$content = array();
$_bank = $_GET["bank"];


echo template($__appvar["templateContentHeader"],$content);

$DB = new DB();
if ($DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties") > 0)
{
	echo "<br>
<br>
" . vt('De tabel TijdelijkeRekeningmutaties is niet leeg') . " (".$DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties").")<br>
<br>
" . vt('de import is geannuleerd') . " ";
	exit;
}

?>
<form action="positieImport.php" enctype="multipart/form-data" method="POST"   name="controleForm" >
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="aanvullen" value="0" />
<input type="hidden" name="bank" value="<?=$_bank?>" />
<!-- Name of input element determines name in $_FILES array -->



<?php
switch ($_bank)
{
  case "stroeve":
  case "stroeveEigen":
    $banknaam = "Stroeve";
    break;
  case "gilis":
    $banknaam = "Gilissen";
    break;
  default:
    echo "geen bank opgegeven!";
    exit();
    break;
}
echo "<b>" . vt('Positie import') . " $banknaam</b><br><br>";
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";


if ($_bank == "gilis")
{
  $f1 = "EXIBAL";
  $f2 = "EXIPOS";

?>

<div class="form">
  <div class="formblock">
  <div class="formlinks"><?=$f1?> <?= vt('bestand'); ?> </div>
  <div class="formrechts">
    <input type="file" name="importfile" size="70">
    
  </div>
</div>  
<div class="form">  
  <div class="formblock">
  <div class="formlinks"><?=$f2?> <?= vt('bestand'); ?> </div>
  <div class="formrechts">
    <input type="file" name="importfile1" size="70">
  </div>
</div>
<?  
}
else
{
?>

<div class="form">
  <div class="formblock">
  <div class="formlinks"><?= vt('Importbestand'); ?> </div>
  <div class="formrechts">
    <input type="file" name="importfile" size="50">
  </div>
</div>
<?
}
?>
<div class="form">
<div class="formblock">
<div class="formlinks"><?= vt('datum'); ?> &nbsp;</div>
<div class="formrechts">
<input type="text" name="datum" value="<?=date("d-m-Y")?>" size="15">
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="button" value="Verwerken" onClick="document.controleForm.submit();">
&nbsp;&nbsp;&nbsp;&nbsp;
</div>
</div>

</div>
</form>

<?
echo template($__appvar["templateRefreshFooter"],$content);
?>