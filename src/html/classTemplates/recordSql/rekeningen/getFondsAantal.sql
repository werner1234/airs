SELECT ifnull(SUM(Aantal),0) as aantal
    FROM Rekeningmutaties
    WHERE
    Rekening IN
    (
       SELECT Rekening FROM Rekeningen WHERE Portefeuille IN
       (
         SELECT  Portefeuille FROM Rekeningen WHERE Rekening = "{account}" AND  consolidatie=0
       )
    )
AND Fonds = "{fonds}"
AND Boekdatum >= "{year}-01-01"
AND Boekdatum < "{date}"