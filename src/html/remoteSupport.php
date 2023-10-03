<?php
// set module naam voor autenticatie leeg = iedereen.
include_once("wwwvars.php");

$content = array();
echo template($__appvar["templateContentHeader"],$content);
?>


<h2><?= vt('Remote support'); ?></h2>
<br />
  <div class="buttonDiv" style="width:200px;text-align: center"><a href="https://get.teamviewer.com/t36canx" target="_blank" style="color:navy""> <?= vt('Download teamviewer'); ?></a></div>
<br />

<?
echo template($__appvar["templateRefreshFooter"],$content);
?>