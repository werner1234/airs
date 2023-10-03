<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/14 09:26:36 $
    File Versie         : $Revision: 1.1 $

    $Log: api-orders.php,v $
    Revision 1.1  2018/09/14 09:26:36  cvs
    update 14-9-2018


*/

/*
   action Order

   fields
      ISIN(m),
      currency(m),
      date(c),
      numberOfPrices(c)
*/

//if (
//  $arg["ISIN"] == "" OR
//  $arg["currency"] == ""
//)
//{
//  $error[] = "empty mandatory fields";
//}

if (noErrors())
{

  $db = new DB();
  $db2 = new DB();

  $query = "
  SELECT 
    OrdersV2.id as id , 
    OrdersV2.id as orderid, 
    OrdersV2.fondsOmschrijving, 
    OrdersV2.transactieSoort,
    OrdersV2.fondsValuta,
    OrdersV2.Depotbank , 
    OrderRegelsV2.portefeuille, 
    OrderRegelsV2.rekening as rekeningnrOld, 
    REPLACE(OrderRegelsV2.rekening,Rekeningen.valuta,'') as rekeningnr,
    if(OrderRegelsV2.rekening <> '',Rekeningen.valuta, Fondsen.valuta) as valuta, 
    OrderRegelsV2.aantal, 
    OrderRegelsV2.kosten, 
    OrderRegelsV2.brokerkosten, 
    OrderRegelsV2.opgelopenRente, 
    OrderRegelsV2.brutoBedrag, 
    OrderRegelsV2.nettoBedrag,
    OrderUitvoeringV2.uitvoeringsPrijs ,
    OrderUitvoeringV2.uitvoeringsDatum ,
    OrdersV2.ISINCode as fondsCode , 
    Fondsen.Fonds,
    Fondsen.ISINCode,
    OrderRegelsV2.client , 
    OrderRegelsV2.regelNotaValutakoers as valutakoers,
    IFNULL(BbLandcodes.settlementDays, 0) as settlementDays 
  FROM 
    (OrdersV2) 
  LEFT JOIN OrderRegelsV2 ON 
    OrdersV2.id = OrderRegelsV2.orderid 
  LEFT JOIN OrderUitvoeringV2 ON 
    OrdersV2.id = OrderUitvoeringV2.orderid 
  LEFT JOIN Fondsen ON 
    OrdersV2.fonds = Fondsen.Fonds
  LEFT JOIN BbLandcodes ON 
    Fondsen.bbLandcode = BbLandcodes.bbLandcode
  LEFT JOIN Rekeningen ON 
    OrderRegelsV2.rekening = Rekeningen.rekening
WHERE 
  OrdersV2.orderStatus = '2' 
  ";

  $db->executeQuery($query);
  $tel = 0;
  $datesArray = array("uitvoeringsDatum");
  while ($rec = $db->nextRecord())
  {
    foreach ($datesArray as $date)
    {
      $rec[$date] = toJsonDate($rec[$date]);
    }

    $tel++;

    $p = explode("-", substr($rec['uitvoeringsDatum'], 0, 10));
    $uitvoeringsJul = mktime(8, 0, 0, $p[1], $p[2], $p[0]);
    $dagvanweek = date('N', $uitvoeringsJul);
    $baseDays = ($rec['settlementDays'] > 0)?$rec['settlementDays']:2;

    if ($dagvanweek <= (5 - $baseDays) AND $dagvanweek < 6)
    {
      $extraDagen = 0;
    }
    elseif ($dagvanweek <= (10 - $baseDays) AND $dagvanweek < 6)
    {
      $extraDagen = 2;
    }
    else
    {
      $extraDagen = 4;
    }

    $settleDatum = date('Y-m-d', $uitvoeringsJul + (($baseDays + $extraDagen) * 86400) + 3605) . "T00:00:00";

    if ($rec["valutakoers"] == 0 OR
      $rec['fondsValuta'] == $rec['valuta'])
    {
      $query = "
     SELECT 
       MAX(uitvoeringsDatum) as uitvoeringsDatum  
     FROM 
       OrderUitvoeringV2 
     WHERE 
       orderid = '" . $rec['orderid'] . "'
    ";
      $uitvoering = $db2->lookupRecordByQuery($query);
      $uitvoeringsDatumWhere = ($uitvoering['uitvoeringsDatum'] != '')?" AND Datum<='" . $uitvoering['uitvoeringsDatum'] . "' ":"";

      $query = "
      SELECT 
        koers,
        Valuta 
      FROM 
        Valutakoersen 
      WHERE 
        Valuta = '" . $rec['fondsValuta'] . "' $uitvoeringsDatumWhere 
      ORDER BY 
        Datum DESC
        ";

      $valutaKoers = $db2->lookupRecordByQuery($query);
      $rec['valutakoers'] = $valutaKoers['koers'];

      $output[] = $rec;
    }


  }
}



function toJsonDate($in)
{
  return str_replace(" ", "T", $in);
}
