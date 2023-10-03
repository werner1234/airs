<?php
/*
    AE-ICT sourcemodule created 11 jul. 2022
    Author              : Chris van Santen
    Filename            : reconCashVerwerk.php


*/

include_once "wwwvars.php";
session_start();

if ($_GET["file"] == 1)
{

  $delimiter = "\t";
  $filename = "cashRecon_{$_SESSION["cashRecon"]["bank"]}_" . date('Y-m-d_H:i:s') . ".csv";
  $header = array(
    "portefeuille",
    "reknr_BANK",
    "reknr_AIRS",
    "client",
    "vermogensbeheerder",
    "bank",
    "airs",
    "verschil",
    "match",
  );
  $f = fopen('php://memory', 'w');
  fputcsv($f, $header, $delimiter);

  foreach ($_SESSION["cashRecon"]["results"] as $row)
  {
    $dataRow = array(
      $row["portefeuille"],
      $row["rekening"],
      $row["reknrAirs"],
      "",
      "",
      number_format($row["bedrag"],2,".",""),
      number_format($row["AIRS"],2,".",""),
      number_format($row["verschil"],2,".",""),
      "",
    );
    fputcsv($f, $dataRow, $delimiter);
  }
  fseek($f, 0);
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="' . $filename . '";');
  fpassthru($f);
  exit;
}

$filename = $_SESSION["cashRecon"]["file"];
$bank     = $_SESSION["cashRecon"]["bank"];
$datum    = $_SESSION["cashRecon"]["datum"];

if (!file_exists($filename))
{
  echo "recon bestand bestaat niet meer";
}
if (!$handle = @fopen($filename, "r"))
{
  $error[] = "FOUT bestand $filename is niet leesbaar";
  return false;
}

$bankRekeningen = array();
$reconArray     = array();
$depot          = strtoupper($bank);

include_once("reconCash_{$depot}.php");

unlink($filename);

$reknrs = array();
$db = new DB();
//
// bepaal de rekenings aan de AIRS zijde tbv de rekeningmutaties
//
$query = "
SELECT * FROM Rekeningen WHERE
  (
   Rekeningen.RekeningDepotbank in ('" . implode("','", $bankRekeningen) . "') OR
   Rekeningen.Rekening in ('". implode("','", $bankRekeningen) . "')  
  )
  AND 
    Rekeningen.depotbank = '{$depot}'
  AND
    Rekeningen.consolidatie = 0 
  ";


$db->executeQuery($query);

while ($rec = $db->nextRecord())
{
  $reknrs[] = $rec["Rekening"];
}

$query = "
  SELECT
    Rekeningmutaties.Rekening, 
    Rekeningen.RekeningDepotbank, 
    CASE WHEN
      Rekeningen.RekeningDepotbank = '' 
    THEN
      Rekeningen.Rekening 
    ELSE 
      Rekeningen.RekeningDepotbank 
    END AS 'matchRek',	
    SUM(Rekeningmutaties.Bedrag) as totaal
  FROM
    Rekeningmutaties
  LEFT JOIN Rekeningen ON 
	  Rekeningen.Rekening = Rekeningmutaties.Rekening
  WHERE
    Rekeningmutaties.Rekening in ('". implode("','", $reknrs) . "')
    AND
    ( 
      Rekeningmutaties.boekdatum >= '".substr($datum,0,4)."-01-01' AND 
      Rekeningmutaties.boekdatum <= '{$datum}' 
    ) 
      AND
    Rekeningen.consolidatie = 0 
  GROUP BY
    Rekeningmutaties.Rekening, 
    Rekeningen.RekeningDepotbank 
";


//
// matchRek is het rekeningnr uit het bankbestand
//
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $reconArray[$rec["matchRek"]]["AIRS"]     = round($rec["totaal"],2);
  $reconArray[$rec["matchRek"]]["verschil"] = $reconArray[$rec["matchRek"]]["bedrag"] - $reconArray[$rec["matchRek"]]["AIRS"];
  $reconArray[$rec["matchRek"]]["reknrAirs"] = $rec["Rekening"];
}

?>
  <style>
    table{
      font-family: Consolas;
      font-size: 12px;
      padding:5px;
      border:2px solid #999;
    }
    .tdH{
      background: #0a246a;
      color: white;
    }
    .td1, .td2, .td3{
      padding: 5px;
      border-bottom: #BBB 1px solid;
    }
    .ar{ text-align: right}
    .rood{background: maroon; color: white}
  </style>
<h3>CashRecon <?=strtoupper($bank)?></h3>
<table>
  <thead>
  <tr>
    <td class='tdH td1 '>portefeuille</td>
    <td class='tdH td1 '>reknr BANK</td>
    <td class='tdH td1 '>reknr AIRS</td>
    <td class='tdH td1 '>client</td>
    <td class='tdH td1 '>vermogensbeheerder</td>
    <td class='tdH td1 ar'>bank</td>
    <td class='tdH td1 ar'>airs</td>
    <td class='tdH td1  ar'>verschil</td>
  </tr>
  </thead>
<tbody>
<?php
$_SESSION["cashRecon"]["results"] = $reconArray;
foreach ($reconArray as $key=>$item)
{

  $bank     = number_format($item["bedrag"],2);
  $airs     = number_format($item["AIRS"],2);
  $verschil = number_format($item["verschil"],2);
  echo "
  <tr>
      <td class='td1 '>{$item["portefeuille"]}</td>
      <td class='td1 '>{$item["rekening"]}</td>
      <td class='td1 '>{$item["reknrAirs"]}</td>
      
      <td class='td1 '></td>
      <td class='td1 '></td>
      <td class='td1  ar'>{$bank}</td>
      <td class='td1  ar'>{$airs}</td>
      <td class='td1  ar'>{$verschil}</td>
      
    </tr>
  ";
}

?>
</tbody>
</table>
<br/>
<br/>
<a href="?file=1">maak CSV bestand</a>
<?


function stripBOM($field)
{
  $response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $field);
  return $response;
}

function makeNumber($in)
{
  return str_replace(",", ".",trim($in));
}