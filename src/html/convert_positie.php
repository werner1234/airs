<?php
/* 	
  AE-ICT source module
    Author                 : $Author: cvs $
    Laatste aanpassing     : $Date: 2019/08/23 11:35:29 $
    File Versie            : $Revision: 1.4 $
 		
    $Log: convert_positie.php,v $
    Revision 1.4  2019/08/23 11:35:29  cvs
    call 8024

    Revision 1.3  2013/12/16 08:20:59  cvs
    *** empty log message ***

    Revision 1.1  2011/06/22 11:47:03  cvs
    *** empty log message ***

 		
 	
*/

include_once("wwwvars.php");
include_once("convert_functies.php");
include_once ("../classes/AE_cls_fileUpload.php");
$upl = new AE_cls_fileUpload();
session_start();
$_SESSION['NAV'] = "";
//session_write_close();
$content = array();
global $USR;


if ($_GET["deleteTemp"] == 1)
{
  $db = new DB();
  $query = "DELETE FROM TijdelijkePositieLijst WHERE add_user = '$USR'";
  $db->executeQuery($query);
}

$afshriftDatum = $_POST["afshriftDatum"];
// if poster \

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
		$_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile'][type]."), alleen .csv bestanden zijn toegestaan.";
	}
	// check error
	if($_FILES['importfile'][error] != 0)
	{
		$_error = "Fout: bestand niet ingevuld of bestaat niet (".$_FILES['importfile']['name'].")";
	}


	if (empty($_error) AND
      $upl->checkExtension($_FILES['importfile2']['name']) AND
      $upl->checkExtension($_FILES['importfile']['name']))
	{

		$importcode = date("YmdHis").$USR;  //datum als JJJJMMDDUUMM
		$importfile = $__appvar["basedir"]."/html/importdata/convert_".$_POST['bank']."_".$importcode.".csv";
//    $importfile2 = $__appvar["basedir"]."/html/importdata/convert_".$_POST['bank']."_".$importcode."_2.csv";
    move_uploaded_file($_FILES['importfile2']['tmp_name'],$importfile2);
		if(move_uploaded_file($_FILES['importfile']['tmp_name'],$importfile))
		{
		  $_POST["manualBoekdatum"] = $manualBoekdatum;
		  $_POST["file"] = $importfile;
		  $_POST["batchid"] = $importcode;
		  
      $extra = "";
			if (count(file($importfile)) > 0 )
			{
			 /*
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
        */
        $_POST["extra"] = $extra;
        $_SESSION["convert"]["_POST"] = $_POST;
		    $_SESSION["convert"]["_FILES"] = $_FILES;
			  header("Location: import/convert_positie_".$_POST['bank']."_import.php" );
			  exit();
			}
			// als target bestand leeg is
			$_error = "Fout : bronbestand verkeerd geselecteerd of leeg ($importfile).";
			// verwijder het lege bestand
			if (file_exists($importfile) ) unlink($importfile);
	//		if (file_exists($importfile2) ) unlink($importfile2);
      
		}
		else
			$_error = "Fout : upload error.";

	}
	echo template($__appvar["templateContentHeader"],$content);
	echo $_error;

	exit;
}

$db = new DB();
$query = " SELECT count(id) as id FROM TijdelijkePositieLijst WHERE add_user = '$USR'";
$db->SQL($query);
$tplRec = $db->lookupRecord();

echo template($__appvar["templateContentHeader"],$content);

if ($tplRec["id"] > 0)
{
?>  
  <br /><h3><?= vt('Tijdelijke conversiebestand is niet leeg!'); ?>, <br />er zijn <?=$tplRec["id"]?> <?= vt('items door u aangemaakt.'); ?></h3>
  <b><?= vt('Kies een actie'); ?> :</b><br /><br />
  &nbsp;&nbsp;<a href="<?=$PHP_SELF?>?deleteTemp=1" ><u><?= vt('Verwijder alle tijdelijke posities'); ?></u></a><br /><br />
  &nbsp;&nbsp;<a href="tijdelijkepositielijstList.php"><u><?= vt('Ga naar het tijdelijke positie overzicht'); ?></u></a>
<?  
exit();
}

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
	  return false;
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


<form enctype="multipart/form-data" action="<?=$PHP_SELF?>" method="POST"  name="editForm">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="posted" value="true" />
<!-- Name of input element determines name in $_FILES array -->
<br /><b><?= vt('Posities converteren'); ?></b><br /><br />
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>
<div class="form">
<div class="formblock">
<div class="formlinks"><?= vt('Importbestand'); ?> </div>
<div class="formrechts">
<input type="file" name="importfile" size="50" />
</div>
</div>




<div class="form">
  <div class="formblock">
  <div class="formlinks"> <?= vt('Vermogensbeheerder'); ?></div>
  <div class="formrechts">
    <select name="vermogensbeheerder">
<?
  //vermogensbeheerderSelector($_POST["vermogensbeheerder"])
?>  
      <OPTION value='PCO' ><?= vt('(PCO) Private CFO Services'); ?></OPTION>
    </select> 
  </div>
</div>  


<div class="form">
<div class="formblock">
  <div class="formlinks"> <?= vt('Welke bank'); ?></div>
  <div class="formrechts">
    <input type="radio" name="bank" value="bin" /> Binck &nbsp;&nbsp;<br />
    <input type="radio" name="bank" value="aab" /> ABN NL&nbsp;&nbsp;<br />
  </div>
</div>

<!--
<div class="form">
  <div class="formblock">
  <div class="formlinks">Afschriftdatum &nbsp;</div>
  <div class="formrechts">
    <input type="text" name="afshriftDatum" value="<?=$afshriftDatum?>" size="15"> dd-mm-jjjj (invullen overruled de datum in het bestand)
  </div>
</div>
-->

<div class="formblock">
  <div class="formlinks"> &nbsp;</div>
  <div class="formrechts">
    <button onclick="submitter();"> <?= vt('Converteer bestand'); ?> </button>
  </div>
</div>

</form>
</div>
<?
}
echo template($__appvar["templateRefreshFooter"],$content);
?>