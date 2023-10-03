<?php
/*
    AE-ICT CODEX source module versie 1.6, 20 juni 2011
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/05/20 12:11:58 $
    File Versie         : $Revision: 1.40 $
*/
include_once("init.php");
include_once("../../classes/HTML_rapportList.php");
$data = array_merge($_POST, $_GET, $_SESSION["htmlRapportVars"]);
//$__debug=true;debug($data);
$editScript = "positielijstEdit.php";
$allow_add  = false;
$__appvar['rowsPerPage'] = 1000;
$list = new rapportList("htmlATT", $_GET["portefeuille"]);
$list->postData = $data;
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("htmlATT","datum",array());
$list->addColumn("htmlATT","waardeBegin",array());
$list->addColumn("htmlATT","stortingen",array());
$list->addColumn("htmlATT","onttrekkingen",array());
$list->addColumn("htmlATT","ongerealiseerd",array());
$list->addColumn("htmlATT","opbrengsten",array());
$list->addColumn("htmlATT","kosten",array());
$list->addColumn("htmlATT","rente",array());
$list->addColumn("htmlATT","resultaatVerslagperiode",array());
$list->addColumn("htmlATT","waardeHuidige",array());
$list->addColumn("htmlATT","performance",array());
$list->addColumn("htmlATT","perfCumulatief",array());
$list->addColumn("htmlATT","specifiekeIndexPerformance",array("hideColumn"=>true));
$list->addColumn("htmlATT","specifiekeIndexVorige",array("hideColumn"=>true));
$list->addColumn("htmlATT","specifiekeIndex",array("hideColumn"=>true));
$list->addColumn("htmlATT","gerealiseerd",array("hideColumn"=>true));


$list->setupFilter('attHtmlRapport', array(
//  'groupings' => array ('hoofdcategorie', 'beleggingscategorie'),
  'sortFields' => array('datum'),
  'sortOrder' => array('ASC'),
  'hideOrderBreak' => false
));

$db = new DB();
$query = "SELECT * FROM Fondsen WHERE Fonds ='".$data["specifiekeIndex"]."' ";
$fondsRec = $db->lookupRecordByQuery($query);

switch ($_GET["interval"])
{
  case "btnKwartaal":
    $list->setWhere("soort = 'kwartaal'");
    break;
  case "btnJaar":
    $list->setWhere("soort = 'jaar'");
    break;
  default:
    $_GET["interval"] = "btnMaand";
    $list->setWhere("soort = 'maand'");
}
// set searchstring
$list->setSearch((isset ($_GET['selectie']) ? $_GET['selectie'] : null));
// select page
$list->selectPage((isset ($_GET['page']) ? $_GET['page'] : null));



$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList((isset ($_GET['page']) ? $_GET['page'] : null), $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch((isset ($_GET['selectie']) ? $_GET['selectie'] : null)));

$content['pageHeader'] = "<br />";

$content['javascript'] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";

$list->postData['allowExport'] = true;

$list->setRapportData();

switch ($_GET["interval"])
{
  case "btnKwartaal":
    $list->setWhere("soort = 'kwartaal'");
    break;
  case "btnJaar":
    $list->setWhere("soort = 'jaar'");
    break;
  default:
    $_GET["interval"] = "btnMaand";
    $list->setWhere("soort = 'maand'");
}

if ( ! isset($data['type']) ) {
  echo $list->getRapportJsCss($content, 'trans');
}

$list->postData['reportDate'] = dbdate2form($list->postData['start']) .' t/m '. dbdate2form($list->postData['stop']);

$letopTxt = "";
if (getVermogensbeheerderField("PerformanceBerekening") != 4)
{
  $letopTxt = vt("(LET OP: performance kan afwijken van de clientrapportage. Onderstaande is gebaseerd op maandelijkse waardering)");
}

$list->postData['currentHtmlRapportUrl'] = 'rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=ATT&Portefeuille='.$list->postData['portefeuille'].'&datum_van='.$list->postData['start'].'&datum_tot='.$list->postData['stop'];
$_SESSION['currentHtmlRapportUrl'] = $list->postData['currentHtmlRapportUrl'];

