<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/11/20 13:23:22 $
    File Versie         : $Revision: 1.11 $

    $Log: moduleZ_POST_trade.php,v $
    Revision 1.11  2019/11/20 13:23:22  cvs
    call 8224

    Revision 1.10  2019/11/04 13:58:19  cvs
    call 8224

    Revision 1.9  2019/04/15 07:12:40  cvs
    call 7687

    Revision 1.8  2018/12/14 08:32:04  cvs
    call 7410

    Revision 1.7  2018/11/30 12:04:58  cvs
    call 7245

    Revision 1.6  2018/11/19 14:26:51  cvs
    update naar VRY omgeving

    Revision 1.5  2018/10/24 10:09:29  cvs
    call 7175

    Revision 1.4  2018/10/24 06:55:05  cvs
    call 7175

    Revision 1.3  2018/10/08 06:23:13  cvs
    call 7175, bevindingen 5-10

    Revision 1.2  2018/09/14 09:38:13  cvs
    Naar VRY omgeving ter TEST

    Revision 1.1  2018/09/07 10:11:45  cvs
    commit voor robert call 6989

    Revision 1.3  2018/07/02 07:51:11  cvs
    call 6709

    Revision 1.2  2018/06/18 06:59:57  cvs
    update naar VRY omgeving

    Revision 1.1  2018/05/25 09:34:52  cvs
    25-5-2018


*/

include_once("wwwvars.php");
include_once ("moduleZ_functions.php");

$ids = $_SESSION["moduleZ_data"];

$fmt = new AE_cls_formatter();

$msgType = ($_GET["mzType"] == "mm")?"MoneyMarket":"Rebalancing";


$db = new DB();
$query = "
      SELECT 
        OrderRegelsV2.portefeuille,
        OrderRegelsV2.externeBatchId,
        OrdersV2.id as id , 
        OrdersV2.id as orderid, 
        OrdersV2.fondsOmschrijving, 
        OrdersV2.transactieSoort,
        OrdersV2.fondsValuta,
        OrdersV2.Depotbank , 
        OrderRegelsV2.rekening as rekeningnrOld, 
        REPLACE(OrderRegelsV2.rekening,Rekeningen.valuta,'') as rekeningnr,
        if(OrderRegelsV2.rekening <> '',Rekeningen.valuta, Fondsen.valuta) as valuta, 
        ROUND(OrderRegelsV2.aantal,6) AS aantal, 
        OrderRegelsV2.kosten, 
        OrderRegelsV2.brokerkosten, 
        OrderRegelsV2.opgelopenRente, 
        OrderRegelsV2.brutoBedrag, 
        OrderRegelsV2.nettoBedrag,
        OrderRegelsV2.orderbedrag,
        OrderUitvoeringV2.uitvoeringsPrijs ,
        OrderUitvoeringV2.uitvoeringsDatum ,
        OrdersV2.ISINCode as fondsCode , 
        Fondsen.Fonds,
        Fondsen.ISINCode,
        OrderRegelsV2.client , 
        OrderRegelsV2.regelNotaValutakoers as valutakoers,
        BbLandcodes.settlementDays,
        modulezTijdelijkeBatch.record AS batchData
      FROM (OrdersV2) 
        LEFT JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid 
        LEFT JOIN OrderUitvoeringV2 ON OrdersV2.id = OrderUitvoeringV2.orderid 
        LEFT JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
        LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
        LEFT JOIN Rekeningen ON OrderRegelsV2.rekening = Rekeningen.rekening
        LEFT JOIN modulezTijdelijkeBatch ON modulezTijdelijkeBatch.batch = OrderRegelsV2.externeBatchId AND modulezTijdelijkeBatch.portefeuille = OrderRegelsV2.portefeuille
            
      WHERE 
        OrderRegelsV2.id IN (".implode(",", $ids).")
      ORDER BY
        OrderRegelsV2.portefeuille

";
//$__debug = true;
//debug($query);
$db->executeQuery($query);
$dataSetOK = true;
$batch_id  = "";
$error     = array();
$mmArray   = array();
$batches   = array();
$orderTotaalPerPortefeuille = array();
$orderRegels = 0;

