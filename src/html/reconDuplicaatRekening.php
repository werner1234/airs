<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/02/10 14:57:17 $
 		File Versie					: $Revision: 1.7 $

 		$Log: reconDuplicaatRekening.php,v $
 		Revision 1.7  2020/02/10 14:57:17  cvs
 		call 8410
 		
 		Revision 1.6  2020/02/10 14:55:42  cvs
 		call 8410
 		
 		Revision 1.5  2018/12/14 15:56:45  cvs
 		consolidatie
 		
 		Revision 1.4  2018/05/30 13:12:44  cvs
 		call 6908
 		
 		Revision 1.3  2018/04/06 11:12:45  cvs
 		veld memo toegevoegd
 		
 		Revision 1.2  2017/08/30 14:51:47  cvs
 		call 5552
 		
 		Revision 1.1  2015/03/16 12:38:58  cvs
 		*** empty log message ***
 		

*/
include_once("wwwvars.php");
session_start();
$_SESSION[NAV] = "";
session_write_close();
//$content = array();
session_start();
if ($_GET["actie"] == "csv")
{

  foreach ($_SESSION["reconDuplResult"] as $row)
  {
    $out .= implode(";",$row)."\r\n";
  }

  header("Content-type: text/csv");
  header("Content-Disposition: attachment; filename=reconDuplicaat_".$_GET["datum"].".csv");
  header("Pragma: no-cache");
  header("Expires: 0");
  echo $out;
  exit;
}


echo template($__appvar["templateContentHeader"],$content);
?>
<style>
    .error{
      margin: 25px;
      background: red;
      color: white;
      padding: 20px;
      width: 400px;
      text-align: center;
      font-size: 1.5em;
   }


   .reconTable{
     border: 1px #333 solid;
     padding: 4px;
   }
   .reconTable td{
     border-bottom: 1px #999 solid;
     padding:2px 5px 2px 5px;
   }
   .reconTable .headRow{
     background: #EEE;
   }
   .reconTable .headRow td{
     font-weight: bold;
   }
   .ar{
     text-align: right;
   }
   .ac{
     text-align: center;
   }
   .br{
     border-right: 2px #333 solid;
     padding-right: 5px;
   }
   .color1{
     background: white;
   }
   .color2{
     background: beige;
   }
   .rowPass{
     background: #ccffcc;
   }
   .rowFail{
     background: #ff6699;
   }
  </style>


  <br />
  <b>Reconciliatie duplicaat rekeningen</b><br><br>
<?

