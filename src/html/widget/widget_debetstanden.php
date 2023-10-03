<?php
include_once ("init.php");
$fmt = new AE_cls_formatter();
$db = new DB();

$dialogName = "debet";

$cache = new AE_cls_WidgetsCaching($dialogName, 120);

if ($cache->useCache())
{
  $cont = str_replace("<!--ttl-->", $cache->updateStamp(),$cache->content);
  echo $cont;
  exit;
}

$cfg = new AE_config();
$var_debet = $USR."_widget_var_".$dialogName."_debet";
$var_rows = $USR."_widget_var_".$dialogName."_rows";
$var_port = $USR."_widget_var_".$dialogName."_port";
$var_columns = $USR."_widget_var_".$dialogName."_colums";

if (!$showPort = $cfg->getData($var_port))
{
 $showPort = ("Geen portefeuilleselectie");
}

$wFilt = new AE_cls_WidgetsFilter($showPort);
$wFilt->getPortefeuilleAccess();
$extraWhere = $wFilt->extraQuery;
$extraJoin  = $wFilt->extraJoin;


$debetDate = _julDag($cfg->getData($var_rows));
$debetBedrag = (float)$cfg->getData($var_debet);

$rows = (int) $cfg->getData($var_rows);
if ($rows == 0) $rows = 10;



