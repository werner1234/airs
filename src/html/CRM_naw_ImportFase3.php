<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/24 11:36:54 $
    File Versie         : $Revision: 1.9 $

    $Log: CRM_naw_ImportFase3.php,v $
    Revision 1.9  2018/10/24 11:36:54  cvs
    call 6713

    Revision 1.8  2018/09/23 17:14:23  cvs
    call 7175

    Revision 1.7  2018/03/12 10:28:26  cvs
    call 6713

    Revision 1.6  2018/03/07 13:31:08  cvs
    call 6713

    Revision 1.5  2017/11/17 14:09:03  cvs
    call 6145 bevindingen

    Revision 1.4  2017/11/17 11:00:51  cvs
    call 6145

    Revision 1.3  2017/11/17 08:03:57  cvs
    call 6145

    Revision 1.2  2017/11/13 13:31:21  cvs
    call 6145 bevindingen

    Revision 1.1  2017/11/08 07:31:26  cvs
    call 6145



*/

include_once("wwwvars.php");
include_once "../classes/AIRS_cls_CRM_naw_importHelper.php";
$pageHead = "Importeren...";

$tmpl = new AE_template();
$tmpl->appendSubdirToTemplatePath("crmImport");

$deletedFields = array_filter(explode(",", $_REQUEST["deleteFlds"]));

if ($_GET["action"] == "go")
{
  $data = $_SESSION["crmImportPOST"];
  $pageHead = "" . vt('Verwerkt') . "...";
  $dryrun = false;
}
else
{
  $data = $_POST;
  $_SESSION["crmImportPOST"] = $data;
  $dryrun = true;
}

//debug($deletedFields);
//debug($_REQUEST);
//debug($_SESSION["crmImportPOST"]);

$import = new CRM_naw_importHelper($data["profile"],true);

foreach($data as $k=>$v)
{
  if (substr($k,0,7) == "import_" AND trim($v) != "" AND !in_array($v, $deletedFields))
  {
    $fld = substr($k,7);
    $importArray[$fld] = array("field"=>$v,"type"=>$_POST["type_".$fld]);
  }
}

if ($dryrun)
{
  $import->setSetting("profile",$data["profile"]);
  $import->setSetting("delimiter",$data["delimiter"]);
  $import->setSetting("dateFormat",$data["dateFormat"]);
  $import->setSetting("dateDelimiter",$data["dateDelimiter"]);
  $import->setSetting("decimalChar",$data["decimalChar"]);
  $import->setSetting("koppelMethode",$data["koppelMethode"]);
  $import->setSetting("columMapping",$importArray);
  if ($data["saveProfile"] == 1)
  {
    $import->saveProfile();
  }
  $import->setSetting("filename", $data["filename"]);  // bewaar gegevens van de vorige fase zonder opslaan
  $_SESSION["crmImportProfile"] = $import->settings;
}

$q = "";

echo template($__appvar["templateContentHeader"],$content);
$tmpl = new AE_template();
$tmpl->appendSubdirToTemplatePath("crmImport");
?>
  <style>
    <?=$tmpl->parseBlockFromFile("crmImport.css");?>
  </style>

  <div class="container">
  <h1><?=$pageHead?></h1>
  <br/>
  <br/>
  <button class="btnPrev"><?= vt('vorige'); ?></button>

  <br/>
  <br/>
<?

if (!$import->setupConverters($data))
{
  echo $import->showErrors();
?>
  <script>
    $(document).ready(function(){
      $(".btnPrev").click(function(e) {
        e.preventDefault();
        window.open("CRM_naw_ImportFase2.php","content");
      });
    });
  </script>
<?

  echo template($__appvar["templateRefreshFooter"],$content);

  exit;
}
//$import->showSettings();
$nietGekoppeld = "";
$filename = $data["filename"];
//debug($filename);
ini_set('auto_detect_line_endings',TRUE);
$handle = fopen($filename,'r');
$count = 0;
//debug($import);
$db = new DB();

$import->dryrun = $dryrun;
$csvDelimiter = ($data['delimiter'] == "tab")?"\t":$data['delimiter'];

?>
  <style>
    .overview td{
      width: 150px;
    }
    .overview .head{
      background: rgba(20,60,90,.8);
      color: #FFF;
    }

  </style>
<fieldset>
  <legend><?= vt('Gekoppelde velden'); ?></legend>
  <table class="overview">
    <tr>
      <td class="head"><?= vt('AIRS veldnaam'); ?></td>
      <td class="head"><?= vt('Import veldnaam'); ?></td>
      <td class="head"><?= vt('Veldtype'); ?></td>
    </tr>
