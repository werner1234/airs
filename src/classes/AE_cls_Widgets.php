<?php
/*
    AE-ICT sourcemodule created 01 sep. 2021
    Author              : Chris van Santen
    Filename            : AE_cls_Widgets.php


*/

class AE_cls_Widgets
{
  var $db;
  var $cfg;
  var $user;
  var $layout = array();
  var $widgetMapping;
  var $widgetsInstalled;
  var $maxWidgetsAllowed = 8;
  var $widgets = 0;
  var $jsValues  = "";
  var $jsAddValues = "";
  var $firstFreeRowPos = 0;
  var $xPos = 0;
  var $widgetData = array();
  var $layName = "";
  var $tab = "";

  function AE_cls_Widgets($tab="")
  {

    $this->tab  = $tab;
    $this->db   = new DB();
    $this->cfg  = new AE_config();

    $this->user = str_replace(".","-",$_SESSION["USR"]);
    $this->user .= ($tab == "")?"":"_".$tab;

    $this->layName = $this->user."_widgetFrontLayout";
    $this->widgets = (int) $this->cfg->getData($this->user."_widgetAantal");
    if ($this->widgets == 0)
    {
      $this->initLayout(true);
    }

    $this->widgetsInstalled = array(
//      "leeg"            => array("url" => "widget_leeg.php", "txt" => "geen widget"),
      "afloopIdBewijs"    => array("url" => "widget_afloopIdBewijs.php",          "txt" => vt("Afloop legitimatiebewijzen")),
      "afloopLei"         => array("url" => "widget_afloopLei.php",               "txt" => vt("Afloop LEI-nummer")),
      "cashPosities"      => array("url" => "widget_cashPosities.php",            "txt" => vt("Cash posities")),
      "contactplanning"   => array("url" => "widget_contactplanning.php",         "txt" => vt("Contactplanning")),
      "debet"             => array("url" => "widget_debetstanden.php",            "txt" => vt("Debetstanden")),
      "externe links"     => array("url" => "widget_externelinks.php",            "txt" => vt("Externe links")),
      "favRelaties"       => array("url" => "widget_favorieteRelaties.php",       "txt" => vt("Favoriete clienten")),
      "gespreksverslag"   => array("url" => "widget_gespreksverslag.php",         "txt" => vt("Gespreksverslagen")),
      "importStatus"      => array("url" => "widget_importStatus.php",            "txt" => vt("Import status ")),
      "interne links"     => array("url" => "widget_internelinks.php",            "txt" => vt("Interne links")),
      "orders"            => array("url" => "widget_orders.php",                  "txt" => vt("Openstaande orderregel")),
      "orders2"           => array("url" => "widget_orders2.php",                 "txt" => vt("Openstaande orders")),
      "portaalMutaties"   => array("url" => "widget_portaalMutaties.php",         "txt" => vt("Portaal mutaties")),
      "portaalVragen"     => array("url" => "widget_portaalVragen.php",           "txt" => vt("Portaal vragenlijsten")),
      "reviewdatum"       => array("url" => "widget_reviewdatum.php",             "txt" => vt("Reviewdatum")),
      "signalering"       => array("url" => "widget_signalering.php",             "txt" => vt("Signalering rendement")),
      "signaleringStort"  => array("url" => "widget_signaleringStortOnttr.php",   "txt" => vt("Signalering Stortingen/Onttrekkingen")),
      "stort-onttr"       => array("url" => "widget_storting-ontrekking.php",     "txt" => vt("Stortingen/Onttrekkingen")),
      "taken"             => array("url" => "widget_taken.php",                   "txt" => vt("Taakoverzicht")),
      "t10_H_rendement"   => array("url" => "widget_top10_hoogsteRendement.php",  "txt" => vt("Top 10 hoogste rendement")),
      "t10_L_rendement"   => array("url" => "widget_top10_laagsteRendement.php",  "txt" => vt("Top 10 laagste rendement")),
      "t10_H_vermogen"    => array("url" => "widget_top10_vermogen.php",          "txt" => vt("Top 10 hoogste vermogens")),
      "updateInfo"        => array("url" => "widget_updateInfo.php",              "txt" => vt("Update infomatie AIRS ")),
      "verjaardagen"      => array("url" => "widget_verjaardagen.php",            "txt" => vt("Verjaardagslijst")),
      "zorgplicht"        => array("url" => "widget_zorgplicht.php",              "txt" => vt("Zorgplichtafwijkingen")),
    );
    // remove admin widgets for other user groups
    global $__appvar;

    if($__appvar["bedrijf"]!="HOME")
    {
      unset($this->widgetsInstalled["importStatus"]);
    }

    $this->getSettings();

  }

  function widgetId($x)
  {
    return $this->user."_widgetLayout_".$x;
  }

