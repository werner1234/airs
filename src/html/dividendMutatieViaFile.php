<?php
/*
    AE-ICT sourcemodule created 07 jul. 2021
    Author              : Chris van Santen
    Filename            : dividendMutatieViaFile.php


*/

include_once('../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
session_start();
$_SESSION["NAV"] = "";
session_write_close();

global $__appvar;

$cfg            = new AE_config();
$db             = new DB();

$content["style"] = '
<link rel="stylesheet" href="widget/css/font-awesome.min.css" >
<link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">
<link rel="stylesheet" href="style/workspace.css" type="text/css" media="screen">
<link rel="stylesheet" href="style/AIRS_default.css" type="text/css" media="screen">
<link rel="stylesheet" href="style/dropzone.css"  type="text/css" media="screen">
';
$content['jsincludes'] = '
<script type="text/javascript" src="javascript/jquery-min.js"></script>
<script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
<script type="text/javascript" src="javascript/algemeen.js"></script>
<script type="text/javascript" src="javascript/dropzone.js"></script>
  
';
echo template($__appvar["templateContentHeader"], $content);

$tmpl = new AE_template();

$rowTmpl = "
  
    <tr class='{rowClass}'>
       <td><input type='checkbox' checked class='check' name='{id}' id='chk_{id}' /></td>
       <td>{0}</td>
       <td>{1}</td>
       <td>{2}</td>
       <td>{3}</td>
       <td>{4}</td>
       <td>{5}</td>
       <td>{6}</td>
       <td>{7}</td>
    </tr>
  
";

$tmpl->loadTemplateFromString($rowTmpl, "row");
?>
<style>
  .spinner{
    position: absolute;
    top:100px;
    left:100px;
    text-align: center;
    display: none;
    padding: 2rem;
    background: white;
    border:1px solid #999;
    border-radius: 10px;
  }
  table{
    margin-top:3em;
    margin-left:3em;
  }
  .rowHeader td{
    padding: 3px 10px;
    background: navy;
    color: white;
  }

  .rowData td{
    padding: 3px 10px;
    background: white;
    color: #333;
    border-bottom: 1px #999 solid;
  }
  .rowInvalid td {
    color:white;
    background: Maroon;
  }
</style>
 <div class="spinner">
   <img src="images/loading.gif"/><br/><br/>
   Moment geduld a.u.b.
 </div>
<?php

if ($_POST['posted'])
{

  ?>
  <script>
    $(".spinner").show();
  </script>
  <?php
  ob_flush();flush();
  $output         = array();
  $geldImport     = false;
  $stukkenImport  = false;

  // check filetype
  if ($_FILES['bestand']["error"] == 0)
  {
    if (  $_FILES['bestand']["type"] != "text/comma-separated-values" &&
          $_FILES['bestand']["type"] != "text/x-csv" &&
          $_FILES['bestand']["type"] != "text/csv" &&
          $_FILES['bestand']["type"] != "application/octet-stream" &&
          $_FILES['bestand']["type"] != "application/vnd.ms-excel" &&
          $_FILES['bestand']["type"] != "text/plain")
    {
      $_error = "FOUT: verkeerd geld bestandstype(".$_FILES['bestand']["type"]."), alleen tekst bestanden zijn toegestaan.";
    }

  }

  if (empty($_error))
  {
    session_start();
    echo '<script> $(".spinner").hide(200); </script>';

    $filename = $_FILES['bestand']["tmp_name"];
    if (!$handle = @fopen($filename, "r"))
    {
      $error[] = "FOUT bestand $filename is niet leesbaar";
      return false;
    }

    $row      = 0;
    while ($data = fgetcsv($handle, 4096, ";"))
    {
//      debug($data, $row);
      $row++;
      if ($row == 1)
      {
        if ($data[0] != "Fonds" OR $data[1] != "Grootboekrekening")
        {
          $_error[] = "Geen geldige geld bestand";
        }
        else
        {
          $header = $data;
        }
        continue;
      }
      if (trim($data[0] == ""))  // sla lege regels over
      {
        continue;
      }

      $vData = $data;
      $vData["fondsValid"] = getFonds($data[0]);
      $output[] = $vData;
    }

  }


  if (count($_error) > 0)
  {
    foreach($_error as $item)
    {
      echo "<li>$item</li>";
    }
  }


  if (count($output) > 0)
  {
    $_SESSION["divMutDataSet"] = $output;
    $invalidRows = 0;
?>
    <form method="post" id="postForm" action="dividendMutatieSelectie.php">
      <input type="hidden" name="viaFile" value="1">
      <input type="hidden" name="posted" value="1">

    <table>

<?php
    $header["rowClass"] = "rowHeader";
    $header["id"] = "header";
    echo $tmpl->parseBlock("row", $header);

    foreach ($output as $key=>$row)
    {
      $row["rowClass"]  = "rowData";
      $row["id"]        = "chk_".$key;
      if (!$row["fondsValid"])
      {

        $row["rowClass"] .= " rowInvalid";
        $invalidRows++;
      }
      echo $tmpl->parseBlock("row", $row);

    }
    echo "</table>";
    if ($invalidRows != 0)
    {
?>
      <br/>
      <br/>
      <h2>Er zijn ongeldige Fondsen gevonden, corrigeer het bestand en probeer opnieuw</h2>
<?
    }
    else
    {
?>
      <br/>
      <br/>
      <button id="btnSubmit">Verwerk de aangevinkte regels</button>
      </form>
<?php
    }
?>
    <br/>
    <br/>
    <br/>
    <br/>
    <script>
      $(document).ready(function()
      {
        $("#btnSubmit").click(function(e)
        {
          e.preventDefault();
          $("#postForm").submit();
        });
        $("#chk_header").click(function()
        {
          const chk = ($(this).attr("checked") == "checked");
          $(".check").attr("checked", chk);
        });
      });
    </script>
<?php
  }



//  unlink($filename);
  echo template($__appvar["templateRefreshFooter"], $content);
  exit;
}




if (!$_FILES['importfile']['name'])
{

  // TRM leegmaken
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
  $db->executeQuery($query);
  ?>
  <style>
    #bestand2{
      display: none;


    }
    .rowHeader td{
      padding: 3px 10px;
      background: navy;
      color: white;
    }
    .rowInvalid td {
      color:white;
      background: Maroon;
    }
    .rowData td{
      padding: 3px 10px;
      background: white;
      color: #333;
      border-bottom: 1px #999 solid;
    }

    #feedback{
      display: none;
      padding: 2em;
      background: maroon;
      color: white;
      border-radius: 10px;
    }

    legend{
      padding: 5px;
      background: rgba(20,60,90,1);
      color: white;
    }
  </style>
  <script>

  </script>

  <form enctype="multipart/form-data" method="POST"  name="editForm">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="posted" value="true" />
    <!-- Name of input element determines name in $_FILES array -->
    <br />
