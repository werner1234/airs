<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/06/27 09:39:24 $
 		File Versie					: $Revision: 1.2 $

 		$Log: reconOverigePosities.php,v $
 		Revision 1.2  2018/06/27 09:39:24  cvs
 		call 3517
 		
 		Revision 1.1  2018/06/18 14:39:51  cvs
 		call 3517
 		

*/
include_once("wwwvars.php");
session_start();
$_SESSION["NAV"] = "";
session_write_close();
//$content = array();
session_start();

$recon = new reconcilatieClass("", getLaatsteValutadatum());
$excludedDepots = $recon->excludedDepots;

$db = new DB();

if ($_GET["delTemp"] == 1)
{

  $query = "DELETE FROM tijdelijkeRecon WHERE add_user = '$USR' ";
  $db->executeQuery($query);
}


echo template($__appvar["templateContentHeader"],$content);


if ($db->QRecords("SELECT id FROM tijdelijkeRecon WHERE tijdelijkeRecon.change_user = '$USR' ") > 0 AND $_GET["delTemp"] <> 2)
{
  ?>
  <style>
    .fout{

      margin: 25px;
      background: red;
      color: white;
      padding: 20px;
      width: 400px;
      text-align: center;
    }
    .btnRecon{
      height: 40px;
      width: 300px;
      margin:10px;

    }

  </style>
  <div class="fout">
    Tijdelijke reconciliatiemutaties gevonden voor <?= $USR ?><br/><br />

    <a href="<?= $PHP_SELF ?>?delTemp=1"><button class="btnRecon"> verwijder tijdelijke reconciliatiemutaties </button></a>
    <a href="<?= $PHP_SELF ?>?delTemp=2"><button class="btnRecon"> tijdelijke reconciliatiemutaties aanvullen </button></a>
  </div>
  <?
  exit;
}

?>

  <br />
  <b>Reconciliatie Overige posities</b><br><br>
<?
$db1 = new DB();
$data = $_POST;
$excluded =array();
if ($data["posted"] == "true")
{
  foreach ($_POST as $k=>$v)
  {
    if ($v == 1)
    {
      $excluded[] = str_replace("_"," ",$k);
    }
  }

?>
  <div>
    De volgende depotbanken zijn uitgesloten: <br/>
    <?=implode(", ",$excluded)?>
  </div>
  <br/>
  <br/>
  <br/>
  <div id="loading"><img src="images/loading.gif"></div>
<?
  $query = "
  SELECT 
    Portefeuille,
    Depotbank,
    Vermogensbeheerder
  FROM 
    Portefeuilles 
  WHERE
    Vermogensbeheerder = '".$data["VB"]."' AND
    Einddatum > NOW() AND
    Depotbank NOT IN ('".implode("','",$excluded)."')
  ORDER BY
    Depotbank, 
    Portefeuille
  ";

  $db->executeQuery($query);
  $prevDepot = "";
  while ($rec = $db->nextRecord())
  {
//    debug($rec);
    if ($prevDepot != $rec["Depotbank"])
    {
      $prevDepot = $rec["Depotbank"];
      $recon->depotbank = $rec["Depotbank"];
    }

    $portefeuille = $rec["Portefeuille"];
    echo "<li>$portefeuille ($prevDepot)</li>";
    ob_flush(); flush();
    $recon->fillPortefeuilleInfo($portefeuille, true);   // recon record aanmaken met AIRS data
    $fondswaarden = $recon->getAirsPortefeuilleWaarde($portefeuille, $recon->testDate);
//debug($fondswaarden, $portefeuille);
    for ($x = 0; $x < count($fondswaarden); $x++)
    {
      $record = $fondswaarden[$x];
      if (trim($record["fonds"]) == "")
      {
        continue;
      }

      $record["portefeuille"] = $portefeuille;
      if ($record["type"] <> "fondsen")
        continue;

      $recon->updateReconTable($record);
    }

//    $recon->getAirsPortefeuilles()

   $query = "
    SELECT 
      Portefeuilles.*,
      Rekeningen.Rekening as Rekening
    FROM 
      Portefeuilles 
    LEFT JOIN 
      Rekeningen ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
    WHERE  
      Portefeuilles.Vermogensbeheerder = '".$data["VB"]."'  AND 
      Rekeningen.Depotbank = '".$rec["Depotbank"]."' AND
      Portefeuilles.Einddatum > NOW() AND
      Rekeningen.Rekening <> '' AND
      Rekeningen.Memoriaal = 0 AND
      Rekeningen.Inactief = 0 ";

    $db1->executeQuery($query);

    while ($portRec = $db1->nextRecord())
    {

      $record["type"] = "cash";
      if ($rekRec = $recon->getRekening($portRec["Rekening"]) )
      {
        $record["portefeuille"] = $rekRec["Portefeuille"];
        $record["rekening"] = $rekRec["Rekening"];
        $record["Einddatum"] = $rekRec["Einddatum"];
        $record["Accountmanager"] = $rekRec["Accountmanager"];
        $record["bedrag"] = "AIRS";
        $record["valuta"] = $rekRec["Valuta"];
        $record["depot"] = "";  // force AIRS
        $recon->addRecord($record);
      }

    }



  }


?>
  <h2>klaar</h2>
  <script>
    $(document).ready(function(){
      $("#loading").hide();
    });
  </script>
  <a href="tijdelijkereconList.php" >ga naar ingelezen posities</a>
<?
  echo template($__appvar["templateRefreshFooter"],$content);
  exit;
}



$query = "SELECT Vermogensbeheerder, Naam FROM Vermogensbeheerders ORDER BY Vermogensbeheerder ";
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $options .= "\n\t<option value='".$rec["Vermogensbeheerder"]."'>".$rec["Vermogensbeheerder"]." - ".$rec["Naam"]."</option>";
}
?>
  
  
<form  action="<?= $PHP_SELF ?>" method="POST"  name="editForm">
  <input type="hidden" name="posted" value="true" />

  <div class="form">
    <div class="formblock">
      <div class="formlinks">Geeft de vermogenbeheerder &nbsp;</div>
      <div class="formrechts">
        <select name="VB">
          <?=$options?>
        </select>

      </div>
    </div>

  <div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <br/>
      <br/>
      De onderstaande depotbanken worden overgeslagen als aangevinkt
      <?
      foreach ($excludedDepots as $depot)
      {
        echo "\n<br/><input type='checkbox' name='$depot' value='1' checked /> $depot";
      }
      ?>
    </div>

  </div>
  <div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <br/>
      <br/>
      <input type="submit" value="  verder  "  style="padding: 10px; cursor: pointer">
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