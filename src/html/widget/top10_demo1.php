<?php
include_once ("init.php");
echo $tmpl->parseBlock("kop",array("header" => "TEST box"));

echo $tmpl->parseBlock("voet",array("stamp" => date("H:i")));
?>
