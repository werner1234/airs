<?php
/*
    AE-ICT sourcemodule created 11 jul. 2022
    Author              : Chris van Santen
    Filename            : index.php


*/

include_once "wwwvars.php";

session_start();
$_SESSION["NAV"] = "";

$cfg = new AE_config();

if ($_REQUEST["posted"] == "true")
{

  $_SESSION["cashRecon"] = array();
  if ($_FILES["importfile"]["error"] == 0)
  {
    $cashName = "/tmp/cashrecon_".date("Ymd_His").".txt";
    move_uploaded_file($_FILES["importfile"]["tmp_name"], $cashName);
    $_SESSION["cashRecon"] = array(
      "file"  => $cashName,
      "bank"  => $_REQUEST["bank"],
      "datum" => formdate2db($_REQUEST["afshriftDatum"])
    );

    header("location: reconCashVerwerk.php");
  }
  exit;
}

session_write_close();

$content["style"] = '
<link rel="stylesheet" href="../widget/css/font-awesome.min.css" >
<link rel="stylesheet" href="../widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">
<link rel="stylesheet" href="../style/workspace.css" type="text/css" media="screen">
<link rel="stylesheet" href="../style/AIRS_default.css" type="text/css" media="screen">
<link rel="stylesheet" href="../style/dropzone.css"  type="text/css" media="screen">
';
$content['jsincludes'] = '
<script type="text/javascript" src="../javascript/jquery-min.js"></script>
<script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>
<script type="text/javascript" src="../javascript/algemeen.js"></script>
<script type="text/javascript" src="../javascript/dropzone.js"></script>
  
';


echo template("../".$__appvar["templateContentHeader"], $content);
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



  if ($DB->QRecords("SELECT id FROM tijdelijkeRecon WHERE tijdelijkeRecon.change_user = '$USR' ") > 0 AND $_GET["delTemp"] <> 2)
  {
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
      .btnRecon{
        height: 40px;
        width: 300px;
        margin:10px;

      }

    </style>
    <div class="fout">
      Tijdelijke reconciliatiemutaties gevonden voor <?= $USR ?><br/><br />

      <a href="<?= $PHP_SELF ?>?delTemp=1"><button class="btnRecon"> verwijder tijdelijke reconciliatiemutaties </button></a>
      <a href="<?= $PHP_SELF ?>?delTemp=2"><button class="btnRecon"> tijdelijke reconciliatiemutaties aanvullen </button></a>
    </div>
    <?
    exit;
  }

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


  <br/>
  <h2>Cash recon</h2>
  <form enctype="multipart/form-data" action="<?=$PHP_SELF?>" method="POST" name="editForm">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="posted" value="true"/>
    <!-- Name of input element determines name in $_FILES array -->
    <br/>
    <b>Reconciliatiebestand inlezen</b><br><br>
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

    <div class="formblock">
      <div class="formlinks">Selecteer depotbank</div>
      <div class="formrechts">
        <input type="radio" name="bank" class="bankSelect" value="aab" id="abn"/><label for="abn" >ABN-AMRO&nbsp;&nbsp;</label><br>
        <input type="radio" name="bank" class="bankSelect" value="bin" id="bin" checked/><label for="bin" >BINCK&nbsp;&nbsp;</label><br>
        <input type="radio" name="bank" class="bankSelect" value="tgb" id="tgb"/><label for="tgb" >TGB&nbsp;&nbsp;</label><br>
      </div>
    </div>
    <div class="form">
      <div class="formblock">
        <div class="formlinks"><span id="posBestand">Positiebestand</span></div>
        <div class="formrechts">
          <input type="file" name="importfile" size="50" value="<?=$formVal["file"]?>">
        </div>
      </div>


      <div class="formblock">
        <div class="formlinks">Datum &nbsp;</div>
        <div class="formrechts">
          <input type="text" name="afshriftDatum" id="afshriftDatum" value="<?=$datum?>" size="15"> dd-mm-jjjj
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
echo template("../".$__appvar["templateRefreshFooter"], $content);

