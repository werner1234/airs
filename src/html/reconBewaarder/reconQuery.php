<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/06/22 10:08:24 $
    File Versie         : $Revision: 1.4 $

    $Log: reconQuery.php,v $
    Revision 1.4  2020/06/22 10:08:24  cvs
    call 8702

    Revision 1.3  2019/02/27 15:06:46  cvs
    call 5995

    Revision 1.2  2018/04/25 14:27:09  cvs
    no message

    Revision 1.1  2017/09/20 06:21:04  cvs
    megaupdate 2722



*/

include_once("wwwvars.php");
//aetodo: query nog uitwerken!!
$__debug = true;

$split = explode("-",$_GET["datum"]);
$jaar = $split[2];
$dbdatum = $split[2]."-".$split[1]."-".$split[0];


$db = new DB();
$db2 = new DB();

$query ="
SELECT
	Rekeningmutaties.Rekening,
	Portefeuilles.Portefeuille,
	Portefeuilles.Client AS Client,
	Portefeuilles.Accountmanager AS Accountmanager,
	CASE
    WHEN Rekeningmutaties.Bewaarder <> '' THEN
      Rekeningmutaties.Bewaarder
    ELSE
      Rekeningen.Depotbank
  END AS 'BewaardDoor',
 Rekeningmutaties.Fonds,
 sum(Rekeningmutaties.Aantal) AS 'TotAantal'
FROM
	Rekeningmutaties
INNER JOIN Rekeningen ON 
  Rekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Portefeuilles ON 
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
LEFT JOIN Fondsen ON 
  Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
  Rekeningen.consolidatie = 0 AND
	YEAR (Rekeningmutaties.Boekdatum) = '$jaar' AND 
	Rekeningmutaties.Verwerkt = '1' AND 
	Boekdatum <= '$dbdatum' AND 
	(Grootboekrekening = 'Fonds' OR (Rekeningmutaties.Grootboekrekening = 'KRUIS'  AND Rekeningmutaties.Fonds != ''))
GROUP BY
	Rekeningmutaties.Rekening,
	CASE
    WHEN Rekeningmutaties.Bewaarder <> '' THEN
      Rekeningmutaties.Bewaarder
    ELSE
      Rekeningen.Depotbank
  END,
  Rekeningmutaties.Fonds,
  Fondsen.ISINcode,
  Fondsen.Valuta
 
";

if ($_GET["action"] == "csv")
{
  $db->executeQuery($query);
  $rows = "";
  while ($rec = $db->nextRecord())
  {

    if ($rows == "")
    {
      $head[] = "Recon rekening";
      foreach ($rec as $k=>$v)
      {
        $head[] = $k;
      }
      $rows = '"'.implode('";"',$head).'"'."\r\n";
    }
    $ReconRekening = fetchRek($rec["Portefeuille"],$rec["BewaardDoor"]);
    $rows .= '"'.$ReconRekening.'";"'.implode('";"',$rec).'"'."\r\n";
  }

  header("Content-type: text/csv");
  header("Content-Disposition: attachment; filename='".date("Ymd_H:i")."_Postities_per_bewaarder.csv"."'");
  header("Pragma: no-cache");
  header("Expires: 0");
  echo $rows;
  exit;
}

$content  = array("title"=>$PHP_SELF);

$content['style'] = '
  <link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
  <script type="text/javascript" src="../javascript/jquery-min.js"></script>
  <script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>
  <script language=JavaScript src="../javascript/algemeen.js" type=text/javascript></script>
  <link rel="stylesheet" href="../style/smoothness/jquery-ui-1.11.1.custom.css"></script>
';

echo template("../".$__appvar["templateContentHeader"],$content);



?>
<style>
  .headTR{

    background: #999;

  }
  .headTR td{
    color: white;
    padding: 4px;
  }
  .ar{
    text-align: right;
    padding-right: 4px;
  }
  .ac{
    text-align: center;

  }
  .dataTR{

  }
  .dataTR td{
    border-bottom: 1px solid #999;
  }
</style>
<h1>Postities per bewaarder</h1>
  <br/>
  <br/>
  <a href="../reconSelectDepotbank.php"><button>Terug naar selectie</button></a>
  <a href="?action=csv&datum=<?=$_GET["datum"]?>"><button>Maak .CSV bestand</button></a>
  <br/>
  <br/>
<table >
  <tr class="headTR">
    <td>RECON Rekening</td>
    <td>Rekening</td>
    <td>Portefeuille</td>
    <td>Client</td>
    <td>AccMan</td>
    <td>BewaardDoor</td>
    <td>Fonds</td>
    <td>TotAantal</td>

  </tr>

<?

$db->executeQuery($query);
while ($rec = $db->nextRecord())
{

?>
  <tr class="dataTR">
    <td><?=fetchRek($rec["Portefeuille"],$rec["BewaardDoor"])?></td>
    <td><?=$rec["Rekening"]?></td>
    <td><?=$rec["Portefeuille"]?></td>
    <td class="ac"><?=$rec["Client"]?></td>
    <td class="ac"><?=$rec["Accountmanager"]?></td>
    <td class="ac"><?=$rec["BewaardDoor"]?></td>
    <td><?=$rec["Fonds"]?></td>
    <td class="ar"><?=$rec["TotAantal"]?></td>

  </tr>
<?
}

?>
</table>
<br/>
<br/>
  <a href="../reconSelectDepotbank.php"><button>Terug naar selectie</button></a>
  <a href="?action=csv"><button>Maak .CSV bestand</button></a>
<?

echo template("../".$__appvar["templateRefreshFooter"],$content);

function fetchRek($portefeuille, $depot)
{
  $db2 = new DB();
  $query = "
      SELECT 
        Portefeuille, 
        Rekening, 
        Depotbank 
      FROM 
        Rekeningen
      WHERE 
        Portefeuille = '$portefeuille' AND
        Depotbank = '$depot' AND
        memoriaal = 1 AND
        consolidatie = 0 AND
        inactief = 0
       ORDER BY 
         inactief ASC,
         Rekening ASC";
  $rekRec = $db2->lookupRecordByQuery($query);
//  debug($query);
//  debug($query);
  return $rekRec["Rekening"];



}
