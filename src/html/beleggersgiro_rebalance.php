<?php
/*
    AE-ICT sourcemodule created 16 apr. 2021
    Author              : Chris van Santen
    Filename            : beleggersgiro_rebalance.php


*/

include_once("wwwvars.php");
//include_once ("moduleZ_functions.php");

$fase2 = false;
$cfg = new AE_config();
$db = new DB();
$fmt = new AE_cls_formatter();
$lkp = new AE_lookup();
$kpl = new AIRS_koppelingen();
$rows = array();
$airsBatchId = date("Ymd")."T".date("His");
$caIds = array();
//$kpl->getModuleRecords();
//debug($kpl->dataSet);

$_SESSION['NAV'] = "";

if ($_POST["posted"] == "fase2")
{
//  $data = $_SESSION["moduleZ-rebalance"];
//  unset($_SESSION["moduleZ-rebalance"]);

  $query = "
  SELECT
    Portefeuilles.Portefeuille,
    Portefeuilles.Modelportefeuille,
    closeAccount.id as caId,
    CASE
      WHEN closeAccount.Portefeuille IS NULL THEN
      '0' ELSE '1' 
      END AS 'CloseAccount' 
  FROM
    Portefeuilles
    LEFT JOIN ( 
      SELECT 
        DISTINCT Portefeuille, 
        id
      FROM 
        APIextern_closeAccounts 
      WHERE 
        Afgehandeld = 0 
      ) closeAccount 
      ON Portefeuilles.Portefeuille = closeAccount.portefeuille 
  WHERE
    Startdatum > '2000-01-01' AND    
    Einddatum >= NOW() AND 
    consolidatie = 0  AND 
    ModelPortefeuille <> ''
  ";
  global $__debug;
  $__debug = true;
debug($query);
  $db->executeQuery($query);

  while ($rec = $db->nextRecord())
  {
    $rec["batch"] = $airsBatchId;
    $data[] = $rec;
  }
debug($data);
  define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
  include_once("rapport/PDFOverzicht.php");
  include_once("rapport/Modelcontrole.php");
  include_once("../classes/portefeuilleSelectieClass.php");
  $content = array(
    "pageHeader" => "<br><div class='edit_actionTxt'><b>Rebalance posities</b></div><br><br>"
  );
  echo template($__appvar["templateContentHeader"],$content);

  //$modzBatch = new modulezTijdelijkeBatch();

  foreach($data as $portefeuilleData)
  {
    debug($portefeuilleData);

     //$modzBatch->addRecord($portefeuilleData["batch"],$portefeuilleData);

    if($portefeuilleData['CloseAccount'] == '1')
    {
      $caIds[] = $portefeuilleData["caId"];
      $typeRapport = 'liquideren';
    }
    else
    {
      $typeRapport = 'vastbedrag';
    }

    $rapportageDatum = date('Y-m-d');

    $selectData=array('datumVan'                    => db2jul(date('Y',$rapportageDatum).'-01-01'),
                      'datumTm'                     => db2jul($rapportageDatum),
                      'skipPortefeuilleSelectie'    => true,
                      'externeBatchId'              => $portefeuilleData['batch'],
                      'modelcontrole_rapport'       => $typeRapport,
                      'selectedPortefeuilles'       => array($portefeuilleData['Portefeuille']),
                      'modelcontrole_portefeuille'  => $portefeuilleData['Modelportefeuille'],
                      'modelcontrole_rebalance'     => 1,
                      'modelcontrole_afronding'     => 4,
                      'modelcontrole_level'         => 'fonds',
                      'modelcontrole_uitvoer'       => 'alles',
                      'modelcontrole_filter'        => 'gekoppeld',
                      'modelcontrole_percentage'    => '0.0',
                      'modelcontrole_vastbedrag'    => 0
                      );

    debug($selectData);


    $rapport = new Modelcontrole($selectData);
    $rapport->writeRapport();

    if(count($rapport->orderData)>0)
    {
      $log = $rapport->OutputOrder();
      echo "<li>Voor portefeuille " . $portefeuilleData['portefeuille'] . " zijn " . $log['message']."<br>\n";
    }
    else
    {
      echo "<li>Voor portefeuille " . $portefeuilleData['portefeuille'] . " zijn geen orderregels aangemaakt.<br>\n";
    }
  }
?>
  <br/>
  <br/>
  <br/>
  &nbsp;&nbsp;<button><a href="tijdelijkebulkordersv2List.php">Ga naar de tijdelijke orders</a></button>
<?
  echo template($__appvar["templateRefreshFooter"],$content);
  exit;
}

$content = array(
  "pageHeader" => "<br><div class='edit_actionTxt'><b>Rebalance posities</b></div><br><br>"
);
echo template($__appvar["templateContentHeader"],$content);

?>
  <link href="widget/classTemplates/widget.css" rel="stylesheet" type="text/css" media="screen">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <style>
    .moduleZtable{
      min-width: 800px;
    }
    .moduleZtable td{
      padding: 5px;
    }
    .moduleZheader{
      background: rgba(20,60,90,1);
      color: #FFF;
    }
    .moduleZheader td{
      color: #FFF;
    }
    #loading{
      display: none;
      box-sizing: padding-box;
      z-index:999;
      background: rgba(50,50,50,.5);
      color: white;
      font-size: 2rem;
      background-repeat: no-repeat;
      background-position: center;
      padding: 25px;
      text-align: center;
      width: 450px;
    }
    #foutMelding{

      width: 1000px;
      height: auto;
      color: whitesmoke;
      padding: 0;
      border:1px solid #999;
      box-shadow: 4px 4px 4px #333;
    }
    .foutHead{
      background: maroon;
      color: whitesmoke;
      padding: 10px 20px;
      font-size: 1.3em;
      width: calc(100%-40px);
      height: 20px;
    }
    .foutBody{
      background: beige;
      color: maroon;
      width: calc(100%-40px);
      padding: 10px;
    }
  </style>
  <br/>
  <div class="moduleZ-container">
  <div id="loading"><i class="fa fa-spinner fa-spin" style="font-size:36px"></i> moment aub</div>
  <div >
    <form method="post" id="formReb">
<?
  if ($fase2 AND $result["code"] != "")
  {
  ?>   <div id="foutMelding">
    <div class="foutHead">Foutmelding(en)</div>
    <div class="foutBody">FOUT <?=$result["code"]?>: <?=$result["message"]?></div>
  </div>
  <?

  }
  else
  {
  ?>
  <input type="hidden" name="posted" value="fase2"/>

      <button id="btnSubmitReb" >  start laden van de selectie  </button>

<?

?>
    </form>
  </div>
  <script type="text/javascript" src="javascript/jquery-min.js"></script>
  <script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
  <link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">

  <script>
    $(document).ready(function(){
      $("#btnSubmitReb").click(function (e) {
        e.preventDefault();
        $("#loading").show(100);
        $("#formReb").submit();

      });
    });
  </script>

<?
}
echo template($__appvar["templateRefreshFooter"],$content);

