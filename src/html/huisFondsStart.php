<?php
/*
    AE-ICT sourcemodule created 11 jul. 2022
    Author              : Chris van Santen
    Filename            : index.php


*/

include_once "wwwvars.php";

if ($_REQUEST["delTemp"] == "1")
{
  $db = new DB();
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '{$USR}' ";
  $db->executeQuery($query);
}


$db = new DB();
$query = "SELECT `id` FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '{$USR}'";
if ($db->lookupRecordByQuery($query))
{
  echo template($__appvar["templateContentHeader"], $content);
?>
  <style>
    .fout{

      margin: 25px;
      background: red;
      color: white;
      padding: 20px;
      width: 400px;
      text-align: center;
    }


  </style>
  <div class="fout">
    <?=vt("Tijdelijke rekeningmutaties gevonden voor")?>" <?=$USR?><br/><br />

    <a href="<?=$PHP_SELF?>?delTemp=1"><button> <?=vt("verwijder tijdelijke rekeningmutaties")?> </button></a>
  </div>
  <?
  exit;
}

session_start();
$_SESSION["NAV"] = "";

$cfg = new AE_config();

if ($_REQUEST["posted"] == "true")
{

  $_SESSION["huisfonds"] = array();
  if ($_FILES["importfile"]["error"] == 0)
  {
    $hsName = "/tmp/huisfonds_".date("Ymd_His").".txt";
    move_uploaded_file($_FILES["importfile"]["tmp_name"], $hsName);
    $_SESSION["huisfonds"] = array(
      "file"  => $hsName,
      "datum" => $_REQUEST["afboekdatum"]
    );

    header("location: huisFondsVerwerk.php");
  }
  exit;
}

session_write_close();

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
if (!$_FILES['importfile']['name'])
{
  ?>
  <style>
    #bestand2{
      display: none;


    }
    legend{
      padding: 5px;
      background: rgba(20,60,90,1);
      color: white;
    }
  </style>
  <script>

  </script>

  <?
  $DB = new DB();

  $query = "SELECT Valutakoersen.Datum FROM (Valutakoersen) WHERE Valuta = 'EUR' ORDER BY Datum DESC";
  $dRec = $DB->lookupRecordByQuery($query);
  $datum = dbdate2form($dRec["Datum"]);





  ?>

  <style>
    .inp {
      line-height: 24px;
      padding: 4px;
      font-weight: bold;
    }

    #bestand2Oms {
      display: none;
      float: left;
      width: 120px;
      background: #E9E9E9;


    }

    #bestand2Input {
      display: none;
      float: left;
      width: 500px;
    }

    #bestand1Oms {
      float: left;
      width: 120px;
      background: #E9E9E9;
    }

    #bestand1Input {
      float: left;
      width: 500px;
    }

    .csSelect {
      font-size: 1.1em;
      padding: 2px 5px 2px 5px;
    }

    #dialogMdlPortVrg {
      visibility: hidden;
      padding: 5px;;
      display: inline-block;
      height: 18px;
      width: 130px;
      background: orange;
    }

    article {
      width: 300px;
      float: left;
    }

    article :after {
      clear: both;
    }

    #mergeToggle {
      background: #999 !important;
      margin: 5px;
    }

    .mergeContainer {
      width: 1000px;
    }

    .mergeHeader {
      background: rgba(20, 60, 90, 1);
      color: white;
      margin: 0;
      padding: 5px;
    }

    .mergeContent {
      display: none;
      width: 100%;
      height: 350px;
    }

    #feedback {
      display: none;
      padding: 2em;
      background: maroon;
      color: white;
      border-radius: 10px;
    }
  </style>


  <h3>Huisfondsen: positionele administratie</h3>
  <br/>

  <form enctype="multipart/form-data" action="<?=$PHP_SELF?>" method="POST" name="editForm">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="posted" value="true"/>
    <!-- Name of input element determines name in $_FILES array -->
    <br/>
    <b>Huisfonds bestand inlezen</b><br><br>
    <?php
    if ($_error)
    {
      echo "<b style=\"color:red;\">" . $_error . "</b>";
    }


    ?>




    <div class="formblock">
      <div class="formlinks">&nbsp;</div>
      <div class="formrechts">
        <div id="feedback"></div>
      </div>
    </div>
    <?

    ?>

    <div class="form">
      <div class="formblock">
        <div class="formlinks"><span id="posBestand">Bestand</span></div>
        <div class="formrechts">
          <input type="file" name="importfile" size="50" value="<?=$formVal["file"]?>">
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">afboekdatum </div>
        <div class="formrechts">
          <input  class="AIRSdatepicker" type="text"  size="14" value="<?=date("d-m-Y");?>" name="afboekdatum" id="afboekdatum" ' >
        </div>
      </div>



      <div class="formblock">
        <div class="formlinks"> &nbsp;</div>
        <div class="formrechts"><br/>
          <input type="button" id="btnSubmit" value="importeren" onclick="submitter();">
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

    $( ".AIRSdatepicker" ).datepicker({
      showOn: "button",
      buttonImage: "javascript/calendar/img.gif",//"images/datePicker.png",
      buttonImageOnly: true,
      dateFormat: "dd-mm-yy",
      dayNamesMin: ["Zo", "Ma", "Di", "Wo", "Do", "Vr", "Za"],
      monthNames: ["januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december"],
      monthNamesShort: [ "Jan", "Feb", "Mrt", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec" ],
      nextText: "volgende maand",
      prevText: "vorige maand",
      currentText: "huidige maand",
      changeMonth: true,
      changeYear: true,
      yearRange: '1900:2050',
      closeText: "sluiten",
      showAnim: "slideDown",
      showButtonPanel: true,
      showOtherMonths: true,
      selectOtherMonths: true,
      numberOfMonths: 1,
      showWeek: true,
      firstDay: 1
    });

    function submitter()
    {
      if (document.editForm.importfile.value == '' )
      {
        feedback("Selecteer eerst een importbestand");
        return;
      }
      $("#btnSubmit").val("Bezig met inlezen...");
      $("#btnSubmit").prop("disabled",true);
      document.editForm.submit();
    }

    $(document).ready(function()
    {
      // options vullen
      // optStd

      $("#bank").html(optStd);

      $("#selections").dblclick(function ()
      {
        //console.log($(this).val());
        procesAction("load");
      });

      $(".btnSelection").click(function(e)
      {
        e.preventDefault();
        const action     = $(this).data("action");
        procesAction(action);
      });

    });

  </script>
  <?
}
echo template($__appvar["templateRefreshFooter"], $content);