if ( ! isset($data['type']) ) {
  echo $list->getRapportHeader($rapportBackButtons, null, $letopTxt);
}


if ( isset($data['type']) && $data['type'] === 'xls' ) {
  $headers = $list->getHeaderExport($portefeuilleData);

  while($listDatas = $list->getRow())
  {
    $listDatas["ongerealiseerd"]["value"] = $listDatas["ongerealiseerd"]["value"] + $listDatas["gerealiseerd"]["value"];
    $rowData[] = $list->buildRowExport($listDatas);
  }
  $list->makeXls($headers, $rowData, $list->postData['portefeuille'] . '_ATT');
  exit();
}
?>


  <div class="box box12" style="overflow: auto;">

    <div class="wrapper1">
      <div class="div1">
      </div>
    </div>

    <table id="transTable" class="table-hover" cellspacing="0" border="0" style="display: table; width: 100%;">
      <?php
      echo $list->printHeader($portefeuilleData);

      /**
       * Groeperings header opnieuw instellen dmv een class
       */
      //      debug($list->filter['groupings']);
      foreach ( $list->filter['groupings'] as $groupingKey => $grouping )
      {
        $list->sortData[$grouping]['headerClass'] = 'header' . ($groupingKey + 1);
      }

      /** pak de 1e en laatste value van de grouping array */
      $list->setOrderBlock(reset($list->filter['groupings']), end($list->filter['groupings']));

      $started   = false;
      $benchmarkCumArray = 0;
      $grafDate  = "";
      $firstRow = null;

      $totalPerf = -152;
      $lastRecord = array();
      while($listData = $list->getRow())
      {

        $listData["ongerealiseerd"]["value"] = $listData["ongerealiseerd"]["value"] + $listData["gerealiseerd"]["value"];
        if (!$started AND
          $listData["perfCumulatief"]["value"] <> 0 AND
          $listData["performance"]["value"] <> 0 AND
          $listData["specifiekeIndexPerformance"]["value"] <> -100 )
        {
          $started = true;
          $grafDate = $listData["datum"]["value"];
        }

        $portPerfCumArray[$listData["datum"]["value"]] = round($listData["perfCumulatief"]["value"], 2);
        $portPerfArray[$listData["datum"]["value"]] = round($listData["performance"]["value"], 2);


        if ($started)
        {

          $BmArray[$listData["datum"]["value"]] = round($listData["specifiekeIndexPerformance"]["value"],2);
          if ($totalPerf == -152 AND $BmArray[$listData["datum"]["value"]] != 0)
          {
            $totalPerf = $BmArray[$listData["datum"]["value"]];
          }
          else
          {
            $totalPerf =  ( (( 1 + ($totalPerf/100)) * ((1 + ($BmArray[$listData["datum"]["value"]]/100)))) -1 ) * 100;
          }

          $BmCumArray[$listData["datum"]["value"]] = round($totalPerf,2);
        }
        else
        {
          $BmArray[$listData["datum"]["value"]] = 0;
          $BmCumArray[$listData["datum"]["value"]] = 0;
        }

        /**
         * Controlleer de order breaks
         */
        $list->checkOrderBreak($listData, $lastRecord);

        $list->sumRowTotals($listData);                                    // totaliseer voor Eindtotaal
        echo $list->buildRow($listData);                                   // print regel
        if ( empty ($firstRow) ) {$firstRow = $listData;}
        $lastRecord = $listData;
      }
