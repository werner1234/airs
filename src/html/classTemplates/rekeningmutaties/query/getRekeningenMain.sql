SELECT {fields}
  FROM Rekeningen,Portefeuilles 
  WHERE Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
  AND Portefeuilles.Client = '{clientId}'
  AND Memoriaal IN ({memoriaal})
  AND Rekeningen.Inactief = 0
  ORDER BY `Rekening` DESC