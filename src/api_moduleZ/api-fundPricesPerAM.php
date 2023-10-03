<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/02/06 08:15:50 $
    File Versie         : $Revision: 1.4 $

    $Log: api-fundPricesPerAM.php,v $
    Revision 1.4  2019/02/06 08:15:50  cvs
    no message

    Revision 1.3  2018/07/06 08:29:02  cvs
    call 7032

    Revision 1.1  2018/03/16 11:13:50  cvs
    call 6710



*/

/*
   action fundPricesPerAM

   fields
      AssMan(m),
      days(c)
*/

if (
  $arg["assMan"] == ""
)
{
  $error[] = "empty mandatory fields";
}

$db = new DB();
$db2 = new DB();

$multiResult = ((int)$arg["days"] > 1);

$query = "
SELECT
  Fondsen.ISINCode,
  Fondsen.Valuta,
  Fondsen.Fonds,
  Fondsen.Omschrijving,
  Fondsen.EindDatum
FROM
  Fondsen
ORDER BY
  Fondsen.Fonds
 
";
//debug($query);
$db->executeQuery($query);
while($rec = $db->nextRecord())
{

  $history = array();
  if ((int)$arg["days"] > 1)
  {
    $kQuery = "
    SELECT 
      Fondskoersen.*,
      Valutakoersen.Koers AS valutaKoers,
      Valutakoersen.Valuta  
    FROM 
      Fondskoersen 
    INNER JOIN Valutakoersen ON 
      Valutakoersen.Datum = Fondskoersen.Datum  AND Valuta = '".$rec["Valuta"]."'  
    WHERE 
      Fondskoersen.Fonds='{fonds}' AND 
      Fondskoersen.change_date >= DATE_SUB(NOW(), INTERVAL ".(int)$arg["days"]." DAY) 
    ORDER BY 
      Fondskoersen.Datum DESC
   ";
  }
  else
  {
    $kQuery = "
    SELECT 
      Fondskoersen.*,
      Valutakoersen.Koers AS valutaKoers,
      Valutakoersen.Valuta 
    FROM 
      Fondskoersen 
    INNER JOIN Valutakoersen ON 
      Valutakoersen.Datum = Fondskoersen.Datum  AND Valuta = '".$rec["Valuta"]."'    
    WHERE 
      Fondskoersen.Fonds='{fonds}' 
    ORDER BY 
      Fondskoersen.Datum DESC
      
      ";
  }


  $query2 = str_replace("{fonds}", $rec["Fonds"], $kQuery);
//debug($query2);
  if ($multiResult)
  {
    $db2->executeQuery($query2);
    while($kRec = $db2->nextRecord())
    {
      if (count($history) == 0)
      {
        $koersRec = $kRec;
      }
      $history[] = array(
        "value"              => $kRec["Koers"],
        "value_date"         => dbToJson($kRec["Datum"]),
        "add_date"           => dbToJson($kRec["add_date"]),
        "change_date"        => dbToJson($kRec["change_date"]),
      );
    }

  }
  else
  {
    $koersRec = $db2->lookupRecordByQuery($query2);
  }

  $data = array(
    "isin"                            => $rec["ISINCode"],
    "short_description"               => $rec["Fonds"],
    "long_description"                => $rec["Omschrijving"],
    "value"                           => $koersRec["Koers"],
    "currency"                        => $rec["Valuta"],
    "value_date"                      => dbToJson($koersRec["Datum"]),
    "add_date"                        => dbToJson($koersRec["add_date"]),
    "change_date"                     => dbToJson($koersRec["change_date"]),
    "end_date"                        => dbToJson($rec["EindDatum"]),
    "exchange_rate"                   => (float)$koersRec["valutaKoers"],
    "fund_value_euro"                 => round(($koersRec["Koers"] * $koersRec["valutaKoers"]),5),
    "fund_value_instrument_currency"  => (float) $koersRec["Koers"],
    "instrument_currency"             => $rec["Valuta"],
  );
  if (count($history) > 1)
  {
    $data["history"] = $history;
  }

  $output[] = $data;
}



