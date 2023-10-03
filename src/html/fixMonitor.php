<?php
include_once("wwwvars.php");

session_start();
$_SESSION["submenu"] = "";
$_SESSION["NAV"] = "";
session_write_close();
$_DB_resources[1]['server'] = "83.149.75.14";
$_DB_resources[1]['user']   = "monitor";
$_DB_resources[1]['passwd'] = "4191vj(U4)";
$_DB_resources[1]['db']     = "FIXqueue";
$content = array();
echo template($__appvar["templateContentHeader"],$content);

$db = new DB();
$query = "SELECT * FROM queueMeldingen ORDER BY id DESC LIMIT 5";

$db->executeQuery($query);
?>
<style>
  td{
    border-bottom:1px #333 solid;
  }
  legend{
    width: 50%;
    background: #333;
    color: white;
    padding:4px 15px;
  }
  .aantal{
    color:maroon;
    font-size: 18px;
    font-weight: bold;
    padding: 10px;
  }
  .container{
    width: 800px;
    margin:0 auto;
  }
</style>
<div class="container">
<h1>FIX monitor van <?=date("H:i, d-m-Y")?></h1>
<fieldset>
  <legend>laatste 5 queueberichten</legend>

<table>
  <tr>
    <td>stamp</td>
    <td>bericht</td>
  </tr>

<?php
while($rec = $db->nextRecord())
{
  echo "<tr><td style='width: 120px'>{$rec["add_date"]}</td><td>{$rec["text"]}</td>";
}


$query = "SELECT count( id ) AS aantal FROM fixRecords WHERE verwerkt = 0";
$fixrecords = $db->lookupRecordByQuery($query);
$query = "SELECT count( id ) AS aantal FROM airsClientQueue WHERE verwerkt = 0";
$queuerecords = $db->lookupRecordByQuery($query);
?>
</table>
</fieldset>
<br/>
<br/>
  <fieldset>
    <legend>AIRSqueue en FIX onverwerkt</legend>

    <table>
      <tr>
        <td>AIRS queue berichten</td>
        <td class="aantal"><?=$queuerecords["aantal"]?></td>
        <td> < 20 is normaal</td>
      </tr>
      <tr>
        <td>FIX records berichten</td>
        <td class="aantal"><?=$fixrecords["aantal"]?></td>
        <td> < 20 is normaal</td>
      </tr>

    </table>
  </fieldset>

</div>
<?php

echo template($__appvar["templateRefreshFooter"],$content);
