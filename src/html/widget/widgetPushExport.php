<?php
/*
    AE-ICT sourcemodule created 19 apr 2019
    Author              : Chris van Santen
    Filename            : widgetPushExport.php

*/



if (version_compare(phpversion(), '5.3.0', '<'))
  include_once("AE_lib2.php3");
include_once("../../config/local_vars.php");
include_once("../../config/vars.php");
include_once("../../config/auth.php");
include_once("../../classes/AE_cls_WidgetsFilter.php");
include_once("../../classes/AE_cls_WidgetsCaching.php");
$newLine   = "\r\n";
$delimiter = "\t";





//debug($_GET,true,true);

//debug($_SESSION["widgetExport"][$_GET["module"]]);

$data = $_SESSION["widgetExport"][$_GET["module"]];
$first = true;
$out = array();
foreach ($data as $row)
{
  $rowItems = array();
  if ($first)
  {
    $headerItems = array();
    foreach ($row as $k => $v)
    {
      $headerItems[] = $k;

//      $v = str_replace("|", " ", $v);
      $v = str_replace("\n", " ", $v);
      $rowItems[]    = $v;
    }
    $out[] = $headerItems;
    $out[] = $rowItems;
    $first = false;
  }
  else
  {
    foreach ($row as $k => $v)
    {
      $v = str_replace("\n", " ", $v);
      $rowItems[]    = $v;
    }
    $out[] = $rowItems;
  }

}
$filename = "widget_".$_GET["module"]."_".date("YmdHi");
//header('Content-Type: text/csv; charset=utf-8');
//header('Content-Disposition: inline; filename='.$filename);
//foreach ($out as $row)
//{
//  echo implode($delimiter,$row).$newLine;
//}
//debug($out);
include_once("AE_cls_xls.php");
$xls = new AE_xls();
$xls->setData($out);
$xls->OutputXls($filename=$filename.'.xls');