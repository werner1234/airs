<?php
include_once("../../classes/Autocomplete.php");
include_once("../../classes/AE_cls_Json.php");
session_start();
if (!isset($_SESSION["USR"]))
{
  header("HTTP/1.0 404 Not Found");
  exit;
}
$autocomplete = new Autocomplete();
$data = $autocomplete->getAutoCompleteList($_GET['object'], $_GET['field'], $_GET['term']);

$AEJson = new AE_Json();
echo $AEJson->json_encode($data);