<?php
include_once ("init.php");
$fmt = new AE_cls_formatter();
$db = new DB();
?>
<?=$tmpl->parseBlock("kopZonder",array("header" => "Airs update informatie"));?>

<div class="rTable">

<?
$kopData = array(
  "kol1" => vt("Datum"),
  "kol2" => vt("Versie"),
  "kol3" => vt("Omschrijving"),

  "tit1" => vt("Datum"),
  "tit2" => vt("Programmaversie"),
  "tit3" => vt("Omschrijving"),

  "btrTit1" => "Datum",
  "btrTit2" => "Programmaversie",
  "btrTit3" => "Omschrijving",
);

echo $tmpl->parseBlockFromFile("updateInfo/updateInfo_tableHead.html",$kopData);

if ($_SESSION['usersession']['gebruiker']['updateInfoAan'])
{
  $query = "
  SELECT 
    id,
    versie,
    informatie,
    add_date 
  FROM 
    updateInformatie 
  WHERE 
    publiceer=1 
    
  ORDER BY 
    versie DESC
  LIMIT 5";

  $db->executeQuery($query);
  if ($db->records() > 0)
  {

    while ($data = $db->nextRecord())
    {
      $rOut = "";
      $r = explode("\n", $data['informatie']);
      foreach ($r as $item)
      {
        if (trim($item) == "")
        {
          continue;
        }
        $rOut .= "<li>" . trim($item) . "</li>\n";
      }
      $rOut .= "<br/>";
      $rowData = array(
        "kol1Class" => "bgEEE  ",
        "kol2Class" => "bgFFF bold ",
        "kol3Class" => "bgEEE autoHeight",
        "kol1"      => $fmt->format("@D{form}", $data['add_date']),
        "kol2"      => $fmt->format("@n{.3}", $data['versie']),
        "kol3"      => $rOut,
        "tit1" => "Datum",
        "tit2" => "Programmaversie",
        "tit3" => "Omschrijving",
        "btrTit1" => $kopData['btrTit1'],
        "btrTit2" => $kopData['btrTit2'],
        "btrTit3" => $kopData['btrTit3'],
      );
      echo $tmpl->parseBlockFromFile("updateInfo/updateInfo_tableRow.html", $rowData);
    }

  }
  else
  {
    echo '<h4>' . vt('Geen update informatie beschikbaar.') . '</h4>';
  }
}
else
{
  echo '<h4>' . vt('Geen informatie beschikbaar.') . '</h4>';
}

?>
</div> <!-- rTable -->
<?= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));?>