SELECT ROUND(SUM(aantal), 8) as `aantal`, `{participantenTable}`.*, `registration_number` from `{participantenTable}`
  LEFT JOIN `{participantenFondsVerloopTable}` ON `{participantenTable}`.`id` = `{participantenFondsVerloopTable}`.`participanten_id`
  WHERE `{participantenTable}`.`crm_id` = "{client}"
  AND `datum` <= "{date}"
  AND  aantal IS NOT NULL
  AND TRIM(aantal) <> ""
  AND  `transactietype` != 'U'
  GROUP BY `registration_number`
  {order}
--   ORDER BY `datum`