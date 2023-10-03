<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/08/23 11:36:09 $
    File Versie         : $Revision: 1.11 $

    $Log: CRM_naw_ImportFase2.php,v $
    Revision 1.11  2019/08/23 11:36:09  cvs
    call 8024

    Revision 1.10  2018/10/24 11:36:54  cvs
    call 6713

    Revision 1.9  2018/09/23 17:14:23  cvs
    call 7175

    Revision 1.8  2018/06/25 07:33:17  cvs
    call 6713

    Revision 1.7  2018/03/12 10:28:26  cvs
    call 6713

    Revision 1.6  2018/03/07 10:24:34  cvs
    kop aangepast

    Revision 1.5  2018/02/07 13:19:32  cvs
    call 6549

    Revision 1.4  2017/12/01 11:20:38  cvs
    check of tempdir bestaat, aanmaken indien niet aanwezig

    Revision 1.3  2017/11/17 08:03:57  cvs
    call 6145

    Revision 1.2  2017/11/13 13:31:21  cvs
    call 6145 bevindingen

    Revision 1.1  2017/11/08 07:31:26  cvs
    call 6145



*/

include_once("wwwvars.php");
include_once "../classes/AIRS_cls_CRM_naw_importHelper.php";
include_once ("../classes/AE_cls_fileUpload.php");
$upl = new AE_cls_fileUpload();
//debug($_GET,"GET");
//debug($_POST,"POST");
//debug($_SESSION["crmImportProfile"]);

$tmpl = new AE_template();
$tmpl->appendSubdirToTemplatePath("crmImport");

$_SESSION["NAV"]='';
$content[ ] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";
$msg = "";

$filename = "";
$reloadFromFase3 = false;
if (count($_SESSION["crmImportProfile"]) > 0)
{
  $data = $_SESSION["crmImportProfile"];
  $filename = $data["filename"];
  unset($data["columMapping"]);
  $reloadFromFase3 = true;

}
else
{
  $data=$_POST;
}

if ($data['koppelMethode'] == "airsId")
{
    $koppelMethode = "AIRS id";

    $jsValidate = '
      if ($("#import_id").val() == "")
      {
        alert("' . vt('Airs ID moet gekoppeld zijn') . '");
        $("#import_id").css("background","#FFCCCC").focus();
      }
    ';
}
else
{
  $koppelMethode = "Extern id";
  unset($import->crmFields["id"]);
  $jsValidate = '
      if ($("#import_externID").val() == "")
      {
        alert("' . vt('externID moet gekoppeld zijn') . '");
        $("#import_externID").css("background","#FFCCCC").focus();
      }
  ';
}

$profile = $data["profile"];
$import = new CRM_naw_importHelper($profile);

if ($reloadFromFase3)
{
  $import->settings = $_SESSION["crmImportProfile"];
}
if ($data["profileSaveName"] != "")
{
  $profile = $data["profileSaveName"];
  $import->profileNames[] = $profile;
  $import->saveProfileNames();
  $import->profile = $profile;
}

$import->setSetting("profile",$data["profile"]);
$import->setSetting("delimiter",$data["delimiter"]);
$import->setSetting("dateFormat",$data["dateFormat"]);
$import->setSetting("decimalChar",$data["decimalChar"]);
$import->setSetting("koppelMethode",$data["koppelMethode"]);
$import->saveProfile();

$db = new DB();

if (!$reloadFromFase3)
{
  $tempDir = $__appvar["basedir"]."/html/importdata";
  if (!is_dir($tempDir))
  {
    mkdir($tempDir, 0777);
  }

  $fileInfo = $_FILES["bestand"];
  $tmpFile  = $_FILES["bestand"]["tmp_name"];
  $filename = $__appvar["basedir"]."/html/importdata/crmimport_".rand(11111,99999);
  $csvDelimiter = ($data['delimiter'] == "tab")?"\t":$data['delimiter'];
  move_uploaded_file($tmpFile, $filename);
  if ($fileInfo["error"] == 0 AND $upl->checkExtension($_FILES['bestand']['name']))
  {
    ini_set('auto_detect_line_endings',TRUE);
    $handle = fopen($filename,'r');
    $row = 0;
    $header  = fgetcsv($handle, 8096, $csvDelimiter) ;
    $datarow = fgetcsv($handle, 8096, $csvDelimiter) ;
    fclose($handle);
    ini_set('auto_detect_line_endings',FALSE);
  }
  else
  {
    $msg = vt("Kan bestand niet lezen, probeer opnieuw");
    unlink($tmpFile);
  }
}
else
{
  $csvDelimiter = ($data['delimiter'] == "tab")?"\t":$data['delimiter'];
  ini_set('auto_detect_line_endings',TRUE);
  $handle = fopen($filename,'r');
  $row = 0;
  $header  = fgetcsv($handle, 8096, $csvDelimiter) ;
  $datarow = fgetcsv($handle, 8096, $csvDelimiter) ;
  fclose($handle);
  ini_set('auto_detect_line_endings',FALSE);
}

$fileRows = 0;
$countHandle = fopen($filename,'r');
while($row = fgetcsv($countHandle, 8096, $csvDelimiter) )
{
  if (count($row) > 1)
  {
    $fileRows++;
  }
}
fclose($countHandle);

