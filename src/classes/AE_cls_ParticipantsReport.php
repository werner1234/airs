<?php

class AE_ParticipantsReport {
  var $__appvar = array();
  
  function AE_ParticipantsReport()
  {
    global $__appvar;
    $this->__appvar = $__appvar;
    
    $this->AEArray = new AE_Array();
    $this->AENumbers = new AE_Numbers();
    $this->AEParticipant = new AE_Participants();
    $this->AEDate = new AE_datum();
    
    $this->Fonds = new Fonds();
    $this->crmNaw = new Naw ();
    $this->Participanten = new Participanten();
    $this->AEPortal = new AE_Portal();
    
    define('FPDF_FONTPATH', $__appvar['basedir'] . '/html/font/');
    
  }
  
  function makePdfPositionOneFondsAllClient($rows, $filename, $data)
  {
    global $__appvar;
    include_once('../html/rapport/rapportVertaal.php');
    include_once("../classes/AE_cls_fpdf.php");
    include_once("rapport/PDFRapport.php");
    $layoutParticipants = $this->AEParticipant->getParticipant();  
    $this->pdf = new PDFRapport('P','mm');
    $this->pdf->Rapportagedatum = date('d-m-Y');
    $this->pdf->rapport_type='Participatie';
    loadLayoutSettings($this->pdf,'','',$layoutParticipants[0]['crm_id']);

    $rapportDir = $this->__appvar['rapportdir'] . DIRECTORY_SEPARATOR . 'participants' . DIRECTORY_SEPARATOR . 'participantRapportages' . DIRECTORY_SEPARATOR . 'positieFonds' . DIRECTORY_SEPARATOR;

    if ( file_exists( $rapportDir . 'rapport_'.$this->pdf->portefeuilledata['Layout'] .'.php') ) {
      $includeFile = 'rapport_'.$this->pdf->portefeuilledata['Layout'] .'.php';
    } else {
     $includeFile = 'default.php';
    }

    $huidigeVermogensbeheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    
    if (file_exists($rapportDir . $includeFile) ) {
      include_once ($rapportDir . $includeFile);
    } else {
      exit('Geen rapport gevonden');
    }
    exit();
  }
  
  
  function makePdfCourse($rows, $filename,$data)
  {
    global $__appvar;
    include_once('../html/rapport/rapportVertaal.php');
    include_once("../classes/AE_cls_fpdf.php");
    include_once("rapport/PDFRapport.php");
    $layoutParticipants = $this->AEParticipant->getParticipant();  
    $this->pdf = new PDFRapport('P','mm');
    $this->pdf->Rapportagedatum = date('d-m-Y');
    $this->pdf->rapport_type='Participatie';
    loadLayoutSettings($this->pdf,'','',$layoutParticipants[0]['crm_id']);

    $rapportDir = $this->__appvar['rapportdir'] . DIRECTORY_SEPARATOR . 'participants' . DIRECTORY_SEPARATOR . 'participantRapportages' . DIRECTORY_SEPARATOR . 'verloop' . DIRECTORY_SEPARATOR;

    if ( file_exists( $rapportDir . 'rapport_'.$this->pdf->portefeuilledata['Layout'] .'.php') ) {
      $includeFile = 'rapport_'.$this->pdf->portefeuilledata['Layout'] .'.php';
    } else {
     $includeFile = 'default.php';
    }

    $huidigeVermogensbeheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    
    if (file_exists($rapportDir . $includeFile) ) {
      include_once ($rapportDir . $includeFile);
    } else {
      exit('Geen rapport gevonden');
    }
    exit();
  }

function makePdfPositionOneFondsOneClient ($data) {
  global $__appvar;
    include_once('../html/rapport/rapportVertaal.php');
    include_once("../classes/AE_cls_fpdf.php");
    include_once("rapport/PDFRapport.php");
    $layoutParticipants = $this->AEParticipant->getParticipant();  
    
    $BeleggingscategoriePerFonds = new BeleggingscategoriePerFonds();
    define('EURO', chr(128));
    
    $this->pdf = new PDFRapport('P','mm');
    $this->pdf->Rapportagedatum = date('d-m-Y');
    $this->pdf->rapport_type='Participatie';
    loadLayoutSettings($this->pdf,'','',$layoutParticipants[0]['crm_id']);

     $rapportType = 'positie' . DIRECTORY_SEPARATOR . 'clientFonds';
    $rapportDir = $this->__appvar['rapportdir'] . DIRECTORY_SEPARATOR . 'participants' . DIRECTORY_SEPARATOR . $rapportType . DIRECTORY_SEPARATOR;


    if ( file_exists( $rapportDir . 'rapport_'.$this->pdf->portefeuilledata['Layout'] .'.php') ) {
      $includeFile = 'rapport_'.$this->pdf->portefeuilledata['Layout'] .'.php';
    } else {
     $includeFile = 'default.php';
    }

    $huidigeVermogensbeheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    
    if (file_exists($rapportDir . $includeFile) ) {
      include_once ($rapportDir . $includeFile);
    } else {
      exit('Geen rapport gevonden');
    }
    exit();

}


/**
 * makePdfBulk
 * @param type $rows
 * @param type $filename
 * @param int $data
 * @param type $outputType
 * @return type
 */
function makePdfBulk ($data, $portal = false) {
  global $__appvar;
  include_once('../html/rapport/rapportVertaal.php');
  include_once("../classes/AE_cls_fpdf.php");
  include_once("rapport/PDFRapport.php");
  $layoutParticipants = $this->AEParticipant->getParticipant();  
    
  $BeleggingscategoriePerFonds = new BeleggingscategoriePerFonds();
  define('EURO', chr(128));
  $filterCrmIds = array();
  /** get all participants **/
  $participantsResult = null;
    
    
  /** wanneer een filter is aangezet maak een lijst met crm_ids **/
    $db = new DB();
    /** filter op client van tot en met **/
    if ( ( isset ($data['client_from']) && ! empty ($data['client_from']) ) && ( isset ($data['client_to']) && ! empty ($data['client_to']) ) ) {
      /** ophalen zoekveld bij id **/
      $participant_from = $this->crmNaw->parseById($data['client_from_id'], 'zoekveld');
      $participant_to = $this->crmNaw->parseById($data['client_to_id'], 'zoekveld');
      
      $participantsQuery = 'SELECT `zoekveld`,`registration_number`,`crm_id` FROM participanten, CRM_naw  
        WHERE `zoekveld` >= "' . $participant_from . '"
        AND `zoekveld` <= "' . $participant_to . '"
        AND `participanten`.`crm_id` = `CRM_naw`.`id`
        GROUP BY crm_id ORDER BY zoekveld ASC
      ';
      $db->executeQuery($participantsQuery);
      while ( $participantsResult = $db->nextRecord() ) {
        $filterCrmIds[] = $participantsResult['crm_id'];
      }
    } elseif ( ( isset ($data['client_from']) && ! empty ($data['client_from']) ) && ( ! isset ($data['client_to']) || empty ($data['client_to']) ) ) {
      $participant_from = $this->crmNaw->parseById($data['client_from_id'], 'zoekveld');
      $participantsQuery = 'SELECT `zoekveld`,`registration_number`,`crm_id` FROM participanten, CRM_naw  
        WHERE `zoekveld` = "' . $participant_from . '"
        AND `participanten`.`crm_id` = `CRM_naw`.`id`
        GROUP BY crm_id
      ';
      
      $db->executeQuery($participantsQuery);
      $participantsResult = $db->nextRecord();
      if ( ! empty($participantsResult) ) {
        $filterCrmIds[] = $participantsResult['crm_id'];
      }
    }

    if ( isset($data['Fonds']) && ! empty ($data['Fonds']) ) {
      $participantsQuery = 'SELECT `zoekveld`,`registration_number`,`crm_id` FROM participanten, CRM_naw  
        WHERE `participanten`.`crm_id` = `CRM_naw`.`id`
        AND `fonds_fonds` = "'.$data['Fonds'].'"
        GROUP BY crm_id ORDER BY zoekveld ASC
      ';
      
      $db->executeQuery($participantsQuery);
      while ( $participantsResult = $db->nextRecord() ) {
        $calcTotal = 0;
        if ( $data['rapportType'] === 'verloop' ) {
          $calcTotal = $this->AEParticipant->oneFondsOneClient($participantsResult['crm_id'], $data['Fonds'], null, $data['DateStart'], $data['DateEnd']);

          if ( ! empty($calcTotal) && $calcTotal['foot']['aantal'] > 0 && $calcTotal['value']['aantal'] > 0) {
//          if ( ! empty($calcTotal) ) {
            $filterCrmIds[] = $participantsResult['crm_id'];
          }
        }
        elseif ($data['rapportType'] === 'positie') {
          $calcTotal = $this->AEParticipant->positionOneFondsOneClient ($participantsResult['crm_id'], $data['Fonds'], $data['DateEnd']);
          if ( ! empty($calcTotal) ) {
            $filterCrmIds[] = $participantsResult['crm_id'];
          }
        }
      }
      
      if ( empty ($filterCrmIds) ) {
        echo 'Er zijn geen gegevens gevonden dia aan de opgegeven criteria voldoen.';
        exit();
      }
    }

  /** einde filters **/

  $participants = $this->AEParticipant->getParticipant($filterCrmIds);
  
  $this->pdf = new PDFRapport('P','mm');
  $this->pdf->Rapportagedatum = date('d-m-Y');
  $this->pdf->rapport_type='Participatie';

  $crmId=0;
  foreach($layoutParticipants as $key=>$crmData)
  {
    if($crmData['crm_id'] <> 0)
    {
      $crmId = $crmData['crm_id'];
      break;
    }
  }
  loadLayoutSettings($this->pdf,'','',$crmId);
  
  $rapportType = '';
  switch ($data['rapportType'])
  {
    case 'positie':
      $rapportType = 'positie';
      break;
    case 'verloop':
      $rapportType = 'verloop';
      break;
    case 'positie-client':
      $rapportType = 'positie' . DIRECTORY_SEPARATOR . 'client';
      break;
    case 'positie-client-fonds':
      $rapportType = 'positie' . DIRECTORY_SEPARATOR . 'clientFonds';
      break;
    default:
        break;
  }
  $rapportDir = $this->__appvar['rapportdir'] . DIRECTORY_SEPARATOR . 'participants' . DIRECTORY_SEPARATOR . $rapportType . DIRECTORY_SEPARATOR;

  if ( file_exists( $rapportDir . 'rapport_'.$this->pdf->portefeuilledata['Layout'] .'.php') ) {
    $includeFile = 'rapport_'.$this->pdf->portefeuilledata['Layout'] .'.php';
  } else {
   $includeFile = 'default.php';
  }

  $huidigeVermogensbeheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
  if (file_exists($rapportDir . $includeFile) ) {
    include_once ($rapportDir . $includeFile);
  } else {
    exit('Geen rapport gevonden');
  }

  if ( $portal === true ) {
    echo $this->AEPortal->messages;
  }
  
}

function formatDataForView($data) {
  $data['zoekveld'] = strlen($data['zoekveld']) > 15 ? substr($data['zoekveld'],0,15)."..." : $data['zoekveld'];
  $data['waarde'] = $this->AENumbers->viewFormat2Decimals($data['waarde']);
  $data['aantal'] = $this->AENumbers->viewFormatMinMaxDecimals($data['aantal']);
  $data['koers'] = $this->AENumbers->viewFormatMinMaxDecimals($data['koers']);

  if ( isset ($data['total_waarde']) ) {$data['total_waarde'] = $this->AENumbers->viewFormat2Decimals($data['total_waarde']);}
  if ( isset ($data['total_aantal']) ) {$data['total_aantal'] = $this->AENumbers->viewFormatMinMaxDecimals($data['total_aantal']);}
  if ( isset ($data['total_koers']) ) {$data['total_koers'] = $this->AENumbers->viewFormatMinMaxDecimals($data['total_koers']);}
  
  return $data;
}

  
  
