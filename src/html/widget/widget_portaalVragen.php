<?php
include_once ("init.php");
$fmt = new AE_cls_formatter();
$db = new DB();

$dialogName = ("portaalVragen");

$cache = new AE_cls_WidgetsCaching($dialogName, 180);

if ($cache->useCache())
{
  $cont = str_replace("<!--ttl-->", $cache->updateStamp(),$cache->content);
  echo $cont;
  exit;
}

$cfg = new AE_config();

//$var_rows = $USR."_widget_var_".$dialogName."_rows";
$var_port = $USR."_widget_var_".$dialogName."_port";

$var_columns = $USR."_widget_var_".$dialogName."_colums";

if (!$showPort = $cfg->getData($var_port))
{
  $showPort = "Geen portefeuilleselectie";
}

$wFilt = new AE_cls_WidgetsFilter($showPort);
$wFilt->getPortefeuilleAccess();
$extraWhere = "";
$extraJoin  = "";
$extraWhere = $wFilt->extraQuery;
$extraJoin  = $wFilt->extraJoin;


$columnData = array(
  "portefeuille" => array(
    "dbField" => "Portefeuille",
    "koptxt"  => vt("Portefeuille"),
    "title"   => vt("Portefeuille"),
    "show"    => (int) $columnSettings["portefeuille"],
    "width"   => "20",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "client" => array(
    "dbField" => "Client",
    "koptxt"  => vt("Client"),
    "title"   => vt("Client"),
    "show"    => (int) $columnSettings["client"],
    "width"   => "20",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "naam" => array(
    "dbField" => "naam",
    "koptxt"  => vt("Naam"),
    "title"   => vt("Naam"),
    "show"    => (int) $columnSettings["naam"],
    "width"   => "20",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "portaalDatumIngevuld" => array(
    "dbField" => "portaalDatumIngevuld",
    "koptxt"  => vt("Ing. d.d."),
    "title"   => vt("Ingevuld d.d."),
    "show"    => (int) $columnSettings["portaalDatumIngevuld"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold ",
    "format"  => "@D{d}-{m}-{Y}"
  ),
  "omschrijving" => array(
    "dbField" => "omschrijving",
    "koptxt"  => vt("Vragenlijst"),
    "title"   => vt("Vragenlijst"),
    "show"    => (int) $columnSettings["omschrijving"],
    "width"   => "20",
    "fixed"   => 1,
    "class"   => "bgEEE borderU",
    "format"  => ""
  ),
  "accountmanager" => array(
    "dbField" => "Accountmanager",
    "koptxt"  => vt("Accountmanager"),
    "title"   => vt("Accountmanager"),
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
    "show"    => (int) $columnSettings["tweedeAanspreekpunt"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),


);

$widgetHelp = new AE_cls_WidgetsHelper($columnData, $var_columns);



$query = "
SELECT
  CRM_naw.id,
  CRM_naw.naam,
  VragenLijstenPerRelatie.omschrijving,
  VragenLijstenPerRelatie.portaalDatumIngevuld,
  Portefeuilles.Portefeuille,
  Portefeuilles.Accountmanager,
	Portefeuilles.tweedeAanspreekpunt,
	Portefeuilles.Client
FROM
  VragenLijstenPerRelatie
INNER JOIN CRM_naw on 
  CRM_naw.id = VragenLijstenPerRelatie.nawId
INNER JOIN Portefeuilles ON 
  CRM_naw.portefeuille = Portefeuilles.Portefeuille
  $extraJoin  
WHERE
  $extraWhere
  VragenLijstenPerRelatie.portaalStatus = 'ingevuld'
ORDER BY
  VragenLijstenPerRelatie.portaalDatumIngevuld

";
//debug($query);


//echo $tmpl->parseBlock("kop",array("header" => "Portaal vragenlijsten ($showPort)", "btnSetup" => "btn_".$dialogName));
$parse = array(
  "header" => vtb("Portaal vragenlijsten (%s) ", array($showPort)),
  "btnSetup" => "btn_".$dialogName,
  "btnCache" => $cache->btnCache());
//debug($parse);
$out = $tmpl->parseBlock("kop",$parse);

$out .= '

  <div class="rTable">

    <div class="rTableRow">
      ';
      foreach ($widgetHelp->columnData as $k=> $v)
      {
        if ($v["show"] != 1) {continue;}
        $out .=  "<div class='rTableHead' ".$widgetHelp->getWidth($v["width"]) ." title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
      }

  $out .= '
  </div>
    <div style="clear: both"></div>
';


    $db->executeQuery($query);

    if ( $showPort == "Geen portefeuilleselectie")
    {
      $out .=  vt("Configureer eerst deze widget.");
    }
    else if($db->records()> 0)
    {
    while( $data = $db->nextRecord() )
    {

$out .= '
    <div class="rTableRow">
';
      foreach ($widgetHelp->columnData as $k=> $v)
      {
        if ($v["show"] != 1) {continue;}
        $negren = "";

        if ($v["format"] != "")
        {
          $value =  $fmt->format($v["format"], $data[$v["dbField"]]);
        }
        else
        {
          $value = $data[$v["dbField"]];
        }

        if ($v["dbField"] == "Portefeuille")
        {
          $value = "<a target='content' href='CRM_nawEdit.php?action=edit&id={$data["id"]}&lastTab=9&frameSrc=".base64_encode("vragenlijstenperrelatieList.php?rel_id=".$data["id"])."'><button><i class='fa fa-vcard' aria-hidden='true'></i></button></a> ".$data[$v["dbField"]];
        }

        $out .=  "<div title='".$v['title']."' class='rTableCell ".$v["class"]." $negren' ".$widgetHelp->getWidth($v["width"]) ."> ".$value."</div>\n";
      }
      $out .= '
    </div>
    ';

  }

}
else
{
  $out .=  "<div>" . vt('Geen items gevonden') . "</div>";
}
$out .= '

  </div>

<!-- Dialoog '.$dialogName.' -->
<div id="setupWidget_'.$dialogName.'" title="' . vt('Instellen portaal vragenlijsten') . '" class="setupWidget">
  <br/>

  <div class="formblock">
    <div class="formlinks">' . vt('Portefeuilleselectie') . ' </div>
    <div class="formrechts">
      <select name="port_'.$dialogName.'" id="port_'.$dialogName.'">
        '.$widgetHelp->makeAccessOptions($showPort).'
      </select>
    </div>
  </div>
  <p><br/></p>
  <p>
    '.$widgetHelp->makeHtmlInput().'
  </p>
</div>
<script>
  $(document).ready(function(){
 
    '.$cache->JSinit().'

    $("#btn_'.$dialogName.'").click(function(){
      setup'.$dialogName.'Dialog.dialog("open");
    });

    var setup'.$dialogName.'Dialog = $("#setupWidget_'.$dialogName.'").dialog(
    {
      autoOpen: false,
      height: 410,
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

