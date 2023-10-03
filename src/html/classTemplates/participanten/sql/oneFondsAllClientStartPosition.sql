SELECT ROUND(SUM(aantal), 8) as `aantal` from `{participantenTable}`
  LEFT JOIN `{participantenFondsVerloopTable}` ON `{participantenTable}`.`id` = `{participantenFondsVerloopTable}`.`participanten_id`

  WHERE `fonds_fonds` = "{fonds}"

  AND `datum` < "{startDate}"
AND  `transactietype` != 'U'