//debug($BmArray);
//debug($BmCumArray);
      $list->reportTotals['waardeBegin'] = $firstRow['waardeBegin']['value'];
      $list->reportTotals['waardeHuidige'] = $lastRecord['waardeHuidige']['value'];
      $list->reportTotals['perfCumulatief'] = $lastRecord['perfCumulatief']['value'];

      $list->printTotalFooter($lastRecord);
      ?>
    </table>
  </div>

  <div id="filterDialog" title="Filter">
    <div id="tabs">
      <ul>
        <li><a href="#tabs-2"><?=vt('Periode');?></a></li>
        <li><a href="#tabs-1"><?=vt('kolommen tonen/verbergen');?></a></li>

      </ul>
      <div id="tabs-1" style="padding:0px;">
        <form  action="<?=$_SERVER['REQUEST_URI'];?>" method="POST">

          <ul style="list-style: none;">
            <?php
            foreach ( $list->columns as $colomnData )
            {
              $options = array_merge($list->getPropArray($colomnData['name']),$colomnData["options"]);
              if ( ! isset ($options['hideColumn']) || $options['hideColumn'] === false && ( ! isset ($options['fixed']) || $options['fixed'] === false ) )
              {
                echo '<li>
              <input name="columnVisible['.$colomnData['name'].']"  class="showHideFilter" type="hidden" data-colname="'.$colomnData['name'].'" value="off" >
              <input name="columnVisible['.$colomnData['name'].']"  class="showHideFilter" type="checkbox" '.($options['visible'] === true ? 'checked="checked"' : '').' data-colname="'.$colomnData['name'].'" >
              <input name="columnOrder[]" value="'.$colomnData['name'].'"  type="hidden"  >
              ' . strip_tags ($options['description']) . '</li>';
              }
            }
            ?>
          </ul>

          <div class="form-actions">
            <span class="closeFilter btn-new btn-default"><i class="fa fa-times" aria-hidden="true"></i> <?=vt('Sluiten');?></span>
            <button name="saveBtn" value="showVisibility" class="btn-new btn-save" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=vt('Opslaan');?></button>
            <button name="saveBtn" value="saveVisibility" class="btn-new btn-save" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=vt('Opslaan als standaard');?></button>
          </div>
        </form>
      </div>

      <div id="tabs-2" style="padding:0px;">
        <form  action="../rapportFrontofficeClientAfdrukkenHtml.php" method="POST" id="generateForm">
          <input type="hidden" name="Portefeuille" value="<?=$data["portefeuille"]?>" />
          <input type="hidden" name="altFonds" id="altFonds" value="<?=$data["altFonds"]?>" />
          <input type="hidden" name="srtAlt" id="srtAltDialog" value="<?=$data["srtAlt"]?>" />
          <input type="hidden" name="rapport_types" value="ATT" />
          <div class="padded-10" style="display: inline-block;">
            <br />
            <table border="0">
              <tr>
                <td width="200px" style="border: none; vertical-align: top;">
                  <label for="periodeStartDatum"><?=vt('Vanaf datum');?></label>
                  <br />
                  <input size="10" type="text" name="datum_van" id="periodeStartDatum" class="AIRSdatepicker" value="<?=jul2form(db2jul($data['start']))?>"/>
                  <br /><br />
                  <button class="btn-new btnValue" data-btn="<?=jul2form(db2jul($list->postData['Startdatum']))?>"><?=vt('Vanaf start');?></button>
                  <button class="btn-new btnValue" data-end="<?=date('d-m-Y');?>" data-btn="01-01-<?=date("Y")?>" ><?=vt('YTD');?></button>
                </td>
                <td width="200px" style="border: none; vertical-align: top;">
                  <label for="periodeStopDatum"><?=vt('Tot datum');?> </label>
                  <br />
                  <input size="10" type="text" name="datum_tot" id="periodeStopDatum" class="AIRSdatepicker" value="<?=jul2form(db2jul($data['stop']))?>"/>
                  <br /><br /><br />
                </td>
              </tr>
            </table>
            <br />
          </div>

          <div class="form-actions">
            <span class="closeFilter btn-new btn-default"><i class="fa fa-times" aria-hidden="true"></i> <?=vt('Sluiten');?></span>
            <button id="btnGenerate" class="btn-new btn-save" ><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=vt('Rapport opnieuw genereren');?></button>
          </div>
        </form>

        <div id="showReportFor" style="margin-left: 20px;margin-top: 30px;">
          <p><strong><?=vt('Rapport tonen per');?>:</strong></p>
          <button class="btn-new btn-default switchButtons" id="btnMaand"><?=vt('maanden');?></button>
          <button class="btn-new btn-default switchButtons" id="btnKwartaal"><?=vt('kwartalen');?></button>
          <button class="btn-new btn-default switchButtons" id="btnJaar"><?=vt('jaren');?></button>
        </div>

      </div>

    </div>

  </div>

  <div style="margin-top:20px;" class="formHolder box box12">
    <div style="height:30px;" class="formTitle textB">
      <span><?=vt('Vergelijkingsmaatstaven');?></span>
    </div>


    <div class="formContent">
      <div style="margin-left:13px; margin-bottom: -3px;">
        <article id="fondsInput">
          <table>
            <thead>
            <tr>
              <td></td>
              <td><?=vt('periode');?></td>
              <td><?=vt('cumulatief');?></td>
            </tr>
            </thead>
            <tr>
              <td><label for="enablePortefeuille"> <?=vt('Portefeuille');?>: <b><i><?=$data['portefeuille']?></i></b></label></td>
              <td class="ac"><input type="checkbox" class="chkBoxes" id="enablePortefeuille" checked /></td>
              <td class="ac"><input type="checkbox" class="chkBoxes" id="enableCumPortefeuille" checked /></td>
            </tr>
            <?php if ($BmCumArray <> 0 AND trim($data["specifiekeIndex"]) <> "") { ?>
              <tr>
                <td><label for="enableIndex"> <?=vt('Gekoppelde benchmark');?>: <b><i><?=$fondsRec["Omschrijving"]?></i></b></label></td>
                <td class="ac"><input type="checkbox" class="chkBoxes" id="enableIndex" checked /></td>
                <td class="ac"><input type="checkbox" class="chkBoxes" id="enableCumIndex" checked /></td>
              </tr>
            <?php }
            if ($data["srtAlt"] != "")
            {
              $selectedF = ($data["srtAlt"] == "fonds")?"SELECTED":"";
              $selectedP = ($data["srtAlt"] != "fonds")?"SELECTED":"";
            }
