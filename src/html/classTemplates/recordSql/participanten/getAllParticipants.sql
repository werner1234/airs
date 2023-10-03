SELECT 
`{table}`.`crm_id`,
`CRM_naw`.`id`,
`CRM_naw`.`naam`,
`CRM_naw`.`zoekveld`,
`CRM_naw`.`CRMGebrNaam`

FROM `{table}`
LEFT JOIN `CRM_naw` ON `CRM_naw`.`id` = `{table}`.`crm_id`
GROUP BY `crm_id`
