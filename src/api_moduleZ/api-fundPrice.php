<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/06 08:29:02 $
    File Versie         : $Revision: 1.3 $

    $Log: api-fundPrice.php,v $
    Revision 1.3  2018/07/06 08:29:02  cvs
    call 7032

    Revision 1.2  2018/07/04 11:19:11  cvs
    call 7032

    Revision 1.1  2018/03/16 11:13:50  cvs
    call 6710



*/

/*
   action fundPrice

   fields
      ISIN(m),
      currency(m),
      date(c),
      numberOfPrices(c)
*/

if (
  $arg["ISIN"] == "" OR
  $arg["currency"] == ""
)
{
  $error[] = "empty mandatory fields";
}
$db = new DB();
$query = "SELECT * FROM Fondsen WHERE ISINCode = '".$arg["ISIN"]."' AND Valuta = '".$arg["currency"]."' ";

if (noErrors())
{
  if (!$rec = $db->lookupRecordByQuery($query))
  {
    //$error[] = $query;
    $error[] = "no results for ISIN-valuta combination";
  }
  else
  {
    if ($arg["date"] != "" )  // niet leeg
    {
      $d = explode("-", $arg["date"]);
      if (!checkdate($d[1],$d[2],$d[0]) OR strlen($arg["date"]) != 10)   // valid date en 10 lang YYYY-MM-DD
      {
        $error[] = "invalid date";
      }
      else
      {
        $dateStr = " AND Fondskoersen.Datum <= '".$arg["date"]."' ";
      }

    }
    else
    {
      $dateStr = " AND Fondskoersen.Datum <= NOW()";
    }
    $aantal = ((int) $arg["numberOfPrices"] > 0 )?(int) $arg["numberOfPrices"]:1;
    $query = "
      SELECT
        Fondskoersen.Fonds,
        Fondskoersen.Datum,
        Fondskoersen.Koers,
        Valutakoersen.Koers AS valutaKoers,
        Valutakoersen.Valuta 
      FROM
        Fondskoersen
        INNER JOIN Valutakoersen ON 
          Valutakoersen.Datum = Fondskoersen.Datum AND Valutakoersen.Valuta = '{$arg["currency"]}' 
      WHERE
        Fondskoersen.Fonds = '{$rec["Fonds"]}' 
        {$dateStr}
      ORDER BY
        Fondskoersen.Datum DESC
        limit {$aantal}
";


//SELECT * FROM Fondskoersen WHERE Fonds='".$rec["Fonds"]."' ".$dateStr." ORDER BY Datum DESC LIMIT $aantal";
//    debug($query);
    $db->executeQuery($query);
    while ($krs = $db->nextRecord())
    {
      $krsOut[] = array("date"  => substr($krs["Datum"],0,10),
                        "fund_value_instrument_currency"  => (float) $krs["Koers"],
                        "fund_value_euro"                 => round(($krs["Koers"] * $krs["valutaKoers"]),5),
                        "exchange_rate"                   => (float)$krs["valutaKoers"],
                        "price"                           => (float)$krs["valutaKoers"],

        );
    }
    $output =array(
      "values" => array(
        "ISIN"                  => $arg["ISIN"],
        "instrument_currency"   => $arg["currency"],
        "fullname"              => $rec["Omschrijving"],
        "AIRScode"              => $rec["Fonds"],
        "currency"              => $arg["currency"],
        "priceData"             => $krsOut

      )
    );
  }
}
