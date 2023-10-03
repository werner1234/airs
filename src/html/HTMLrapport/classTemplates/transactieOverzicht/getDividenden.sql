-- ophalen van de transacties bij een fonds en portefeuille
-- ophalen VV koers via een plugin query {valutaKoersQuery}
-- rekenval toegevoegd
SELECT
  Rekeningmutaties.Boekdatum,
  Rekeningmutaties.omschrijving as rekeningOmschrijving,
  Rekeningmutaties.Grootboekrekening as Grootboekrekening,
  Rekeningmutaties.Bedrag as Bedrag,
  Rekeningmutaties.Valutakoers as Valutakoers,
  Rekeningmutaties.Valuta as Valuta,
  Rekeningen.Valuta as rekValuta,
  Rekeningmutaties.Rekening,
  IF (Debet <> 0 ,-1 * Debet,Credit) as BedragInValuta

FROM
  Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen
WHERE
  Rekeningmutaties.Rekening = Rekeningen.Rekening AND
  Rekeningmutaties.Fonds = Fondsen.Fonds AND
  Rekeningen.Portefeuille = '{portefeuille}' AND
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
  Rekeningmutaties.Verwerkt = '1' AND
  Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND
  Rekeningmutaties.Transactietype <> 'B' AND
  Rekeningmutaties.Grootboekrekening IN ('DIV','DIVBE','RENOB','RENME') AND
  Rekeningmutaties.Fonds = '{fonds}'
ORDER BY
  Rekeningmutaties.Boekdatum DESC, Rekeningmutaties.Fonds, Rekeningmutaties.id
