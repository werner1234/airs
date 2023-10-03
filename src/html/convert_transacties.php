<?php
/* 	
  AE-ICT source module
    Author                 : $Author: cvs $
    Laatste aanpassing     : $Date: 2019/08/23 11:35:29 $
    File Versie            : $Revision: 1.3 $
*/


include_once("wwwvars.php");
session_start();
$_SESSION['NAV'] = "";
session_write_close();
$content = array();
global $USR;

// if poster
if($_POST['posted'])
{
	unset($manualBoekdatum);
  if(!empty($afshriftDatum))
	{
		$dd = explode($__appvar["date_seperator"],$afshriftDatum);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			$_error = "Fout: ongeldige afschriftdatum opgegeven";
		}
		else
		{
		  $manualBoekdatum = $dd[2]."-".$dd[1]."-".$dd[0];
		}
	}
	// check filetype
	if($_FILES['importfile'][type] != "text/comma-separated-values" &&
	   $_FILES['importfile'][type] != "text/x-csv" &&
	   $_FILES['importfile'][type] != "text/csv" &&
	   $_FILES['importfile'][type] != "application/octet-stream" &&
	   $_FILES['importfile'][type] != "application/vnd.ms-excel" &&
	   $_FILES['importfile'][type] != "text/plain")
	{
		$_error = "" . vt('FOUT: verkeerd bestandstype') . "(".$_FILES['importfile'][type]."), " . vt('alleen .csv bestanden zijn toegestaan.') . "";
	}
	// check error
	if($_FILES['importfile'][error] != 0)
	{
		$_error = "" . vt('Fout: bestand niet ingevuld of bestaat niet') . " (".$_FILES['importfile']['name'].")";
	}

  if ($_POST["bank"] == "abnbes")
  {
  	// check filetype
	  if($_FILES['importfile2'][type] != "text/comma-separated-values" &&
	     $_FILES['importfile2'][type] != "text/x-csv" &&
	     $_FILES['importfile2'][type] != "text/csv" &&
	     $_FILES['importfile2'][type] != "application/octet-stream" &&
	     $_FILES['importfile2'][type] != "application/vnd.ms-excel" &&
	     $_FILES['importfile2'][type] != "text/plain")
	  {
		  $_error = "" . vt('FOUT: verkeerd bestandstype') . "(".$_FILES['importfile2'][type]."), " . vt('alleen .csv bestanden zijn toegestaan.') . "";
  	}


	  // check error

	  if($_FILES['importfile2'][error] != 0)
	  {
		  $_error = "" . vt('Fout: bestand niet ingevuld of bestaat niet') . " (".$_FILES['importfile2']['name'].")";
	  }

  }

  if (!$upl->checkExtension($_FILES['importfile']['name']))
  {
    $_error[] = vt("Fout: veboden bestandsformaat");
  }

  if (!$upl->checkExtension($_FILES['importfile2']['name']))
  {
    $_error = vt("Fout: veboden bestandsformaat");
  }


	if (empty($_error))
	{
		$importcode = date("YmdHis").$USR;  //datum als JJJJMMDDUUMM
		$importfile = $__appvar["basedir"]."/html/importdata/transaktie_".$_POST['bank']."_".$importcode.".csv";
    $importfile2 = $__appvar["basedir"]."/html/importdata/transaktie_".$_POST['bank']."_".$importcode."_2.csv";
    move_uploaded_file($_FILES['importfile2']['tmp_name'],$importfile2);
		if(move_uploaded_file($_FILES['importfile']['tmp_name'],$importfile))
		{
      $extra = "";
			if (count(file($importfile)) > 0 )
			{
			  if ($_POST["bank"]  == "abnbec")
        {
          $_POST["bank"] = "abnbe";
          $extra = "&type=c";
        }
        if ($_POST["bank"]  == "abnbes")
        {
          $_POST["bank"] = "abnbe";
          $extra = "&type=s";
        }
			  header("Location: import/".$_POST['bank']."_convert.php?file=".urlencode($importfile).$extra."&manualBoekdatum=".$manualBoekdatum);
			  exit();
			}
			// als target bestand leeg is
			$_error = "" . vt('Fout : bronbestand verkeerd geselecteerd of leeg') . " ($importfile).";
			// verwijder het lege bestand
			if (file_exists($importfile) ) unlink($importfile);
		}
		else
			$_error = vt("Fout : upload error.");
	}
	echo template($__appvar["templateContentHeader"],$content);
	echo $_error;

	exit;
}

echo template($__appvar["templateContentHeader"],$content);
if(!$_FILES['importfile']['name'])
{

?>
<style>
#bestand2{
  display: none;


}
</style>
<script>
function toggleDiv(id)
{
  var e = document.getElementById(id);
  e.style.display = "inline";

}
function submitter()
{

	if (document.editForm.importfile.value == '')
	{
	  alert('Selecteer eerst een importbestand');
	  return;
	}

  function checkradio(group)
  {
    var bool = false;
    if (group.length)
    { // group
      for (var b = 0; b < group.length; b++)
      {
        if (group[b].checked)
        {
          bool = true;
        }
      }
    }
    else if (group.checked)
    {
      bool = true;
    }
    return bool;
  }

  if (!checkradio(document.editForm.bank)   )
  {
    alert('Geef aan van welke bank de import betreft');
  }
  else
    document.editForm.submit();
}
</script>


<form enctype="multipart/form-data" action="convert_transacties.php" method="POST"  name="editForm">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="posted" value="true" />
<!-- Name of input element determines name in $_FILES array -->
<b><?= vt('transactie conversie'); ?></b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>
<div class="form">
<div class="formblock">
<div class="formlinks"><?= vt('Conversie'); ?> </div>
<div class="formrechts">
<input type="file" name="importfile" size="50">
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> <?= vt('Welke bank'); ?></div>
<div class="formrechts">
<input type="radio" name="bank" value="stroeve">  TGB (bankview)&nbsp;&nbsp;<br>
<input type="radio" name="bank" value="binckv2"> Binck V2&nbsp;&nbsp;<br>
<input type="radio" name="bank" value="abn"> ABN-AMRO&nbsp;&nbsp;<br>
<input type="radio" name="bank" value="snssec"> SNS Securities&nbsp;&nbsp;<br>
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"><?= vt('Afschriftdatum'); ?> &nbsp;</div>
<div class="formrechts">
<input type="text" name="afshriftDatum" value="<?=$afshriftDatum?>" size="15"> dd-mm-jjjj (<?= vt('invullen overruled de datum in het bestand'); ?>)
</div>
</div>
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="button" value="importeren" onclick="submitter();">
</div>
</div>

</form>


</div>
<?
}
echo template($__appvar["templateRefreshFooter"],$content);
?>
?>