?>
            <tr>
              <td><label for="enableFonds"> <?=vt('Alternatieve vergelijking');?>:
                  <select id="srtAlt" name="srtAlt">
                    <option value="fonds" <?=$selectedF?>><?=vt('Fonds');?></option>
                    <option value="portefeuille" <?=$selectedP?>><?=vt('Portefeuille');?></option>
                  </select>
                  <input type="name" id="fonds" name="fonds" placeholder="<?= vt('Selecteer fonds/ISIN'); ?>"  /></label></td>
              <td class="ac"><input type="checkbox" class="chkBoxes" id="enableFonds" /></td>
              <td class="ac"><input type="checkbox" class="chkBoxes" id="enableCumFonds" /></td>
            </tr>
          </table><br />
        </article>
      </div>
    </div>

    <article class="extraInfoiArticle" >
      <table class="attTable" >
        <tr>
          <td>
            <br/><br/>
            <div class="extraInfoChart" id="chartContainer"></div>
          </td>
        </tr>
      </table>
  </div>
  </article>

  <style>
    .tooltipSpan{
      display: inline-block;
      width: 100px;
    }
  </style>

<?


$title = "performance";
$fmt = new AE_cls_formatter();
//debug($chartArray);
$labels[] = "\n    { label: '" . vt('begin') . "', x: 0,  y: 0},";

$indx = 0;

foreach ($portPerfCumArray as $k => $v)
{
  $indx++;
  $labels[] = "\n    { label: '".$fmt->format("@D{d}-{m}-{Y}",$k)."', x: $indx,  y: ".$v.", " . vt('delta') . ": ".$portPerfArray[$k]."},";

}
$labels2[] = "\n    { label: '" . vt('begin') . "', x: 0,  y: 0},";

