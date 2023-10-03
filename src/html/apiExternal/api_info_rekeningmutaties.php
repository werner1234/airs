<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/26 09:30:07 $
    File Versie         : $Revision: 1.1 $

    $Log: api_info_rekeningmutaties.php,v $
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


$rekening = $__ses["data"]["rekening"];

/////////////////////////////////////


$db = new DB();

$query = "
SELECT
  Rekeningmutaties.Boekdatum,
  Rekeningmutaties.omschrijving as rekeningOmschrijving,
  Rekeningmutaties.Grootboekrekening as Grootboekrekening,
  Rekeningmutaties.Bedrag as Bedrag,
  Rekeningmutaties.Transactietype
FROM
  Rekeningmutaties
WHERE
  Rekeningmutaties.Rekening = '{$rekening}' AND
  Rekeningmutaties.Verwerkt = '1' AND
  Rekeningmutaties.Boekdatum >= '".(date("Y")-2)."-01-01' AND
  Rekeningmutaties.Boekdatum <= '{$datum}'
ORDER BY
  Rekeningmutaties.Boekdatum ASC, Rekeningmutaties.id
";

$output = array();
$db->executeQuery($query);
$notFirstRec = false;
while($rec = $db->nextRecord())
{
  if ($rec["Transactietype"] == "B" and $notFirstRec) continue;
  $notFirstRec = true;
  $output[] = $rec;
}

