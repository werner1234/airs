<?php
include_once ("init.php");
$fmt = new AE_cls_formatter();
$db = new DB();
?>
<?=$tmpl->parseBlock("kopZonder",array("header" => vt("lege widget")));?>

<div class="rTable">
  <h4><?= vt('ga naar instellingen om een widget te configureren.'); ?></h4>
</div> <!-- rTable -->
<?=mktime()?>
<?= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));?>