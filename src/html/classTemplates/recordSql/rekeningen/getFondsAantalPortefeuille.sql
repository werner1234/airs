SELECT
  ifnull(SUM(Rekeningmutaties.Aantal),0) AS aantal
FROM
  Rekeningmutaties
  JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE Rekeningen.Portefeuille = '{portefeuille}'
AND Rekeningmutaties.Verwerkt = '1'
AND Rekeningmutaties.Fonds = '{fonds}'
AND Boekdatum >= "{year}-01-01"
AND Boekdatum < "{date}"