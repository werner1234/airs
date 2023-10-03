<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2019/08/23 11:38:57 $
File Versie					: $Revision: 1.26 $

$Log: portefeuillesControle.php,v $
Revision 1.26  2019/08/23 11:38:57  cvs
call 8024

Revision 1.25  2011/03/04 07:14:58  cvs
*** empty log message ***

Revision 1.24  2011/01/08 16:08:44  rvv
*** empty log message ***

Revision 1.23  2010/11/03 16:25:11  rvv
*** empty log message ***

Revision 1.22  2010/11/03 10:43:23  cvs
*** empty log message ***

*/
include_once("wwwvars.php");
include_once ("../classes/AE_cls_fileUpload.php");
$upl = new AE_cls_fileUpload();

if($_POST['posted'])
{
  $_bank = $_POST['bank'];
  if (!$upl->checkExtension($_FILES['importfile']['name']))
  {
    echo vt("Fout: veboden bestandsformaat");
    exit;
  }
  if ($_bank <> "raboExcel")
  {
  // check filetype
  if($_FILES['importfile'][type] != "text/comma-separated-values" &&
     $_FILES['importfile'][type] != "text/x-csv" &&
     $_FILES['importfile'][type] != "text/csv" &&
     $_FILES['importfile'][type] != "application/octet-stream" &&
     $_FILES['importfile'][type] != "application/vnd.ms-excel" &&
     $_FILES['importfile'][type] != "text/plain")
  {
    $_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile'][type]."), alleen text bestanden zijn toegestaan.";
  }
  // check error

  if($_FILES['importfile'][error] != 0)
  {
    $_error = "Fout: bestand niet ingevuld of bestaat niet (".$_FILES['importfile']['name'].")";
  }

  }
  /*
  Als gilissen dan nog een bestand inlezen
  */
  if ($_bank == "gilis")
  {

    if($_FILES['importfile'][error] != 0)  // als fout foutmelding aanpassen
    {
      $_error = "Fout: EXIBAL bestand niet ingevuld of bestaat niet (".$_FILES['importfile']['name'].")";
    }
    // check filetype
    if($_FILES['importfile1'][type] != "text/comma-separated-values" &&
       $_FILES['importfile1'][type] != "text/x-csv" &&
       $_FILES['importfile1'][type] != "text/csv" &&
       $_FILES['importfile1'][type] != "application/octet-stream" &&
       $_FILES['importfile1'][type] != "text/plain")
    {
      $_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile1'][type]."), alleen text bestanden zijn toegestaan.";
    }
    // check error

    if($_FILES['importfile1'][error] != 0)
    {
      $_error = "Fout: EXIPOS bestand niet ingevuld of bestaat niet (".$_FILES['importfile1']['name'].")";
    }

  }
   /*
  Als SNS dan nog een bestand inlezen
  */
  if ($_bank == "bpere")
  {

    if($_FILES['importfile'][error] != 0)  // als fout foutmelding aanpassen
    {
      $_error = "Fout: Saldi bestand niet ingevuld of bestaat niet (".$_FILES['importfile']['name'].")";
    }
    // check filetype
    if($_FILES['importfile1'][type] != "text/comma-separated-values" &&
       $_FILES['importfile1'][type] != "text/x-csv" &&
       $_FILES['importfile1'][type] != "text/csv" &&
       $_FILES['importfile1'][type] != "application/octet-stream" &&
       $_FILES['importfile1'][type] != "text/plain")
    {
      $_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile1'][type]."), alleen text bestanden zijn toegestaan.";
    }
    // check error

    if($_FILES['importfile1'][error] != 0)
    {
      $_error = "Fout: Postitie bestand niet ingevuld of bestaat niet (".$_FILES['importfile1']['name'].")";
    }

  }
  /*
  Als SNS dan nog een bestand inlezen
  */
  if ($_bank == "sns")
  {

    if($_FILES['importfile'][error] != 0)  // als fout foutmelding aanpassen
    {
      $_error = "Fout: Saldi bestand niet ingevuld of bestaat niet (".$_FILES['importfile']['name'].")";
    }
    // check filetype
    if($_FILES['importfile1'][type] != "text/comma-separated-values" &&
       $_FILES['importfile1'][type] != "text/x-csv" &&
       $_FILES['importfile1'][type] != "text/csv" &&
       $_FILES['importfile1'][type] != "application/octet-stream" &&
       $_FILES['importfile1'][type] != "text/plain")
    {
      $_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile1'][type]."), alleen text bestanden zijn toegestaan.";
    }
    // check error

    if($_FILES['importfile1'][error] != 0)
    {
      $_error = "Fout: Postitie bestand niet ingevuld of bestaat niet (".$_FILES['importfile1']['name'].")";
    }

  }
     /*
  Als SNS securities dan nog een bestand inlezen
  */
  if ($_bank == "snssec")
  {

    if($_FILES['importfile'][error] != 0)  // als fout foutmelding aanpassen
    {
      $_error = "Fout: Saldi bestand niet ingevuld of bestaat niet (".$_FILES['importfile']['name'].")";
    }
    // check filetype
    if($_FILES['importfile1'][type] != "text/comma-separated-values" &&
       $_FILES['importfile1'][type] != "text/x-csv" &&
       $_FILES['importfile1'][type] != "text/csv" &&
       $_FILES['importfile1'][type] != "application/octet-stream" &&
       $_FILES['importfile1'][type] != "text/plain")
    {
      $_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile1'][type]."), alleen text bestanden zijn toegestaan.";
    }
    // check error

    if($_FILES['importfile1'][error] != 0)
    {
      $_error = "Fout: Postitie bestand niet ingevuld of bestaat niet (".$_FILES['importfile1']['name'].")";
    }

  }

  if ($_bank == "abn")
  {

    if($_FILES['importfile'][error] != 0)  // als fout foutmelding aanpassen
    {
      $_error = "Fout: 5XX bestand niet ingevuld of bestaat niet (".$_FILES['importfile']['name'].")";
    }
    // check filetype
    if($_FILES['importfile1'][type] != "text/comma-separated-values" &&
       $_FILES['importfile1'][type] != "text/x-csv" &&
       $_FILES['importfile1'][type] != "text/csv" &&
       $_FILES['importfile1'][type] != "application/octet-stream" &&
       $_FILES['importfile1'][type] != "text/plain")
    {
      $_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile1'][type]."), alleen text bestanden zijn toegestaan.";
    }
    // check error

    if($_FILES['importfile1'][error] != 0)
    {
      $_error = "Fout: 940 bestand niet ingevuld of bestaat niet (".$_FILES['importfile1']['name'].")";
    }

  }


  $fileCopieOk = false;
  $file1CopieOk = false;
  if (empty($_error))
  {
    $importcode = date("YmdHi");  //datum als JJJJMMDDUUMM
    $importfile = $__appvar["basedir"]."/temp/controle_".$importcode.".csv";
    if ($_bank <> "raboExcel")
    {
      if(move_uploaded_file($_FILES['importfile']['tmp_name'],$importfile))
      {
        $fileCopieOk = true;
      }
      else
       $_error = "Fout : upload error.";

      if ($_bank == "gilis" OR $_bank == "abn" OR $_bank == "snssec" OR $_bank == "sns" OR $_bank == "bpere" OR $_bank == "abnbe")
      {
        $importfile1 = $__appvar["basedir"]."/temp/controle_pos_".$importcode.".csv";
        if(move_uploaded_file($_FILES['importfile1']['tmp_name'],$importfile1))
        {
          $file1CopieOk = true;
        }
        else
          $_error = "Fout : upload error.";
      }
    }


    if ($_error)
    {
      echo "Foutmelding: ".$_error;
      exit();
    }

    if (count(file($importfile)) > 0 AND $_bank <> "gilis" AND $_bank <> "abn" AND $_bank <> "sns" AND $_bank <> "snssec" AND $_bank <> "bpere" AND $_bank <> "abnbe")
    {
      header('Location: controlePortefeuilles.php?bank='.urlencode($_bank).'&datum='.urlencode($_POST['datum']).'&uitvoer='.urlencode($_POST['uitvoer']).'&naar='.urlencode($_POST['naar']).'&file='.urlencode($importfile).'&Vermogensbeheerder='.urlencode($_POST['Vermogensbeheerder']));
      exit();
    }
    if (count(file($importfile)) > 0 AND count(file($importfile1)) > 0)
    {
      $exe = 'Location: controlePortefeuilles.php?bank='.urlencode($_bank).'&datum='.urlencode($_POST['datum']).'&uitvoer='.urlencode($_POST['uitvoer']).'&naar='.urlencode($_POST['naar']).'&file='.urlencode($importfile).'&file1='.urlencode($importfile1);
      header($exe);
      exit();
    }
    // als target bestand leeg is
    $_error = "Fout : bronbestand verkeerd geselecteerd of leeg ($importfile).";
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

?>
<form action="portefeuillesControle.php" enctype="multipart/form-data" method="POST"   name="controleForm" target="importFrame">
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
  case "gilisVt":
    $banknaam = "Gilissen VT";
    break;
  case "binck":
    $banknaam = "Binck bank";
    break;
  case "abn":
    $banknaam = "ABN-AMRO";
    break;
  case "abnbe":
    $banknaam = "ABN-AMRO Belgie";
    break;
  case "bpere":
    $banknaam = "BPERE";
    break;
  case "sns":
    $banknaam = "SNS";
    break;
  case "snssec":
    $banknaam = "SNS Securities";
    break;
  case "ant":
    $banknaam = "ANT";
    break;
  case "rabo":
    $banknaam = "Rabo";
    break;
  case "raboTrans":
    $banknaam = "Rabo Transacties";
    break;
  case "raboExcel":
    $banknaam = "Rabo via Excelmap";
    break;
  default:
    echo "geen bank opgegeven!";
    exit();
    break;
}
echo "<b>" . vt('Portefeuille controle') . " $banknaam</b><br><br>";
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";


if ($_bank == "gilis"  or
    $_bank == "abn"    or
    $_bank == "abnbe"  or
    $_bank == "sns"    or
    $_bank == "snssec" or
    $_bank == "rabo"   or
    $_bank == "bpere")
{
  switch ($_bank)
  {
    case "abn":
      $f1 = "MT5XX";
      $f2 = "MT940";
      break;
    case "abnbe":
      $f1 = "saldi";
      $f2 = "postities";
      break;
    case "gilis":
      $f1 = "EXIBAL";
      $f2 = "EXIPOS";
      break;
    case "bpere":
      $f1 = "saldi";
      $f2 = "postities";
      break;
    case "sns":
      $f1 = "saldi";
      $f2 = "postities";
      break;
    case "snssec":
      $f1 = "saldi";
      $f2 = "postities";
      break;
    case "rabo":
      $f1 = "saldi";
      $f2 = "postities";
      break;
  }

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
  if ($_bank <> "raboExcel")
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
}
?>
<div class="form">
<div class="formblock">
<div class="formlinks"><?= vt('datum'); ?> &nbsp;</div>
<div class="formrechts">
<input type="text" name="datum" value="<?if($bank=='gilisVt') echo jul2form(db2jul(getLaatsteValutadatum())); else echo date("d-m-Y");?>" size="15">
</div>
</div>


<?
if($bank=='gilisVt')
{
?>
<div class="form">
<div class="formblock">
<div class="formlinks"><?= vt('Vermogensbeheerder'); ?> &nbsp;</div>
<div class="formrechts">
<select type="select" name="Vermogensbeheerder" >
<option value=""> --- </option>
<?
  $DB = new DB();
  $DB->SQL("SELECT Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder");
  $DB->Query();
  while($gb = $DB->NextRecord())
  {
	echo "<option value=\"".$gb['Vermogensbeheerder']."\">".$gb['Vermogensbeheerder']."</option>  ";
  }
?>
</select>
</div>
</div>
<?
}
?>

<div class="form">
<div class="formblock">
<div class="formlinks"> <?= vt('uitvoer'); ?> &nbsp;</div>
<div class="formrechts">
<input type="radio" name="uitvoer" value="verschillen" checked> Verschillen
<input type="radio" name="uitvoer" value="alles" > Alles
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> <?= vt('naar'); ?> &nbsp;</div>
<div class="formrechts">
<input type="radio" name="naar" value="scherm" > <?= vt('scherm'); ?>
<input type="radio" name="naar" value="csv" > <?= vt('CSV (Excel)'); ?>
<input type="radio" name="naar" value="xls" checked > <?= vt('XLS (Excel)'); ?>
<input type="radio" name="naar" value="mutaties" > <?= vt('Tijdelijke rekeningmutaties'); ?>
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

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<iframe width="800" height="400" name="importFrame"></iframe>
</div>
</div>

</div>
<?
echo template($__appvar["templateRefreshFooter"],$content);
?>