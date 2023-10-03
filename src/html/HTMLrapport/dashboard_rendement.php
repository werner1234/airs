<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/10/27 08:51:29 $
    File Versie         : $Revision: 1.5 $

    $Log: dashboard_rendement.php,v $
    Revision 1.5  2017/10/27 08:51:29  cvs
    no message

    Revision 1.4  2017/07/21 13:52:56  cvs
    call 5933

    Revision 1.3  2017/06/26 11:37:54  cvs
    no message

    Revision 1.2  2017/06/02 14:21:10  cvs
    no message

    Revision 1.1  2017/04/26 15:06:22  cvs
    call 5816



*/

include_once("../HTMLrapport/init.php");
//
$portefeuille = $_GET["port"];
$USR = $_SESSION["USR"];


$tmpl = new AE_template();
$tmpl->templatePath = getcwd()."/classTemplates/";
$tmpl->appendSubdirToTemplatePath("dashboard");
$tmpl->loadTemplateFromFile("REN_head.html","head");
$tmpl->loadTemplateFromFile("REN_dataRow.html","datarow");

$fmt = new AE_cls_formatter(",",".");

include_once("../../classes/htmlReports/htmlDashboardHelper.php");
$dash = new htmlDashboardHelper($portefeuille);
$dash->tableName = "_htmlDashboardRen";
$dash->initModule();

$startJaar = substr($_GET["start"],0,4);
$stopJaar = substr($_GET["stop"],0,4);
$dash->getData(false, $_GET["start"], $_GET["stop"]);
$datasetJ = $dash->getRecords("jaar",$_GET["start"], $_GET["stop"]);
//$datasetJ = $dash->getRecords("jaar",(date("Y")-1)."-01-01");
//debug($datasetJ);

$datasetM =  $dash->getRecords("maand",$stopJaar."-01-01", $_GET["stop"]);

$letopTxt = "";
if (getVermogensbeheerderField("PerformanceBerekening") != 4)
{
  $letopTxt = vt("(LET OP: performance kan afwijken van de clientrapportage. Onderstaande is gebaseerd op maandelijkse waardering)");
}


?>

  <br/>
<div style="color: red;"><?=$letopTxt?></div>
  <table class="extraInfoTable" style="width: 100%;">

<?
  echo $tmpl->parseBlock("head", array(
      "kol1" => vt("Maand"),
      "kol2" => vtb("Performance %s maand", '<br/>'),
      "kol3" => vtb("Cumulatieve %s performance", '<br/>')));
  echo rendYTD($datasetM);
?>
  </table>
<br/>
<br/>


  <table class="extraInfoTable" style="width: 100%;">

    <?
    echo $tmpl->parseBlock("head", array(
      "kol1" => vt("Jaar"),
      "kol2" => vt("Performance %s jaar", '<br/>'),
      "kol3" => vtb("Cumulatieve %s performance", '<br/>')));
    echo rendJaar($datasetJ);
    ?>
  </table>
<br/>
<br/>
<br/>
<br/>
<br/>


<?
function rendJaar($dataset)
{
  global $tmpl, $fmt;
  $out = "";
//  debug($dataset);

  foreach ($dataset as $row)
  {

    $tCum[] = $row["performance"];
    $cum = 1;
    foreach ($tCum as $item)
    {
      $cum *= (1 + ($item / 100));
    }
    $cum = ($cum - 1) * 100;

    $out .= $tmpl->parseBlock("datarow", array(
      "kol1" => $fmt->format("@D {Y}", $row["datum"]),
      "kol2" => $fmt->format("@N {.2}", $row["performance"]),
      "kol3" => $fmt->format("@N {.2}", $cum),
    ));

  }
  return $out;
}

function rendYTD($dataset)
{
  global $tmpl, $fmt;
  $out = "";
  foreach($dataset as $row)
  {

    $tCum[] = $row["performance"];
    $cum = 1;
    foreach($tCum as $item)
    {
      $cum *= (1 + ($item/100));
    }
    $cum = ($cum - 1) * 100;

    $out .= $tmpl->parseBlock("datarow",array(
      "kol1" => $fmt->format("@D{M} {Y}", $row["datum"] ),
      "kol2" => $fmt->format("@N {.2}", $row["performance"]),
      "kol3" => $fmt->format("@N {.2}", $cum),
    ));

  }

  $maand = $out;
  return $out;

}

?>