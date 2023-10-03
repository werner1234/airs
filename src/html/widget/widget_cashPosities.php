<?php
include_once ("init.php");
$fmt = new AE_cls_formatter();
$db = new DB();

$dialogName = ("cashposities");

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
  "bedragVV" => array(
    "dbField" => "bedragVV",
    "koptxt"  => vt("Saldo in valuta"),
    "title"   => vt("Saldo in valuta"),
    "btrTitle"   => "Saldo in valuta",
    "show"    => (int) $columnSettings["bedragVV"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU bold ar",
    "format"  => "@N{.2}"
  ),
  "valuta" => array(
    "dbField" => "valuta",
    "koptxt"  => vt("Valuta"),
    "title"   => vt("Valuta"),
    "btrTitle"   => "Valuta",
    "show"    => (int) $columnSettings["valuta"],
    "width"   => "5",
    "fixed"   => 0,
    "class"   => "bgEEE borderU bold ar",
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
  "tweedeAanspreekpunt" => array(
    "dbField" => "tweedeAanspreekpunt",
    "koptxt"  => vt("2e aanspr."),
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
	Rekeningen.Deposito
	
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
  actuelePortefeuilleWaardeInValuta as bedragVV,
  valuta,
  crmLaatsteFondsWaarden.portefeuille,
  crmLaatsteFondsWaarden.rekening,
  Portefeuilles.Accountmanager,
	Portefeuilles.tweedeAanspreekpunt,
	Portefeuilles.Client
	
FROM
  `crmLaatsteFondsWaarden`
LEFT JOIN  Portefeuilles ON 
  crmLaatsteFondsWaarden.portefeuille = Portefeuilles.Portefeuille
WHERE
  `rekening` in ('" .implode("','",$rekArray). "')
AND 
  `type` = 'rekening'
HAVING
  bedrag >= $debetBedrag  
ORDER BY   
  bedrag DESC
LIMIT 0, ".$rows;

  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $result[] = $rec;
  }
}

//$output = $tmpl->parseBlock("kop",array("header" => "Top $rows, Cash posities ($showPort) <!--ttl-->", "btnSetup" => "btn_".$dialogName));

$parse = array(
  "header" => vtb("Top %s, Cash posities (%s) ", array($rows, $showPort)),
  "btnSetup" => "btn_".$dialogName,
  "btnCache" => $cache->btnCache().$cache->dataState());
//debug($parse);
$output = $tmpl->parseBlock("kop",$parse);


$output .= "\n
  <div class='rTable'>

    <div class='rTableRow'>
";

      foreach ($widgetHelpVermogen->columnData as $k=> $v)
      {
        if ($v["show"] != 1) {continue;}
        $output .= "\n<div class='rTableHead' ".$widgetHelpVermogen->getWidth($v["width"]) ." btr-title='".$v['btrTitle']."' title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
      }
$output .= "
    </div>
    <div style='clear: both'></div>
";


if ( $showPort == ("Geen portefeuilleselectie"))
{
  $output .= vt("Configureer eerst deze widget.");
}
elseif (count($result) == 0)
{
  $output .= vt("Geen enkele rekening voldoet aan uw criteria");
}
else
{
  foreach ($result as $data)
  {

    $output .= "
    <div class='rTableRow'>
    ";

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
      $output .= "\n<div btr-title='".$v['btrTitle']."' title='".$v["title"]."' class='rTableCell ".$v["class"]." $negren' ".$widgetHelpVermogen->getWidth($v["width"]) ."> ".$value."</div>\n";
    }

    $output .= "
    </div>
    
    ";

  }

}

$selected = ($showPort == "alle")?"SELECTED":"";


$output .= ' 

</div> <!-- rTable -->

<!-- Dialoog <?=$dialogName?> -->
<div id="setupWidget_'.$dialogName.'" title="Instellen Cash posities" class="setupWidget">
  <div class="formblock">
    <div class="formlinks">' . vt('Portefeuilleselectie') . ' </div>
    <div class="formrechts">
      <select name="port_'.$dialogName.'" id="port_'.$dialogName.'">
        '.$widgetHelpVermogen->makeAccessOptions($showPort).'
      </select>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks">' . vt('Aantal getoonde regels:') . ' </div>
    <div class="formrechts">
      <input name="showedRows_'.$dialogName.'" id="showedRows_'.$dialogName.'" type="number" value="'.$rows.'" style="width: 50px;"/>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks">' . vt('Bedrag groter dan') . ' </div>
    <div class="formrechts">
     <input value="'.$debetBedrag.'" id="bedrag_'.$dialogName.'" style="width: 50px;"/>
    </div>
  </div>

  <p><br/></p>
  <p><br/></p>

  <p>
    '.$widgetHelpVermogen->makeHtmlInput().'
  </p>

</div>

</div> 

<script>
  $(document).ready(function(){
    var prev_rows = '.$rows.';
    '.$cache->JSinit().'
    $("#btn_'.$dialogName.'").click(function(){
      setup'.$dialogName.'Dialog.dialog("open");
    });

    var setup'.$dialogName.'Dialog = $("#setupWidget_'.$dialogName.'").dialog(
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
          var rows = $("#showedRows_'.$dialogName.'").val();
          updateCFG("'.$var_rows.'", rows, "'.$dialogName.'");
          updateCFG("'.$var_port.'", $("#port_'.$dialogName.'").val() );
          updateCFG("'.$var_debet.'", $("#bedrag_'.$dialogName.'").val() );
          $(".kolCheck'.$widgetHelpVermogen->uid.'").each(function()
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

$output .= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));
$cache->addToCache($output);
echo $output;

// lokale functies
function _julDag($dbDatum)
{
  $parts = explode("-",$dbDatum);
  $julian = mktime(1,1,1,$parts[1],$parts[2],$parts[0]);
  return floor($julian / 86400);
}


