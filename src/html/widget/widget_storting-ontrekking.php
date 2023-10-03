<?php
include_once ("init.php");
$fmt = new AE_cls_formatter();
$db = new DB();

$dialogName = "stortonttr";

$cache = new AE_cls_WidgetsCaching($dialogName, 120);

if ($cache->useCache())
{
  $cont = str_replace("<!--ttl-->", $cache->updateStamp(),$cache->content);
  echo $cont;
  exit;
}

$cfg = new AE_config();
$var_bedrag = $USR."_widget_var_".$dialogName."_bedrag";
$var_rows = $USR."_widget_var_".$dialogName."_rows";
$var_port = $USR."_widget_var_".$dialogName."_port";
$var_dagen = $USR."_widget_var_".$dialogName."_dagen";
$var_columns = $USR."_widget_var_".$dialogName."_colums";

if (!$showPort = $cfg->getData($var_port))
{
  $showPort = "Geen portefeuilleselectie";
}

$wFilt = new AE_cls_WidgetsFilter($showPort);
$wFilt->getPortefeuilleAccess();
$extraWhere = $wFilt->extraQuery;
$extraJoin  = $wFilt->extraJoin;


$rows      = (int)$cfg->getData($var_rows);
$mutDagen  = (int)$cfg->getData($var_dagen);
$mutBedrag = (float)$cfg->getData($var_bedrag);

