<?php

/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : reconV3Start.php

  $Log: reconV3Start.php,v $
  Revision 1.19  2020/07/03 11:26:23  cvs
  call 8724


 */
include_once("wwwvars.php");
session_start();
$_SESSION["NAV"] = "";
session_write_close();

$cfg = new AE_config();
$reconV3s = $cfg->getData("reconV3s");
if ($reconV3s == "")
{
  $reconV3s = "[]";
}


if ($__appvar["bedrijf"] == "HOME")
{
  $reconBedrijvenOld = array(
    'ACM', 'AND', 'ANO', 'ANT', 'ASN','AUR','AVE',
    'BEO', 'BES', 'BOX', 'BUG',
    'C4C', 'CAP', 'CAS', 'CEN', 'CFF','CSG',
    'DOO', 'DOU', 'DUI', 'DV2',
    'ERC', 'EVO',
    'FCM', 'FCT', 'FDX', 'FIN',
    'GRO', 'GUA',
    'HAJ', 'HEE', 'HEN',
    'IBE', 'IDC','IVM',
    'JAN', 'JMK',
    'KYA',
    'LBC',
    'MAT', 'MCP', 'MER', 'MUL',
    'NJN', 'NOE',
    'ORC',
    'PAS', 'PEC', 'PWM',
    'RDE', 'REN', 'RRP',
    'SEQ', 'SHP', 'SLV', 'STE',
    'TEI', 'THB', 'TOP', 'TPA',
    'VAL', 'VEC', 'VED', 'VLC', 'VRY',
    'WAT', 'WEY', 'WIS', 'WMP'
  );


  $cashOnlyDepotBanken = array("TGB");

  $excludeArray = array('AEI',  'EFI',  'FEX');
  $db = new DB();
  $query = "
    SELECT 
        DISTINCT `Bedrijfsgegevens`.`Bedrijf` 
    FROM `Bedrijfsgegevens` 
    LEFT JOIN `MONITOR_bedrijfDepot` ON 
        `Bedrijfsgegevens`.`Bedrijf` = `MONITOR_bedrijfDepot`.`bedrijf`  
    ORDER BY 
        `Bedrijfsgegevens`.`Bedrijf`";


  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {

    if (!in_array($rec["Bedrijf"], $excludeArray))
    {
      $reconBedrijven[] = $rec["Bedrijf"];
    }
  }


}
else
{
  $reconBedrijven = array($__appvar["bedrijf"]);
}



$formVal = $_SESSION["reconv3"];

if ($_GET["delTemp"] == 1)
{
  $DB = new DB();
  $query = "DELETE FROM tijdelijkeRecon WHERE add_user = '$USR' ";
  $DB->executeQuery($query);
  $cfg = new AE_config();
  $field = "reconV3-import-status_".$USR;
  $cfg->deleteField($field);
  $cfg->addItem($field, "cleanTable");
}


