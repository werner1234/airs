<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 21 juli 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/07/03 10:28:22 $
    File Versie         : $Revision: 1.7 $
 		
    $Log: CRM_blanco_mutatieQueueList.php,v $
naar RVV 20210120

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../config/JSON.php");
session_start();

$fmt = new AE_cls_formatter();
//$bl = new CRM_blanco_mutatieQueue();
$subHeader     = "";
$mainHeader    = "Blanco mutatie wachtrij";
$_SESSION['NAV'] = "";

if ($_POST)
{



  $db  = new DB();
  $db2 = new DB();
  $blancoId = (int)$_POST["blancoId"];
  $volgnr = (int)$_POST["volgnr"];
  $crmId = (int)$_POST["crmId"];
    switch ($_POST["action"])
    {
      case "delete":
        $query = "UPDATE CRM_blanco_mutatieQueue SET afgewerkt = 1, verwerktDoor = '$USR', verwerktDatum = NOW() WHERE id = $blancoId";
        $db->executeQuery($query);
        $msg = "<h2>De mutaties zijn afgekeurd en worden niet verwerkt</h2>";
        break;
      case "verwerken":
        //setjes maken
        $setjes = array();
        $flds = explode(",",$_REQUEST["records"]);
        foreach ($flds as $item)
        {
          $i = explode("|",$item);
          $setjes[$i[1]][] = $i[0];
        }
//        debug($setjes);

        foreach ($setjes as $crmId=>$records)
        {
          if ($crmId > 0)
          {
            $query = "SELECT * FROM CRM_naw WHERE id = " . $crmId;

            $crmRec = $db->lookupRecordByQuery($query);
          }
          if ($blancoId > 0)
          {
            $query = "SELECT * FROM CRM_blanco_mutatieQueue WHERE id = " . $blancoId;
            $blancoRec = $db->lookupRecordByQuery($query);
            $blancoJson = json_decode($blancoRec["jsonData"], true);
            if (count($blancoJson) < 1)
            {
              $blancoJson = unserialize($blancoRec["jsonData"]);
            }
          }
          //debug($_POST);
          $fieldsToUpdate = $records;
          //debug($crmRec);
          //debug($blancoRec);
          //debug($blancoJson);
//          debug($fieldsToUpdate);
          $trackArray = array();
          if ($crmId > 0)
          {
            $queryStart = "
    UPDATE `CRM_naw` SET
      `change_date` = NOW()
      , `change_user` = 'blanco'
    ";
            $queryEnd = "\n WHERE `id` = {$crmId}";
          }
          else
          {
            $queryStart = "
    INSERT INTO `CRM_naw` SET
      change_date = NOW()
      , `change_user` = 'blanco'
      , `add_date` = NOW()
      , `add_user` = 'blanco'
      , `aktief` = 1
      , `prospect` = 1
      , `prospectEigenaar` = '$USR'
    ";
            $trackArray[] = array("prospect", "", "1");
            $trackArray[] = array("prospectEigenaar", "", $USR);
            $queryEnd = "";
            $crmId = -1;
          }

          $excludeDefaults = array();

          foreach ($fieldsToUpdate as $fld)
          {
            $queryMid .= "\n, `$fld` = '" . mysql_real_escape_string($blancoJson[$fld]) . "' ";
            $excludeDefaults[] = $fld;
            $trackArray[] = array($fld, (string)$crmRec[$fld], $blancoJson[$fld]);
          }

          // start call 10599  standaard vulling toepassen
          $db2 = new DB();
          $querySV = "
            SELECT 
              veld,waarde 
            FROM 
              StandaardVeldVulling 
            WHERE 
              `tabel` = 'naw' AND `veld` NOT IN ('".implode("','", $excludeDefaults)."')"; // velden overslaan die gevuld zijn in de Blanco API

          $db2->executeQuery($querySV);
          while($defRec = $db2->nextRecord())
          {
            $queryMid .= "\n, `{$defRec["veld"]}` = '" . mysql_real_escape_string($defRec["waarde"]) . "' ";
            $trackArray[] = array($defRec["veld"], "", $defRec["waarde"]);
          }
          // einde call 10599

          $query = $queryStart . $queryMid . $queryEnd;

          if ($db->executeQuery($query))
          {
            if ($crmId == -1)
            {
              $crmId = $db->last_id();
            }
            foreach ($trackArray as $row)
            {
              addTrackAndTrace("CRM_naw", $crmId, $row[0], $row[1], $row[2], $USR);
            }
          }
          $verwerkt += count($trackArray);
        }

          $query = "UPDATE CRM_blanco_mutatieQueue SET afgewerkt = 1, verwerkt = 1, verwerktDoor = '$USR', verwerktDatum = NOW() WHERE id = $blancoId";
          $db->executeQuery($query);
          $msg = "<h2>".$verwerkt." velden verwerkt</h2>";


        break;
      default:
        $msg = "<h2>onbekende actie</h2>";
    }







}

