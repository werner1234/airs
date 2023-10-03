SELECT 
  SUM(ROUND(`Bedrag`,2)) AS `Totaal` 
  FROM `Rekeningmutaties` 
  WHERE `Afschriftnummer` = "{copy}"
  AND `Rekening` = "{account}"