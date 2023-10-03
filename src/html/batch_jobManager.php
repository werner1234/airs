<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/28 13:11:49 $
    File Versie         : $Revision: 1.4 $

    $Log: batch_jobManager.php,v $
    Revision 1.4  2018/03/28 13:11:49  cvs
    call 3503

    Revision 1.3  2018/03/28 12:36:06  cvs
    call 3503

    Revision 1.2  2018/03/28 12:35:07  cvs
    call 3503

    Revision 1.1  2018/03/09 12:45:08  cvs
    call 3503



*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$msg = "";
$fmt = new AE_cls_formatter();

$data = array_merge($_GET,$_POST);

//debug($data);
if($data["action"] == "deleteBatch")
{

  // verwijder tijdelijke recondata
  $db = new DB();
  $query = "DELETE FROM tijdelijkeRecon WHERE batch = '".$data["batch"]."'";
  $db->executeQuery($query);
  // verwijder de job
  $jobDel = new AIRS_cls_reconJob();
  $jobDel->deleteBatch($data["batch"]);
  $msg = "batch en tijdelijke recon regels van batch ".$data["batch"]." verwijderd";
}

if($data["action"] == "delete")
{
  $jobDel = new AIRS_cls_reconJob();
  $msg = $jobDel->deleteJobs($data["records"]);
}

if ($data["action"] == "add")
{

  $keyval = array();
  $data["uitvoer"] = ($data["uitvoer"] == "on"?"meer":"enkel");
  $data["queued"] = 1;
  $updFields = array("prio","depotbank","naam","reconDatum","uitvoer","queued");
  $jobAdd = new AIRS_cls_reconJob($data["batchnr"]);
//  debug($data);
  $jobAdd->getJob();
//  debug($_FILES);
//  debug($jobAdd);
  if ($_FILES["positionFile"]["error"] == 0 AND $_FILES["cashFile"]["error"] == 0)
  {
    debug("trigger");
    // $jobAdd->process2Files($_FILES);
  }
  foreach ($updFields as $fld)
  {
    if (trim($data[$fld]) != "")
    {
      if ($fld == "reconDatum")
      {
        $date = split("-", trim($data[$fld]));
        $keyval[$fld] = $date[2]."-".$date[1]."-".$date[0];
      }
      else
      {
        $keyval[$fld] = trim($data[$fld]);
      }
    }
  }
  if (count($keyval) > 0 )
  {
    $jobAdd->updateJob($keyval);
  }

  $msg = "Job is aangemaakt";
}

$job = new AIRS_cls_reconJob();
$job->initModule();
$_SESSION["reconJob"]["Batch"] = $job->batch;

$subHeader     = "";
$mainHeader    = "<h2>Batch verwerking recon</h2>";
$_SESSION['NAV'] = "";

$db = new DB();



$query = "SELECT Valutakoersen.Datum FROM (Valutakoersen) WHERE Valuta = 'EUR' ORDER BY Datum DESC";
$dRec = $db->lookupRecordByQuery($query);
$datum = dbdate2form($dRec["Datum"]);


$query = "
SELECT
  *
FROM
  reconJobs
WHERE
  status IN ('aangemaakt', 'verwerken', 'klaar')
ORDER BY
  prio,
  batchnr ASC
";

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";
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

