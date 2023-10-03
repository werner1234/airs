<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/06/12 14:08:15 $
    File Versie         : $Revision: 1.7 $

    $Log: dashboard.php,v $
    Revision 1.7  2020/06/12 14:08:15  rm
    8689

    Revision 1.6  2017/07/21 13:53:42  cvs
    call 5933

    Revision 1.5  2017/06/26 11:37:54  cvs

    Revision 1.4  2017/06/02 14:21:10  cvs
    no message

    Revision 1.3  2017/04/26 15:20:05  cvs
    call 5816

    Revision 1.2  2017/04/26 15:06:34  cvs
    call 5816

    Revision 1.1  2017/04/26 07:57:50  cvs
    no message



*/

include_once("init.php");
include_once("../../classes/htmlReports/htmlDashboardHelper.php");
include_once("../../classes/HTML_rapportList.php");


$frmt = new AE_cls_formatter();

$tmpl = new AE_template();
$tmpl->templatePath = getcwd()."/classTemplates/";
$tmpl->appendSubdirToTemplatePath("dashboard");
$tmpl->loadTemplateFromFile("TRANS_dataRow.html","TRANS_datarow");

$fmt = new AE_cls_formatter();
$data = array_merge($_GET,$_POST);

$portefeuille = $data["port"];
if ($data['start'] != "")
{
  $p = explode("-",$data["start"]);
  $startInterval = $p[2]."-".$p[1]."-".$p[0];
}
else
{
  $startInterval = (date("Y")-1)."-01-01";
  $data["start"] = "01-01-".(date("Y")-1);
}

if ($data['stop'] != "")
{
  $p = explode("-",$data["stop"]);
  $stopInterval = $p[2]."-".$p[1]."-".$p[0];
}
else
{
  $stopInterval = date("Y-m-d");
  $data["stop"] = date("d-m-Y");
}


$list = new rapportList("htmlTRANS", $portefeuille);
$list->postData['portefeuille'] = $portefeuille;
$list->setRapportData();
$list->postData["reportDate"] = $data["start"] ." t/m ".$data['stop'];


$dash = new htmlDashboardHelper($portefeuille);
$dash->initModule();

unset($_SESSION["NAV"]);
unset($_SESSION["submenu"]);



$portRec = $dash->portefeuilleData;

$USR = $_SESSION["USR"];
$_ATT = array();
$_TRANS = array();
$content = array(
  "style"  => '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">',
  "style2" => "
<link rel='stylesheet' href='../HTMLrapport/css/HTMLrapportes.css'>
<link rel='stylesheet' href='../style/aeStyle.css'>
<link rel='stylesheet' href='../style/fontAwesome/font-awesome.min.css'>
<link rel='stylesheet' href='../style/jquery.css' >
<link rel='stylesheet' href='../style/smoothness/jquery-ui-1.11.1.custom.css' >
<script type='text/javascript' src='../javascript/jquery-min.js'></script>
<script type='text/javascript' src='../javascript/jquery-ui-min.js'></script>


<script type='text/javascript' src='../javascript/bootstrapTooltip.js'></script>
"
);
echo template("../".$__appvar["templateContentHeader"],$content);

echo $list->getRapportHeader($rapportBackButtons);
?>

<style>
  *{
    font-family: "Lucida Grande", "trebuchet ms", verdana, sans-serif;
  }
  #contenContainer{
    width: calc(100% - 20px) !important;
    height: 99%;
    /*margin: 15px;*/
  }
  .contentRow{

    margin:9px;
  }
  .contentRow:after{
    clear: both;
  }
  .cellHeader{
    /*width: calc(100% - 4px);*/
    background: #143C5A;
    color: white;
    font-size: 1.5em;
    padding: 3px;
    text-align: center;
    margin: 0;

  }
  .contentLeft{
    width: 48%;
    float: left;
    min-height: 300px;
    border: 1px solid #eee;
    box-shadow: #EEE 3px 3px 3px;
  }
  .contentRight{
    width: 48%;
    float: right;
    min-height: 300px;
    border: 1px solid #eee;
    box-shadow: #EEE 3px 3px 3px;
  }
  .trHeader td{
    background: #999;
  }
  .loading{
    width: 100%;
    height: 100%;
    background: url("../images/loading.gif") no-repeat center;
  }
</style>