  /**
 * Format induvidual fields
 * @param type $values
 * @param type $this->AENumbers
 * @return type
 */
function formatVerloopFields($values)
{
//  debug($values);
  /** if only 2 in an array it is an head or footer * */
  if (count($values) == 2)
  {
    if ( strpos($values['datum'],'Positie op') !== false ) {
      $values['aantal'] = $this->AENumbers->viewFormatMinMaxDecimals($values['aantal']);
    }
    
    switch ( strip_tags ($values['transactietype']) )
    {
      case 'Totaal':
        $values['aantal'] = $this->AENumbers->viewFormatMinMaxDecimals($values['aantal']);
        break;
      case 'Koers':
        $values['aantal'] = $this->AENumbers->viewFormatMinMaxDecimals($values['aantal']);
        break;
      case 'Waarde':
        $values['aantal'] = $this->AENumbers->viewFormat2Decimals($values['aantal']);
        break;
    }
  }
  else
  {
    switch ( strtolower ($values['transactietype']) )
    {
      case 'v':
        $values['transactietype'] = 'Verkoop';
        break;
      case 'a':
        $values['transactietype'] = 'Aankoop';
        break;
      case 'b':
        $values['transactietype'] = 'Beginboeking';
        break;
      case 'd':
        $values['transactietype'] = 'Deponering';
        break;
      case 'l':
        $values['transactietype'] = 'Lichting';
        break;
      case 'bk':
        $values['transactietype'] = 'Bijkopen';
        break;
      case 'dv':
        $values['transactietype'] = 'Deelverkoop';
        break;
      case 'h':
        $values['transactietype'] = 'Herbelegging';
        break;
    }
    $values['zoekveld'] = strlen($values['zoekveld']) > 15 ? substr($values['zoekveld'],0,15)."..." : $values['zoekveld'];
    $values['waarde'] = $this->AENumbers->viewFormat2Decimals($values['waarde']);
    $values['aantal'] = $this->AENumbers->viewFormatMinMaxDecimals($values['aantal']);
    $values['koers'] = $this->AENumbers->viewFormatMinMaxDecimals($values['koers']);

    if ( isset ($values['total_waarde']) ) {$values['total_waarde'] = $this->AENumbers->viewFormat2Decimals($values['total_waarde']);}
    if ( isset ($values['total_aantal']) ) {$values['total_aantal'] = $this->AENumbers->viewFormatMinMaxDecimals($values['total_aantal']);}
    if ( isset ($values['total_koers']) ) {$values['total_koers'] = $this->AENumbers->viewFormatMinMaxDecimals($values['total_koers']);}
//    $values['transactietype'] = $AEParticipanten->transactionTypes[$values['transactietype']];
  }
  
//  debug($values);
  return $values;
}


/**
 * Format induvidual fields
 * @param type $values
 * @param type $AENumbers
 * @return type
 */
function formatPositionFields($values)
{
  /** if only 2 in an array it is an head or footer * */
  if (count($values) == 2)
  {
    switch ( strip_tags ($values['registration_number']) )
    {
      case 'Totaal':
        $values['aantal'] = $this->AENumbers->viewFormatMinMaxDecimals($values['aantal']);
        break;
      case 'Koers':
        $values['aantal'] = $this->AENumbers->viewFormatMinMaxDecimals($values['aantal']);
        break;
      case 'Waarde':
        $values['aantal'] = $this->AENumbers->viewFormat2Decimals($values['aantal']);
        break;
    }
  }
  else
  {
    $values['zoekveld'] = strlen($values['zoekveld']) > 55 ? substr($values['zoekveld'],0,55)."..." : $values['zoekveld'];

    $values['waarde'] = $this->AENumbers->viewFormat2Decimals($values['waarde']);
    $values['aantal'] = $this->AENumbers->viewFormatMinMaxDecimals($values['aantal']);
    $values['koers'] = $this->AENumbers->viewFormatMinMaxDecimals($values['koers']);
//
    if ( $values['total_waarde'] ) {$values['total_waarde'] = $this->AENumbers->viewFormat2Decimals($values['total_waarde']);}
    if ( $values['total_waarde'] ) {$values['total_aantal'] = $this->AENumbers->viewFormatMinMaxDecimals($values['total_aantal']);}
    if ( $values['total_waarde'] ) {$values['total_koers'] = $this->AENumbers->viewFormatMinMaxDecimals($values['total_koers']);}
  }
  return $values;
}



}