echo template($__appvar["templateContentHeader"],$content);
?>

  <style>
   #addDialog{
     display: none;
     background:#eee;
     width: 100%;
   }
   #msgDialog{
     display: none;
     background:#60676a;
     color: white;
     font-size: 16px;
     border-radius: 10px;
     text-align: center;
     padding: 10px;
     width: 95%;
     margin-bottom: 20px;
   }
    .dropzone{
      margin-left: 10px;
      margin-right: 10px;
    }
    .leftTd{
      width: 100px;
      font-weight: bold;
      padding-left: 50px;
    }
    #tabs{
      display: block;

    }
    #tabs-1, #tabs-2{
      overflow-y: scroll;
    }

  </style>
  <button id="btnReload"><i class="fa fa-angle-double-right"></i> herladen</button>&nbsp;&nbsp;&nbsp;<button id="btnDaemon" class="btnRed"> Handmatig verwerking starten volgende job </button>
  <article id="msgDialog" data-len="<?=strlen($msg)?>"><?=$msg?></article>

  <div id="tabs">
    <ul>
      <li><a href="#tabs-1">Invoer/wachtrij</a></li>
      <li><a href="#tabs-2">Verwerkt</a></li>
    </ul>

    <div id="tabs-1">
      <button id="btnAdd" class="btnGreen">Toevoegen</button>&nbsp;&nbsp;&nbsp;
      <button id="btnDelete" class="btnRed">Verwijder selectie</button>
      <br/>
      <br/>
      <article id="addDialog">
        <h2>Batchjob aanmaken</h2>
        <h4>Batch id: <b><?=$job->batch?></b></h4>
        <p>
          Selecteer bestanden:
        </p>

        <!-- Change /upload-target to your upload address -->
        <form action="batch_reconFileUpload.php" class="dropzone" id="inpDropzone"></form>

        <form id="addForm" enctype="multipart/form-data" method="post" >
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="batchnr" value="<?=$job->batch?>">
          <table id="inpDuoFiles">
            <tr>
              <td class="leftTd"> positie <span id="posTxt"></span> :</td>
              <td class="rightTd"> <input type="file" name="positionFile" style="width: 600px"></td>
            </tr>
            <tr>
              <td class="leftTd"> cash <span id="casTxt"></span> :</td>
              <td class="rightTd"> <input type="file" name="cashFile" style="width: 600px"></td>
            </tr>

          </table>

          <table>
            <tr>
              <td>Omschrijving</td>
              <td>Depotbank</td>
              <td>Recondatum</td>
              <td>Prio</td>
            </tr>
            <tr>
              <td>
                <input name="naam" style="width: 400px" value="Recon job toegevoegd door <?=$USR?>" />
              </td>
              <td>
                <select name="depotbank" id="depotbank">
                  <option value="">---</option>
                  <optgroup label="1 bestand">
                    <option value="BIN">Binck</option>
                    <option value="FVL">FVL</option>
                    <option value="TGB">TGB</option>
                  </optgroup>
      <!--            <optgroup label="2 bestanden">-->
      <!--              <option value="AAB">ABN</option>-->
      <!--              <option value="GIRO">DeGiro</option>-->
      <!--              <option value="UBS">UBS</option>-->
      <!--            </optgroup>-->
                </select>
              </td>
              <td>
                <input type="text" name="reconDatum" id="reconDatum" class="AIRSdatepicker" value="<?= $datum ?>" style="width: 80px">
              </td>
              <td>
                <select name="prio">
                  <option value="1">1 (zsm)</option>
                  <option value="2">2 (na 9.30 uur)</option>
                  <option value="3" SELECTED>3</option>
                  <option value="4">4</option>
                  <option value="5" >5 (na 15 uur)</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                  <option value="8">8 (na 19 uur)</option>
                  <option value="9">9</option>
                </select>
              </td>
              <td>
                <input type="checkbox"  name="uitvoer" /> Afzonderelijke uitvoer

              </td>
            </tr>
          </table>

          <br/>
          <br/>
          <button id="btnAddForm" class="btnGreen">toevoegen</button>

        </form>
      </article>
      <table class="listTable" cellspacing="0" style="width: 100%">
        <colgroup>
          <col style="width: 5%;"/>
          <col style="width: 25%;"/>
          <col style="width: 25%"/>
          <col style="width: 25%"/>
          <col style=""/>
        </colgroup>
        <tr>
          <td class="listKop wp10"><input type="checkbox" id="selectAll"></td>
          <td class="listKop wp10">Batch</td>
          <td class="listKop wp30">Omschrijving</td>
          <td class="listKop wp10">Status</td>
          <td class="listKop wp5">Depotbank</td>
          <td class="listKop wp10">Recondatum</td>
          <td class="listKop wp5">Prio</td>
          <td class="listKop wp5">Aantal Bestanden</td>
          <td class="listKop wp5">Uitvoer</td>
          <td class="listKop wp5">TR regels</td>
          <td class="listKop wp15">Aangemaakt</td>
        </tr>
        <?
        $query = "
          SELECT
            *
          FROM
            reconJobs
          WHERE
            status IN ('aangemaakt', 'verwerken','afgekeurd')
          ORDER BY
            prio,
            batchnr ASC
          ";
        $db->executeQuery($query);
        while ($rec = $db->nextRecord())
        {
//debug($rec);
          $bestanden = (int)count(explode(",",substr($rec["bestanden"],1)));
          $mutatie  = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["change_date"]);
          if ($rec["queued"] == 1)
          {
            $checkbox = "<input type='checkbox' class='afvink' data-id='{$rec["id"]}' />";
          }
          else
          {
            $rows = $job->reconRows($rec["batchnr"]);
            if ($rows > 0)
            {
              $checkbox = "
                <button title='bekijk recon' class='reconL' data-id='".$rec["batchnr"]."'><i class='fa fa-table'></i></button>
                <button title='verwijder' class='btnDeleteBatch' data-id='".$rec["batchnr"]."'><i class='fa fa-trash'></i></button>";
            }
            else
            {

              $checkbox = "
              <button title='klik voor extra info' class='reconInfo' data-id='".$rec["batchnr"]."'><i class='fa fa-info'></i></button>
              <button title='klik voor extra info' class='btnDeleteBatch btnRed' data-id='".$rec["batchnr"]."'><i class='fa fa-trash'></i></button>";
            }

          }
          echo "
    <tr>
      <td class='wp10'>$checkbox</td>
      <td class='wp10'>{$rec["batchnr"]}</td>
      <td class='wp30'>{$rec["naam"]}</td>
      <td class='wp10'>{$rec["status"]}</td>
      <td class='wp5'>{$rec["depotbank"]}</td>
      <td class='wp10'>{$fmt->format("@D{form}",$rec["reconDatum"])}</td>
      <td class='wp5'>{$rec["prio"]}</td>
      <td class='wp5'>{$bestanden}</td>
      <td class='wp5'>{$rec["uitvoer"]}</td>
      <td class='wp5'>{$rows}</td>
      <td class='wp10' nowrap>{$rec["add_user"]}, {$fmt->format("@D{d}-{m} {H}:{i}",$rec["add_date"])}</td>
    </tr>";
        }

        ?>
      </table>
      <br/>      <br/>      <br/>      <br/>      <br/>
      <form action="" method="post" id="mutForm">
        <input type="hidden" name="portefeuille" value="<?=$_GET["p"]?>" />
        <input type="hidden" name="action" id="action" value="" />
        <input type="hidden" name="records" id="records" value="" />
      </form>
    </div>
    <div id="tabs-2" >

      <table class="listTable" cellspacing="0" >
        <colgroup>
          <col style="width: 5%;"/>
          <col style="width: 25%;"/>
          <col style="width: 25%"/>
          <col style="width: 25%"/>
          <col style=""/>
        </colgroup>
        <tr>
          <td class="listKop wp10"><input type="checkbox" id="selectAll"></td>
          <td class="listKop wp10">Batch</td>
          <td class="listKop wp30">Omschrijving</td>
          <td class="listKop wp10">Status</td>
          <td class="listKop wp5">Depotbank</td>
          <td class="listKop wp10">Recondatum</td>
          <td class="listKop wp5">Prio</td>
          <td class="listKop wp5">Aantal Bestanden</td>
          <td class="listKop wp5">Uitvoer</td>
          <td class="listKop wp5">TR regels</td>
          <td class="listKop wp10">Mutatie</td>
        </tr>
        <?
        $query = "
          SELECT
            *
          FROM
            reconJobs
          WHERE
            status IN ('klaar')
          ORDER BY
            change_date DESC
          ";

        $db->executeQuery($query);
        while ($rec = $db->nextRecord())
        {
//          debug($rec);
          $bestanden = (int)count(explode(",",substr($rec["bestanden"],1)));
          $mutatie  = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["change_date"]);
          if ($rec["queued"] == 1)
          {
//            $checkbox = "<input type='checkbox' class='afvink' data-id='{$rec["id"]}' />";
            $checkbox = "
            <button title='klik voor extra info' class='reconInfo' data-id='".$rec["batchnr"]."'>i</button>
            <button title='klik voor extra info' class='btnDeleteBatch' data-id='".$rec["batchnr"]."' data-jump='1'>d</button>
            ";
          }
          else
          {
            $rows = $job->reconRows($rec["batchnr"]);
            if ($rows > 0)
            {
              $checkbox = "
              <button title='bekijk recon' class='reconL' data-id='".$rec["batchnr"]."' data-jump='1'><i class='fa fa-table'></i></button>
              <button title='verwijderen' class='btnDeleteBatch btnRed' data-id='".$rec["batchnr"]."' data-jump='1'><i class='fa fa-trash'></i></button>";
            }
            else
            {
              continue;
              $checkbox = "...";
            }

          }
          echo "
    <tr>
      <td class='wp10'>$checkbox</td>
      <td class='wp10'>{$rec["batchnr"]}</td>
      <td class='wp30'>{$rec["naam"]}</td>
      <td class='wp20'>{$rec["status"]}</td>
      <td class='wp5'>{$rec["depotbank"]}</td>
      <td class='wp10'>{$fmt->format("@D{form}",$rec["reconDatum"])}</td>
      <td class='wp5'>{$rec["prio"]}</td>
      <td class='wp5'>{$bestanden}</td>
      <td class='wp5'>{$rec["uitvoer"]}</td>
      <td class='wp5'>{$rows}</td>
      <td class='wp10' nowrap>{$fmt->format("@D{d}-{m} {H}:{i}",$rec["change_date"])}</td>
    </tr>";
        }

        ?>
      </table>
    </div>
  </div>

  <div id="infoDialog" title="Info bij een batch">
    <div id="infoTxt">
      test 123

    </div>

  </div>


  <script>
    function collectData()
    {
      var records = [];
      console.log("start");
      $(".afvink:checked").each(function(index, elem)
      {

        records.push($(this).data("id"));

      });
      $("#records").val(records.join(","));
      console.log($("#records").val());
    }

    $(document).ready(function ()
    {

      var infoDialog = $('#infoDialog').dialog(
      {
        autoOpen: false,
        height: 500,
        width: '70%',
        modal: true,
        buttons:
          {
            "Sluiten": function()
            {
              $( this ).dialog( "close" );
            }
          },
          close: function ()
          {
          }
      });


      $( "#tabs" ).tabs();
<?
if ($_GET["jump"] == 1)
      {
?>
      $('#tabs').tabs('select', '#tabs-2');
<?
      }
?>

      $(".btnDeleteBatch").click(function(e){
        e.preventDefault();
        var b = $(this).data("id");
        var j = ($(this).data("jump") == 1)?"1":"0";
        window.open("batch_jobManager.php?action=deleteBatch&jump="+j+"&batch="+b,"content");
      });
      $("#inpDropzone").hide();
      $("#inpDuoFiles").hide();

      $("#depotbank").change(function(){
        var v = $(this).val();
        switch (v)
        {
          case "AAB":
            $("#posTxt").html("(5xx)");
            $("#casTxt").html("(940)");
            break;
          case "UBS":
            $("#posTxt").html("(ZAH)");
            $("#casTxt").html("(ZAQ)");
            break;
          default:
            $("#posTxt").html("x");
            $("#casTxt").html("x");
        }
        switch (v)
        {
          case "":
            $("#inpDropzone").hide();
            $("#inpDuoFiles").hide();
            break;
          case "BIN":
          case "FVL":
          case "TGB":
            $("#inpDropzone").show(300);
            $("#inpDuoFiles").hide();
            break;
          case "GIRO":
          case "UBS":
          case "AAB":
            $("#inpDropzone").hide();
            $("#inpDuoFiles").show(300);
            break;
        }
      })

      if ($("#msgDialog").data("len") > 0)
      {
        $("#msgDialog").show(300);
        setTimeout(function(){ $("#msgDialog").hide(300); },2000)
      }

      $("#selectAll").change(function () {
        console.log("change");
        if ($("#selectAll").is(":checked"))
        {
          console.log("check");
          $(".afvink").prop('checked',true);
        }
        else
        {
          console.log("uncheck");
          $(".afvink").prop('checked',false);
        }
      });

      $("#btnAdd").click(function(e){
        e.preventDefault();

        $("#btnAdd").hide();
        $("#btnDelete").hide();

        $("#addDialog").show(300);

      });
      $("#btnReload").click(function(e){
        e.preventDefault();
        window.open("?", "content");
      });

      $("#btnDaemon").click(function(e){
        e.preventDefault();
        window.open("recon/bathReconVerwerk.php", "daemon");
      });

      $(".reconL").click(function(e){
        e.preventDefault();
        window.open("tijdelijkereconList.php?batch="+$(this).data("id"), "content");
      });

      $(".reconInfo").click(function(e){
        e.preventDefault();
       $("#infoTxt").load("ajax/batchReconInfo.php?batch="+$(this).data("id"));
       infoDialog.dialog("open");
      });

      $("#btnDelete").click(function(e){
        e.preventDefault();
        $("#action").val("delete");
        collectData();
        $("#mutForm").submit();
      });




    });
  </script>
<?
if($__debug)
{
  echo getdebuginfo();
}
echo "<br/><br/><br/><br/>";
echo template($__appvar["templateRefreshFooter"],$content);