<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/22 14:20:49 $
    File Versie         : $Revision: 1.2 $

    $Log: api_info_transacties.php,v $
    Revision 1.2  2018/10/22 14:20:49  cvs
    call 7228

    Revision 1.1  2018/09/26 09:30:07  cvs
    update naar DEMO



*/

$portefeuille = $__ses["data"]["portefeuille"];
if ($__ses["data"]["rapportDatum"])
{
  $datum = $__ses["data"]["rapportDatum"];
}
else
{
  $datum = date("Y-m-d");
}


$fonds = rawurldecode($__ses["data"]["fonds"]);

/////////////////////////////////////


$db = new DB();

$query = "
SELECT
  *
FROM
  Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen
WHERE
  Rekeningmutaties.Rekening = Rekeningen.Rekening AND
  Rekeningmutaties.Fonds = Fondsen.Fonds AND
  Rekeningen.Portefeuille = '$portefeuille' AND
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
  Rekeningmutaties.Verwerkt = '1' AND
  Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND

  Grootboekrekeningen.FondsAanVerkoop = '1' AND
  Rekeningmutaties.Boekdatum <= '$datum' AND
  Rekeningmutaties.Boekdatum >= '1971-01-01' AND
  Rekeningmutaties.Fonds = '$fonds'
ORDER BY
  Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id

";

$output = array();
$db->executeQuery($query);

while($rec = $db->nextRecord())
{
  $output[] = $rec;
}
