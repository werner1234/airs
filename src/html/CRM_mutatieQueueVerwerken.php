<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 21 juli 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/02/01 13:05:43 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: CRM_mutatieQueueVerwerken.php,v $
    Revision 1.1  2018/02/01 13:05:43  cvs
    update naar airsV2

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$fmt = new AE_cls_formatter();

$subHeader     = "";
$mainHeader    = vt("CRM mutatie wachtrij");
$_SESSION['NAV'] = "";

if ($_POST)
{
  $db  = new DB();
  $db2 = new DB();
  if ($_POST["action"] == "delete")
  {
    $query = "UPDATE CRM_mutatieQueue SET afgewerkt = 1, verwerktDoor = '$USR', verwerktDatum = NOW() WHERE id IN (".$_POST["records"].")";
    $db->executeQuery($query);
    $msg = "<h2>".count(explode(",",$_POST["records"]))." " . vt('mutaties afgekeurd en niet verwerkt voor portefeuille') . " ".$_POST["portefeuille"]."</h2>";
  }

  if ($_POST["action"] == "verwerken")
  {
    $query = "SELECT * FROM CRM_mutatieQueue WHERE id IN (".$_POST["records"].") ORDER BY veld, change_date ASC";
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      switch ($rec["type"])
      {
        case "date":
          $split = explode("-",$rec["wordtWaarde"]);
          $waarde = $split[2]."-".$split[1]."-".$split[0];
          break;
        default:
          $waarde = $rec["wordtWaarde"];
          break;

      }

      $query = "UPDATE CRM_naw SET `".$rec["veld"]."` = '".$waarde."', change_date = NOW(), change_user = '$USR' WHERE `portefeuille` = '".$rec["portefeuille"]."'";
      $db2->executeQuery($query);

      addTrackAndTrace("CRM_naw", $rec["CRM_id"], $rec["veld"], $rec["wasWaarde"], $waarde, $USR);
      //debug($query);
    }

    $query = "UPDATE CRM_mutatieQueue SET afgewerkt = 1, verwerkt = 1, verwerktDoor = '$USR', verwerktDatum = NOW() WHERE id IN (".$_POST["records"].")";
    $db->executeQuery($query);
    $msg = "<h2>".count(explode(",",$_POST["records"]))." mutaties verwerkt voor portefeuille ".$_POST["portefeuille"]."</h2>";
  }


}

$db = new DB();
$query = "
SELECT
  portefeuille,
  min(change_date) as oudste,
  max(change_date) as jongste
FROM
  CRM_mutatieQueue
WHERE
  afgewerkt = 0 AND
  verwerkt = 0
GROUP BY
  portefeuille
ORDER BY
  change_date DESC
";

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";
$content["style"] = '
<link rel="stylesheet" href="widget/css/font-awesome.min.css" >
<link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">
<link rel="stylesheet" href="style/workspace.css" type="text/css" media="screen">
<link rel="stylesheet" href="style/AIRS_default.css" type="text/css" media="screen">

';
$content['jsincludes'] = '
<script type="text/javascript" src="javascript/jquery-min.js"></script>
<script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
<script src="javascript/algemeen.js" type=text/javascript></script>

';

echo template($__appvar["templateContentHeader"],$content);
?>
<style>
  #mutaties{
    width: 90%;
    min-height: 500px;
    border: #999 2px solid;
    padding: 10px;
  }
  button{
    padding:4px 6px 4px 6px!important;
  }
</style>

<table class="listTable" cellspacing="0">
  <colgroup>
    <col style="width: 40px;"/>
    <col style="width: 200px;"/>
    <col style="width: 150px;"/>
    <col style="width: 150px;"/>
  </colgroup>
  <tr>
  <td class="listKop"></td>
  <td class="listKop"><?= vt('Portefeuille'); ?></td>
  <td class="listKop"><?= vt('Oudste mutatie'); ?></td>
  <td class="listKop"><?= vt('Jongste mutatie'); ?></td>
  </tr>
<?

  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $jongste = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["jongste"]);
    $oudste  = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["oudste"]);
    echo "
    <tr>
      <td><button title='" . vt('klik om de mutaties te verwerken') . "' data-id='{$rec["portefeuille"]}' class='btnPortefeuille '><i class='fa fa-cog'></i></button> </td>
      <td>{$rec["portefeuille"]}</td>
      <td>{$oudste}</td>
      <td>{$jongste}</td>
    </tr>";
  }

?>
</table>
<br/>
<br/>
<br/>
<br/>
<div id="mutaties">
<?=$msg?>
</div>

<script>
  $(document).ready(function ()
  {
    $(".btnPortefeuille").click(function ()
    {
      var portefeuille = $(this).data("id");
      $("#mutaties").load("CRM_mutatieQueueSelecteer.php?p="+portefeuille);
    })
<?
  if ($_GET["preloadPort"])
  {
    echo '
    $("#mutaties").load("CRM_mutatieQueueSelecteer.php?p='.$_GET["preloadPort"].'");
    ';
  }
?>
  });
</script>
<?
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>