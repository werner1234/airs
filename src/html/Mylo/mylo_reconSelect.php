<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2020/05/25 09:13:26 $
  File Versie					: $Revision: 1.30 $

  $Log: reconSelectDepotbank.php,v $

 */
include_once("wwwvars.php");
session_start();
$_SESSION["NAV"] = "";
session_write_close();
//$content = array();
global $USR;

$__appvar["templateContentHeader"] = "../".$__appvar["templateContentHeader"];
$__appvar["templateRefreshFooter"] = "../".$__appvar["templateRefreshFooter"];

$content = array(
  "style" => '
<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
<script type="text/javascript" src="../javascript/jquery-min.js"></script>
<script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>

<link rel="../stylesheet" href="style/fontAwesome/font-awesome.min.css">
  '
);


if ($_GET["delTemp"] == 1)
{
  $DB = new DB();
  $query = "DELETE FROM tijdelijkeRecon WHERE add_user = '$USR' ";
  $DB->executeQuery($query);
}

// if poster
if ($_POST['posted'])
{
  $cfg = new AE_config();
  $field = "reconV3-import-status_".$USR;
  $cfg->deleteField($field);
  $cfg->addItem($field, "import done");

  unset($manualBoekdatum);
  if (!empty($afshriftDatum))
  {
    $dd = explode($__appvar["date_seperator"], $afshriftDatum);
    if (!checkdate(intval($dd[1]), intval($dd[0]), intval($dd[2])))
    {
      $_error = "Fout: ongeldige afschriftdatum opgegeven";
    }
    else
    {
      if ($dd[2] < 100)
        $dd[2] += 2000;
      $manualBoekdatum = $dd[2]."-".substr("0".$dd[1], -2)."-".substr("0".$dd[0], -2);
    }
  }
  // check filetype
  if ($_FILES['importfile']["type"] != "text/comma-separated-values" &&
      $_FILES['importfile']["type"] != "text/x-csv" &&
      $_FILES['importfile']["type"] != "text/csv" &&
      $_FILES['importfile']["type"] != "application/octet-stream" &&
      $_FILES['importfile']["type"] != "application/vnd.ms-excel" &&
      $_FILES['importfile']["type"] != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" &&
      $_FILES['importfile']["type"] != "text/plain")
  {
    $_error = "FOUT: verkeerd bestandstype(".$_FILES['importfile']["type"]."), alleen .csv bestanden zijn toegestaan.";
  }
  // check error
  if ($_FILES['importfile']["error"] != 0)
  {
    $_error = "Fout: bestand niet ingevuld of bestaat niet (".$_FILES['importfile']['name'].")";
  }




  if (empty($_error))
  {
    $importcode = date("YmdHis").$USR;  //datum als JJJJMMDDUUMM
    $importfile = $__appvar["basedir"]."/html/importdata/transaktie_".$_POST['bank']."_".$importcode.".csv";
    $importfile2 = $__appvar["basedir"]."/html/importdata/transaktie_".$_POST['bank']."_".$importcode."_2.csv";
    if (trim($_FILES['importfile3']['tmp_name']) <> "")
    {
      $importfile3 = $__appvar["basedir"]."/html/importdata/transaktie_".$_POST['bank']."_".$importcode."_3.csv";
      move_uploaded_file($_FILES['importfile3']['tmp_name'], $importfile3);
    }
    else
    {
      $importfile3 = "";
    }


    move_uploaded_file($_FILES['importfile2']['tmp_name'], $importfile2);
    if (move_uploaded_file($_FILES['importfile']['tmp_name'], $importfile))
    {
      $extra = "";
      if (count(file($importfile)) > 0)
      {

        if (substr($_POST["bank"],0,4) == "bew_")
        {
          $pad = "reconBewaarder";
        }
        else
        {
          $pad = "recon";
        }

        $redirect = "Location: ../".$pad."/".$_POST['bank']."_reconImport.php?file=".urlencode($importfile)."&file2=".urlencode($importfile2)."&file3=".urlencode($importfile3).$extra."&manualBoekdatum=".$manualBoekdatum;
        
        header($redirect);
        exit();
      }
      // als target bestand leeg is
      $_error = "Fout : bronbestand verkeerd geselecteerd of leeg ($importfile).";
      // verwijder het lege bestand
      if (file_exists($importfile))
        unlink($importfile);
    }
    else
      $_error = "Fout : upload error.";
  }
  echo template($__appvar["templateContentHeader"], $content);
  echo $_error;

  exit;
}

