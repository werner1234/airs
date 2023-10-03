<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/23 17:14:23 $
    File Versie         : $Revision: 1.1 $

    $Log: CRM_Simbis_callShow.php,v $
    Revision 1.1  2018/09/23 17:14:23  cvs
    call 7175



*/

include_once("wwwvars.php");
$subHeader    = "";
$mainHeader   = "Simbis call details";

$fmt = new AE_cls_formatter();

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$db = new DB(DBsimbis);
$query = "
SELECT
  mCall_calls.*,
	
	selectievelden.omschrijving as `statusFull`
FROM
	mCall_calls
INNER JOIN selectievelden ON 
  mCall_calls.`status` = selectievelden.prio
WHERE
	mCall_calls.id = '".$_GET["id"]."'
AND 
  selectievelden.module = 'callStatus'
ORDER BY 
  mCall_calls.id DESC
";

$callRec = $db->lookupRecordByQuery($query);
$query = "
SELECT 
  *
FROM 
  mCall_calls_werk
WHERE 
  call_id = '".$_GET["id"]."' 
ORDER BY 
  id DESC";
$db->executeQuery($query);
debug($query);
echo template($__appvar["templateContentHeader"],$content);


$cnt = $callRec["omschrijving"];
$cnt = str_replace('<img src="textAreaData', '<img src="http://airs.simbis.nl/textAreaData', $cnt);
$cnt = str_replace('<img src="/textAreaData', '<img src="http://airs.simbis.nl/textAreaData', $cnt);
?>


<style>
  legend{
    width: 200px;
    padding: 5px;
    border-radius: 4px;
    background: #333;
    color: #FFF;
  }
  .clr{
    clear: both;
    border-top: 1px solid #999;
  }
  .textbox{


    padding: 10px;
    padding-bottom: 20px;


  }
  .TBleft{

    float: left;
    width: 150px;
    text-align: right;

    vertical-align: top;
  }
  .TBright{
    vertical-align: top;
    float: left;
    margin:0 !important;
    display: inline-table;
  }
</style>
<button class="terug"> << terug </button>
<br/>
<br/>
<fieldset>
  <legend>Call informatie:  <b><?=$callRec["id"]?></b></legend>
  <div class="textbox">
    <div class="TBleft">Datum :</div>
    <div class="TBright"><?=$fmt->format("@D{form}",$callRec["add_date"])?>  (<?=$callRec["add_user"]?>)</div>
  </div>
  <div class="textbox">
    <div class="TBleft">Betreft :</div>
    <div class="TBright"><?=$callRec["betreft"]?></div>
  </div>
  <div class="textbox">
    <div class="TBleft">Omschrijving :</div>
    <div class="TBright"><?=trim($cnt)?></div>
  </div>
</fieldset>
<br/>
<br/>
<br/>
<fieldset>
  <legend>Voortgang</legend>

<?

while ($vgRec = $db->nextRecord())
{
  $cnt = $vgRec["omschrijving"];

  $cnt = str_replace('<img src="textAreaData', '<img src="http://airs.simbis.nl/textAreaData', $cnt);
  $cnt = str_replace('<img src="/textAreaData', '<img src="http://airs.simbis.nl/textAreaData', $cnt);
?>
  <div class="textbox">
    <div class="TBleft"><b>Datum :</b></div>
    <div class="TBright"><?=$fmt->format("@D{form}",$vgRec["add_date"])?> (<?=$vgRec["add_user"]?>)</div>
    <div class="TBleft">&nbsp;</b></div>
    <div class="TBright"><?=$cnt?></div>
  </div>
  <div class="clr"></div>
<?

}
?>
</fieldset>
<br/>
<br/>
<button class="terug"> << terug </button>
<script>
  $(document).ready(function(){
    $(".terug").click(function(){
      window.history.back();
    });
  });
</script>
<?
echo template($__appvar["templateRefreshFooterZonderMenu"],$content);

?>
