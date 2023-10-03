<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/24 06:36:05 $
    File Versie         : $Revision: 1.2 $

    $Log: batchReconInfo.php,v $
    Revision 1.2  2018/07/24 06:36:05  cvs
    call 7041

    Revision 1.1  2018/03/28 13:15:23  cvs
    call 3503



*/

include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/AE_cls_formatter.php");


$job = new AIRS_cls_reconJob($_GET["batch"]);
$job->getJob();
$j = $job->currentJob;
$fmt = new AE_cls_formatter();
//debug($j);
$uitvoer = ($j["uitvoer"] == "enkel")?"naar één batch":"naar meerdere batches";

if ($j["status"] == "aangemaakt")
{
?>
  <link rel="stylesheet" href="style/AIRS_default.css" type="text/css" media="screen">
  <h2> Info bij batch <?=$j["batchnr"]?> </h2>
  <div id="batchInfo">
    <table>
      <tr><td class="tKol1">Aangemaakt d.d.</td><td calss="tKol2"><?=$fmt->format("@D{form}",$j["add_date"])?> om <?=$fmt->format("@D{H}:{i}",$j["add_date"])?> door <?=$j["add_user"]?></td></tr>
      <tr><td class="tKol1">Verwerkt d.d.</td><td calss="tKol2"><?=$fmt->format("@D{form}",$j["change_date"])?> om <?=$fmt->format("@D{H}:{i}",$j["change_date"])?></td></tr>
      <tr><td class="tKol1">Uitvoer batches</td><td calss="tKol2"><?=$uitvoer?> </td></tr>
      <tr><td class="tKol1">Naam</td><td calss="tKol2"><?=$j["naam"]?></td></tr>
      <tr><td class="tKol1">Status</td><td calss="tKol2"><b>Aangemaakt</b></td></tr>
      <tr><td class="tKol1">Prio</td><td calss="tKol2"><?=$j["prio"]?></td></tr>
      <tr><td class="tKol1">bestanden</td><td calss="tKol2"><?=str_replace(",","<li>",$j["bestanden"])?></td></tr>


    </table>
  </div>
<?
}
else
{


?>

  <link rel="stylesheet" href="style/AIRS_default.css" type="text/css" media="screen">
  <h2> Info bij batch <?=$j["batchnr"]?> </h2>
  <div id="batchInfo">
    <table>
      <tr><td class="tKol1">Aangemaakt d.d.</td><td calss="tKol2"><?=$fmt->format("@D{form}",$j["add_date"])?> om <?=$fmt->format("@D{H}:{i}",$j["add_date"])?> door <?=$j["add_user"]?></td></tr>
      <tr><td class="tKol1">Verwerkt d.d.</td><td calss="tKol2"><?=$fmt->format("@D{form}",$j["change_date"])?> om <?=$fmt->format("@D{H}:{i}",$j["change_date"])?></td></tr>
      <tr><td class="tKol1">Uitvoer batches</td><td calss="tKol2"><?=$uitvoer?> </td></tr>
      <tr><td class="tKol1">Naam</td><td calss="tKol2"><?=$j["naam"]?></td></tr>
      <tr><td class="tKol1">Depotbank</td><td calss="tKol2"><?=$j["depotbank"]?></td></tr>
      <tr><td class="tKol1">VB's</td><td calss="tKol2"><?=$j["vermogenbeheerders"]?></td></tr>
      <tr><td class="tKol1">Verwerkingtijd</td><td calss="tKol2"><?=$j["verwerkingsTijd"]?> sec</td></tr>
      <tr><td class="tKol1">Logging</td><td calss="tKol2"><?=nl2br($j["log"])?></td></tr>

    </table>
  </div>
<?
}
?>

<br/>