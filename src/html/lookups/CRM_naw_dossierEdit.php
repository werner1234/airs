<?php
/**
 * update crm_naw_dossierEdit type
 * 
 * @author RM
 * @since 16-9-2014
 * 
 * Loads local data including vars and databases
 * 
 * Loads the ajax class
 *
 *
Author              : $Author: cvs $
Laatste aanpassing  : $Date: 2018/07/24 06:39:25 $
File Versie         : $Revision: 1.5 $

$Log: CRM_naw_dossierEdit.php,v $
Revision 1.5  2018/07/24 06:39:25  cvs
call 7041

Revision 1.4  2016/11/25 09:26:39  rvv
*** empty log message ***

Revision 1.3  2016/11/20 10:21:36  rvv
*** empty log message ***

 
 */


include_once('../../config/local_vars.php');
include_once('../../config/vars.php');
include_once('../../config/applicatie_functies.php');
include_once('../../classes/AE_cls_mysql.php');
include_once('../../classes/mysqlTable.php');
include_once('../../classes/CRMeMailing.php');
session_start();
if (!isset($_SESSION["USR"]))
{
  header("HTTP/1.0 404 Not Found");
  exit;
}
$nawDossierObject = new Naw_dossier();
$data = array_merge($_GET,$_POST);
$db = new DB();
if(isset($data['templateId']))
{
  $query="SELECT CRM_naw.*, Portefeuilles.* FROM CRM_naw LEFT JOIN Portefeuilles ON CRM_naw.portefeuille=Portefeuilles.Portefeuille WHERE CRM_naw.id='".$data['rel_id']."'";
  $db->SQL($query);
  $crmData=$db->LookupRecord();
  $emailing=new CRMeMailing();
  $crmData=$emailing->getAllFields($crmData);
  $query="SELECT template FROM CRM_naw_dossier_templates WHERE id='".$data['templateId']."'";
  $db->SQL($query);
  $template=$db->LookupRecord();
  $template=$template['template'];
  foreach ($crmData as $key=>$val)
    $template = str_replace("{".$key."}", $val, $template);

  echo $template;
  exit;
}
else
{
  $query = 'SELECT ' . $data['veld'] . ' as oldValue FROM `' . $nawDossierObject->data['table'] . '` WHERE ' . $nawDossierObject->data['identity'] . ' = "' . $data['dossierId'] . '"';
  $db->SQL($query);
  $oldValue = $db->lookupRecord();
  $oldValue = $oldValue['oldValue'];
  
  $dossierUpdateTypeQuery = 'UPDATE `' . $nawDossierObject->data['table'] . '`
  SET `' . $data['veld'] . '` = "' . mysql_real_escape_string($data['newValue']) . '", 
  change_date=now(),change_user="' . mysql_real_escape_string($_SESSION['usersession']['user']) . '"
  WHERE ' . $nawDossierObject->data['identity'] . ' = "' . $data['dossierId'] . '"';
  
  
  $db->executeQuery($dossierUpdateTypeQuery);
  
  addTrackAndTrace($nawDossierObject->data['table'], $data['dossierId'], $data['veld'], $oldValue, $data['newValue'], $_SESSION['usersession']['user']);
}