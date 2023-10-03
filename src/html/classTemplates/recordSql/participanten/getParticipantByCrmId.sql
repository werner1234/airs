SELECT `{table}`.*,
`CRM_naw`.`id`,
`CRM_naw`.`naam`,
`CRM_naw`.`zoekveld`,
`CRM_naw`.`CRMGebrNaam`
FROM `{table}`

  LEFT JOIN `CRM_naw` ON `CRM_naw`.`id` = `{table}`.`crm_id`
  WHERE `crm_id` IN ( {crm_id} )