  function getSettings()
  {

    $this->jsValues .= "\n\twdgValues.push( {field: 'widget-0', value: 'dummy'} );";
    for($x=1; $x <= $this->widgets; $x++)
    {
      $v = $this->cfg->getData($this->widgetId($x));
      $this->layout[$this->widgetId($x)] = $v;
      if (trim($v) != "")
      {
        $this->jsValues .= "\n\twdgValues.push( {field: 'widget-".$x."', value: '".$this->widgetsInstalled[$v]["url"]."'} );";
      }
      else
      {
        $this->jsValues .= "\n\twdgValues.push( {field: 'widget-".$x."', value: 'widget_leeg.php'} );";
        $this->jsAddValues .= "\n\tgrid.addWidget($(\"<div><div class='grid-stack-item-content' id='widget-".$x."'/></div>\") ,0,0,3,2,true);";
      }

    }
    $this->jsAddValues .= "\n\n";
    $this->jsValues    .= "\n\n";
  }


  ////////////////////////////////////////////

  function getOptions($currentValue = "")
  {
    $out = "\n\t<option value='' > uitgeschakeld </option>";
    foreach ($this->widgetsInstalled as $k => $v)
    {
      $selected = ($currentValue == $k)?"SELECTED":"";
      $out .= "\n\t<option value='$k' $selected>".$v["txt"]."</option>";
    }
    return $out;
  }


  function addWidgetToLayout($layName, $aantal=1)
  {
    for($x=0; $x < $aantal; $x++)
    {
      $nextWidget = count($this->widgetData) + 1;
      $this->widgetData[$nextWidget] = array(
        'id'     => 'widget-'.$nextWidget,
        'x'      => $this->xPos * 3,
        'y'      => $this->firstFreeRowPos,
        'width'  => 3,
        'heigth' => 3,
      );
      $this->xPos++;
      if ($this->xPos > 2)
      {
        $this->xPos = 0;
        $this->firstFreeRowPos += 3;
      }
    }

     $this->putLayoutSettings();
  }

  function initLayout($new=false)
  {
    if ($new)
    {
      $this->widgets = 4; // temp moet later weg
      $defaultLayout = '[
    {"id":"widget-4","x":0,"y":0,"width":6,"height":3},
    {"id":"widget-3","x":6,"y":0,"width":5,"height":3},
    {"id":"widget-2","x":0,"y":3,"width":6,"height":3},
    {"id":"widget-1","x":6,"y":3,"width":5,"height":3}]';
    }

    $items = $this->widgets;
    for ($i=0; $i < $items; $i++)
    {
      $y = intval($i/3);
      $x = ($i%3)*3;
      $out[] = '{"id":"widget-'.($i+1).'","x":'.$x.',"y":'.$y.',"width":3,"height":2}';
    }
    $json = "[".implode(", ", $out)."]";

    if ($new)
    {
      $json = $defaultLayout;
      $this->cfg->addItem($this->user."_widgetAantal", "4");
      $this->cfg->addItem($this->user."_widgetLayout_1", "orders");
      $this->cfg->addItem($this->user."_widgetLayout_2", "taken");
      $this->cfg->addItem($this->user."_widgetLayout_3", "verjaardagen");
      $this->cfg->addItem($this->user."_widgetLayout_4", "zorgplicht");
    }

    $this->cfg->addItem($this->layName, $json);
//    $this->cfg->addItem($this->layName, "");

    return $json;
  }


  function getLayoutSettings()
  {
    global $widgetReload;
    $v = $this->cfg->getData($this->layName);

    $this->widgetData = array();
    if (trim($v) == "")
    {
      $v = $this->initLayout();
      $widgetReload = true;
    }

//    $vArray = json_decode($v);
//    $tel = 0;
//    foreach ($vArray as $item)
//    {
//      $tel++;
//      $this->widgetData[$tel] = (array)$item;
//      $item = (array)$item;
//      $h[] = $item["y"] + $item["height"];
//    }
//    $this->firstFreeRowPos =  max($h);
//
//    if (count($this->widgetData) < $this->widgets)
//    {
//      $this->addWidgetToLayout($this->layName, $this->widgets - count($this->widgetData));
//      $this->putLayoutSettings($this->layName);
//      $v = $this->cfg->getData($this->layName);
//    }

    return $v;
  }

  function putLayoutSettings()
  {
//    /return true;
    $tel = 0;
    foreach($this->widgetData as $item)
    {
      $tel++;
      $out[] = $item;
      if ($tel >= $this->widgets)
      {
        break;
      }

    }
    $json = json_encode($out);
    $this->cfg->addItem($this->layName, $json);

  }

  function getJS()
  {
    return $this->jsValues;
  }

  function showSettings()
  {
    debug($this->layout);
  }

  function getLayout()
  {


  }

}