while($rec = $db->nextRecord())
{
  $orderRegels++;
  $batches[$rec["externeBatchId"]] ++;
  if ($batch_id == "")
  {
    $batch_id = $rec["externeBatchId"];
  }


  $delta  = (substr($rec["transactieSoort"],0,1) == "V")?$rec["nettoBedrag"]:$rec["nettoBedrag"] * -1;
  $aantal = (substr($rec["transactieSoort"],0,1) == "V")?-1 *$rec["aantal"]:$rec["aantal"];

//  $orderBedragDelta = (substr($rec["transactieSoort"],0,1) == "V")?$rec["orderbedrag"]:$rec["orderbedrag"] * -1;

  // call 7687 accountSluiten erbij zoeken
  $batchData = (array)json_decode($rec["batchData"]);
//  debug($batchData);

  $orderTotaalPerPortefeuille[$rec["portefeuille"]]["orderBedrag"] += $rec["orderbedrag"];
  $orderTotaalPerPortefeuille[$rec["portefeuille"]]["nettoBedrag"] += $delta;
  $orderTotaalPerPortefeuille[$rec["portefeuille"]]["closeAccount"] = $batchData["accountSluiten"];

  $data[$rec["portefeuille"]][] = array(
    "isin"                            => $rec["ISINCode"],
    "participation_delta"             => (float)$aantal,
    "instrument_currency"             => $rec["fondsValuta"],
    "fund_value_euro"                 => (float)$rec["valutakoers"] * $rec["uitvoeringsPrijs"],
    "fund_value_instrument_currency"  => (float)$rec["uitvoeringsPrijs"],
    "exchange_rate"                   => (float)$rec["valutakoers"],
    "description"                     => $rec["transactieSoort"]." ".$rec["aantal"]." x ".$rec["fondsOmschrijving"].", koers ".$rec["fondsValuta"]." ".$rec["uitvoeringsPrijs"],
    "date"                            => str_replace(" ", "T", $rec["uitvoeringsDatum"]),
    "transfer_delta"                  => (float)$delta,
  );

  if ($delta == 0)
  {
    $dataSetOK = false;
  }

}

$query = "
    SELECT 
      COUNT(id) AS aantal
    FROM OrderRegelsV2
    WHERE 
      externeBatchId = '".$batch_id."' AND 
      orderregelStatus < 5 ";


$countRec = $db->lookupRecordByQuery($query);
//debug($query,$countRec);
//debug(array(
//        $orderRegels,
//        $countRec["aantal"],
//        count($batches)
//      ));


//if ($orderRegels != (int)$countRec["aantal"])
//{
//  $error[] = 'niet alle orderregels zijn verwerkt, batch incompleet ';
//}

if(count($batches) != 1)
{
  $error[] = 'meerdere batches geselecteerd, dit is niet toegestaan ';
}


if (count($error) > 0)
{
  echo "<h2>$msgType: Validatie foutmelding(en), verwerking afgebroken!</h2>";
  echo"<ul>";
  foreach ($error as $item)
  {
    echo "<li>$item</li>";
  }
  echo"</ul><br/><br/><br/>";
  exit;
}


