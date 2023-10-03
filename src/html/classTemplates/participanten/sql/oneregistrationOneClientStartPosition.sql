SELECT ROUND(SUM(aantal), 8) as `aantal` from `{participantenTable}`
  LEFT JOIN `{participantenFondsVerloopTable}` ON `{participantenTable}`.`id` = `{participantenFondsVerloopTable}`.`participanten_id`

  WHERE `participanten_id` = "{registration}"
  AND `{participantenTable}`.`crm_id` = "{crm_id}"

  AND `datum` < "{startDate}"

  AND  `transactietype` != 'U'