$data = $_POST;
if ($data["posted"] == "true")
{
  $afschriftDatum = $data["afshriftDatum"];
  unset($manualBoekdatum);
  if (!empty($afshriftDatum))
  {
    $dd = explode($__appvar["date_seperator"], $afshriftDatum);
    if (!checkdate(intval($dd[1]), intval($dd[0]), intval($dd[2])))
    {
      $_error = "Fout: ongeldige afschriftdatum opgegeven";
    }
    else
    {
      if ($dd[2] < 100)
        $dd[2] += 2000;
      $manualBoekdatum = $dd[2]."-".substr("0".$dd[1], -2)."-".substr("0".$dd[0], -2);
    }
  }
  
  $db = new DB();
  $db2 = new DB();

  $recon = new reconcilatieClass();

  $query = "
  SELECT
    RekeningenDuplicaat.id,
    RekeningenDuplicaat.Rekening,
    Rekeningen.Portefeuille,
    Rekeningen.Depotbank,
    RekeningenDuplicaat.RekeningDuplicaat,
    RekeningenDuplicaat.actief,
    RekeningenDuplicaat.Memo
  FROM
    (RekeningenDuplicaat)
  INNER JOIN Rekeningen ON RekeningenDuplicaat.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie = 0
  WHERE
    RekeningenDuplicaat.actief <> 0 
  AND   
    RekeningenDuplicaat.Rekening NOT LIKE '%MEM'";

  $db->executeQuery($query);
  $excelRow = array();
  $excelRow[] = array(
   "Rekening",
   "Portefeuille",
   "Waarde",
   "Dupl. rekening",
   "Dupl. portefeuille",
   "Dupl. waarde",
   "cash akkoord",
   "sec akkoord"
  );

?>
  <a href="?actie=csv&datum=<?=$afschriftDatum?>"><button>maak CSV bestand</button></a>
  <h2>Reconciliatie voor datum: <?=$afschriftDatum?></h2>
  <table class="reconTable">
    <tr class="headRow">
      <td class="color1">Rekening</td>
      <td class="color1">Portefeuille</td>
      <td class="color1">Waarde</td>
      <td class="color2">Dupl. rekening</td>
      <td class="color2">Dupl. portefeuille</td>
      <td class="color2">Dupl. waarde</td>
      <td>cash akkoord</td>
      <td>sec akkoord</td>
      <td>memo</td>
    </tr>
  <?
    $portefeuilleArray = array();
    while ($duplRec = $db->nextRecord())
    {
//      debug($duplRec, "duplRec");
      $recon->depotbank = $duplRec["Depotbank"];

      $q = "SELECT * FROM Rekeningen WHERE Rekening = '".$duplRec["RekeningDuplicaat"]."' AND  `consolidatie` = 0 ";
      $duplPortefeuille = $db2->lookupRecordByQuery($q);


      $waarde = $recon->getAirsPortefeuilleWaardeDuplicaat($duplRec["Portefeuille"], $manualBoekdatum);
      if (!array_key_exists($duplRec["Portefeuille"],$portefeuilleArray))
      {
        $portefeuilleArray[$duplRec["Portefeuille"]] = $recon->checksumPortefeuille($waarde);
      }

      $waarde2 = $recon->getAirsPortefeuilleWaardeDuplicaat($duplPortefeuille["Portefeuille"], $manualBoekdatum);
      if (!array_key_exists($duplPortefeuille["Portefeuille"],$portefeuilleArray))
      {
        $portefeuilleArray[$duplPortefeuille["Portefeuille"]] = $recon->checksumPortefeuille($waarde2);
      }

      $stukkenVerschil = imagecheckbox($portefeuilleArray[$duplPortefeuille["Portefeuille"]] == $portefeuilleArray[$duplRec["Portefeuille"]]);
      $rekeningWaarde = $recon->getAIRSvalutaWaardeDuplicaat($duplRec["Rekening"],$manualBoekdatum);
      $rekeningWaardeDupl = $recon->getAIRSvalutaWaardeDuplicaat($duplRec["RekeningDuplicaat"],$manualBoekdatum);
      $cashVerschil = imagecheckbox($rekeningWaarde == $rekeningWaardeDupl);
      if ( $rekeningWaarde <> $rekeningWaardeDupl )
      {
        $trClass = "rowFail";
      }
      else
      {
        $trClass = "rowPass";
      }

      if ( $portefeuilleArray[$duplPortefeuille["Portefeuille"]] <> $portefeuilleArray[$duplRec["Portefeuille"]]   )
      {
//        debug(array($portefeuilleArray[$duplPortefeuille["Portefeuille"]],$portefeuilleArray[$duplRec["Portefeuille"]] ), $duplPortefeuille["Portefeuille"]);
        $trClass2 = "rowFail";
      }
      else
      {

        $trClass2 = "rowPass";
      }
        $excelRow[] = array(
        $duplRec["Rekening"],
        $duplRec["Portefeuille"],
        $rekeningWaarde,
        $duplRec["RekeningDuplicaat"],
        $duplPortefeuille["Portefeuille"],
        $rekeningWaardeDupl,
        $rekeningWaarde == $rekeningWaardeDupl,
        $portefeuilleArray[$duplPortefeuille["Portefeuille"]] == $portefeuilleArray[$duplRec["Portefeuille"]],
        $duplRec["Memo"]
      ) ;
      $row = "<tr>";
      $row .= "  <td class='color1'>".$duplRec["Rekening"]."</td>";
      $row .= "  <td class='color1'>".$duplRec["Portefeuille"]."</td>";
      $row .= "  <td class='color1 ar br'>".$rekeningWaarde."</td>";
      $row .= "  <td class='color2'>".$duplRec["RekeningDuplicaat"]."</td>";
      $row .= "  <td class='color2'>".$duplPortefeuille["Portefeuille"]."</td>";
      $row .= "  <td class='color2 ar br'>".$rekeningWaardeDupl."</td>";
      $row .= "  <td class='ac $trClass'>$cashVerschil</td>";
      $row .= "  <td class='ac $trClass2'>$stukkenVerschil</td>";
      $row .= "  <td class=''>{$duplRec["Memo"]}</td>";
      $row . "</tr>";
      echo "\n".$row;
    }

    $_SESSION["reconDuplResult"] = $excelRow;

    session_commit();
  ?>
  </table>

  <?
  echo template($__appvar["templateRefreshFooter"],$content);
  exit;
}
?>
  
  
<form  action="<?= $PHP_SELF ?>" method="POST"  name="editForm">
    <input type="hidden" name="posted" value="true" />
    <?php
    if ($_error)
      echo "<b style=\"color:red;\">".$_error."</b>";
    ?>



<div class="form">
  <div class="formblock">
    <div class="formlinks">Geef datum &nbsp;</div>
    <div class="formrechts">
      <input type="text" name="afshriftDatum" value="<?= date("d-m-Y") ?>" size="15"> dd-mm-jjjj 
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <input type="submit" value="bereken" >
    </div>
  </div>
</div>
</form>

<script>
  $(document).ready(function () {

  });
</script>
<?
  echo template($__appvar["templateRefreshFooter"], $content);

?>