if ($_GET["action"] != "go" AND $msgType == 'Rebalancing')
{
  //debug($orderTotaalPerPortefeuille);

//debug($ids);

  if ($msgType == 'Rebalancing')
  {
    $modzBatch = new modulezTijdelijkeBatch();

    ?>
    <hr>
    <style>
      td{
        padding: 5px;
      }
      .ar{
        text-align: right;
      }

      .w10pc{
        width: 8%;
      }
      .mz{
        background: lightskyblue;
      }
    </style>
    <table border="1" cellspacing="0" cellpadding="0" style="padding: 5px; background: whitesmoke">
      <tr>
        <td>portefeuille</td>
        <td class="mz">MZ saldo Liq.</td>
        <td>saldo Liq.</td>
        <td>nettoBedrag</td>
        <td>orderBedrag</td>
        <td>verschil</td>
        <td>nw liq AIRS</td>
        <td class="mz">MZ bedrag gewenst</td>
        <td>def MM order</td>
        <td>sluit acc.</td>




      </tr>
    <?

    $dbFonds = new DB();
    $query = "  SELECT Fonds FROM Fondsen WHERE id in ('14358')";
    $fndsRec = $dbFonds->lookupRecordByQuery($query);
    $mmFonds = $fndsRec["Fonds"];

    foreach ($orderTotaalPerPortefeuille as $portefeuille=>$record)
    {

      $rebalanceBedrag = $modzBatch->getSaldo($portefeuille, $batch_id);
      $cashBedragMz = $modzBatch->getCashSaldo($portefeuille, $batch_id);
      //$delta = number_format($rebalanceBedrag - $record["orderBedrag"],2);
      $verschil = $record["nettoBedrag"] - $record["orderBedrag"];
      $saldoLiq = getLiqSaldo($portefeuille);
      $nwLiqSaldo = $saldoLiq + $record["nettoBedrag"];
      $aanpassingMM = $nwLiqSaldo - $rebalanceBedrag;
      $defMMorder =  $nwLiqSaldo - $rebalanceBedrag;
      $sluitAcc = $record["closeAccount"]?" SLUITEN ":"";


      //if ((float)$defMMorder != 0 )
      {
        echo "
        <tr>
          <td>$portefeuille</td>
          <td class='ar w10pc mz'>".$fmt->format("@n{.2}",$cashBedragMz)."</td>
          <td class='ar w10pc'>".$fmt->format("@n{.2}",$saldoLiq)."</td>
          <td class='ar w10pc'>".$fmt->format("@n{.2}",$record["nettoBedrag"])."</td>
          <td class='ar w10pc'>".$fmt->format("@n{.2}",$record["orderBedrag"])."</td>
          <td class='ar w10pc'>".$fmt->format("@n{.2}",$verschil)."</td>
          <td class='ar w10pc'>".$fmt->format("@n{.2}",$nwLiqSaldo)."</td>
          <td class='ar w10pc mz'>".$fmt->format("@n{.2}",$rebalanceBedrag)."</td>
          <td class='ar w10pc'>".$fmt->format("@n{.2}",$defMMorder)."</td>
          <td class='ac w10pc '>".$sluitAcc."</td>
        </tr>
      ";

        if (!$record["closeAccount"])  // call 7687 alleen meenemen als accsluiten == false
        {
          $mmArray[] = array(
            "portefeuille" => $portefeuille,
            "fonds"        => $mmFonds,
            "cashPositie"  => $defMMorder,
            "batch"        => "$batch_id"
          );
        }


      }
    }
//debug($mmArray);

    echo "</table><hr>";
?>
    <div id="buttonArea">
      <button class="btnClicked" id="btnRB">Verzend naar ModuleZ</button>
<?
    if (count($mmArray) != 0)
    {
      $_SESSION["moduleZ-cash"] = $mmArray;
?>
      <button class="btnClicked" id="btnMM">Genereer MM transacties</button>
<?
    }
?>
    </div>
    <script>
      $(document).ready(function () {
        $(".btnClicked").click(function(e){
          e.preventDefault();
          var url = "";
          var id = $(this).attr("id");
          if (id == "btnMM"){
            url = "moduleZ_getHandelTransfer.php?posted=1&nota=1";
          }
          if (id == "btnRB"){
            url = "<?=$PHP_SELF?>?mzType=<?=$_GET["mzType"]?>&action=go";
          }
          $("#buttonArea").html('<i class="fa fa-spinner fa-spin" style="font-size:36px"></i> moment aub');
          document.location.href = url;

        });
      });
      </script>
<?

  }

}
else
{

//debug($mmArray);

//checks aan de moduleZ kant
//fund_value_euro = exchange_rate *  fund_value_instrumentcurrency.
//
//  En of:
//
//transferdelta = participation_delta * fund_value_euro

  unset($_SESSION["moduleZ_data"]);

  $accounts = array();
  foreach ($data as $k=>$data)
  {
    $accounts[] = array(
      "account_number" => $k,
      "positions_delta" => $data
    );
  }

  $out = array(
    "type" => $msgType,
    "batch_id" => $batch_id,
    "accounts" => $accounts
  );

  if ($dataSetOK)
  {
    $jso = json_encode($out);
    $result =  mzApiPOST("trade",$jso);
    $test = (array)json_decode($result);


    if ($test["result"] == "ok-200")  //alles is goed gegaan??
    {
      $query = "UPDATE OrderRegelsV2 SET change_date = NOW(), change_user = '$USR', printDate = NOW() WHERE id IN (".implode(",", $ids).")";
      $db->executeQuery($query);
      echo "<b>Succes</b><br/><br/>&nbsp;&nbsp;orders succesvol verzonden, ververs pagina.";
    }
    else
    {
      $__debug=true;
      $err = (array)json_decode($result);

      echo "<b>Foutmelding:</b><br/><br/>&nbsp;&nbsp;".$err["code"]." >> ".$err["message"];

    }
  }
  else
  {
    echo "<b>Foutmelding:</b><br/><br/>&nbsp;&nbsp;order(s) met leeg nettoBedrag (transferDelta)";
  }

}
  function getLiqSaldo($portefeuille)
  {
    $db = new DB();
    $query = "
  SELECT
    SUM( Bedrag ) AS totaal 
  FROM
    Rekeningmutaties
    INNER JOIN Rekeningen ON 
      Rekeningmutaties.Rekening = Rekeningen.Rekening
    INNER JOIN Portefeuilles ON 
      Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
  WHERE
    boekdatum >= '".date("Y")."-01-01' AND 
    boekdatum <= NOW()   AND 
    Portefeuilles.Portefeuille = '{$portefeuille}' 
    ";
    $rec = $db->lookupRecordByQuery($query);
    return (float)$rec["totaal"];

  }
