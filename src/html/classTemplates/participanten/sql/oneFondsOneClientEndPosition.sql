SELECT ROUND(SUM(aantal), 8) as `aantal` from `{participantenTable}`
  LEFT JOIN `{participantenFondsVerloopTable}` ON `{participantenTable}`.`id` = `{participantenFondsVerloopTable}`.`participanten_id`

  WHERE `fonds_fonds` = "{fonds}"
  AND `{participantenTable}`.`crm_id` = "{crm_id}"

  AND (`datum` BETWEEN "{startDate}" AND "{endDate}")

  AND  aantal IS NOT NULL
  AND TRIM(aantal) <> ""
        AND  `transactietype` != 'U'
  ORDER BY `datum`
        
      
