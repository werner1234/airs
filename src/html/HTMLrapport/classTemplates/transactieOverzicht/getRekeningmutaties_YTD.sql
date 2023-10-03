-- ophalen van de mutatie bij een rekening vanaf 1-1
-- ophalen VV koers via een plugin query {valutaKoersQuery}
--

SELECT
  Rekeningmutaties.Boekdatum,
  Rekeningmutaties.omschrijving as rekeningOmschrijving,
  Rekeningmutaties.Grootboekrekening as Grootboekrekening,
  Rekeningmutaties.Bedrag as Bedrag,
  Rekeningmutaties.Transactietype
FROM
  Rekeningmutaties
WHERE
  Rekeningmutaties.Rekening = '{rekening}' AND
  Rekeningmutaties.Verwerkt = '1' AND
  Rekeningmutaties.Boekdatum >= '{ytd}' AND
  Rekeningmutaties.Boekdatum <= '{dateStop}'
ORDER BY
  Rekeningmutaties.Boekdatum ASC, Rekeningmutaties.id