$db = new DB();
$db2 = new DB();
$query = "
SELECT
  id,
  blancoId,
  jsonData,
  add_date,
  md5
FROM
  CRM_blanco_mutatieQueue
WHERE
  afgewerkt = 0 AND
  verwerkt = 0  
  
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
    <col style="width: 150px;"/>
  </colgroup>
  <tr>
  <td class="listKop"></td>
  <td class="listKop">naam</td>
  <td class="listKop">plaats</td>
  <td class="listKop">match</td>
  <td class="listKop">datum</td>
  </tr>
<?


  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $crmids = array();
    $blob = json_decode($rec["jsonData"],1 );
    if (count($blob) < 1)
    {
      $blob = unserialize($rec["jsonData"]);
    }
    $volgnr = 0;
    if (trim($rec["blancoId"]) == "")
    {
      $query = "UPDATE CRM_blanco_mutatieQueue SET afgewerkt = 1, verwerktDoor = '$USR', verwerktDatum = NOW() WHERE id = {$rec["id"]}";
      $db2->executeQuery($query);
      continue; // zonder blancoid naar afgewerkt en overslaan call 8380
    }
    $query = "SELECT id, blancoId, CONCAT(`portefeuille`,' ',`zoekveld`) as portefeuille FROM `CRM_naw` WHERE `blancoId` LIKE '{$rec["blancoId"]}%'";

    $db2->executeQuery($query);
    while($crmRec = $db2->nextRecord())
    {
      $volgnr++;
      $stamp  = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["add_date"]);
      $crmids[] = $crmRec["id"];
      $portefeuille = $crmRec["portefeuille"];

    }
    if ($volgnr == 0)
    {
      $portefeuille = "- nieuw -";
      $stamp  = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["add_date"]);
      echo "
    <tr>
      <td><button title='klik om de mutaties te verwerken' data-id='{$rec["id"]}' data-crmId='0' data-volg='{$volgnr}' class='btnPortefeuille '><i class='fa fa-cog'></i></button> </td>
      <td>{$blob["naam"]}</td>
      <td>{$blob["plaats"]}</td>
      <td style='font-weight: 700;'>{$portefeuille}</td>
      <td>{$stamp}</td>
    </tr>";
    }
    else
    {
      $match  = (count($crmids) > 1)?"meerdere matches":$portefeuille;
      $crmids = implode("|",$crmids);

      echo "
    <tr>
      <td><button title='klik om de mutaties te verwerken' data-id='{$rec["id"]}' data-crmId='{$crmids}' data-volgnr='{$volgnr}' class='btnPortefeuille '><i class='fa fa-cog'></i></button> </td>
      <td>{$blob["naam"]}</td>
      <td>{$blob["plaats"]}</td>
      <td style='font-weight: 700;'>{$match}</td>
      <td>{$stamp}</td>
    </tr>";
    }



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
      var recId = $(this).data("id");
      var crmId = $(this).data("crmid");
      var volgnr = $(this).data("volgnr");
      $("#mutaties").load("CRM_blancoSelecteer.php?id="+recId+"&crmId="+crmId+"&volgnr="+volgnr);
    })
<?
  if ($_GET["preloadPort"])
  {
    echo '
    $("#mutaties").load("CRM_blancoSelecteer.php?id='.$_GET["preloadPort"].'");
    ';
  }
?>
  });
</script>
<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("Berichten ophalen Blanco","CRM_blanco_mutatieQueueGetNew.php");
echo template($__appvar["templateRefreshFooter"],$content);

function copyRec($table, $id)
{
  $db = new DB();
  $query = "SELECT * FROM `$table` WHERE `id` = $id";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    $query = "INSERT INTO `$table` SET ";
    $query .= "add_date = NOW() ";
    unset($rec["id"]);
    unset($rec["add_date"]);
    foreach ($rec as $f=>$v)
    {
      $query .= ", `$f` = '$v'";
    }
    $db->executeQuery($query);
    return $db->last_id();

  }
  else
  {
    return false;
  }

}