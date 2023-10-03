-- ophalen van de transacties bij een fonds en portefeuille
-- ophalen VV koers via een plugin query {valutaKoersQuery}
--
SELECT
  *,
  Rekeningmutaties.Valuta as rekMutVal
FROM

  Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen
WHERE
  Rekeningmutaties.Rekening = Rekeningen.Rekening AND
  Rekeningmutaties.Fonds = Fondsen.Fonds AND
  Rekeningen.Portefeuille = '{portefeuille}' AND
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
  Rekeningmutaties.Verwerkt = '1' AND
  Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND

  Grootboekrekeningen.FondsAanVerkoop = '1' AND
  Rekeningmutaties.Boekdatum <= '{boekDatum}' AND
  Rekeningmutaties.Boekdatum >= '{ytd}' AND
  Rekeningmutaties.Fonds = '{fonds}'
ORDER BY
  Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id
