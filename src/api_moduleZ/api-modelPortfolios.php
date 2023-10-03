<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/14 09:25:13 $
    File Versie         : $Revision: 1.2 $

    $Log: api-modelPortfolios.php,v $
    Revision 1.2  2018/09/14 09:25:13  cvs
    no message

    Revision 1.1  2018/03/16 11:13:50  cvs
    call 6710



*/

/*
   action modelportfolios

   fields
      portfolio(c),
      date(c),

*/

$db = new DB();
$query = "SELECT * FROM Fondsen WHERE ISINCode = '".$arg["ISIN"]."' AND Valuta = '".$arg["currency"]."' ";
$where = "Portefeuilles.Vermogensbeheerder = '{$__glob["VB"]}' ";

if (trim($arg["portfolio"]) != "")
{
  $where .= " AND ModelPortefeuilleFixed.Portefeuille = '".trim($arg["portfolio"])."'";
}

if (trim(substr($arg["date"],0,10)) != "")
{
  $d = explode("-", $arg["date"]);
  if (!checkdate($d[1],$d[2],$d[0]) OR strlen($arg["date"]) != 10)   // valid date en 10 lang YYYY-MM-DD
  {
    $error[] = "invalid date";
  }
  else
  {
    $where = " AND ModelPortefeuilleFixed.Datum <= '".$arg["date"]."' ";
  }
}

$query = "
  SELECT
    ModelPortefeuilleFixed.Portefeuille,
    ModelPortefeuilleFixed.Fonds,
    Fondsen.Omschrijving,
    Fondsen.Valuta,
    Fondsen.ISINCode,
    ModelPortefeuilleFixed.Datum,
    ModelPortefeuilleFixed.Percentage,
    Portefeuilles.Vermogensbeheerder,
    Portefeuilles.SoortOvereenkomst as `product`,
    Portefeuilles.Risicoklasse as `profile`
  FROM
    ModelPortefeuilleFixed 
  INNER JOIN Fondsen ON 
    ModelPortefeuilleFixed.Fonds = Fondsen.Fonds
  INNER JOIN Portefeuilles ON 
    ModelPortefeuilleFixed.Portefeuille = Portefeuilles.Portefeuille
  WHERE 
    $where
";

if (noErrors())
{
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $tempArray[$rec["product"]][$rec["profile"]][] =array(

      "portfolio"  => $rec["Portefeuille"],
      "isin"       => $rec["ISINCode"],
      "currency"   => $rec["Valuta"],
      "date"       => $rec["Datum"],
      "instrument" => $rec["Fonds"],
      "percentage" => $rec["Percentage"],

    );
  }

  $idx = -1;

  foreach ($tempArray as $product=>$profiles)
  {
    $idx++;
    $output[$idx]["product"] = $product;

    foreach($profiles as $profile => $funds)
    {
      $output[$idx]["profiles"][] = array(
        "profile" => $profile,
        "fund"    => $funds);

    }

  }
}