$indx = 0;
foreach ($portPerfArray as $k => $v)
{
  $indx++;
  $labels2[] = "\n    { label: '".$fmt->format("@D{d}-{m}-{Y}",$k)."', x: $indx,  y: ".$v." },";

}
$labels3[] = "\n    { label: '" . vt('begin') . "', x: 0,  y: 0},";
$indx = 0;
foreach ($BmCumArray as $k => $v)
{
  $indx++;
  $labels3[] = "\n    { label: '".$fmt->format("@D{d}-{m}-{Y}",$k)."', x: $indx,  y: ".$v.", " . vt('delta') . ": ".$BmArray[$k]."},";

}
$labels4[] = "\n    { label: '" . vt('begin') . "', x: 0,  y: 0},";
$indx = 0;
foreach ($BmArray as $k => $v)
{
  $indx++;
  $labels4[] = "\n    { label: '".$fmt->format("@D{d}-{m}-{Y}",$k)."', x: $indx,  y: ".$v." },";

}

$arg = explode("&",$_SERVER["QUERY_STRING"]);


$urlArg = $_SERVER["SCRIPT_NAME"]."?";
foreach ($arg as $item)
{
  if (substr($item,0,8) != "interval")
  {
    $urlArg .= $item."&";
  }
}
$urlArg .= "interval=";




