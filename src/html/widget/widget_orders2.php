<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/28 12:53:14 $
    File Versie         : $Revision: 1.3 $

    $Log: widget_orders2.php,v $
    Revision 1.3  2018/09/28 12:53:14  cvs
    widget caching

    Revision 1.2  2018/05/02 12:11:25  cvs
    call 6601

    Revision 1.1  2018/03/21 13:59:28  cvs
    call 6601

    Revision 1.12  2018/02/15 07:13:25  cvs
    no message

    Revision 1.11  2018/02/14 13:59:30  cvs
    no message



*/

include_once ("init.php");
$DB = new DB();
$cfg = new AE_config();

$dialogName = "openOrders2";

$cache = new AE_cls_WidgetsCaching($dialogName, 60);

if ($cache->useCache())
{
  $cont = str_replace("<!--ttl-->", $cache->updateStamp(),$cache->content);
  echo $cont;
  exit;
}

$var_columns = $USR."_widget_var_".$dialogName."_colums";

$var_port = $USR."_widget_var_".$dialogName."_port";
$showPort = $cfg->getData($var_port);
if (!$showPort = $cfg->getData($var_port))
{
  $showPort = "Geen portefeuilleselectie";
}

$wFilt = new AE_cls_WidgetsFilter($showPort);
$wFilt->getPortefeuilleAccess();
$extraWhere = $wFilt->extraQuery;
$extraJoin  = $wFilt->extraJoin;


//echo $tmpl->parseBlock("kop",array("header" => "<span id='records'></span> Openstaande orders ($showPort)", "btnSetup" => "btn_".$dialogName));

$parse = array(
  "header" => "<span id='records'></span> " . vtb('Openstaande orders (%s)', array(vt($showPort))) . " ",
  "btnSetup" => "btn_".$dialogName,
  "btnCache" => $cache->btnCache());
//debug($parse);
$out = $tmpl->parseBlock("kop",$parse);

$fmt = new AE_cls_formatter();

