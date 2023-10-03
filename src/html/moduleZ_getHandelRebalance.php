<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/12/14 08:32:04 $
    File Versie         : $Revision: 1.15 $

    $Log: moduleZ_getHandelRebalance.php,v $
    Revision 1.15  2018/12/14 08:32:04  cvs
    call 7410

    Revision 1.14  2018/11/19 14:26:51  cvs
    update naar VRY omgeving

    Revision 1.13  2018/11/07 17:05:52  rvv
    *** empty log message ***

    Revision 1.12  2018/11/07 11:48:30  cvs
    call 7282

    Revision 1.11  2018/11/07 09:43:49  rvv
    *** empty log message ***

    Revision 1.10  2018/10/31 17:20:54  rvv
    *** empty log message ***

    Revision 1.9  2018/10/19 07:04:18  cvs
    call 7175

    Revision 1.8  2018/10/08 06:23:13  cvs
    call 7175, bevindingen 5-10

    Revision 1.7  2018/09/23 17:14:23  cvs
    call 7175

    Revision 1.6  2018/09/14 09:37:30  cvs
    call 6989

    Revision 1.5  2018/09/12 14:46:13  rvv
    *** empty log message ***

    Revision 1.4  2018/09/08 17:42:21  rvv
    *** empty log message ***

    Revision 1.3  2018/09/07 14:17:29  cvs
    commit voor robert call 6989

    Revision 1.2  2018/09/07 11:11:36  cvs
    commit voor robert call 6989

    Revision 1.1  2018/09/07 10:11:45  cvs
    commit voor robert call 6989

    Revision 1.3  2018/07/02 08:08:25  cvs
    call 6709

    Revision 1.2  2018/07/02 07:49:17  cvs
    call 6709

    Revision 1.1  2018/06/18 06:59:57  cvs
    update naar VRY omgeving

    Revision 1.1  2018/05/25 09:34:52  cvs
    25-5-2018


*/

include_once("wwwvars.php");
include_once ("moduleZ_functions.php");

$fase2 = false;
$cfg = new AE_config();
$db = new DB();
$fmt = new AE_cls_formatter();
$lkp = new AE_lookup();
$kpl = new AIRS_koppelingen();
$rows = array();


//$kpl->getModuleRecords();
//debug($kpl->dataSet);

$_SESSION['NAV'] = "";