$columnDataVermogen = array(
  "portefeuille" => array(
    "dbField" => "portefeuille",
    "koptxt"  => vt("Portefeuille"),
    "title"   => vt("Portefeuille"),
    "btrTitle"   => "Portefeuille",
    "show"    => (int) $columnSettings["portefeuille"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "rekening" => array(
    "dbField" => "rekening",
    "koptxt"  => vt("Rekening"),
    "title"   => vt("Rekening"),
    "btrTitle"   => "Rekening",
    "show"    => (int) $columnSettings["rekening"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "client" => array(
    "dbField" => "Client",
    "koptxt"  => vt("Client"),
    "title"   => vt("Client"),
    "btrTitle"   => "Client",
    "show"    => (int) $columnSettings["client"],
    "width"   => "20",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "bedrag" => array(
    "dbField" => "bedrag",
    "koptxt"  => vt("Saldo in &euro;"),
    "title"   => vt("Saldo in &euro;"),
    "btrTitle"   => "Saldo in &euro;",
    "show"    => (int) $columnSettings["vermogen"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold ar",
    "format"  => "@N{.2}"
  ),
  "accountmanager" => array(
    "dbField" => "Accountmanager",
    "koptxt"  => vt("Accountmanager"),
    "title"   => vt("Accountmanager"),
    "btrTitle"   => "Accountmanager",
    "show"    => (int) $columnSettings["accountmanager"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "soortOvereenkomst" => array(
    "dbField" => "SoortOvereenkomst",
    "koptxt"  => vt("SoortOvereenkomst"),
    "title"   => vt("SoortOvereenkomst"),
    "btrTitle"   => "SoortOvereenkomst",
    "show"    => (int) $columnSettings["soortOvereenkomst"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "tweedeAanspreekpunt" => array(
    "dbField" => "tweedeAanspreekpunt",
    "koptxt"  => vt("Tweede aanspreekpunt"),
    "title"   => vt("Tweede aanspreekpunt"),
    "btrTitle"   => "Tweede aanspreekpunt",
    "show"    => (int) $columnSettings["tweedeAanspreekpunt"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "internDepot" => array(
    "dbField" => "InternDepot",
    "koptxt"  => vt("I.Depot"),
    "title"   => vt("Intern depot"),
    "btrTitle"   => "Intern depot",
    "show"    => (int) $columnSettings["internDepot"],
    "width"   => "5",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ac",
    "format"  => "@checkbox"
  ),





);

$widgetHelpVermogen = new AE_cls_WidgetsHelper($columnDataVermogen, $var_columns);


//if ($debetDate < _julDag(date("Y-m-d")))
{
  $query = "
SELECT
	Rekeningen.Rekening,
	Portefeuilles.Client,
	Rekeningen.Deposito,
	Portefeuilles.SoortOvereenkomst
FROM
	Rekeningen
LEFT JOIN Portefeuilles ON
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille
LEFT JOIN CRM_naw ON 
  CRM_naw.portefeuille = Portefeuilles.Portefeuille
$extraJoin
WHERE
  $extraWhere
  Portefeuilles.Einddatum > NOW() AND
  Rekeningen.consolidatie = 0 AND
	(CRM_naw.aktief = 1 OR ISNULL(CRM_naw.aktief) ) 
ORDER BY
  Rekeningen.Rekening
";

  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    if (substr($rec["Rekening"],-3) != "MEM" AND $rec["Deposito"] != 1)
    {
      $rekArray[] = $rec["Rekening"];
    }
  }


  $query =
    "
SELECT

  actuelePortefeuilleWaardeEuro as bedrag,
  crmLaatsteFondsWaarden.portefeuille,
  crmLaatsteFondsWaarden.rekening,
  Portefeuilles.Accountmanager,
	Portefeuilles.tweedeAanspreekpunt,
	Portefeuilles.Client,
  Portefeuilles.SoortOvereenkomst
       
	
FROM
  `crmLaatsteFondsWaarden`
LEFT JOIN  Portefeuilles ON 
  crmLaatsteFondsWaarden.portefeuille = Portefeuilles.Portefeuille
WHERE
  `rekening` in ('" .implode("','",$rekArray). "')
AND 
  `type` = 'rekening'
HAVING
  bedrag < $debetBedrag  
ORDER BY   
  bedrag
LIMIT 0, ".$rows;

  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $result[] = $rec;
  }
}

//echo $tmpl->parseBlock("kop",array("header" => "Top $rows, Debetstanden ($showPort)", "btnSetup" => "btn_".$dialogName));
$parse = array(
  "btnExport" => "<button class='btn-new btn-default pull-right fa fa-file-excel-o headSetup' id='btnExport_{$dialogName}' aria-hidden='true' title='Exporteer'></button>",
  "header" => vtb("Top %s, Debetstanden (%s)", array($rows, $showPort)),
  "btnSetup" => "btn_".$dialogName,
  "btnCache" => $cache->btnCache().$cache->dataState(),
  );
//debug($parse);
$out = $tmpl->parseBlock("kop",$parse);

$out .= '
  <div class="rTable">

    <div class="rTableRow">
';
      foreach ($widgetHelpVermogen->columnData as $k=> $v)
      {
        if ($v["show"] != 1) {continue;}
        $out .= "<div class='rTableHead' ".$widgetHelpVermogen->getWidth($v["width"]) ." btr-title='".$v["btrTitle"]."' title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
      }
    $out .= '
    </div>
    <div style="clear: both"></div>
';


if ( $showPort == ("Geen portefeuilleselectie"))
{
  $out .= vt("Configureer eerst deze widget.");
}
elseif (count($result) == 0)
{
  $out .= vt("Geen enkele rekening voldoet aan uw criteria");
}
else
{
  $row = 0;
  foreach ($result as $data)
  {
    $row++;

$out .= '
    <div class="rTableRow">
      ';
      foreach ($widgetHelpVermogen->columnData as $k=> $v)
      {
        if ($v["show"] != 1) {continue;}
        $negren = "";
        if ($v["dbField"] == "bedrag")
        {
          $negren = $data["bedrag"] <= 0?"negatief":"";
        }
        if ($v["format"] != "")
        {
          $value =  $fmt->format($v["format"], $data[$v["dbField"]]);
        }
        else
        {
          $value = $data[$v["dbField"]];
        }
        $widgetExportData[$row][$k] = $data[$v["dbField"]];
        $out .= "<div class='rTableCell ".$v["class"]." $negren' btr-title='".$v['btrTitle']."' title='".$v["title"]."' ".$widgetHelpVermogen->getWidth($v["width"]) ."> ".$value."</div>\n";
      }

    $out .= '
    </div>
 ';
  }

}
$_SESSION["widgetExport"][$dialogName] = $widgetExportData;
$selected = ($showPort == "alle")?"SELECTED":"";

$out .= '
</div> <!-- rTable -->

<!-- Dialoog '. $dialogName.' -->
<div id="setupWidget_'. $dialogName.'" title="' . vt('Instellen Debetstanden') . '" class="setupWidget">
  <div class="formblock">
    <div class="formlinks">' . vt('Portefeuilleselectie') . ' </div>
    <div class="formrechts">
      <select name="port_'. $dialogName.'" id="port_'. $dialogName.'">
        '. $widgetHelpVermogen->makeAccessOptions($showPort).'
      </select>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks">' . vt('Aantal getoonde regels') . ': </div>
    <div class="formrechts">
      <input name="showedRows_'. $dialogName.'" id="showedRows_'. $dialogName.'" type="number" value="'. $rows.'" style="width: 50px"/>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks">' . vt('Bedrag kleiner dan') . ' </div>
    <div class="formrechts">
     <input value="'. $debetBedrag.'" id="bedrag_'. $dialogName.'" style="width: 50px"/>
    </div>
  </div>

  <p><br/></p>
  <p><br/></p>

  <p>
    '. $widgetHelpVermogen->makeHtmlInput().'
  </p>

</div>

</div>
<script>
  $(document).ready(function(){
    var prev_rows = '. $rows.';

    $("#btnExport_'.$dialogName.'").click(function(e){
      e.preventDefault();
      console.log("export '.$dialogName.' clicked");
      window.open("widget/widgetPushExport.php?module='.$dialogName.'","wExport");
    }); 

    '.$cache->JSinit().'

    $("#btn_'. $dialogName.'").click(function(){
      setup'. $dialogName.'Dialog.dialog("open");
    });

    var setup'. $dialogName.'Dialog = $("#setupWidget_'. $dialogName.'").dialog(
    {
      autoOpen: false,
      height: 450,
      width: "50%",
      modal: true,
      position:  { my: "center", at: "top", of: window },
      buttons:
      {
        "' . vt('Sluiten') . '": function()
        {
          $( this ).dialog( "close" );
        },
        "' . vt('Opslaan') . '": function()
        {
          $( this ).dialog( "close" );
          var rows = $("#showedRows_'. $dialogName.'").val();
          updateCFG("'. $var_rows.'", rows);
          updateCFG("'. $var_port.'", $("#port_'. $dialogName.'").val() );
          updateCFG("'. $var_debet.'", $("#bedrag_'. $dialogName.'").val() );
          $(".kolCheck'. $widgetHelpVermogen->uid.'").each(function()
          {
            var val = "'. $var_columns.'#" + $(this).attr("id") + "#" +  ($(this).prop( "checked" )?"1":"0");
            updateCFG("kolom", val);
          });
          reloadPage();
        }
      },
      close: function ()
      {
      }
    });




  });
</script>
';
$out .= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));

$cache->addToCache($out);
echo $out;


// lokale functies
function _julDag($dbDatum)
{
  $parts = explode("-",$dbDatum);
  $julian = mktime(1,1,1,$parts[1],$parts[2],$parts[0]);
  return floor($julian / 86400);
}