<?
  foreach($import->settings["columMapping"] as $k=>$v)
  {
    echo "<tr><td>{$k}</td><td>{$v["field"]}</td><td>{$v["type"]}</td></tr>";
  }

?>
  </table></fieldset><br/><br/><br/>
<?
while($row = fgetcsv($handle, 8096, $csvDelimiter) )
{
//debug($row);
  $count++;
  if ($count == 1 )
  {
    $temp = $import->mapColumns($row);
    $map = $temp[0];
    if (count($temp[1]) > 0)  // niet gekoppelde velden
    {
      $nietGekoppeld = "<fieldset><legend>" . vt('Niet gekoppelde velden') . "</legend><ul><li>".implode("</li><li>",$temp[1])."</li></ul></fieldset><br/><br/><br/>";
      if ($dryrun)
      {
        echo $nietGekoppeld;
      }
    }
    continue;
  }
  if ($row[0] == NULL)
  {
    continue; // skip lege regels
  }

  if ($data["koppelMethode"] == "externId")
  {

    $externId = trim($row[$import->externId]);

    if ($externId != "" AND $import->externId != -1)
    {
      if ($import->getCRM_nawRecordByExternId($externId))
      {
        if (queryFields($row))
        {
          $query = "UPDATE CRM_naw SET ".implode(", ",$q)." WHERE externID = '".$externId."'";

          if (!$dryrun)
          {
            $db->executeQuery($query);
          }
          $import->commitTrackAndTrace($import->CRM_nawRec["id"], $externId, "UPDATE",$row[$zoekveldCol]);
        }
        else
        {
          $import->csvLog[] = array(vt("importfout"),"" . vt('regel') . " $count",$errorString);
        }
      }
      else
      {
        $import->CRM_nawRec = array();
        if (  queryFields($row,array(
          "aktief = 1",
          "prospect = 1",
          "add_date = NOW()",
          "add_user = '".$import->user."'",

        ))
        )
        {
          $query = "INSERT INTO CRM_naw SET " . implode(", ", $q) . " ";
          if (stristr($query,"`naam`") AND stristr($query,"`zoekveld`") )
          {
            if (!$dryrun)
            {
              $db->executeQuery($query);
            }
            $import->commitTrackAndTrace($db->last_id(), $externId, "INSERT",$row[$zoekveldCol]);
          }
          else
          {
            $import->csvLog[] = array(vt("importfout"),"" . vt('regel') . " $count",vt("verplichte velden naam en/of zoekveld niet gevuld"));
          }

        }
        else
        {
          $import->csvLog[] = array(vt("importfout"),"" . vt('regel') . " $count",$errorString);
        }
      }
    }
  }
  else  // AIRS id
  {
    $airsId = trim($row[$import->airsId]);
    if ($airsId != "" AND $import->airsId != -1)
    {
      if ($import->getCRM_nawRecordByAirsId($airsId))
      {
        if (queryFields($row))
        {
          $query = "UPDATE CRM_naw SET ".implode(", ",$q)." WHERE id = '".$airsId."'";
          if (!$dryrun)
          {
            $db->executeQuery($query);
          }
          $import->commitTrackAndTrace($airsId, $import->CRM_nawRec["externId"], "AirsID UPDATE",$row[$zoekveldCol]);
        }
        else
        {
          $import->csvLog[] = array(vt("importfout"),"" . vt('regel') . " $count",$errorString);
        }
      }
      else
      {
        $import->CRM_nawRec = array();
        if (  queryFields($row,array(
          "aktief = 1",
          "prospect = 1",
          "add_date = NOW()",
          "add_user = '".$import->user."'",


        ))
        )
        {
          if (!$dryrun)
          {
            // op dit moment niet toegestaan om toe te voegen..
            //$query = "INSERT INTO CRM_naw SET " . implode(", ", $q) . " ";
            //$db->executeQuery($query);
            //$import->commitTrackAndTrace($airsId, "", "AirsID  $airsId onbekend INSERT niet toegestaan!");
          }
          $import->csvLog[] = array(vt("importfout"),"" . vt('regel') . " $count","AirsID  $airsId onbekend, INSERT niet toegestaan!");
        }
        else
        {
          $import->csvLog[] = array(vt("importfout"),"" . vt('regel') . " $count",$errorString);
        }
      }
    }
  }
}