<?php
    if ($_error)
      echo "<b style=\"color:red;\">".$_error."</b>";
?>
    <br/>
    <h2>Dividend mutaties, inlezen bestand</h2>
    <div class="formblock">
      <div class="formlinks">&nbsp; </div>
      <div class="formrechts">
        <div id="feedback"></div>
      </div>
    </div>

    <div class="form">
      <div class="formblock">
        <div class="formlinks"><span id="bestand1">Bestand</span> </div>
        <div class="formrechts">
          <input type="file" name="bestand" size="50" value="">
        </div>
      </div>



      <div class="formblock">
        <div class="formlinks"> &nbsp;</div>
        <div class="formrechts"><br/>
          <input type="button" value="importeren" onclick="submitter();">
        </div>
      </div>

    </div>

    <br/>
    <br/>

  </form>

  <script>
    function feedback(txt,greenColor)
    {
      if (greenColor)
      {
        $("#feedback").css("background","rgba(20,90,20,1)");
      }
      else
      {
        $("#feedback").css("background","maroon");
      }

      if (txt != "")
      {
        $("#feedback").html(txt);
        $("#feedback").show(300);
      }
      else
      {
        $("#feedback").html("");
        $("#feedback").hide();
      }
    }

    function submitter()
    {
      if (document.editForm.bestand.value == '' )
      {
        feedback("Selecteer eerst een importbestand");
        return;
      }

      document.editForm.submit();
    }


  </script>
<?
}
echo template($__appvar["templateRefreshFooter"], $content);


function getFonds($fondsCode)
{

  $fonds = array();
  $db = new DB();
  $query = "SELECT * FROM Fondsen WHERE Fonds = '{$fondsCode}' ";
  if ($fonds = $db->lookupRecordByQuery($query))
  {
    return true;
  }
  else
  {
    return false;
  }
}