?>
  <script type="text/javascript" src="../javascript/canvasjs.min.js"></script>
  <script>
    var fondsLabels         = {};
    var portefeuilleOff     = {};
    var portefeuilleOn      = {};
    var portefeuilleCumOn   = {};
    var portefeuilleCumOff  = {};
    var fondsCumOff         = {};
    var fondsCumOn          = {};
    var fondsOff            = {};
    var fondsOn             = {};
    var benchOn             = {};
    var benchOff            = {};
    var benchCumOn          = {};
    var benchCumOff         = {};
    var indexFonds          = "";
    var altFondsOms         = "";
    var fondsEnabled        = false;

    function getIndex(deIndex)
    {
      console.log("getIndex");
      $.ajax(
        {
          type: "POST",
          url: "../lookups/getFondsIndexData.php",
          async: false,
          dataType: 'json',
          data:
          {
            soort: $("#srtAlt").val(),
            fonds: deIndex,
            start: "<?=$data["start"]?>",
            stop: "<?=$data["stop"]?>",
            interval: "<?=$_GET["interval"]?>",
            grafdate: "<?=$grafDate?>"
          }
        }).done(function (msg)
      {
          console.log(msg);
        fondsLabels = msg[0];
        fondsCumLabels = msg[1];
        altFondsOms = msg[2];
      });
    }

    var chart = new CanvasJS.Chart("chartContainer",
      {
        exportEnabled: true,
        theme: "theme4",
        name: "Rendement:",
        title:{ fontSize: 16,
          fontWeight: "bold",
          text: "<?=vt('Rendement in %, periode');?> <?=$fmt->format("@D{form}",$data["start"])?> t/m <?=$fmt->format("@D{form}",$data["stop"])?>"  },
        animationEnabled: true,

        axisX:{  labelAngle: -50,     lineDashType: "dot"},
        axisY:{  title: "",           lineDashType: "dot",    stripLines: [{  value:0, color: "Navy", lineDashType: "solid"  }] },
        data: [
          {  // portefeuilleOn  // range 0
            type: "column",
            color: "#b9b9eb",
            legendText:"<?=vt('Portefeuille');?>: <?=$data['portefeuille']?>",
            showInLegend: true,
            dataPoints:
              [
                <?=implode("\n",$labels2);?>
              ]
          },
          {  // portefeuilleCumOn  // range 1
            type: "spline",
            color: "#5555c8",
            legendText:"<?=vt('Portefeuille Cumulatief');?>",
            showInLegend: true,
            toolTipContent: "{label} <?=vt('portefeuille');?><hr/><span class='tooltipSpan'><?=vt('cumulatief');?></span>: {y}%<br/><span class='tooltipSpan'><?=vt('delta');?></span>: {delta}%",
            dataPoints:
              [
                <?=implode("\n",$labels);?>
              ]
          },
          {
            <?
            if (trim($data["specifiekeIndex"]) <>  "")
            {
            ?>
            // benchOn  // range 2
            type: "column",
            color: "#fa7864",
            legendText:"Benchmark: <?=$fondsRec["Omschrijving"]?>",
            showInLegend: true,
            dataPoints:
              [
                <?=implode("\n",$labels4);?>
              ]
          },
          { // benchCumOn  // range 3
            type: "spline",
            color: "#e6280a",
            legendText:"Benchmark Cumulatief",
            showInLegend: true,
            toolTipContent: "{label} Benchmark<hr/><span class='tooltipSpan'><?= vt('cumulatief'); ?></span>: {y}%<br/><span class='tooltipSpan'><?= vt('delta'); ?></span>: {delta}%",
            dataPoints:
              [
                <?=implode("\n",$labels3);?>
              ]
          },
        <?
        }
        else
        {
        ?>
      },
      {},
      <?
      }
      ?>
      {},  // range 4
      {},  // range 5
    ],
    legend:
    {
      cursor: "pointer",
        horizontalAlign: "center", // left, center ,right
      verticalAlign: "bottom",
      itemclick: function (e)
    {
      if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible)
      {
        e.dataSeries.visible = false;
      } else
      {
        e.dataSeries.visible = true;
      }
    }
    }
    });

    /*
     document ready
     */




    $(document).ready(function()
    {

      $(document).on('click', 'button[name=saveBtn]', function (e) {
        e.preventDefault();
        $btn = $(this);
        AEConfirm(
          'Weet u zeker dat u deze instellingen wilt opslaan?',
          'Standaard instellingen',
          function () {
            $('<input>').attr('type','hidden').attr('name','saveBtn').attr('value',$btn.val()).appendTo($btn.parents('form:first'));
            $btn.parents('form:first').submit();
          },
          function () {
            return false;
          }
        );
      });


      $("#btnGenerate").click(function(e){
        e.preventDefault();
        $("#srtAltDialog").val($("#srtAlt").val());
        $("#altFonds").val($("#fonds").val());
        $("#btnGenerate").waitMe({
          effect: 'bounce',
          text: 'herberekenen',
          bg: 'rgba(255,255,255,.8)',
          color: 'Navy',
          maxSize: 20,
          textPos: 'horizontal',
          fontSize: '14px',
          onClose: function() {}
        });
        $("#generateForm").submit();
      });
      $(".btnValue").click(function(e)
      {
        e.preventDefault();
        $("#periodeStartDatum").val($(this).attr("data-btn"));

        if ( typeof $(this).attr("data-end") != 'undefined' ) {
          $("#periodeStopDatum").val($(this).attr("data-end"));
        }
      });

      $("#terugBtn").click(function(e)
      {
        var ind = $(this).attr("id");
        console.log(ind);
        window.open("../rapportFrontofficeClientSelectie.php", "content");
      });

      $(".switchButtons").click(function(e)
      {
        var ind = $(this).attr("id");
        console.log(ind);
        console.log("<?=$urlArg?>"+ind);

        window.open("<?=$urlArg?>"+ind, "content");
      });


      $(".switchButtons").click(function(){

        var sw = $(this).attr("id");
        var url= "<?=$urlArg?>";
        window.open(url + sw + "&ind=" + fondsEnabled,"content");

      });
      $("#enableFonds").hide();
      $("#enableCumFonds").hide();
      var altFonds = $("#altFonds").val();
      var urlAlt   = "../lookups/getFondsIndex.php?start=<?=$data["start"]?>&stop=<?=$data["stop"]?>";
      console.log("voor "+altFonds);
      if (altFonds != "")
      {
        console.log("in "+altFonds);
        getIndex(altFonds);
        indexFonds = altFonds;
        $("#fonds").val(altFonds);
        $("#enableFonds").show(300);
        $("#enableFonds").prop('checked', true).change();
        $("#enableCumFonds").show(300);
        $("#enableCumFonds").prop('checked', true).change();
      }
      $("#srtAlt").change(function(){
        if ($("#srtAlt").val() == "fonds")
        {

          urlAlt = "../lookups/getFondsIndex.php?start=<?=$data["start"]?>&stop=<?=$data["stop"]?>";
        }
        else
        {
          urlAlt = "../lookups/getPortefeuille.php";
        }
        $('#fonds').autocomplete("option", { source: urlAlt });
        console.log(urlAlt);
      });

      $("#fonds").autocomplete(
        {

          source : urlAlt,
          create : function(event, ui)// onCreate sla oude waardes op om te kunnen resetten in onClose bij geen selectie
          {

          },
          close : function(event, ui)// controle of ID gevuld is anders reset naar onCreate waarden
          {
          },
          search : function(event, ui)// als zoeken gestart het ID veld leegmaken
          {
            $("#enableFonds").prop('checked', false).change();
          },
          select : function(event, ui)// bij selectie clientside vars updaten
          {
            if (($("#srtAlt").val() == "fonds"))
            {
              $("#fonds").val(ui.item.Fonds);
              $("#altFonds").val(ui.item.Fonds);
              getIndex(ui.item.Fonds);
              indexFonds = ui.item.Fonds;
            }
            else
            {
              $("#fonds").val(ui.item.portefeuille);
              getIndex(ui.item.portefeuille);
              indexFonds = ui.item.portefeuille ;
            }

            $("#enableFonds").show(300);
            $("#enableFonds").prop('checked', true).change();
            $("#enableCumFonds").show(300);
            $("#enableCumFonds").prop('checked', true).change();

          },
          //autoFocus: true,
          minLength : 2, // pas na de tweede letter starten met zoeken
          delay : 0

        });

      //indexFonds = "<?=$_SESSION["htmlATT"]["fonds"]?>";
      indexFonds = $("#fonds").val();

      portefeuilleOn     = chart.options.data[0];
      portefeuilleCumOn  = chart.options.data[1];
      benchOn            = chart.options.data[2];
      benchCumOn         = chart.options.data[3];

      $("#fonds").val(indexFonds);
      if ( indexFonds != "")
      {
        $("#enableFonds").prop('checked', true);
        $("#enableCumFonds").prop('checked', true);
      }

      $("#<?=$_GET["interval"]?>").prop('disabled', true);
      $("#<?=$_GET["interval"]?>").addClass('btn-active');

      $(".chkBoxes").change(function()
      {
        var id = $(this).attr("id");
        var chk = $(this).is(":checked");
        switch (id)
        {
          case "enablePortefeuille":
            chart.options.data[0] = (chk)?portefeuilleOn:portefeuilleOff;
            break;
          case "enableCumPortefeuille":
            chart.options.data[1] = (chk)?portefeuilleCumOn:portefeuilleCumOff;
            break;
          case "enableIndex":
            chart.options.data[2] = (chk)?benchOn:benchOff;
            break;
          case "enableCumIndex":
            chart.options.data[3] = (chk)?benchCumOn:benchCumOff;
            break;
          case "enableFonds":
            if (indexFonds != "")
            {
              fondsOn =
              {
                type: "column",
                color: "#78d282",
                legendText: altFondsOms ,
                showInLegend: true,
                dataPoints: fondsLabels
              };
              chart.options.data[4] = (chk)?fondsOn:fondsOff;
            }
            else
            {
              chart.options.data[4] = fondsOff;
            }
            break;
          case "enableCumFonds":
            if (indexFonds != "")
            {
              fondsCumOn =
              {
                type: "spline",
                color: "#32a041",
                legendText: altFondsOms + ' cumulatief',
                showInLegend: true,
                dataPoints: fondsCumLabels
              };
              chart.options.data[5] = (chk)?fondsCumOn:fondsCumOff;
            }
            else
            {
              chart.options.data[5] = fondsCumOff;
            }
            break;
          default:
            break;
        }
        chart.render();
      });

      if (indexFonds != "")
      {

        getIndex(indexFonds);
        $("#fonds").val(indexFonds);
//        if (fondsEnabled)
        {
          console.log("fonds....");
          chart.options.data[4] = fondsCumOn;
          $("#enableFonds").show(300);
          $("#enableFonds").prop('checked', true).change();
          $("#enableCumFonds").show(300);
          $("#enableCumFonds").prop('checked', true).change();
        }
      }
      else
      {
        $("#enableFonds").hide(300);
        $("#enableCumFonds").hide(300);
      }

      chart.render();

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


<?
//debug($list->reportTotals);
logAccess();
if($__debug)
{
  echo getdebuginfo();
}
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>