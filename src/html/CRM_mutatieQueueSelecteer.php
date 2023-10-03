<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 21 juli 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/16 07:54:20 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: CRM_mutatieQueueSelecteer.php,v $
    Revision 1.2  2018/03/16 07:54:20  cvs
    call 6257

    Revision 1.1  2018/02/01 13:05:43  cvs
    update naar airsV2

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$fmt = new AE_cls_formatter();

$subHeader     = "";
$mainHeader    = "&nbsp;&nbsp;&nbsp;" . vt('mutaties wachtrij bij portefeuille') . ": ".$_GET["p"];
$_SESSION['NAV'] = "";

$db = new DB();
$query = "
SELECT
  *
FROM
  CRM_mutatieQueue
WHERE
  afgewerkt = 0 AND
  verwerkt = 0 AND 
  portefeuille = '".$_GET["p"]."'
ORDER BY
  veld,
  change_date ASC
      
";



$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";
$content["style"] = '
<link rel="stylesheet" href="widget/css/font-awesome.min.css" >
<link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">
<link rel="stylesheet" href="style/workspace.css" type="text/css" media="screen">


';
$content['jsincludes'] = '
<script type="text/javascript" src="javascript/jquery-min.js"></script>
<script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
<script src="javascript/algemeen.js" type=text/javascript></script>

';

echo template($__appvar["templateContentHeader"],$content);
?>
<style>


</style>

<table class="listTable" cellspacing="0" style="width: 95%">
  <colgroup>
    <col style="width: 5%;"/>
    <col style="width: 25%;"/>
    <col style="width: 25%"/>
    <col style="width: 25%"/>
    <col style=""/>
  </colgroup>
  <tr>
    <td class="listKop"><input type="checkbox" id="selectAll"></td>
    <td class="listKop"><?= vt('veld'); ?></td>
    <td class="listKop"><?= vt('Oude waarde'); ?></td>
    <td class="listKop"><?= vt('Nieuwe waarde'); ?></td>
    <td class="listKop"><?= vt('mutatie'); ?></td>
    <td class="listKop"><?= vt('ip adres'); ?></td>
  </tr>
<?

  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {

    $mutatie  = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["change_date"]);
    echo "
    <tr>
      <td><input type='checkbox' class='afvink' data-id='{$rec["id"]}' /></td>
      <td>{$rec["veld"]}</td>
      <td>{$rec["wasWaarde"]}</td>
      <td>{$rec["wordtWaarde"]}</td>
      <td>{$mutatie}</td>
      <td>{$rec["ip"]}</td>
    </tr>";
  }

?>
</table>
<br/>
<br/>
<?
  if ($_SESSION['usersession']['gebruiker']['CRMlevel'] > 0)  // alleen beheerders mogen verwerken call 6255
  {
?>
    &nbsp;&nbsp;&nbsp;
    <button id="btnVerwerk" class="btnGreen"><?= vt('verwerk mutaties'); ?></button>&nbsp;&nbsp;&nbsp;
    <button id="btnKeuraf" class="btnRed"><?= vt('mutaties afkeuren en niet verwerken'); ?></button>
<?
  }
  else
  {
    echo vt("U heeft geen rechten om deze mutaties te verwerken.");
  }
?>

<br/>
<br/>
<br/>
<br/>
<br/>
<form action="CRM_mutatieQueueVerwerken.php" method="post" id="mutForm">
  <input type="hidden" name="portefeuille" value="<?=$_GET["p"]?>" />
  <input type="hidden" name="action" id="action" value="" />
  <input type="hidden" name="records" id="records" value="" />
</form>

<script>
  function collectData()
  {
    var records = [];
    console.log("start");
    $(".afvink:checked").each(function(index, elem)
    {

      records.push($(this).data("id"));

    });
    $("#records").val(records.join(","));
    console.log($("#records").val());
  }

  $(document).ready(function ()
  {
    $("#selectAll").change(function () {
      console.log("change");
      if ($("#selectAll").is(":checked"))
      {
        console.log("check");
        $(".afvink").prop('checked',true);
      }
      else
      {
        console.log("uncheck");
        $(".afvink").prop('checked',false);
      }
    });
    $("#btnVerwerk").click(function(e){
      e.preventDefault();
      $("#action").val("verwerken");
      collectData();
      $("#mutForm").submit();
    });
    $("#btnKeuraf").click(function(e){
      e.preventDefault();
      $("#action").val("delete");
      collectData();
      $("#mutForm").submit();
    });


  });
</script>
<?
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>