<div id="contenContainer">
  <div class="contentRow">

    <div class="contentLeft">
      <div class="cellHeader"><?=vt('Asset verdeling');?></div>
      <div id="containerASSET">
        <div class="loading"></div>
      </div>
    </div>
    <div class="contentRight">
      <div class="cellHeader"><?=vt('Verloop vermogen');?></div>
      <div id="containerVV">
        <div class="loading"></div>
      </div>
    </div>
  </div>
  <div style="clear: both"></div>
  <div class="contentRow">
    <div class="contentLeft">
      <div class="cellHeader"><?=vt('Laatste 10 transacties');?></div>
      <div id="containerTRANS" style="height: 300px; width: 99%; float: left">
        <div class="loading"></div>
      </div>
    </div>
    <div class="contentRight">
      <div class="cellHeader"><?=vt('Ontwikkeling rendement');?></div>
      <div id="containerRENDEMENT" style="height: 300px; width: 99%; float: left">
        <div class="loading"></div>
      </div>
    </div>
  </div>
</div>

<?php
 $param = "port=".$portefeuille."&start=".$startInterval."&stop=".$stopInterval;
?>
<script type="text/javascript" src="../javascript/jquery-min.js"></script>
<script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>
<script type="text/javascript" src="../javascript/canvasjs.min.js"></script>
<script type='text/javascript' src='../javascript/dropdown.js'></script>

  <script>
    $(document).ready(function(){

      $('.dropdown-toggle').dropdown();
      $("#vanafStart").click(function (e)
      {
        e.preventDefault();
        $("#periodeStartDatum").val($("#vanafStart").data("value"));
      });
      $("#YTD").click(function (e)
      {
        e.preventDefault();
        $("#periodeStartDatum").val($("#YTD").data("value"));
        $("#periodeStopDatum").val("<?=date("d-m-Y");?>");
      });

      filterDialog = $('#filterDialog').dialog({
        autoOpen: false,
        height: 500,
        width: '40%',
        modal: true,
        buttons: {},
        close: function ()
        {
        }
      });

      $(document).on('click', '.closeFilter', function () {
        filterDialog.dialog('close');
      });

      $('#filterDialogBtn').on('click', function ()
      {
        filterDialog.dialog('open');
      });

      $('#tabs').tabs();

      $( ".AIRSdatepicker" ).datepicker({
        showOn: "button",
        buttonImage: "../javascript/calendar/img.gif",//"images/datePicker.png",
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
        yearRange: '2000:2050',
        closeText: "sluiten",
        showAnim: "slideDown",
        showButtonPanel: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 2,
        showWeek: true,
        firstDay: 1
      });

    });
  </script>

<script type="text/javascript">
  $(document).ready(function(){
    $("#containerTRANS").load(encodeURI("dashboard_transacties.php?<?=$param?>"));
    $("#containerASSET").load(encodeURI("dashboard_assetVerdeling.php?<?=$param?>"));
    $("#containerVV").load(encodeURI("dashboard_verloopVermogen.php?<?=$param?>"));
    $("#containerRENDEMENT").load(encodeURI("dashboard_rendement.php?<?=$param?>"));
  });

</script>





  <div id="filterDialog" title="Filter">
    <div id="tabs">
      <ul>
        <li><a href="#tabs-1"><?=vt('Periode');?></a></li>
      </ul>
      <div id="tabs-1" style="padding:0px;">
        <form  method="POST" id="generateForm">
          <input type="hidden" name="port" value="<?=$portefeuille?>" />
          <input type="hidden" name="rapport_types" value="MUT" />

          <div class="padded-10" style="display: inline-block;">
            <br />
            <table border="0">
              <tr>
                <td width="200px" style="border: none; vertical-align: top;">
                  <label for="periodeStartDatum"><?=vt('Vanaf datum');?></label>
                  <br />
                  <input size="10" type="text" name="start" id="periodeStartDatum" class="AIRSdatepicker" value="<?=$frmt->format("@D{form}", $startInterval)?>"/>
                  <br /><br />
                  <button class="btn-new btnValue" id="vanafStart" data-value="<?=jul2form(db2jul($list->postData['Startdatum']))?>"><?=vt('Vanaf start');?></button>
                  <button class="btn-new btnValue" id="YTD" data-value="01-01-<?=date("Y")?>" ><?=vt('YTD');?></button>
                </td>
                <td width="200px" style="border: none; vertical-align: top;">
                  <label for="periodeStopDatum"><?=vt('Tot datum');?> </label>
                  <br />
                  <input size="10" type="text" name="stop" id="periodeStopDatum" class="AIRSdatepicker" value="<?=$frmt->format("@D{form}", $stopInterval)?>"/>
                  <br /><br /><br />
                </td>
              </tr>
            </table>
            <br />
          </div>

          <div class="form-actions">
            <span class="closeFilter btn-new btn-default"><i class="fa fa-times" aria-hidden="true"></i> <?=vt('Sluiten');?></span>
            <button id="btnGenerate" class="btn-new btn-save" ><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=vt('Dashboard opnieuw genereren');?></button>
        </form>
      </div>
    </div>


  </div>



<?

echo template("../".$__appvar["templateRefreshFooter"],$content);
flush();
flush();
flush();
$dash->getData(false, $startInterval, $stopInterval);