$db = new DB();
$query = "SELECT Vermogensbeheerder, Naam FROM Vermogensbeheerders ORDER BY Vermogensbeheerder";
$db->executeQuery($query);
while($rec = $db->nextRecord())
{
  $vbOptions .= "\n\t<option value='".$rec["Vermogensbeheerder"]."'>(".$rec["Vermogensbeheerder"].") ".$rec["Naam"]."</option>";
}
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

      if (!checkradio(document.editForm.bank))
      {
        alert('Geef aan van welke bank de import betreft');
      }
      else
        document.editForm.submit();
    }
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



  <form enctype="multipart/form-data" action="<?= $PHP_SELF ?>" method="POST"  name="editForm">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="posted" value="true" />
    <!-- Name of input element determines name in $_FILES array -->
    <br />
    <b>Reconciliatiebestand inlezen</b><br><br>
    <?php
    if ($_error)
      echo "<b style=\"color:red;\">".$_error."</b>";
    ?>
    <div class="form">


      <div class="formblock">
        <div class="formlinks"><span id="posBestand">Positiebestand</span> </div>
        <div class="formrechts">
          <input type="file" name="importfile" size="50">
        </div>
      </div>

      <div class="formblock" id="posFile" style="display: none; background: #eee">
        <div class="formlinks"><span id="posBestand2">Positiebestand OPT</span> </div>
        <div class="formrechts">
          <input type="file" name="importfile3" size="50">
        </div>
      </div>

      <div class="formblock" id="saldiFile" style="display: none;">
        <div class="formlinks"><span id="cashBestand">Saldibestand MT940</span> </div>
        <div class="formrechts">
          <input type="file" name="importfile2" size="50">
        </div>
      </div>
      <div class="form">
        <div class="formblock">
          <div class="formlinks"> Depotbank</div>
          <div class="formrechts" style="width: 300px">
              <input type="radio" class="bankSelect" name="bank" value="binckv3" id="binckv3" CHECKED/><label for="binckv3" > Binck&nbsp;V3 (;)&nbsp;</label><br>
              <input type="radio" class="bankSelect" name="bank" value="saxo" id="saxo" /><label for="saxo" > Saxo&nbsp;</label><br>


          </div>
        </div>
      </div>

      <div class="form">
        <div class="formblock">
          <div class="formlinks">Afwijkende datum &nbsp;</div>
          <div class="formrechts">
            <input type="text" name="afshriftDatum" id="afshriftDatum" value="<?= $datum ?>" size="15"> dd-mm-jjjj
          </div>
        </div>
        <div class="formblock">
          <div class="formlinks"> &nbsp;</div>
          <div class="formrechts"><br/>
            <input type="button" value="importeren" onclick="submitter();">
          </div>
        </div>
      </div>
  </form>

        <script>
          $(document).ready(function () {
            $("#positieButton").click(function(e){
              e.preventDefault();

              window.open("reconBewaarder/reconQuery.php?datum="+ $("#afshriftDatum").val(), "content");

            });

            $("#bewaarderSumbit").click(function(e){
              e.preventDefault();

              window.open("reconBewaardersPerVb.php?vb="+$("#bewaarderVB").val());

            });

            $("#saldiFile").hide();
            $("#posFile").hide();
            $(".bankSelect").click(function ()
            {
              var value = $(this).val();
              if (value != "kasbank")
              {
                $("#posFile").hide(400);
              }
              switch (value)
              {
                case "abn":
                case "abnh":
                case "bew_abn":
                  $("#posBestand").html("Positiebestand MT5XX: ");
                  $("#cashBestand").html("Cashbestand MT940: ");
                  $("#saldiFile").show(400);
                  break;
                case "abnbe":
                  $("#posBestand").html("Positiebestand: ");
                  $("#cashBestand").html("Cashbestand: ");
                  $("#saldiFile").show(400);
                  break;                  
                case "snssec":
                  $("#posBestand").html("Positiebestand Spos: ");
                  $("#cashBestand").html("Cashbestand Cpos: ");
                  $("#saldiFile").show(400);
                  break;
                case "kasbank":
                  $("#posBestand").html("Positiebestand FND: ");
                  $("#cashBestand").html("Cashbestand GLD: ");
                  $("#saldiFile").show(400);
                  $("#posFile").show(400);
                  break;
                case "caceis":
                  $("#posBestand").html("Positiebestand Holdings: ");
                  $("#cashBestand").html("Cashbestand Balance: ");
                  $("#saldiFile").show(400);
                  $("#posFile").show(400);
                  break;
                case "mdzkas":
                  $("#posBestand").html("Positiebestand FND: ");
                  $("#cashBestand").html("Cashbestand GLD: ");
                  $("#saldiFile").show(400);

                  break;
                case "kbc":
                  $("#posBestand").html("Positiebestand: ");
                  $("#cashBestand").html("Cashbestand: ");
                  $("#saldiFile").show(400);
                case "credswiss":
                  $("#posBestand").html("Positiebestand: ");
                  $("#cashBestand").html("Cashbestand: ");
                  $("#saldiFile").show(400);
                  break;                  
                case "degiro":
                  $("#posBestand").html("Positiebestand: ");
                  $("#cashBestand").html("Cashbestand: ");
                  $("#saldiFile").show(400);
                  break;
                case "kbc":
                  $("#posBestand").html("Positiebestand: ");
                  $("#cashBestand").html("Cashbestand: ");
                  $("#saldiFile").show(400);
                  break;
                case "ubs":
                  $("#posBestand").html("Positiebestand ZAH: ");
                  $("#cashBestand").html("Cashbestand ZAQ: ");
                  $("#saldiFile").show(400);
                  break;
                case "rabo":
                  $("#posBestand").html("Stukken bestand: ");
                  $("#cashBestand").html("Cash bestand: ");
                  $("#saldiFile").show(400);
                  break;
                case "ubslux":
                  $("#posBestand").html("Stukken bestand: ");
                  $("#cashBestand").html("Cash bestand: ");
                  $("#saldiFile").show(400);
                  break;
                default:
                  $("#posBestand").html("Positiebestand: ");
                  $("#saldiFile").hide(400);
              }
              
            });

          });
        </script>
        <?
      }
      echo template($__appvar["templateRefreshFooter"], $content);
      ?>