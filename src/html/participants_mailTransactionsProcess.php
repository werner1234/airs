<?php


include_once("wwwvars.php");
include_once("../classes/editObject.php");

include_once('rapport/rapportVertaal.php');
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/PDFRapport.php");



$AEParticipant = new AE_Participants();
$AEDatum = new AE_datum();
$AENumbers = new AE_Numbers();
$AEValidate = new AE_Validate();

$data = array_merge($_POST, $_GET);

define('FPDF_FONTPATH', $__appvar['basedir'] . '/html/font/');
$verzenden = false;

if ( isset ($data['sendMails']) && (int) $data['sendMails'] === 1)
{
  echo '
    <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">
    <link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">
  ';

  $verzenden = true;
}

$db = new DB();
$query = "SELECT 
`participantenFondsVerloop`.`transactietype`,
`participantenFondsVerloop`.`aantal`,
`participanten`.`crm_id`,
`participantenFondsVerloop`.`koers`,
`participantenFondsVerloop`.`datum`,
`participantenFondsVerloop`.`waarde`,
`participantenFondsVerloop`.`id` AS `record_id`,
`Fondsen`.`Omschrijving`,
`Fondsen`.`Valuta`
FROM `participantenFondsVerloop`

LEFT JOIN participanten on participanten.id = `participantenFondsVerloop`.`participanten_id`
-- LEFT JOIN CRM_naw on participanten.crm_id = CRM_naw.id
LEFT JOIN Fondsen on `Fondsen`.`Fonds` = `participanten`.`fonds_fonds`

WHERE `participantenFondsVerloop`.`id` IN (" . implode(',', array_values($data['toSend'])) . ")

";
$db->QRecords($query);

while ( $transactionData = $db->nextRecord() ) {
  $crmIds[] = $transactionData['crm_id'];
  $transactionDatas[$transactionData['crm_id']][$transactionData['datum']][] = $transactionData;
}

$queryCrm = "Select 
 `Naam`, `email`, `verzendAanhef`, `CRMGebrNaam`, `id` AS `crm_id`
 
 from CRM_naw where `id` IN (".implode(',', $crmIds).")";
$db->QRecords($queryCrm);
while ( $crmData = $db->nextRecord() ) {
  $crmDatas[$crmData['crm_id']] = $crmData;
}



$pdf = new PDFRapport('P','mm');
$pdf->Rapportagedatum = date('d-m-Y');
$pdf->rapport_type='Participatie';


$pdfLayoutSet = false;
foreach ( $crmDatas as $crmData ) {
  /** stel eenmalig de layout in */
  if ( $pdfLayoutSet === false ) {
    loadLayoutSettings($pdf,'','', $crmData['crm_id']);
    $pdfLayoutSet = true;
  }

  if ( ($verzenden === true && $AEValidate->isValidEmail($crmData['email']) === true) || $verzenden === false )
  {

  }
  elseif ( $verzenden === true ) {
    unset($transactionDatas[$crmData['crm_id']]);
    echo '<div class="alert alert-warning" role="alert">Geen email gevonden bij relatie: ' . $crmData['zoekveld'] . '</div>';
  }
}

$rapportDir = $__appvar['rapportdir'] . DIRECTORY_SEPARATOR . 'participants' . DIRECTORY_SEPARATOR . 'participantMail' . DIRECTORY_SEPARATOR;

$layoutFile = 'mail_L'.$pdf->portefeuilledata['Layout'] .'.php';
if ( file_exists( $rapportDir . $layoutFile) ) {
  $includeFile = $layoutFile;
} else {
  $includeFile = 'mail_Ldefault.php';
}

if (file_exists($rapportDir . $includeFile) ) {
  include_once ($rapportDir . $includeFile);
} else {
  exit('Geen rapport gevonden');
}