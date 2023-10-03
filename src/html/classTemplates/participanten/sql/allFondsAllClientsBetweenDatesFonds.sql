SELECT {participantenTable}.*, {participantenFondsVerloopTable}.*, `CRM_naw`.`zoekveld`
  FROM `{participantenTable}`
  LEFT JOIN `{participantenFondsVerloopTable}` ON `{participantenTable}`.`id` = `{participantenFondsVerloopTable}`.`participanten_id`

  LEFT JOIN `CRM_naw` ON `{participantenTable}`.`crm_id` = `CRM_naw`.`id`

  WHERE `fonds_fonds` = "{fonds}"

  AND (`datum` BETWEEN "{startDate}" AND "{endDate}")

  AND  aantal IS NOT NULL
  AND TRIM(aantal) <> ""
      AND  `transactietype` != 'U'
  {order}
--   ORDER BY `datum` ASC