if ($_POST['posted'])
{


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
    session_start();
    $_SESSION["vbArray"] = array();
    $db = new DB();
    $query = "SELECT Vermogensbeheerder FROM `VermogensbeheerdersPerBedrijf` WHERE `Bedrijf` IN ('".implode("','", $_POST["selected"])."')";
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $_SESSION["vbArray"][] = $rec["Vermogensbeheerder"];
    }

    session_commit();

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
        $cfg = new AE_config();
        $field = "reconV3-import-status_".$USR;
        $cfg->putData($field, "import started ".$_POST['bank']);
        $redirect = "Location: reconV3/".$_POST['bank']."_reconV3Import.php?soort=".$_POST["soortRecon"]."&vb=".urlencode($_POST["vb"])."&file=".urlencode($importfile)."&file2=".urlencode($importfile2)."&file3=".urlencode($importfile3).$extra."&modus=".$_POST["modus"]."&manualBoekdatum=".$manualBoekdatum;
        header($redirect);
        exit();
      }
      // als target bestand leeg is
      $_error = "Fout : bronbestand verkeerd geselecteerd of leeg ($importfile).";
      // verwijder het lege bestand
      if (file_exists($importfile))
      {
        unlink($importfile);
      }
    }
    else
    {
      $_error = "Fout : upload error.";
    }
  }
  echo template($__appvar["templateContentHeader"], $content);
  echo $_error;

  exit;
}



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
    <b>Reconciliatiebestand inlezen <a href="./reconCash/reconCashStart.php">__</a></b><br><br>
    <?php
    if ($_error)
      echo "<b style=\"color:red;\">".$_error."</b>";


    ?>

    <style>
      .inp{
        line-height: 24px;
        padding: 4px;
        font-weight: bold;
      }
      #bestand2Oms{
        display: none;
        float: left;
        width:120px;
        background:  #E9E9E9;


      }
      #bestand2Input{
        display: none;
        float: left;
        width:500px;
      }
      #bestand1Oms{
        float: left;
        width:120px;
        background: #E9E9E9;
      }
      #bestand1Input{
        float: left;
        width:500px;
      }
      .csSelect{
        font-size: 1.1em;
        padding: 2px 5px 2px 5px;
      }
      #dialogMdlPortVrg{
        visibility: hidden;
        padding: 5px;;
        display: inline-block;
        height: 18px;
        width: 130px;
        background: orange;
      }
      article{
        width: 300px;
        float: left;
      }
      article :after{
        clear: both;
      }
      #mergeToggle {
        background: #999!important;
        margin: 5px;
      }
      .mergeContainer{
        width: 1000px;
      }
      .mergeHeader{
        background: rgba(20,60,90,1);
        color: white;
        margin: 0;
        padding: 5px;
      }
      .mergeContent{
        display: none;
        width: 100%;
        height: 350px;
      }
      #feedback{
        display: none;
        padding: 2em;
        background: maroon;
        color: white;
        border-radius: 10px;
      }
    </style>

    <div class="formblock">
      <div class="formlinks">Reconbestand </div>
      <div class="formrechts">
        <section class="mergeContainer">
          <div class="mergeHeader"><button id="mergeToggle"><i class="fa fa-angle-double-down" ></i></button> Samenvoegen bestanden</div>
          <div class="mergeContent" >
            <iframe src="reconV3BestandenSamenvoegen.php" frameborder="0" width="100%" height="100%" id="mergeframe" name="mergeframe"></iframe>
          </div>
        </section><br/>



        </div>
      </div>
    </div>
    <br/>
    <br/>
    <h2>Recon versie 3</h2>



    <div class="formblock">
      <div class="formlinks">&nbsp; </div>
      <div class="formrechts">
        <div id="feedback"></div>
      </div>
    </div>
    <?
    if (DBreconRead > 0)
    {
      ?>
      <div class="formblock">
        <div class="formlinks">Modus</div>
        <div class="formrechts">
          <select name="modus" id="modus">
            <option value="1">Standaard</option>
            <option value="<?=DBreconRead?>">Via Shadow</option>
          </select>
        </div>
      </div>
      <?
    }
    ?>
    <div class="formblock">
      <div class="formlinks">Soort Recon</div>
      <div class="formrechts">
        <select name="soortRecon" id="soortRecon">
          <option value="standaard">Standaard</option>
          <option value="cash">Cash only</option>
        </select>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">Selecteer depotbank</div>
      <div class="formrechts">
        <select name="bank" id="bank">


        </select>
      </div>
    </div>
    <div class="form">
      <div class="formblock">
        <div class="formlinks"><span id="posBestand">Positiebestand</span> </div>
        <div class="formrechts">
          <input type="file" name="importfile" size="50" value="<?=$formVal["file"]?>">
        </div>
      </div>





      <div class="formblock">
        <div class="formlinks">Selecteer vermogensbeheerder</div>
        <div class="formrechts">