$columnDataVermogen = array(
  "ordId" => array(
    "dbField" => "ordId",
    "koptxt"  => vt("Id"),
    "title"   => vt("Order Id"),
    "show"    => (int) $columnSettings["ordId"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU al",
    "format"  => ""
  ),
  "aantal" => array(
    "dbField" => "aantal",
    "koptxt"  => vt("Aantal"),
    "title"   => vt("Aantal"),
    "show"    => (int) $columnSettings["aantal"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold ac",
    "format"  => "@N{.0}"
  ),
  "fonds" => array(
    "dbField" => "fondsOmschrijving",
    "koptxt"  => vt("Fonds"),
    "title"   => vt("Fonds"),
    "show"    => (int) $columnSettings["fonds"],
    "width"   => "30",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold ",
    "format"  => ""
  ),
  "transactieSoort" => array(
    "dbField" => "transactieSoort",
    "koptxt"  => vt("TC"),
    "title"   => vt("Transactie Code"),
    "show"    => (int) $columnSettings["transactieSoort"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ac",
    "format"  => ""
  ),


  "status" => array(
    "dbField" => "status",
    "koptxt"  => vt("Status"),
    "title"   => vt("Status"),
    "show"    => (int) $columnSettings["status"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "datum " => array(
    "dbField" => "datum",
    "koptxt"  => vt("Datum"),
    "title"   => vt("Datum"),
    "show"    => (int) $columnSettings["datum"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ar",
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
  "internDepot" => array(
    "dbField" => "InternDepot",
    "koptxt"  => vt("I.Depot"),
    "title"   => vt("Intern depot"),
    "show"    => (int) $columnSettings["internDepot"],
    "width"   => "5",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ar",
    "format"  => "@checkbox"
  ),

  "orderSoort" => array(
    "dbField" => "orderSoort",
    "koptxt"  => vt("Srt"),
    "title"   => vt("Soort order"),
    "show"    => (int) $columnSettings["orderSoort"],
    "width"   => "5",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ar",
    "format"  => ""
  ),



);

$widgetHelpVermogen = new AE_cls_WidgetsHelper($columnDataVermogen, $var_columns);


$out .= '

  <div class="rTable">

    <div class="rTableRow">
      ';
      foreach ($widgetHelpVermogen->columnData as $k=> $v)
      {
        if ($v["show"] != 1) {continue;}
        $out .= "<div class='rTableHead' ".$widgetHelpVermogen->getWidth($v["width"]) ." title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
      }
$out .= '
    </div>
    <div style="clear: both"></div>
';

$ordermoduleAccess=GetModuleAccess("ORDER");

//if($ordermoduleAccess==2)
{
  $query = "
  SELECT
    MIN(OrderRegelsV2.id) as OrdRegId,
    OrdersV2.id as ordId,
    SUM(OrderRegelsV2.aantal) AS aantal,
    OrdersV2.add_date as `datum`,
    OrdersV2.fondsOmschrijving,
    OrdersV2.transactieSoort,
    OrdersV2.orderStatus AS `status`,
    OrdersV2.orderSoort
  FROM
    OrdersV2
  INNER JOIN OrderRegelsV2 ON 
    OrdersV2.id = OrderRegelsV2.orderid
  INNER JOIN Portefeuilles ON 
    OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
--  LEFT JOIN Gebruikers gebBase ON 
--    Portefeuilles.Accountmanager = gebBase.Accountmanager
    $extraJoin
  WHERE 
  $extraWhere
    OrderRegelsV2.orderregelStatus < 3  
  GROUP BY 
    OrdersV2.id 
  ORDER BY
    OrdersV2.add_date DESC
    
  ";

  $DB->executeQuery($query);
}

//$rowData =array(
//  "status" => "Aangemaakt",
//  "datum"  => "2017-05-29 10:15:00",
//  "transactieSoort"  => "A",
//
//);
//$rowData['datum']  = $fmt->format("@D {d}-{m} {H}:{i}", $rowData['datum']);
//echo $tmpl->parseBlockFromFile("orders/orders_tableRow.html",$rowData);

if ( $showPort == "Geen portefeuilleselectie")
{
  $out .= vt("Configureer eerst deze widget.");
}
else if($DB->records() > 0)
{

  $records = $DB->records();

  while ($data = $DB->nextRecord())
  {
    $lnk = '<a target="content" href="ordersEditV2.php?action=edit&orderregelId='.$data["OrdRegId"].'&id='.$data["ordId"].'" class="btn-new btn-default"><button><i class="fa fa-folder-open" aria-hidden="true"></i></button></a>';
    $data['status'] = $__ORDERvar["status"][$data['status']];
    $data['datum']  = $fmt->format("@D {d}-{m} {H}:{i}", $data['datum']);

    $out .= '
    <div class="rTableRow">
     ';
      foreach ($widgetHelpVermogen->columnData as $k=> $v)
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
        if ($v["dbField"] == "ordId")
        {
          $value = $lnk." ".$data["ordId"];
        }
        $out .= "<div title='".$v["title"]."' class='rTableCell ".$v["class"]." $negren' ".$widgetHelpVermogen->getWidth($v["width"]) ."> ".$value."</div>\n";
      }
    $out .= '
    </div>
    ';

  }

}
else
{
  $out .= "<div>" . vt('Geen orders gevonden') . "</div>";
}


$out .= '
</div> <!-- rTable -->

  <div id="setupWidget_'.$dialogName.'" title="' . vt('Instellen openstaande orders') . '" class="setupWidget">
    <div class="formblock">
      <div class="formlinks">' . vt('Portefeuilleselectie') . '</div>
      <div class="formrechts">
        <select name="port_'.$dialogName.'" id="port_'.$dialogName.'">
          '.$widgetHelpVermogen->makeAccessOptions($showPort).'
        </select>
      </div>
    </div>
    <p>
      '.$widgetHelpVermogen->makeHtmlInput().'
    </p>
  </div>
  <script>
    $(document).ready(function()
    {
      
      '.$cache->JSinit().'

      $("#records").text("'.(int) $records.'");
      var reload = false;
      $("#btn_'.$dialogName.'").click(function()
      {
        setup'.$dialogName.'Dialog.dialog("open");
      });

      var setup'.$dialogName.'Dialog = $("#setupWidget_'.$dialogName.'").dialog(
        {
          autoOpen: false,
          height: 420,
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
                updateCFG("'.$var_port.'", $("#port_'.$dialogName.'").val() );
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
  $out .= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));

  $out .= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));

  $cache->addToCache($out);
  echo $out;