$hTemp = array();
for ($x=0; $x < count($header); $x++)
{
  if (in_array($header[$x], $hTemp))
  {
    $msg .= "<br> " . vt('dubbele kolom in invoerbestand') . ": $header[$x]";
  }
  else
  {
    $hTemp[] = $header[$x];
  }

}

echo template($__appvar["templateContentHeader"],$content);
?>

<style>
  <?=$tmpl->parseBlockFromFile("crmImport.css");?>
  .btnRelease{
    background: red;
    padding: 2px;
    margin:0;
  }
  #dataTable th {
    text-align: left;
    background: rgba(20,60,90,1);
    color: #FFF;
    font-weight: normal;
  }
</style>

<div class="container">
  <h1><?= vt('Mapping import velden'); ?></h1>
<?
  if ($msg != "")
  {

    echo '<div style="color:red; background: beige; padding: 10px; max-width: 805px"><h3>meldingen:</h3>'.$msg.'</div>';
  }
?>

  <h2> <?= vt('Bestandsgrootte'); ?>: <?=$fileRows?> <?= vt('regels'); ?>, <?= vt('Inleesmethode'); ?>: <?=$koppelMethode?> </h2>
  <br/>
  <fieldset>
    <legend style="width: 500px; background: #EEE; color:#000; font-size: 1.2em;"><button id="btnPreview"><?= vt('open/dicht'); ?></button>&nbsp;&nbsp; <?= vt('Voorbeeld data uit bestande (kopregel met eerste dataregel)'); ?></legend>

    <table cellpadding="0" cellspacing="0" id="tablePreview">
      <tr><td class="head"><?= vt('veldnaam'); ?></td><td class="head">&nbsp;</td><td class="head"><?= vt('data'); ?></td></tr>
    <?
      for ($x=0; $x < count($header); $x++)
      {
        echo "\n<tr><td class='k1'>{$header[$x]}</td><td class='k2'>=</td><td class='k3'>{$datarow[$x]}</td>";
      }


    ?>
    </table>
  </fieldset>
<br/>
<br/>
  <button class="btnPrev"><?= vt('vorige'); ?></button>
  <button style="float: right;" class="btnSubmit"><?= vt('volgende'); ?></button>
<br/>
<br/>
  <form method="POST"  action="CRM_naw_ImportFase3.php" name="editForm" id="editForm">
<?
  if ($data["profileSaveName"] == "")
  {
  echo '<h2><input type="checkbox" value="1" name="saveProfile" checked> ' . vt('Profiel opslaan') . '</h2>';
  }
?>
  <hr/>
  <h2><?= vt('Mapping naar de AIRS CRM tabel'); ?></h2>

    <input type="hidden" name="profile" value="<?=$profile;?>" />
    <input type="hidden" name="delimiter" value="<?=$data['delimiter'];?>" />
    <input type="hidden" name="dateFormat" value="<?=$data['dateFormat'];?>" />
    <input type="hidden" name="dateDelimiter" value="<?=$data['dateDelimiter'];?>" />
    <input type="hidden" name="decimalChar" value="<?=$data['decimalChar'];?>" />
    <input type="hidden" name="koppelMethode" value="<?=$data['koppelMethode'];?>" />
    <input type="hidden" name="fileRows" value="<?=$fileRows?>" />
    <input type="hidden" name="filename" value="<?=$filename?>" />
    <input type="hidden" name="deleteFlds" id="deleteFlds" value="" />

    <table cellpadding="0" cellspacing="0" id="dataTable" >
      <thead>
      <tr>
        <th><?= vt('AIRS veldnaam'); ?></th>
        <th><?= vt('Import veldnaam'); ?></th>
        <th><?= vt('Veldtype'); ?></th>
      </tr>
      </thead>

      <tbody>
      <?=$import->fieldMappingHtml($header, $data['koppelMethode'])?>
      </tbody>
    </table>

    <br/>
    <br/>
      <button class="btnPrev"><?= vt('vorige'); ?></button>
      <button style="float: right;" class="btnSubmit"><?= vt('volgende'); ?></button>

  </form>

</div>

<script>
  $(document).ready(function(){

    $("#tablePreview").toggle(300);

    $("#btnPreview").click(function(e) {
      e.preventDefault();
      $("#tablePreview").toggle(300);
    });

    $(".btnRelease").click(function(e) {
      e.preventDefault();
      // alert($(this).data("id"));
      var oldval = $("#deleteFlds").val();
      var newval = $(this).data("id");
      $("#deleteFlds").val(oldval+","+newval);
      console.log(oldval+","+newval);
      $(this).closest( "tr" ).hide();

    });

    $(".btnPrev").click(function(e) {
      e.preventDefault();
      window.open("CRM_naw_ImportFase1.php","content");
    });

    $(".btnSubmit").click(function(e){
      e.preventDefault();
      <?=$jsValidate?>
      else
      {
        $("#editForm").submit();
      }
    });

  });
</script>

<?
echo template($__appvar["templateRefreshFooter"],$content);

function getLines($file)
{
  $f = fopen($file, 'rb');
  $lines = 0;

  while (!feof($f)) {
    $lines += substr_count(fread($f, 8192), "\n");
  }

  fclose($f);
  return $lines;
}
