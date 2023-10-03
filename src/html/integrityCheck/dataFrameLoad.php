<?php
/*
    AE-ICT sourcemodule created 27 Mar 2020
    Author              : Chris van Santen
    Filename            : index.php

    $Log: dataFrameLoad.php,v $
    Revision 1.1  2020/06/19 10:49:54  cvs
    call 3205

*/

include_once("wwwvars.php");
$p = explode("html", getcwd());
$__appvar['base_dir']       = $p[0];

$tmpl = new AE_template();
$fmt  = new AE_cls_formatter();

$rowTemplate = <<<EOB
  <tr>
    <td>{table}</td>
    <td class="ac">{idVb}</td>
    <td class="ac">{idAirs}</td>
    <td>{change_dateVb}</td>
    <td>{change_dateAirs}</td>
    <td>{hashVb}</td>
    <td>{hashAirs}</td>
    <td class="ac {matchColor}">{match}</td>
  </tr>
EOB;
$tmpl->loadTemplateFromString($rowTemplate,"row");
$dbH = new DB();
$db = new DB(2); // connect to updateserver
$__appvar['base_dir']."classes/AE_cls_integrityCheck.php";
$ic = new AE_cls_integrityCheck();

$query = "SELECT * FROM `integrityCheck` WHERE id = ".(int)$_GET["id"];

$rec = $db->lookupRecordByQuery($query);

$query = "SELECT * FROM `integrityCheckHome` WHERE bedrijf = '{$rec["vb"]}' ORDER BY exportId DESC";

$recHome = $dbH->lookupRecordByQuery($query);
$query = "SELECT * FROM `integrityCheckHome` WHERE exportId = '{$recHome["exportId"]}' AND bedrijf = '{$rec["vb"]}' ORDER BY exportId DESC";
$dbH->executeQuery($query);
$AirsData = array();
while ($aRec = $dbH->nextRecord())
{
  $AirsData[] = $aRec;
}

//debug($query);
$content = array();
$editcontent['style']  .= '<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
$content['jsincludes'] .= '<link rel="stylesheet" href="../style/smoothness/jquery-ui-1.11.1.custom.css">';
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"../javascript/jquery-min.js\"></script>";
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"../javascript/jquery-ui-min.js\"></script>";
$content['jsincludes'] .= "<link rel=\"stylesheet\" href=\"../style/fontAwesome/font-awesome.min.css\">";
echo template("../".$__appvar["templateContentHeader"],$content);

$rec = json_decode($rec["json"],true);

//$rec["bedrijf"];
?>
<style>
  *{
    font-family: Sans-Serif;
    font-size: 12px;
  }
  tr:nth-child(even) {background: #DEDEDE}
  tr:nth-child(odd) {background: #FFF}
  td{
    border-right: 1px #999 solid;
    padding: 3px;
    margin: 0;

  }
  .red{background: maroon; color: white;}
  .orange{background: orange; color: #333;}
  .brown{background: #856404; color: white;}
  .ac{ text-align: center;}
</style>
<h2>Bedrijf: <?=$rec["bedrijf"]?></h2>
  <h3>batch=<?=$rec["batch"]?></h3>
  <table>
    <thead>
    <tr>
      <td>tabel</td>
      <td>klant_id</td>
      <td>Airs_id</td>
      <td>klant datum</td>
      <td>Airs datum</td>
      <td>hash VB</td>
      <td>hash AIRS</td>
      <td>match</td>
    </tr>
    </thead>

<?php
  $tables = array();
//  debug($rec["result"]);
  foreach($rec["result"] as $tableName=>$tableData)
  {
    $kTableFields[$tableName] = $tableData["fields"];
    foreach ($tableData["records"] as $id=>$idData)
    {
      $kTableIds[$tableName][] = $id;
    }
  }
//  debug($kTableIds);
//debug($kTableFields);
  foreach ($AirsData as $airs)
  {


    $table = $airs["table"];
    $kData = $rec["result"][$table]["records"];
    if ($Ahash = $ic->getHashesHome($airs, $kTableFields))
    {

//      debug($Ahash, $table);
      $kOnly = $kTableIds[$table];
      foreach ($Ahash as $id=>$hashData)
      {

        $vbDate = $fmt->format("@D{form} {H}:{i}", $kData[$id]["change_date"]);
        $airsDate = $fmt->format("@D{form} {H}:{i}", $hashData[1]);
         if (in_array($id, $kOnly))
         {

           $match = ($hashData[0] == $kData[$id]["hash"])?"gelijk":"verschillend";
           $kOnly = array_diff($kOnly, array($id));

//           debug($kOnly,"delete $id");
           $matchArray = array(
             "table"            => $table,
             "idVb"             => $id,
             "idAirs"           => $id,
             "change_dateVb"    => $vbDate,
             "change_dateAirs"  => $airsDate,
             "hashVb"           => substr($hashData[0],-20),
             "hashAirs"         => substr($kData[$id]["hash"],-20),
             "match"            => $match,
           );
         }
         else
         {
           $matchArray = array(
             "table"            => $table,
             "idVb"             => "-",
             "idAirs"           => $id,
             "change_dateVb"    => "",
             "change_dateAirs"  => $airsDate,
             "hashVb"           => "",
             "hashAirs"         => substr($hashData[0],-20),
             "match"            => "VB missing",
             "matchColor"       => "brown"
           );
         }
        echo $tmpl->parseBlock("row", $matchArray);
      }
      if (count($kOnly) > 0)  // ids not in AIRS
      {
        foreach($kOnly as $item)
        {
          $vbDate = $fmt->format("@D{form} {H}:{i}", $kData[$item]["change_date"]);
          $matchArray = array(
            "table"            => $table,
            "idVb"             => $item,
            "idAirs"           => "-",
            "change_dateVb"    => $vbDate,
            "change_dateAirs"  => "",
            "hashVb"           => substr($kData[$item]["hash"],-20),
            "hashAirs"         => "",
            "match"            => "AIRS missing",
            "matchColor"       => "orange"
          );
        }
        echo $tmpl->parseBlock("row", $matchArray);
      }
    }
    else
    {
      $matchArray = array(
        "table"            => $table,
        "idVb"             => "-",
        "idAirs"           => "-",
        "change_dateVb"    => "",
        "change_dateAirs"  => "",
        "match"            => "VB table missing",
        "matchColor"       => "red"
      );
      echo $tmpl->parseBlock("row", $matchArray);
    }

  }

//  debug($matchArray,"matchArray");
//  debug($output, "Klant zijde");
?>

</table>

<?php
echo template("../".$__appvar["templateRefreshFooter"],$content);
