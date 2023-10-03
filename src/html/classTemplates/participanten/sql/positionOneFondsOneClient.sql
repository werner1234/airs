SELECT ROUND(SUM(aantal), 8) as `aantal`, `registration_number`, `CRM_naw`.`zoekveld` from `{participantenTable}`
  LEFT JOIN `{participantenFondsVerloopTable}` ON `{participantenTable}`.`id` = `{participantenFondsVerloopTable}`.`participanten_id`
  LEFT JOIN `CRM_naw` ON `{participantenTable}`.`crm_id` = `CRM_naw`.`id`
  WHERE `fonds_fonds` = "{fonds}"
  AND `{participantenTable}`.`crm_id` = "{client}"
  AND `datum` <= "{date}"
  AND  aantal IS NOT NULL
  AND TRIM(aantal) <> ""
  AND  `transactietype` != 'U'
  GROUP BY `registration_number`
  {order}
--   ORDER BY `datum`