<?
/*
    AE-ICT sourcemodule created 24 okt. 2022
    Author              : Chris van Santen
    Filename            : updateAEconfig.php


*/
$DR = realpath(dirname(__FILE__)."/../..");
include_once($DR."/config/local_vars.php");
include_once($DR."/config/applicatie_functies.php");
include_once($DR."/classes/AE_cls_mysql.php");
include_once($DR."/classes/AE_cls_config.php");
include_once($DR."/config/applicatieVertaling.php");

require($DR."/config/checkLoggedIn.php");

if (count($_GET) == 0)
{
  exit;
}

error_reporting(0);

$cfg = new AE_config();
include_once ($DR."/classes/AE_cls_Widgets.php");
$wdg = new AE_cls_Widgets();

if ($_GET["widget"] != "")
{
  $cache = new AE_cls_WidgetsCaching($_GET["widget"]);
  $cache->deleteCache();
}

$value = $_GET["value"];

$parts = explode("_",$_GET['field']);
$first = array_shift($parts);

$field = implode("_", $parts);

if ($field == "widget_var_favRelaties_data")
{
  $rows = explode("###", $value);
  foreach ($rows as $item)
  {
    $i = explode("|",$item);
    $array[] = array("port"=>$i[0], "prio"=>$i[1], "naam" => $i[2], "relId" => $i[3], "email" => $i[4]);
  }
  $value = serialize($array);
}

if ($field == "widget_var_linksExt_data")
{
  $rows = explode("###", $value);
  foreach ($rows as $item)
  {
    $i = explode("|",$item);
    $array[] = array("site"=>$i[0], "url"=>$i[1]);
  }
  $value = serialize($array);
}



if ($field == "widget_var_linksIntern_data")
{
  $rows = explode("###", $value);

  foreach ($rows as $item)
  {
    $i = explode("|",$item);
    if (trim($i[0]) != "")
    {
      $array[] = array("site"=>$i[0], "url"=>$i[1]);
    }
  }
  $value = serialize($array);
}


switch ($_GET['field'])
{
  case "layoutReset";
    $wdg->initLayout(true);
    break;
  case "kolom":
    _update($value);
    break;
  default:
    $cfg->addItem($_GET['field'], $value);
    $f = explode("_",$_GET['field']);
    if ($f[3] != "")
    {
      $cache = new AE_cls_WidgetsCaching($f[3]);
      $cache->deleteCache();
    }

    break;
}

function _update($value)
{
  global $_GET;
  $p = explode("#", $value);
  $cfg = new AE_config();
  $data = unserialize($cfg->getData($p[0]));
  $f = explode("_",$p[1]);
  $data[$f[1]] = $p[2];
  $cfg->addItem($p[0], serialize($data));

}
