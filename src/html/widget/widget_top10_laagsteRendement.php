<?php
include_once ("init.php");
include_once ("../../classes/AE_cls_WidgetsHelper.php");
$fmt = new AE_cls_formatter();
$db = new DB();

$dialogName = "renLaag";

$cache = new AE_cls_WidgetsCaching($dialogName, 120);

if ($cache->useCache())
{
  $cont = str_replace("<!--ttl-->", $cache->updateStamp(),$cache->content);
  echo $cont;
  exit;
}

$cfg = new AE_config();
$var_rows = $USR."_widget_var_".$dialogName."_rows";
$var_port = $USR."_widget_var_".$dialogName."_port";
$var_columns = $USR."_widget_var_".$dialogName."_colums";

if (!$showPort = $cfg->getData($var_port))
{
  $showPort = "Geen portefeuilleselectie";
}

$wFilt = new AE_cls_WidgetsFilter($showPort);
$wFilt->getPortefeuilleAccess();
$extraWhere = $wFilt->extraQuery;
$extraJoin  = $wFilt->extraJoin;


$rows = (int) $cfg->getData($var_rows);
if ($rows == 0) $rows = 10;

$columnData = array(
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
  "client" => array(
    "dbField" => "Client",
    "koptxt"  => vt("Client"),
    "title"   => vt("Client"),
    "btrTitle"   => "Client",
    "show"    => (int) $columnSettings["client"],
    "width"   => "30",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "vermogen" => array(
    "dbField" => "laatsteWaarde",
    "koptxt"  => vt("Vermogen"),
    "title"   => vt("Vermogen"),
    "btrTitle"   => "Vermogen",
    "show"    => (int) $columnSettings["vermogen"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold ar",
    "format"  => "@N{.0}"
  ),
  "performance" => array(
    "dbField" => "rendement",
    "koptxt"  => vt("% P"),
    "title"   => vt("% Performance"),
    "btrTitle"   => "% Performance",
    "show"    => (int) $columnSettings["performance"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold ar ",
    "format"  => "@N{.1}%"
  ),
  "risicoklasse" => array(
    "dbField" => "Risicoklasse",
    "koptxt"  => vt("Risicoklasse"),
    "title"   => vt("Risicoklasse"),
    "btrTitle"   => "Risicoklasse",
    "show"    => (int) $columnSettings["risicoklasse"],
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
    "koptxt"  => vt("Tweede aanspreekpunt"),
    "title"   => vt("Tweede aanspreekpunt"),
    "btrTitle"   => "Tweede aanspreekpunt",
    "show"    => (int)$columnSettings["tweedeAanspreekpunt"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "modelportefeuille " => array(
    "dbField" => "ModelPortefeuille",
    "koptxt"  => vt("Modelportefeuille"),
    "title"   => vt("Modelportefeuille"),
    "btrTitle"   => "Modelportefeuille",
    "show"    => (int) $columnSettings["modelportefeuille"],
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


);

$widgetHelp = new AE_cls_WidgetsHelper($columnData, $var_columns);

//$out = $tmpl->parseBlock("kop",array("header" => "Top $rows, Laagste rendement ($showPort) <!--ttl-->", "btnSetup" => "btn_".$dialogName));

$parse = array(
  "header" => vtb("Top %s, Laagste rendement (%s) ", array($rows, vt($showPort))),
  "btnSetup" => "btn_".$dialogName,
  "btnCache" => $cache->btnCache().$cache->dataState());
//debug($parse);
$out = $tmpl->parseBlock("kop",$parse);

$out .= '

<div class="rTable">

  <div class="rTableRow">
  ';

    foreach ($widgetHelp->columnData as $k=>$v)
    {
      if ($v["show"] != 1) {continue;}
      $out .= "<div class='rTableHead' ".$widgetHelp->getWidth($v["width"]) ." btr-title='".$v['btrTitle']."' title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
    }
$out .= '    

  </div>
  <div style="clear: both"></div>
';

$query = "
SELECT
	laatstePortefeuilleWaarde.id,
	laatstePortefeuilleWaarde.portefeuille,
	laatstePortefeuilleWaarde.laatsteWaarde,
	laatstePortefeuilleWaarde.rendement,
	laatstePortefeuilleWaarde.rendementModel,
	Portefeuilles.Accountmanager,
	Portefeuilles.tweedeAanspreekpunt,
	Portefeuilles.Risicoklasse,
	Portefeuilles.SoortOvereenkomst,
	Portefeuilles.ModelPortefeuille,
	Portefeuilles.InternDepot,
	Portefeuilles.Client
FROM
	laatstePortefeuilleWaarde
INNER JOIN Portefeuilles ON 
  laatstePortefeuilleWaarde.portefeuille = Portefeuilles.Portefeuille
  $extraJoin
LEFT JOIN CRM_naw ON 
  CRM_naw.portefeuille = Portefeuilles.Portefeuille
WHERE
  $extraWhere
  Portefeuilles.consolidatie = 0 AND
  Portefeuilles.Einddatum > NOW() AND
	(CRM_naw.aktief = 1 OR ISNULL(CRM_naw.aktief) )
ORDER BY
	rendement 
LIMIT 0, $rows
";

$db->executeQuery($query);

if ($showPort == "Geen portefeuilleselectie")
{
  $out .= vt("Configureer eerst deze widget.");
}
else if($db->records()> 0)
{
  while( $data = $db->nextRecord() )
  {

$out .= '
  <div class="rTableRow">
';
    foreach ($widgetHelp->columnData as $k=>$v)
    {
      if ($v["show"] != 1) {continue;}
      $negren = "";
      if ($v["dbField"] == "rendement")
      {
        $negren = $data["rendement"] <= 0?"negatief":"";
      }
      if ($v["format"] != "")
      {
        $value =  $fmt->format($v["format"], $data[$v["dbField"]]);
      }
      else
      {
        $value = $data[$v["dbField"]];
      }
      $out .= "<div btr-title='".$v['btrTitle']."' title='".$v['title']."' class='rTableCell ".$v["class"]." $negren' ".$widgetHelp->getWidth($v["width"]) ."> ".$value."</div>\n";
    }
$out .= '
  </div>
';
  }


}
else
{
  $out .= '<div>' . vt('Geen items gevonden') . '</div>';
}
$selected = ($showPort == "alle")?"SELECTED":"";
$out .= '
</div> <!-- rTable -->

  <!-- Dialoog '.$dialogName.' -->
  <div id="setupWidget_'.$dialogName.'" title="' . vt('Instellen laagste rendement') . '" class="setupWidget">
    <div class="formblock">
      <div class="formlinks">' . vt('Portefeuilleselectie') . '</div>
      <div class="formrechts">
        <select name="port_'.$dialogName.'" id="port_'.$dialogName.'">
          '.$widgetHelp->makeAccessOptions($showPort).'
        </select>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">' . vt('Aantal getoonde regels') . '</div>
      <div class="formrechts">
        <input name="showedRows_'.$dialogName.'" id="showedRows_'.$dialogName.'" type="number" value="'.$rows.'" style="width: 50px"/>
      </div>
    </div>
    <p></p>
    <p></p>
    <p>
      '.$widgetHelp->makeHtmlInput().'
    </p>
  </div>


  <script>
    $(document).ready(function()
    {
      var prev_rows = '.$rows.';

      '.$cache->JSinit().'

      $("#btn_'.$dialogName.'").click(function()
      {
        setup'.$dialogName.'Dialog.dialog("open");
      });

      var setup'.$dialogName.'Dialog = $("#setupWidget_'.$dialogName.'").dialog(
        {
          autoOpen: false,
          height: 500,
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
$out.= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));

$cache->addToCache($out);
echo $out;