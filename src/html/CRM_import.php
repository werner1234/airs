<?php
/*
    AE-ICT sourcemodule created 08 feb. 2021
    Author              : Chris van Santen
    Filename            : CRM_import.php


*/

include_once("wwwvars.php");

//include_once "../classes/AIRS_cls_CRM_naw_importHelper.php";

$referer = end(explode("/",$_SERVER["HTTP_REFERER"]));

$_SESSION["NAV"]='';

$content = array();
$tmpl = new AE_template();
$tmpl->appendSubdirToTemplatePath("crmImport");
if ($_REQUEST["fase"] == 3)
{

  $success = 0;
  $failed = 0;

  foreach($_SESSION["crmImport"]["data"] as $data)
  {
    addCRM($data);
  }
?>
  <br>
  <h3>Items toegevoegd</h3>
  <ul>
    <li><?=$success?> toegevoegd</li>
    <li><?=$failed?> mislukt</li>
  </ul>

  <br/>
  <br/>
  <a href="index.php">Terug naar AIRS</a>
<?php
  exit;
}

if ($_REQUEST["fase"] == 2)
{
  $tempDir = $__appvar["basedir"]."/html/importdata";
  if (!is_dir($tempDir))
  {
    mkdir($tempDir, 0777);
  }

  $fileInfo = $_FILES["bestand"];
  $tmpFile  = $_FILES["bestand"]["tmp_name"];
  $filename = $__appvar["basedir"]."/html/importdata/crmimport_".rand(11111,99999);
  $csvDelimiter = "\t";
  $error = array();
  move_uploaded_file($tmpFile, $filename);
  if ($fileInfo["error"] == 0 )
  {
    ini_set('auto_detect_line_endings',TRUE);
    $handle = fopen($filename,'r');
    $row = 0;

    while ($rawData = fgetcsv($handle, 8096, $csvDelimiter))
    {
      $row++;

      if ($row == 1)
      {
        $header = $rawData;
        continue;
      }

      $data[] = $rawData;

    }
    fclose($handle);
    ini_set('auto_detect_line_endings',FALSE);

    $_SESSION["crmImport"]["header"] = $header;

    $db = new DB();
    $query = "SHOW COLUMNS FROM CRM_naw";
    $db->executeQuery($query);
    while ($f = $db->nextRecord())
    {
      $dbFields[] = $f["Field"];

    }

    if (!in_array("email", $header))
    {
      $error[] = "veld <b>email</b> bestaat niet import afgebroken";
    }
    $c = 0;
    foreach ($header as $fld)
    {
      if ($fld == "email")
      {
        $emailCol = $c;
      }
      $c++;
      if (!in_array($fld, $dbFields))
      {
        $error[] = "veld <b>$fld</b> is geen geldige kolom";
      }
    }

    if (count($error) > 0)
    {
      echo "<h3> Foutmeldingen </h3>";
      echo "<ol><li>";
      echo implode("</li><li>", $error);
      echo "</li></ol>";
      exit;
    }

  }
  else
  {
    echo "Kan bestand niet lezen, probeer opnieuw";
    unlink($tmpFile);
    exit;
  }

}


echo template($__appvar["templateContentHeader"],$content);
?>
<link rel="stylesheet" href="widget/css/font-awesome.min.css">
<link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">


<style>
  <?=$tmpl->parseBlockFromFile("crmImport.css");?>
ol{
  -webkit-margin-before: .3em;
  -webkit-margin-after: .1em;
}
.match{
  background: beige;
  color: maroon;
  font-weight: bold;
}
.containerPage{
  width: 100%;
}
form{
  width: 1000px;
  margin:0 auto;
}
</style>

<div class="containerPage">


<?
  if ($_REQUEST["fase"] == 2)
  {

?>
    <form enctype="multipart/form-data" method="POST"  name="editForm" id="editForm">
    <input type="hidden" name="fase" value="3" />
      <h1 style="text-align:left">CRM import, stap 2 </h1>
    <table>
      <tr>
<?
      foreach($header as $col)
{
  echo "<td style='background: rgba(20,60,90,1); color:white'>$col</td>";
}
?>
      </tr>

<?
$rowCount[0];
foreach ($data as $dRow)
{
  $cMatch = matchEmail($dRow[$emailCol]);
  echo "<tr>";
  for ($x=0; $x < count($header); $x++)
  {
    $dataSes[$rowCount][] = $dRow[$x];
    echo "<td class='c_$x $cMatch'> {$dRow[$x]}</td>";
  }
  echo "</tr>";
  $dataSes[$rowCount][] = $cMatch;
  $rowCount++;


}
$_SESSION["crmImport"]["data"]   = $dataSes;
?>


    </table>
      <br/>
      <br/>
      <br/>
    <input type="submit" value="Geimporteerde regels toevoegen">
    </form>

<?php

  }
  else
  {
?>
    <div>
    <h1 style="float:left">CRM import, stap 1 </h1>
  </div>
  <div style="clear: both"/>
  <div id="msg"></div>
  <form enctype="multipart/form-data" method="POST"  name="editForm" id="editForm">
    <input type="hidden" name="fase" value="2" />
<!--    <input type="hidden" name="profile" value="--><?//=$profile?><!--" />-->

    <fieldset>
      <legend>Data bestand</legend>
      <div class="formblock">
        <div class="formlinks"><label for="bestand" title="bestand">Importbestand</label></div>
        <div class="formrechts">
          <input type="file" name="bestand" id="bestand" value="" />
        </div>
      </div>
    </fieldset>

    <br/>
    <br/>

    <br/><br/>

    <div>
      <button style="float: right;" id="btnSubmit">volgende</button>
    </div>

  </form>

</div>
<script>
  $(document).ready(function(){

    $("#btnSubmit").click(function(e){
      e.preventDefault();
      if ($("#bestand").val() == "")
      {
        $("#msg").html("selecteer het te importeren bestand");
        $("#msg").show(300);
      }
      else
      {
        $("#editForm").submit();
      }
      setTimeout(function(){ $("#msg").hide(300); }, 3000);

    });
  });
</script>
<?php
  }
?>



<?

echo template($__appvar["templateRefreshFooter"],$content);

function addCRM($data)
{

  global $USR, $success, $failed;
  $header = $_SESSION["crmImport"]["header"];

  $add = $data[count($header)] != "match";
  for ($x=0; $x <count($header);$x++)
  {
    $dBlock[] = "`{$header[$x]}` = '".mysql_real_escape_string($data[$x])."'";
  }

  $query = "
  INSERT INTO CRM_naw SET 
  `add_date` = NOW(),
  `add_user` = '$USR', 
  `change_date` = NOW(),
  `change_user` = '$USR', 
  " . implode(", \n", $dBlock);

  $db = new DB();
  if ($db->executeQuery($query))
  {
    addTrackAndTrace("CRM_naw", $db->last_id(), "id", "", $db->last_id(), $USR);
    $success++;
  }
  else
  {
    $failed++;
  }



}

function matchEmail($mail)
{
  $db = new DB();
  $query = "SELECT id FROM CRM_naw WHERE Email = '$mail' ";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return "match";
  }
  else
  {
    return "";
  }
}