<?

          $bedrijven = $reconBedrijven;
          sort($bedrijven);

          $kolommen = 5;
          $aantalBedrijven = count($bedrijven);
          $kolomLengte = ceil($aantalBedrijven/$kolommen);
          $kolomText = array();
          $tel = 0;
          $current = 0;

          foreach ($bedrijven as $bedrijf)
          {
            $tel++;
            if ($tel > $kolomLengte)
            {
              $current++;
              $tel = 1;
            }
            $kolomText[$current] .= "
              <input class='vink' type='checkbox' value='{$bedrijf}' name='selected[]' id='{$bedrijf}'  > <label for='{$bedrijf}'>{$bedrijf}</label><br/>
          ";

          }
          //        debug($kolomText);

          for($a=0; $a < count($bedrijven); $a++)
          {
            $selected = ($vinkArray[$bedrijven[$a]] == 1) ? "checked" : "";
          }
?>

          <table class="vinkTable">
            <tr>
              <td colspan="<?=$kolommen?>" style="text-align: center; background: #0E3460; color: white; min-width: 500px"> Bedrijven </td>
            </tr>
            <tr>
              <?
              for ($k=0; $k < $kolommen; $k++)
              {
                echo "<td>";
                echo $kolomText[$k];
                echo "</td>";
              }
              ?>    </tr>
          </table>
          <div class="buttonBar">
            <button id="checkAll">selecteer alles</button>
            <button id="checkNone">selecteer niets</button>
          </div>
        </div>
          <div class="formrechts" style="width:280px; height:275px; padding:0; margin-left:15px; border:1px solid #333; background: beige">
          <div style="padding: 5px; margin: 0; background: #0E3460; color: white; text-align: center; ">Vastgelegde selecties</div>
         <div style="padding: 10px">
            <button class="btnSelection" data-action="load"  >Laden</button>
            <button class="btnSelection" data-action="save"  >Opslaan</button>
            <button class="btnSelection" data-action="delete">Verwijder</button><br/><br/>
            <input id="selectionName" placeholder="naam v/d "/>
            <br/><br/>
            <select id="selections"  size="8" style="width : 240px; padding: 5px;">
              <option value="---">---</option>
            </select>
         </div>

        </div>
      </div>


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

        <br/>
        <br/>

  </form>




  <script>

    const optStd = `
    <option value="abnv2">ABN v2</option>
    <option value="binck">Binck</option>
    <option value="bincksingle">BinckSingle</option>
    <option value="fundshare" id="fundshare">Fundshare</option>
    <option value="fvl" id="fvl">FVL</option>
    <option value="fvlc" id="fvl">FVLC</option>
    <option value="gs" id="gs" >GS</option>
    <option value="hsbc" id="hsbc" >HSBC</option>
    <option value="ingv2" id="ingv2">ING v2</option>
    <option value="ib" id="ib">IB</option>
    <option value="jb" id="jb">Jul bear</option>
    <option value="lombard" id="lombard" >Lombard</option>
    <option value="lynx" id="lynx" >Lynx</option>
    <option value="optimix" id="optimix" >Optimix</option>
    <option value="pictet" id="pictet" >Pictet</option>
    <option value="rabobank" id="rabobank" >Rabobank</option>
    <option value="saxo" id="saxo" >Saxo</option>
    <option value="tgb" id="tgb" >TGB</option>
    <option value="ubp" id="ubp" >UBP</option>
    <option value="vp" id="vp" >VP</option>
    `;
    const optCash = `
    <option value="abnv2" id="abnv2" >ABN</option>
    <option value="binck" id="binck" >Binck</option>
    <option value="tgb" id="tgb" >TGB</option>

    `;

    function toggleDiv(id)
    {
      var e = document.getElementById(id);
      e.style.display = "inline";
    }

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

      if ($('input[type="checkbox"]:checked').length == 0)
      {
        feedback('Selecteer minimaal 1 vermogensbeheerder');
        $("#feedback").show(300);

        return;
      }
      document.editForm.submit();
    }

    function popOptions(data)
    {
      console.log("in popOptions");
      let dropdown = $('#selections');
      dropdown.empty();
      dropdown.prop('selectedIndex', 0);
      $.each(data, function (key, entry)
      {
        console.log(key," ",entry);
        dropdown.append($('<option></option>').attr('value', entry).text(entry));
      });
    }

    $(document).ready(function()
    {
      // options vullen
      // optStd

      $("#bank").html(optStd);

      $("#soortRecon").change(function(){
        if ($(this).val() == "cash")
        {
          $("#bank").html(optCash);
        }
        else
        {
          $("#bank").html(optStd);
        }

      });

      $.ajax({
        url: 'ajax/reconV3Selections.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: "getNames",
        },
        success: function (data, textStatus, jQxhr) {
          console.log(data);
          popOptions(data);
        },
      });

<?
      if (count($bedrijven) == 1)
      {
?>
        $(".vink").each(function(){
          $(this).attr('checked', true);
        });
<?
      }

?>

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


      function procesAction(action)
      {
        let vbs = [];

        const selected   = $("#selections").val();
        const selectName = $("#selectionName").val();
        const bank       = $("#bank").val();

        $.each($("input[class='vink']:checked"), function()
        {
          vbs.push($(this).val());
        });

        //console.log(`action ${action}  selectie: ${selected}  selectienaam: ${selectName}`);
        // console.table(vbs);
        feedback("");
        switch(action)
        {
          case "load":
            if (selected == "" || selected == null)
            {
              feedback("selecteer eerst een !");
              return;
            }
            break;
          case "delete":

            if (selected == "" || selected == null)
            {
              feedback("selecteer eerst een !");
              return;
            }
            break;
          case "save":
            let msg = "";
            if (selectName == "")
            {
              msg += "<li> geef een naam op voor de ";
            }
            if (vbs.length < 1)
            {
              msg += "<li> selecteer minimaal 1 VB";
            }
            if (msg != "")
            {
              feedback(msg);
              return;
            }
            break;
        }
        $.ajax({
          url: 'ajax/reconV3Selections.php',
          type: 'POST',
          data: {
            action: action,
            selected: selected,
            selectName: selectName,
            vbs: vbs,
            bank: bank
          },
          success: function( data, textStatus, jQxhr ){
            const res = $.parseJSON(data);
            console.log(res);

            switch(res.action)
            {
              case "load":
                console.log(res);
                // fill name
                $("#selectionName").val(res.selectName);
                $("#bank").val(res.bank);
                //  reset checkboxes
                $(".vink").each(function(){
                  $(this).attr('checked', false);
                });
                // set checkboxes
                $(".vink").each(function(){
                  const  v = $(this).attr('value');
                  if ($.inArray(v, res.vbs) != -1)
                  {
                    $(this).attr('checked', true);
                  }

                });
                feedback(` <b>"${res.selectName}"</b> geladen`, "green");
                break;
              case "save":
                popOptions(res.names);
                $("#selectionName").val(res.selectName);
                feedback(` <b>"${res.selectName}"</b> opgeslagen`, "green");
                break;
              case "delete":
                popOptions(res.names);
                $("#selectionName").val(res.selected);
                feedback(` <b>"${res.selected}"</b> verwijderd`, "green");
                break;
            }
          },
          error: function( jqXhr, textStatus, errorThrown ){
            console.log( errorThrown );
          }
        });

      }

      $("#mergeToggle").click(function (e)
      {
        e.preventDefault();
        $(".mergeContent").toggle(300);
      });

      $("label").dblclick(function()
      {
        console.log("dblclick");
        console.log($("#importfile").val());
        if ($("#importfile").val() != "")
        {
          submitter();
        }
       });

        $("#checkAll").click(function (e){
            e.preventDefault();
            $(".vink").each(function(){
                $(this).attr('checked', true);
            });
        });

        $("#checkNone").click(function (e){
            e.preventDefault();
            $(".vink").each(function(){
                $(this).attr('checked', false);
            });
        });

    });

    </script>
        <?
      }
      echo template($__appvar["templateRefreshFooter"], $content);
      ?>