if ($_POST["posted"] == 1)
{
  $data = $_SESSION["moduleZ-rebalance"];
  unset($_SESSION["moduleZ-rebalance"]);

  define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
  include_once("rapport/PDFOverzicht.php");
  include_once("rapport/Modelcontrole.php");
  include_once("../classes/portefeuilleSelectieClass.php");
  $content = array(
    "pageHeader" => "<br><div class='edit_actionTxt'><b>Rebalance posities</b></div><br><br>"
  );
  echo template($__appvar["templateContentHeader"],$content);

  $modzBatch = new modulezTijdelijkeBatch();

  foreach($data as $portefeuilleData)
  {
    debug($portefeuilleData);

     $modzBatch->addRecord($portefeuilleData["batch"],$portefeuilleData);

     $query="SELECT id FROM Portefeuilles WHERE Portefeuille='".$portefeuilleData['portefeuille']."' AND 
                                                soortOvereenkomst='".$portefeuilleData['overeenkomst'] ."' AND 
                                                risicoklasse='".$portefeuilleData['risicoprofiel'] ."' AND 
                                                modelPortefeuille='".$portefeuilleData['modelPortefeuille']."'";

    if($db->QRecords($query)==0)
    {
      echo "Portefeuille ".$portefeuilleData['portefeuille']." is nog niet volledig in Airs bijgewerkt. Modelcontrole afgebroken.<br>\n";
      continue;
    }

    $query="SELECT modelPortefeuille FROM Portefeuilles WHERE Portefeuille='".$portefeuilleData['portefeuille']."'";
    $db->SQL($query);
    $modelPortefeuille=$db->lookupRecord();
//debug($modelPortefeuille,"modelPortefeuille");
debug($portefeuilleData,"portefeuilleData");
    if($portefeuilleData['accountSluiten']==true)
    {
      $typeRapport = 'liquideren';
    }
    else
    {
      $typeRapport = 'vastbedrag';
    }

    $rapportageDatum=date('Y-m-d');//db2jul(getLaatsteValutadatum());
    $selectData=array('datumVan'=>db2jul(date('Y',$rapportageDatum).'-01-01'),
                      'datumTm'=> db2jul($rapportageDatum),
                      'skipPortefeuilleSelectie'=>true,
                      'externeBatchId'=>$portefeuilleData['batch'],
                      'modelcontrole_rapport'=>$typeRapport,
                      'selectedPortefeuilles'=>array($portefeuilleData['portefeuille']),
                      'modelcontrole_portefeuille'=>$modelPortefeuille['modelPortefeuille'],
                      'modelcontrole_rebalance'=>1,
                      'modelcontrole_afronding'=>4,
                      'modelcontrole_level'=>'fonds',
                      'modelcontrole_uitvoer'=>'alles',
                      'modelcontrole_filter'=>'gekoppeld',
                      'modelcontrole_percentage'=>'0.0',
                      'modelcontrole_afronding' =>4,
                      'modelcontrole_vastbedrag'=> (-1 * $portefeuilleData['bedragVrijmaken'])
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

if ($_POST["posted"] == "fase2")
{
  $fase2 = true;
  $prodData = array();

  $result = mzApiGET("trade");
  $result = (array)json_decode($result);

  $airsBatchId = date("Ymd")."T".date("His");

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
    if ($fase2)
    {
//      debug($result);
?>
      <input type="hidden" name="posted" value="1"/>
      <table class="moduleZtable">
        <tr class="moduleZheader">
          <td>Portefeuille</td>
          <td>Client</td>
          <td>Cashpositie</td>
          <td>Bedrag vrijmaken</td>
          <td>Berekend Saldo</td>
          <td>Overeenkomst</td>
          <td>Risicoprofiel</td>
          <td>Modelportefeuille</td>
          <td>Account sluiten</td>
          <td>Batch</td>
        </tr>

<?

        foreach ($result["accounts"] as $item)
        {
          $item = (array) $item;

          $item["batch"]    = $result["batch_id"];
          $risicoSpan       = "";
          $overeenkomstSpan = "";
          $mdlPortSpan      = "";
          if ($portRec = $lkp->getData("Portefeuilles", "Portefeuille = '{$item["account_number"]}'"))
          {
//            debug($portRec);
            $naam    = $portRec["Client"];

            $mdlPortOld      = $portRec["ModelPortefeuille"];
            $mdlPort         = $portRec["ModelPortefeuille"];
            $overeenkomstNw  = $kpl->showAirsDescription($item["product_id"], "products");
            $overeenkomstOld = $portRec["SoortOvereenkomst"];
            $risicoNw        = $kpl->showAirsDescription($item["risk_profile_id"], "riskprofiles");
            $risicoOld       = $portRec["Risicoklasse"];

            if ($risicoOld != $risicoNw OR $overeenkomstOld != $overeenkomstNw)
            {
              if ($risicoOld != $risicoNw)
              {
                $risicoSpan = "<span title='oud: {$risicoOld}\nnieuw: {$risicoNw}' style='color:red; font-weight: bold;'>[ ! ]</span>";
              }

              if ($overeenkomstOld != $overeenkomstNw)
              {
                $overeenkomstSpan = "<span title='oud: {$overeenkomstOld}\nnieuw: {$overeenkomstNw}' style='color:red; font-weight: bold;'>[ ! ]</span>";
              }
            }
            $query = "
                SELECT
                 Portefeuilles.Portefeuille
                FROM
                  Portefeuilles
                JOIN ModelPortefeuilles ON
                  ModelPortefeuilles.Portefeuille = Portefeuilles.Portefeuille
                WHERE
                  SoortOvereenkomst = '{$overeenkomstNw}' AND 
                  Risicoklasse = '{$risicoNw}' ";


              if ($mdlPortRec = $db->lookupRecordByQuery($query))
              {
                if ($mdlPortRec["Portefeuille"] != $mdlPortOld )
                {
                $query = "
                  UPDATE `Portefeuilles` SET
                    change_date = NOW(),
                    change_user = '{$USR}',
                    SoortOvereenkomst = '".$overeenkomstNw."',
                    Risicoklasse = '".$risicoNw."',
                    ModelPortefeuille = '".$mdlPortRec["Portefeuille"]."'
                  WHERE 
                    id = ".(int)$portRec["id"]." ";
                  $db->executeQuery($query);
                  $mdlPortSpan = "<span  title='\noud: {$mdlPortOld}  \nnieuw: {$mdlPortRec["Portefeuille"]}' style='color:Green; font-weight: bold;'> [NW]</span>";
                  $mdlPort = $mdlPortRec["Portefeuille"];
                }

              }
              else
              {
                $mdlPort = "";
                if ($mdlPortOld == $mdlPort)
                {
                  $mdlPortSpan = "<span  style='color:red; font-weight: bold;'>Geen passend modelportefeuille</span>";
                }
                else
                {
                  $mdlPortSpan = "<span style='color:red; font-weight: bold;'>Geen passend modelportefeuille</span> <span  title='\noud: {$mdlPortOld}  \nnieuw: ' style='color:Green; font-weight: bold;'> [NW]</span>";
                }

                $query = "
                  UPDATE `Portefeuilles` SET
                    change_date = NOW(),
                    change_user = '{$USR}',
                    SoortOvereenkomst = '".$overeenkomstNw."',
                    Risicoklasse = '".$risicoNw."',
                    ModelPortefeuille = ''
                  WHERE 
                    id = ".(int)$portRec["id"]." ";
                  $db->executeQuery($query);
              }
              // track and trace
              if ($mdlPort != $mdlPortOld)
              {
                addTrackAndTrace("Portefeuilles", (int)$portRec["id"], "ModelPortefeuille", $mdlPortOld, $mdlPort, $USR) ;
              }
              if ($risicoOld != $risicoNw)
              {
                addTrackAndTrace("Portefeuilles", (int)$portRec["id"], "Risicoklasse", $risicoOld, $risicoNw, $USR) ;
              }
              if ($overeenkomstOld != $overeenkomstNw)
              {
                addTrackAndTrace("Portefeuilles", (int)$portRec["id"], "SoortOvereenkomst", $overeenkomstOld, $overeenkomstNw, $USR) ;
              }

          }
          else
          {
            $naam = "niet in AIRS";
            $mdlPort = "geen koppeling";
          }

          $row = array(
            "portefeuille"      => $item["account_number"],
            "client"            => $naam,
            "cashPositie"       => $item["liquid_balance"],
            "bedragVrijmaken"   => $item["required_liquid_balance"],
            "berekendSaldo"     => $item["liquid_balance"] - $item["required_liquid_balance"] ,
            "overeenkomst"      => $kpl->showAirsDescription($item["product_id"], "products"),
            "risicoprofiel"     => $kpl->showAirsDescription($item["risk_profile_id"], "riskprofiles"),
            "modelPortefeuille" => $mdlPort,
            "accountSluiten"    => $item["close_account"],
            "batch"             => $item["batch"],
          );

          echo "
  <tr >
    <td  class='borderU kp10 '>{$row["portefeuille"]}</td>
    <td  class='borderU kp20 '>{$row["client"]}</td>
    <td  class='borderU ar kp10 bold'>".$fmt->format("@N{.2}", $row["cashPositie"])."</td>
    <td  class='borderU ar kp10 bold'>".$fmt->format("@N{.2}", $row["bedragVrijmaken"])."</td>
    <td  class='borderU ar kp10 bold'>".$fmt->format("@N{.2}", $row["berekendSaldo"])."</td>
    <td  class='borderU kp20 al'>".$row["overeenkomst"]." {$overeenkomstSpan}</td>
    <td  class='borderU kp20 al'>".$row["risicoprofiel"]." {$risicoSpan}</td>
    <td  class='borderU kp20 al'>".$row["modelPortefeuille"]." {$mdlPortSpan}</td>
    <td  class='borderU kp10 ac'>".mz_showCheck($row["accountSluiten"])."</td>
    <td  class='borderU kp10 ac'>".$row["batch"]."</td>
  </tr>
  ";

          $rows[] = $row;
        }

        $_SESSION["moduleZ-rebalance"] = $rows;
        ?>
      </table>
      <br/>
      <br/>
      <input type="submit" value="  akkoord voor verwerking  "/>

<?
    }
    else
    {
      $query = "SELECT * FROM `API_moduleZ_logging` WHERE `referer` LIKE '%/api/trade' AND `method` = 'GET' AND `httpcode` = '200' ORDER BY id DESC";
      if ($rec = $db->lookupRecordByQuery($query))
      {
        $date = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}",$rec["add_date"]);
      }
      else
      {
        $date = "geen vermelding gevonden";
      }
?>
  <input type="hidden" name="posted" value="fase2"/>
  <div>
     Vorige sessie was <?=$date?>
    <br/>
    <br/>
  </div>
      <button id="btnSubmitReb" >  start laden van de selectie  </button>

<?
    }
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

