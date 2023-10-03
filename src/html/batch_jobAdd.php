<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/09 12:45:08 $
    File Versie         : $Revision: 1.1 $

    $Log: batch_jobAdd.php,v $
    Revision 1.1  2018/03/09 12:45:08  cvs
    call 3503



*/
include_once("wwwvars.php");
session_start();
include_once "../../classes/AIRS_cls_reconJob.php";

if ($_GET["action"] == "add")
{
  debug($_GET);
  exit;
}

$job = new AIRS_cls_reconJob();
//$job->initModule();

$_SESSION["reconJob"]["Batch"] = $job->batch;
session_commit();
$content = array();
$content["style"] = '<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
echo template("../".$__appvar["templateContentHeader"],$content);
?>
  <script src="javascript/dropzone.js"></script>
  <link href='style/dropzone.css' rel='stylesheet' type='text/css' media='screen'>


<?

?>
  <h2>batch job aanmaken</h2>
  <p>
    selecteer bestanden:
  </p>

  <!-- Change /upload-target to your upload address -->
  <form action="batch_reconFileUpload.php" class="dropzone">


  </form>

  <form >
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="batchnr" value="<?=$job->batch?>">
    Batch id: <b><?=$job->batch?></b>
    <br/>
    <br/>
    Selecteer de depotbank:
    <select name="depot" >
      <option value="ABN">ABN</option>
      <option value="BIN">Binck</option>
      <option value="KAS">Kasbank</option>
      <option value="SNS">SNS</option>
    </select>

    <br/>
    <br/>
      Selecteer de prioriteit:
    <select name="prio">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5" SELECTED>5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
      </select>
    </select>
    <br/>
    <br/>
    <input type="submit" value="opslaan">
  </form>

<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>