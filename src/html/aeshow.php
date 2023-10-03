<?php

if($_GET['pass'] <> 'verm0gen')
  exit;


  include_once("wwwvars.php");


  $log = new  DB(2);

  $query = "SELECT * FROM terugmelding WHERE id = ".$_GET["id"];

  $log->SQL($query);
  $rec = $log->lookupRecord();

 // listarray($rec);
  $melding    = unserialize($rec[txt]);
  $infoArray  = unserialize($rec["fileinfo"]);
  $dbinfo     = unserialize($rec["dbinfo"]);
  $dbfields   = unserialize($rec["dbfields"]);

 // listarray($melding);
  ?>
  </table>
    <tr>
	  <td>
	    Melding van <?=$rec["bedrijf"]?> log van <?=$rec["datum"]?>
	  </td>
	</tr>
	<tr>
	  <td>
	  Versie info : <?=$melding["general"]["version"]?> release :<?=$melding["general"]["release"]?><br>
	  <hr>
	  Server vars<br>
	  <?=listarray($melding["general"]["server"])?><br>
	  <hr>
	  Session vars<br>
	  <?=listarray($melding["general"]["session"])?><br>
	  <hr>
	  Applicatie vars<br>
	  <?=listarray($melding["general"]["appvar"])?><br>
	  <hr>
	  database vars<br>

  <table bgcolor="Beige" cellpadding="8" border="1" cellspacing="0">
  <tr bgcolor="#DDDDDD">
    <td>
      tabelnaam
    </td>
    <td>
      records
    </td>
    <td>
      grootte
    </td>
    <td>
      create
    </td>
    <td>
      update
    </td>
    <td>
      repair
    </td>
    <td>
      collation
    </td>
  </tr>
<?
  $info = $dbinfo["dbinfo"];
  foreach ($info as $dbinfo)
  {
?>

  <tr>
    <td>
      <?=$dbinfo["Name"]?>
    </td>
    <td>
      <?=$dbinfo["Rows"]?>
    </td>
    <td>
      <?=$dbinfo["Data_length"]?>&nbsp;
    </td>
    <td>
      <?=$dbinfo["Create_time"]?>&nbsp;
    </td>
    <td>
      <?=$dbinfo["Update_time"]?>&nbsp;
    </td>
    <td>
      <?=$dbinfo["Check_time"]?>&nbsp;
    </td>
    <td>
      <?=$dbinfo["Collation"]?>&nbsp;
    </td>
  </tr>
<?

  }
?>
    </table>

    <table bgcolor="Beige" cellpadding="8" border="1" cellspacing="0">
  <tr bgcolor="#DDDDDD">
    <td>
      Table
    </td>
    <td>
      Veldnaam
    </td>
    <td>
      Type
    </td>
    <td>
      Null
    </td>
    <td>
      Key
    </td>
    <td>
      Default
    </td>
  </tr>
<?

  $info = $dbfields["fieldinfo"];
  foreach ($info as $dbinfo)
  {

?>

  <tr>
    <td>
      <?=$dbinfo[0]?>
    </td>
    <td>
      <?=$dbinfo[1]["Field"]?>
    </td>
    <td>
      <?=$dbinfo[1]["Type"]?>
    </td>
    <td>
      <?=$dbinfo[1]["Null"]?>&nbsp;
    </td>
    <td>
      <?=$dbinfo[1]["Key"]?>&nbsp;
    </td>
    <td>
      <?=$dbinfo[1]["Default"]?>&nbsp;
    </td>
  </tr>
<?

  }
?>
    </table>

      <hr>
	  bestanden<br>

  <table bgcolor="Aqua" cellpadding="8" border="1" cellspacing="0">
  <tr bgcolor="#DDDDDD">
    <td>
      bestand
    </td>
    <td>
      datum
    </td>
    <td>
      grootte
    </td>
    <td>
      versie
    </td>
    <td>
      MD5
    </td>
  </tr>
<?

  foreach ($infoArray as $fileInfo)
  {
    $rowdata = explode("|",$fileInfo)
?>

  <tr>
    <td>
      <?=$rowdata[0]?>&nbsp;
    </td>
    <td>
      <?=$rowdata[1]?>&nbsp;
    </td>
    <td>
      <?=$rowdata[2]?>&nbsp;
    </td>
    <td>
      <?=$rowdata[3]?>&nbsp;
    </td>
    <td>
      <?=$rowdata[4]?>&nbsp;
    </td>
  </tr>
<?

  }
?>
    </table>
    <hr>
    local_vars.php <br><br>
    <?=nl2br($rec["local_vars"])?>
	  </td>
	</tr>
	</table>
