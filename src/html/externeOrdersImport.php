<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/08/23 11:38:33 $
    File Versie         : $Revision: 1.3 $

    $Log: modulezUploadRapportages.php,v $
    Revision 1.3  2019/08/23 11:38:33  cvs
    call 8024

    Revision 1.2  2019/05/06 07:19:07  cvs
    call 7753

    Revision 1.1  2019/05/03 14:46:21  cvs
    call 7753

    Revision 1.2  2019/04/17 08:36:39  cvs
    call 7629

    Revision 1.1  2019/04/08 12:48:14  cvs
    call 7629

    Revision 1.1  2018/06/18 06:59:57  cvs
    update naar VRY omgeving

    Revision 1.1  2018/05/25 09:34:52  cvs
    25-5-2018


*/

include_once("wwwvars.php");
include_once ("../classes/AE_cls_fileUpload.php");
$upl = new AE_cls_fileUpload();

$data = array_merge($_GET,$_POST);
$error = "";
$done = "";
$db = new DB();

$ingelezen = 0;

if ($data["action"] == "upload")
{
  $ext = substr(strtolower($_FILES["uploadFile"]["name"]),-4);
  if ($ext != ".csv")
  {
    $error = "Geen csv bestand (".$_FILES["uploadFile"]["name"]."), bewerking afgebroken";
  }
  if ($_FILES["uploadFile"]["size"] < 1)
  {
    $error = "Bestand bevat geen data";
  }

  if ($error == "")
  {
    $done = "<ul>";
    $handle = fopen($_FILES['uploadFile']['tmp_name'], "r");
    $row = 0;
    while ($data = fgetcsv($handle, 4096, ","))
    {
      $row++;
      if ($row == 1)
      {
        if($data[1] != "ISIN" OR $data[3] != "Price")
        {
          $error = "geen geldige header";
          break;
        }
        else
        {
          continue;
        }
      }
      if (count($data) > 0)  // geen lege regels inlezen
      {

        $settle = explode("/", $data[7]);
        $datum = str_replace("T", " ",$data[6]);
        $rows[] = array(
          "transactieCode"    => $data[0],
          "ISIN"              => $data[1],
          "fondsValuta"       => $data[2],
          "uitvoeringskoers"  => $data[3],
          "aantal"            => round($data[4],6),
          "nettobedrag"       => $data[5],
          "datum"             => $datum,
          "settlementdatum"   => "20{$settle[2]}-{$settle[0]}-{$settle[1]}",
          "externOrderId"     => $data[8],
          "executor"          => $data[9],

          "fonds"             => "",
          "beurs"             => "",
          "verwerkt"          => 0

        );
        $done .= "<li>{$data[2]} * {$data[0]}, á {$data[1]} = {$data[3]}</li>";
      }


    }

    $done .= "</ul><br/><br/>klaar met inlezen";
    $db  = new DB();
    $dbl = new DB();


    foreach ($rows as $order)
    {

      $query = "SELECT * FROM `Fondsen` WHERE `ISINCode` = '".$order["ISIN"]."' AND `Valuta` = '".$order["fondsValuta"]."'";
//      debug($query);
      if ($fondsRec = $dbl->lookupRecordByQuery($query))
      {
        $fonds = $fondsRec["Fonds"];
      }
      else
      {
        $error .= "<li>FOUT: orderid {$order["externOrderId"]}, fonds met ISIN {$order["ISIN"]}/{$order["fondsValuta"]} niet gevonden, order niet ingelezen ";
        $fonds = "-NB-";
        continue;
      }

      $query = "SELECT id FROM `externeOrders` WHERE `externOrderId` = '{$order["externOrderId"]}'";
      if ($testRec = $dbl->lookupRecordByQuery($query))
      {
        $error .= "<li>FOUT: orderid {$order["externOrderId"]} bestaat al. Order niet ingelezen ";
        continue;
      }

      switch($order["transactieCode"])
      {
        case "BUY":
          $tc = "A";
          break;
        case "SELL":
          $tc = "V";
          break;
        default:
          $tc=$order["transactieCode"];
      }

      $query = "INSERT INTO `externeOrders` SET 
      
       `ISIN`             = '{$order["ISIN"]}'
      ,`uitvoeringskoers` = '{$order["uitvoeringskoers"]}'
      ,`aantal`           = '{$order["aantal"]}'
      ,`nettobedrag`      = '{$order["nettobedrag"]}'
      ,`datum`            = '{$order["datum"]}'
      ,`settlementdatum`  = '{$order["settlementdatum"]}'
      ,`externOrderId`    = '{$order["externOrderId"]}'
      ,`executor`         = '{$order["executor"]}'
      ,`fonds`            = '{$fonds}'
      ,`beurs`            = '{$order["beurs"]}'
      ,`valuta`           = '{$order["fondsValuta"]}'
      ,`verwerkt`         = '{$order["verwerkt"]}'
      ,`transactieCode`   = '{$tc}'
      ,`add_date`         = NOW()
      ,`add_user`         = '{$USR}'
      ,`change_date`      = NOW()
      ,`change_user`      = '{$USR}'
      

    ";

      $db->executeQuery($query);
      $ingelezen++;
    }
    $done = "<h3>Klaar met inlezen van {$ingelezen} orders</h3>";
    $done .= "<a href='externeOrdersList.php'>Ga naar de externe orderlijst</a>";
  }

}

$mainHeader   = "";
$content['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div>";

echo template($__appvar["templateContentHeader"],$content);

if ($done != "")
{
?>
  <div id="msgDialog" style="color: maroon; font-weight: bold; margin: 20px">
      <?=$error?>
  </div>
  <?=$done?>
<?php
  exit;
}

?>
  <link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen"><link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">


  <style>
    fieldset{
      margin: 10px 20px;
    }
    legend{
      width: 200px;
      height: 18px;
      background: #316AC5;
      color: white;
      font-size: 1rem;
      padding: 4px;
    }
    .pageContainer{
      width: 1050px;

    }
    #msgDialog{
      width: 600px;
      padding: 15px;
      background: maroon;
      color: white;
      font-size: 1.2em;
    }
    #msgDone{
      width: 600px;
      padding: 15px;
      background: darkgreen;
      color: white;
      font-size: 1.2em;
    }
    .vDiv{
      margin-top:10px;
    }
    .lDiv{
      display: inline-block;
      width: 120px;
    }
  </style>
  <link rel="stylesheet" href="widget/css/font-awesome.min.css">
  <br/>
  <div class="pageContainer">
  <h2>Inlezen bestand met externe orders</h2>
<?
  if ($error != "")
  {
?>
    <div id="msgDialog">
      <?=$error?>
    </div>
<?
  }
  if ($done != "")
  {
    ?>
    <div id="msgDone">
      <?=$done?>
    </div>
    <?
  }
?>
  <form enctype="multipart/form-data" method="post" >
    <input type="hidden" name="action" value="upload" />
  <fieldset>
    <legend>Selecteer bestand (.csv)</legend>
    <div id="loadProducts">
      <input type="file" name="uploadFile">
    </div>
    <br/>
    <br/>
    <input type="submit" value="start upload">
  </fieldset>
  </form>



  <br/>
  <br/>
  <br/>

</div>
  <script>

    $(document).ready(function () {


    });

  </script>



<?
echo template($__appvar["templateContentFooter"],$content);

function jsonDate($date)
{
  if ($date == "")
  {
    return "0000-00-00T00:00:00";
  }
  $d = explode("-",$date);
  return $d[2]."-".substr("0".$d[1],-2)."-".substr("0".$d[0],-2)."T00:00:00";
}