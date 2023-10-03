
-- ophalen VV koers via een plugin query {valutaKoersQuery}
--

SELECT
  Rekeningmutaties.Boekdatum,
  Rekeningmutaties.omschrijving as rekeningOmschrijving,
  Rekeningmutaties.Grootboekrekening as Grootboekrekening,
  Rekeningmutaties.Bedrag as Bedrag
FROM
  Rekeningmutaties
WHERE
  Rekeningmutaties.Rekening = '{rekening}' AND
  Rekeningmutaties.Verwerkt = '1' AND
  Rekeningmutaties.Boekdatum <= '{boekDatum}' AND
  Rekeningmutaties.Boekdatum >= '{ytd}'
ORDER BY
  Rekeningmutaties.Boekdatum ASC, Rekeningmutaties.id
LIMIT 500
