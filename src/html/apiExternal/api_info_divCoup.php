<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/22 14:20:49 $
    File Versie         : $Revision: 1.2 $

    $Log: api_info_divCoup.php,v $
    Revision 1.2  2018/10/22 14:20:49  cvs
    call 7228

    Revision 1.1  2018/09/26 09:30:07  cvs
    update naar DEMO



*/
global $__dbDebug;
$portefeuille = $__ses["data"]["portefeuille"];
if ($__ses["data"]["rapportDatum"])
{
  $datum = sanatizeInput($__ses["data"]["rapportDatum"]);
}
else
{
  $datum = date("Y-m-d");
}


$fonds = rawurldecode($__ses["data"]["fonds"]);

/////////////////////////////////////


$db = new DB();
$db->debug = $__dbDebug;

$query = "SELECT * FROM Grootboekrekeningen ORDER BY Grootboekrekening";
$db->executeQuery($query);
while ($gbRec = $db->nextRecord())
{
  $gb[$gbRec["Grootboekrekening"]] = $gbRec["Omschrijving"];
}

$query = "SELECT * FROM Portefeuilles WHERE Portefeuille = '".$mainRec["portefeuille"]."'";
$portRec = $db->lookupRecordByQuery($query);
$rapportageValuta = (trim($portRec["RapportageValuta"]) == "")?"EUR":$portRec["RapportageValuta"];
$output = array();
$output[] = array("statics"=>array(
  "grootboek" => $gb,
  "rapValuta" => $rapportageValuta
));

$query = "
SELECT
  Rekeningmutaties.Boekdatum,
  Rekeningmutaties.omschrijving as rekeningOmschrijving,
  Rekeningmutaties.Grootboekrekening as Grootboekrekening,
  Rekeningmutaties.Bedrag as Bedrag,
  Rekeningmutaties.Valutakoers as Valutakoers,
  Rekeningmutaties.Valuta as Valuta,
  IF (Debet <> 0 ,-1 * Debet,Credit) as BedragInValuta

FROM
  Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen
WHERE
  Rekeningmutaties.Rekening = Rekeningen.Rekening AND
  Rekeningmutaties.Fonds = Fondsen.Fonds AND
  Rekeningen.Portefeuille = '$portefeuille' AND
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
  Rekeningmutaties.Verwerkt = '1' AND
  Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND
  Rekeningmutaties.Transactietype <> 'B' AND
  Rekeningmutaties.Grootboekrekening IN ('DIV','DIVBE','RENOB','RENME') AND
  Rekeningmutaties.Fonds = '$fonds'
ORDER BY
  Rekeningmutaties.Boekdatum DESC, Rekeningmutaties.Fonds, Rekeningmutaties.id

";


$db->executeQuery($query);

while($rec = $db->nextRecord())
{
  $output[] = $rec;
}
