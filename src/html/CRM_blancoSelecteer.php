<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 21 juli 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/07/03 10:28:22 $
    File Versie         : $Revision: 1.6 $
 		
    $Log: CRM_blancoSelecteer.php,v $
   naar RVV 20210120

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../config/JSON.php");

session_start();

$fmt = new AE_cls_formatter();

$ids = explode("|",$_REQUEST["crmId"]);

$subHeader       = "";
$mainHeader      = "Blanco mutaties";
$_SESSION['NAV'] = "";
$kopTxt          = "";
$skipFields      = array("createdAt");
$landenFields    = array("land", "verzendLand");
$db = new DB();


$query = "
SELECT
  *
FROM
  CRM_blanco_mutatieQueue
WHERE
  id = '".(int)$_GET["id"]."'
";
$blancoRec = $db->lookupRecordByQuery($query);




//$mainHeader .= " (".$blancoRec["naam"].")";


$content['pageHeader'] = "
<br>
<div class='edit_actionTxt'>
  <b>$mainHeader</b> <br/>
  
</div>
<br><br>";
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

.tdDimm{
  opacity: 0.4;
  text-line-through-style: ;
}
</style>

<?php

foreach ($ids as $crmId)
{
  $gelijk  = "<tbody class='matching' id='blk-{$crmId}'>";
  $mutatie = "<tbody class='nonMatch'>";
  if ( $crmId> 0)
  {
    $query = "SELECT * FROM `CRM_naw` WHERE id = {$crmId}";
    $crmRec = $db->lookupRecordByQuery($query);
    $kopTxt = $crmRec["naam"].", portefeuille: ".$crmRec["portefeuille"];

  }

?>
<h1>
  <?=$kopTxt?>
</h1>
<table class="listTable" cellspacing="0" style="width: 95%">
  <colgroup>
    <col style="width: 5%;"/>
    <col style="width: 25%;"/>
    <col style="width: 25%"/>
    <col style="width: 25%"/>
    <col style=""/>
  </colgroup>
  <tr>
    <td class="listKop"><input type="checkbox" class="selectAll" data-crmid="<?=$crmId?>" checked></td>
    <td class="listKop">veld</td>
    <td class="listKop">Nieuwe waarde</td>
    <td class="listKop">Oude waarde</td>
  </tr>
<?


  $jsonData = json_decode($blancoRec["jsonData"],true);
//    debug($jsonData);
  foreach ($jsonData as $fld=>$val)
  {
    if (in_array($fld, $skipFields))
    {
      continue;
    }

    if (in_array($fld, $landenFields))
    {
      $val = getIsoLand($val);
    }

    if ($val != $crmRec[$fld])
    {
      $mutatie .= "
      <tr>
        <td><input type='checkbox' class='afvink crm{$crmId}' data-fld='{$fld}|{$crmId}' checked/></td>
        <td>{$fld}</td>
        <td>{$val}</td>
        <td>{$crmRec[$fld]}</td>
      </tr>
    ";
    }
    else
    {
      $gelijk .= "
      <tr>
        <td class='tdDimm'><input type='checkbox' class='afvink' data-fld='{$fld}|{$crmId}' /></td>
        <td class='tdDimm'>{$fld}</td>
        <td class='tdDimm'>{$val}</td>
        <td class='tdDimm'>{$crmRec[$fld]}</td>
      </tr>";
    }
    


  }


echo $mutatie."</tbody>";
echo "<tr><td colspan='1' style='text-align: right; background: #cecece'><button class='btnMatch' id='cmr-{$crmId}'>open</button></td><td colspan='4' style='background: #cecece'> gelijke velden</td></tr>";
echo $gelijk."</tbody>";
?>
</table>
<br/>
  <?php
}
  ?>
<br/>
<?
  if ($_SESSION['usersession']['gebruiker']['CRMlevel'] > 0)
  {
?>
    &nbsp;&nbsp;&nbsp;
    <button id="btnVerwerk" class="btnGreen">verwerk mutaties</button>&nbsp;&nbsp;&nbsp;
    <button id="btnKeuraf" class="btnRed">alle mutaties afkeuren en niet verwerken</button>
<?
  }
  else
  {
    echo "U heeft geen rechten om deze mutaties te verwerken.";
  }
?>

<br/>
<br/>
<br/>
<br/>
<br/>
<form action="CRM_blanco_mutatieQueueList.php" method="post" id="mutForm">
  <input type="hidden" name="blancoId" value="<?=$_GET["id"]?>" />
  <input type="hidden" name="volgnr" value="<?=$_GET["volgnr"]?>" />
  <input type="hidden" name="cmrId" value="<?=(int)$crmRec["id"]?>" />
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

      records.push($(this).data("fld"));

    });
    $("#records").val(records.join(","));
    console.log($("#records").val());
  }

  $(document).ready(function ()
  {
    $(".matching").hide();

    $(".btnMatch").click(function(e){
      e.preventDefault();
      const crmId = $(this).attr("id").substring(4);
      console.log(crmId);
      const state = $(this).text();
      console.log(state);
      if (state == "open")
      {
        $(this).text("sluit");
        $("#blk-"+crmId).show(300);
      }
      else
      {
        $(this).text("open");
        $("#blk-"+crmId).hide(300);
      }
    });
    $(".selectAll").change(function () {
      const crmId = $(this).data("crmid")
      console.log("change ",crmId);
      if ($(this).is(":checked"))
      {
        console.log("check");
        $(".crm"+crmId).prop('checked',true);
      }
      else
      {
        console.log("uncheck");
        $(".crm"+crmId).prop('checked',false);
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


function getIsoLand($iso, $ret="omschrijvingNL")
{
  if (strlen($iso) == 2)
  {
    $db = new DB();
    $query = "SELECT * FROM ISOLanden WHERE landCodeKort = '{$iso}'";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec[$ret];
    }
  }
  else
  {
    return $iso;
  }


}