fclose($handle);
ini_set('auto_detect_line_endings',FALSE);

?>

 <h2><?= vt('Het bestand bevat'); ?> <?=$data["fileRows"]?> <?= vt('regels'); ?></h2>

<?

$_SESSION["crmImportCSVdata"] = $import->csvLog;
if ($dryrun)
{
  $verslagTxt = vt("Test verwerkingsverslag");
}
else
{
  $verslagTxt = vt("Resultaat v/d verwerking");
}

?>

<fieldset>
  <legend><?=$verslagTxt?></legend>
  <ul>
    <li><b><?= vt('id'); ?>, <?= vt('externId'); ?>, <?= vt('actie'); ?>, <?= vt('zoekveld'); ?></b></li>
<?
    $out = "<li>";
    foreach ($import->csvLog as $item)
    {
      $out .= implode(", ", $item);
      $out .= "</li><li>";
    }
    echo substr($out,0,-4);
?>

  </ul>
<? if (!$dryrun)
  {
?>
    <a href="CRM_naw_importVerslagCsv.php"><button><?= vt('naar .CSV'); ?></button></a>
<?
  }
?>
</fieldset>
<?

if ($dryrun)
{
  echo "<br/><br/><a href='?action=go'><button>" . vt('Verwerk het importbestand') . "</button></a>";
}

function queryFieldsOLD($row, $options)
{
  global $map, $q, $import;
  $import->resetTrackAndTrace();
  $q = array(
    "change_date = NOW()",
    "change_user = '".$import->user."'"
  );
  $q = array_merge($q,$options);
  for ($x=0; $x < count($row); $x++)
  {
    $item = trim($row[$x]);
    if ($item != "" AND $map[$x]["crmField"] != '')
    {
      switch(strtolower($map[$x]["type"]))
      {
        case "datum":
          $val = $import->convertDate($item);
          break;
        case "numeriek":
          $val = $import->convertNummeric($item);
          break;
        default:
          $val = mysql_real_escape_string($item);
      }
      $q[] = "`".$map[$x]["crmField"]."` = '".$val."'";
      $import->queueTrackAndTrace($map[$x]["crmField"], $val);
    }
  }
}

function queryFields($row, $options=array())
{
  global $map, $q, $import, $errorString, $dryrun, $zoekveldCol;
  $error = array();
  $col2Field = array();
  $import->resetTrackAndTrace();
  $q = array(
    "change_date = NOW()",
    "change_user = '".$import->user."'"
  );
  $q = array_merge($q,$options);

  foreach ($map as $item)
  {
    $col2Field[$item["col"]][] = $item;
  }
  $zoekveldCol = -1;
  foreach ($col2Field as $col=>$itemArray)
  {
    foreach ($itemArray as $item)
    {
      if ($item["crmField"] == "zoekveld")
      {
        $zoekveldCol = $item["col"];
      }
      switch(strtolower($item["type"]))
      {
        case "datum":
          if (trim($row[$col]) != "")  // lege waardes toestaan
          {
            $val = $import->convertDate($row[$col]);
            $split = explode("-", $val);
            if (!checkdate($split[1], $split[2], $split[0]))
            {
              $error[] = "" . vt('ongeldige datum') . "(".$row[$col]."), " . vt('import regel overgeslagen') . "";
            }
          }
          else
          {
            $val = "";
          }
          break;
        case "numeriek":
          if (trim($row[$col]) != "")  // lege waardes toestaan
          {
            $val = $import->convertNummeric($row[$col]);
            if (!is_numeric($val))
            {
              $error[] = "" . vt('ongeldige getal') . "($val), " . vt('import regel overgeslagen') . "";
            }
          }
          else
          {
            $val = "";
          }
          break;
        default:
          $val = mysql_real_escape_string($row[$col]);
      }
      if ($item["crmField"] != "id")
      {
        $q[] = "`".$item["crmField"]."` = '".$val."'";
      }

      $import->queueTrackAndTrace($item["crmField"], $val);
    }

  }
  if (count($error) > 0)
  {
    $errorString = implode("# ", $error);
    return false;
  }
  else
  {
    $errorString = "";
    return true;
  }
}

?>
  <script>
    $(document).ready(function(){
      $(".btnPrev").click(function(e) {
        e.preventDefault();
        window.open("CRM_naw_ImportFase2.php","content");
      });
    });
  </script>
<?

echo template($__appvar["templateRefreshFooter"],$content);