$columnData = array(
  "Rekening" => array(
    "dbField" => "Rekening",
    "koptxt"  => vt("Rekening"),
    "title"   => vt("Rekening"),
    "btrTitle"   => "Rekening",
    "show"    => (int) $columnSettings["Rekening"],
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
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "Boekdatum" => array(
    "dbField" => "Boekdatum",
    "koptxt"  => vt("Boekdatum"),
    "title"   => vt("Boekdatum"),
    "btrTitle"   => "Boekdatum",
    "show"    => (int) $columnSettings["Boekdatum"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => "@D{form}"
  ),
  "omschrijving" => array(
    "dbField" => "omschrijving",
    "koptxt"  => vt("Omschrijving"),
    "title"   => vt("Omschrijving"),
    "btrTitle"   => "Omschrijving",
    "show"    => (int) $columnSettings["omschrijving"],
    "width"   => "30",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "Valuta" => array(
    "dbField" => "Valuta",
    "koptxt"  => vt("Valuta"),
    "title"   => vt("Valuta"),
    "btrTitle"   => "Valuta",
    "show"    => (int) $columnSettings["Valuta"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ac",
    "format"  => ""
  ),
  "Bedrag" => array(
    "dbField" => "Bedrag",
    "koptxt"  => vt("Bedrag"),
    "title"   => vt("Bedrag"),
    "btrTitle"   => "Bedrag",
    "show"    => (int) $columnSettings["Bedrag"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ar",
    "format"  => "@N{.2}"
  ),
  "Transactietype" => array(
    "dbField" => "Transactietype",
    "koptxt"  => vt("Transactietype"),
    "title"   => vt("Transactietype"),
    "btrTitle"   => "Transactietype",
    "show"    => (int) $columnSettings["Transactietype"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "Portefeuille" => array(
    "dbField" => "Portefeuille",
    "koptxt"  => vt("Portefeuille"),
    "title"   => vt("Portefeuille"),
    "btrTitle"   => "Portefeuille",
    "show"    => (int) $columnSettings["Portefeuille"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
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
    "show"    => (int)$columnSettings["tweedeAanspreekpunt"],
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
    "class"   => "bgEEE borderU ar",
    "format"  => "@checkbox"
  ),
  "deposito" => array(
    "dbField" => "Deposito",
    "koptxt"  => vt("S/D/L"),
    "title"   => vt("Spaar/Deposito/Lening"),
    "btrTitle"   => "Spaar/Deposito/Lening",
    "show"    => (int) $columnSettings["deposito"],
    "width"   => "5",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ar",
    "format"  => "@checkbox"
  ),

);

$widgetHelp = new AE_cls_WidgetsHelper($columnData, $var_columns);


//if ($debetDate < _julDag(date("Y-m-d")))
{
  $query = "
SELECT
	Rekeningen.Rekening,
	Rekeningen.Deposito,
	Portefeuilles.InternDepot,
	Portefeuilles.Client
	
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
	(CRM_naw.aktief = 1 OR ISNULL(CRM_naw.aktief) ) 
ORDER BY
  Rekeningen.Rekening

";

  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    if (substr($rec["Rekening"],-3) != "MEM")
    {
      $rekArray[] = $rec["Rekening"];
      $clientArray[$rec["Rekening"]] = $rec["Client"];
    }
  }


  $query =
    "
SELECT
  Rekeningmutaties.Rekening,
  Rekeningmutaties.Boekdatum,
  Rekeningmutaties.omschrijving,
  Rekeningmutaties.Rekening,
  Rekeningmutaties.Valuta,
  Rekeningmutaties.Bedrag,
  Rekeningmutaties.Transactietype,
  Rekeningen.Deposito,
  Portefeuilles.Portefeuille,
  Portefeuilles.Accountmanager,
  Portefeuilles.tweedeAanspreekpunt,
  Portefeuilles.Client,
  Portefeuilles.SoortOvereenkomst
FROM
  Rekeningmutaties
LEFT JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening 
LEFT JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
WHERE
  Rekeningen.consolidatie = 0 AND
  Rekeningmutaties.Rekening in ('" .implode("','",$rekArray). "') AND
  Rekeningmutaties.Verwerkt = '1' AND
  ABS(Rekeningmutaties.Bedrag) > ".$mutBedrag." AND 
  (Rekeningmutaties.Grootboekrekening = 'STORT' OR Rekeningmutaties.Grootboekrekening = 'ONTTR') AND
  Rekeningmutaties.Boekdatum >= DATE_SUB(CURDATE(),INTERVAL ".$mutDagen." day) 
ORDER BY
  ABS(Rekeningmutaties.Bedrag) DESC
LIMIT 0, $rows
    ";
//debug($query);

  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {

    $result[] = $rec;
  }
}

$rows = (int) $cfg->getData($var_rows);
if ($rows == 0) $rows = 10;

//$out = $tmpl->parseBlock("kop",array("header" => "Top $rows, Stortingen/Onttrekkingen ($showPort) <!--ttl-->", "btnSetup" => "btn_".$dialogName));
$parse = array(
  "btnExport" => "<button class='btn-new btn-default pull-right fa fa-file-excel-o headSetup' id='btnExport_{$dialogName}' aria-hidden='true' title='Exporteer'></button>",
  "header" => vtb("Top %s, Stortingen/Onttrekkingen (%s)", array($rows, vt($showPort))),
  "btnSetup" => "btn_".$dialogName,
  "btnCache" => $cache->btnCache());
//debug($parse);
$out = $tmpl->parseBlock("kop",$parse);
$out .= '

  <div class="rTable">

    <div class="rTableRow">
';

      foreach ($widgetHelp->columnData as $k=>$v)
      {
        if ($v["show"] != 1) {continue;}
        $out .=  "<div title='".$v["title"]."' class='rTableHead' ".$widgetHelp->getWidth($v["width"]) ." btr-title='".$v["btrTitle"]."' title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
      }
      
$out .= '
  </div>
    <div style="clear: both"></div>

<div class="rTable">
';


if ($showPort == "Geen portefeuilleselectie")
{
  $out .=  vt("Configureer eerst deze widget.");
}
else if (count($result) == 0)
{
  $out .=  vt("Geen enkele rekening voldoet aan uw criteria");
}
else
{
  $row = 0;
  foreach ($result as $data)
  {
    $row++;
    $negren = $data["Bedrag"] <= 0?"negatief":"";

$out .= '
    <div class="rTableRow">
';
      foreach ($widgetHelp->columnData as $k=>$v)
      {

        if ($v["show"] != 1) {continue;}
        $negren = "";
        if ($v["dbField"] == "Bedrag")
        {
          $negren = $data["Bedrag"] <= 0?"negatief":"";
        }
        if ($v["format"] != "")
        {
          $value =  $fmt->format($v["format"], $data[$v["dbField"]]);
        }
        else
        {
          $value = $data[$v["dbField"]];
        }

        $widgetExportData[$row][$k] = $value;
        if ($v["format"] == "@checkbox")
        {
          $widgetExportData[$row][$k] = $data[$v["dbField"]];
        }
        $out .=  "<div title='".$v["title"]."' btr-title='".$v["btrTitle"]."' class='rTableCell ".$v["class"]." $negren' ".$widgetHelp->getWidth($v["width"]) ."> ".$value."</div>\n";
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

<!-- Dialoog '.$dialogName.' -->
<div id="setupWidget_'.$dialogName.'" title="' . vt('Instellen stortingen/onttrekkingen') . '" class="setupWidget">
  <div class="formblock">
    <div class="formlinks">' . vt('Portefeuilleselectie') . ' </div>
    <div class="formrechts">
      <select name="port_'.$dialogName.'" id="port_'.$dialogName.'">
        '.$widgetHelp->makeAccessOptions($showPort).'
      </select>
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks">' . vt('Aantal getoonde regels') . ' </div>
    <div class="formrechts">
      <input name="showedRows_'.$dialogName.'" id="showedRows_'.$dialogName.'" type="number" value="'.$rows.'" style="width: 50px"/>
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks">' . vt('Mutaties jonger dan') . ' </div>
    <div class="formrechts">
      <input type="number" style="width: 40px;" value="'.$mutDagen.'" id="dagen_'.$dialogName.'"/> ' . vt('dagen') . '
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks">' . vt('Bedragen groter dan') . ' </div>
    <div class="formrechts">
      <input value="'.$mutBedrag.'" id="bedrag_'.$dialogName.'"/>
    </div>
  </div>

  <p><hr/><br/></p>

  <p>
    '.$widgetHelp->makeHtmlInput().'
  </p>

</div>
<script>
  $(document).ready(function(){
    var prev_rows = '.$rows.';

    $("#btnExport_'.$dialogName.'").click(function(e){
      e.preventDefault();
      console.log("export '.$dialogName.' clicked");
      window.open("widget/widgetPushExport.php?module='.$dialogName.'","wExport");
    });   

    '.$cache->JSinit().'

    $("#btn_'.$dialogName.'").click(function(){
      setup'.$dialogName.'Dialog.dialog("open");
    });

    var setup'.$dialogName.'Dialog = $("#setupWidget_'.$dialogName.'").dialog(
    {
      autoOpen: false,
      height: 560,
      width: "50%",
      modal: true,
      position: {my: "center", at: "top", of: window},
      buttons:
      {
        "' . vt('Sluiten') . '": function()
        {
          $( this ).dialog( "close" );
        },
        "' . vt('Opslaan') . '": function()
        {
          $( this ).dialog( "close" );
          var rows = $("#showedRows_'.$dialogName.'").val();
          updateCFG("'.$var_rows.'", rows);
          updateCFG("'.$var_port.'", $("#port_'.$dialogName.'").val() );
          updateCFG("'.$var_bedrag.'", $("#bedrag_'.$dialogName.'").val() );
          updateCFG("'.$var_dagen.'", $("#dagen_'.$dialogName.'").val() );
          $(".kolCheck'.$widgetHelp->uid.'").each(function()
          {
            var val = "'.$var_columns.'#" + $(this).attr("id") + "#" +  ($(this).prop( "checked" )?"1":"0");
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

