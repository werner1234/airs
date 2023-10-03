<?php
//include files
include_once("wwwvars.php");

/** form to reopen after save **/
$reopen = '';
$return = false;

$validationRules = array();

$AERekeningmutaties = new AE_RekeningMutaties ();

$data = array_merge($_POST, $_GET);

$useTable = 'Rekeningmutaties_v2';
$mutationType = 'definitief';
if ( isset ($data['type']) && $data['type'] == 'temp') {
  $useTable = 'VoorlopigeRekeningmutaties_v2';
  $mutationType = 'temp';
}
$action = $data['action'];
//$action = 'update';
//unset($data['id']);







$verzendVermogensbeheerder = null;
if( ! checkAccess() )
{
  $DB = new DB();
  if($action=='update')
  {
    $query="SELECT Portefeuilles.Vermogensbeheerder FROM Portefeuilles
            Inner Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
            Inner Join Rekeningen ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
            WHERE Rekeningen.Rekening='".$data['Rekening']."' AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
    $DB->SQL($query);
    $DB->Query();
    $vermogensbeheerder = $DB->NextRecord();
    $verzendVermogensbeheerder = $vermogensbeheerder['Vermogensbeheerder'];
  }
}


if ( ! isset ($data['bankTransactieId']) || empty ($data['bankTransactieId']) && $data['mutation_type'] != 'editForm' ) {
  $cfg=new AE_config();
  $bankTransactieId = $cfg->getData('lastRekeningMutatiebankTransactieId') + 1;
  $cfg->addItem('lastRekeningMutatiebankTransactieId',$bankTransactieId);

  $data['bankTransactieId'] = $__appvar['bedrijf'] . '_' . sprintf("%06d", $bankTransactieId);
}



if ( empty($data['id'])) {unset($data['id']);}

function setFieldErrors ($fields, $prefix = '')
{
  foreach ($fields as $fieldId => $fieldData)
  {
    if ( isset($fieldData['error']) )
    {
      $data[$prefix . '' . $fieldId] = array('description' => $fieldData['description'], 'message' => $fieldData['error']);
    }
  }
  return $data;
}

/**
 * process save data before saving
 * @param type $saveData
 * @return type
 */
function processData ($saveData) {
  /** Round values, **/
  $saveData['Credit']   =  round ($saveData['Credit'],  2);
  $saveData['Debet']    =  round ($saveData['Debet'],   2);
  $saveData['Bedrag']   =  round ($saveData['Bedrag'],  2);
  /** End Round values **/
  
  return $saveData;
}

function saveData ($saveData = array(), $prefix = '', $validationRules = array())
{
  global $__funcvar, $__appvar, $useTable, $verzendVermogensbeheerder;
  
  $saveData = processData($saveData);

  $object = new $useTable();
  $object->validationRules = $validationRules;
  $editObject = new editObject($object);
  $editObject->__funcvar = $__funcvar;
  $editObject->__appvar = $__appvar;
  $editObject->verzendVermogensbeheerder = $verzendVermogensbeheerder;
  $editObject->controller('update', $saveData);

  return array(
    'fieldErrors' => setFieldErrors($editObject->form->object->data['fields'], $prefix),
    'result' => array($editObject->result),
    'error' => array($editObject->_error),
    'ids' => array($object->data['fields']['id']['value'])
  );
}

function validateData ($saveData = array(), $prefix = '', $validationRules = array())
{
global $__funcvar, $__appvar, $useTable;
  
  $saveData = processData($saveData);

  $object = new $useTable();
  $object->validationRules = $validationRules;
   
  $editObject = new editObject($object);
  $editObject->__funcvar = $__funcvar;
  $editObject->__appvar = $__appvar;
  $editObject->data = $saveData;
  $editObject->setFields();
 
  $editObject->object->validate();

  return array(
    'fieldErrors' => setFieldErrors($editObject->object->data['fields'], $prefix),
    'result' => array($editObject->object->result),
    'error' => array($editObject->object->error),
  );
}


/**
 * Rollback saved fields for multiple rows saves
 * 
 * @author RM
 * @since 23-9-2014
 * 
 * @global type $__funcvar
 * @global type $__appvar
 * @param int $objectId
 */
function removeData ($objectId)
{
  global $__funcvar, $__appvar, $useTable;
  $object = new $useTable();
  $object->set($object->data['identity'], $objectId);
  $editObject = new editObject($object);
  $editObject->__funcvar = $__funcvar;
  $editObject->__appvar = $__appvar;
  $editObject->controller('delete', array());
}


$saveData = array();
if ( !empty($data['mutation_type']) )
{
  
  /** if account valuta != EUR and row valuta = EUR **/
  
  
  
  switch ($data['mutation_type']) {

/** Geld transacties **/    
    case 'geldtransacties':
//      if ( empty($data['Omschrijving']) )
//      {
//        $data['Omschrijving'] = ' ';
//      }
      $validationRules['Omschrijving'] = false;
      $calculateValue = $data['value'];
      
      $debitCredit = abs ($calculateValue);
      if ( $data['RekeningValuta'] === 'EUR' && $data['Valuta'] != 'EUR' ) {
        $calculateValue = $calculateValue * $data['Valutakoers'];
      }
      
      if ( in_array($data['Grootboekrekening'], array('STORT', 'VKSTO')) ) {
        $data['Credit'] = $debitCredit;
        $data['Bedrag'] = $calculateValue;
        $data['Debet'] = 0;
      } elseif ( $data['Grootboekrekening'] == 'ONTTR' ) {
        $data['Debet'] = $debitCredit;
        $data['Bedrag'] = -abs ($calculateValue);
        $data['Credit'] = 0;
      } elseif ( in_array($data['Grootboekrekening'], array('RENTE', 'Kruis', 'KRUIS')) ) {
        if ( $data['value'] > 0 )
        {
          $data['Credit'] = $debitCredit;
          $data['Debet'] = 0;
          $data['Bedrag'] = $calculateValue;
        }
        else
        {
          $data['Credit'] = 0;
          $data['Debet'] = $debitCredit;
          $data['Bedrag'] = -abs ($calculateValue);
        }
      }
      
      
      $validationRules['Fonds'] = false;
      $reopen = 'geld_transacties';
      $_SESSION['reload']['geld_transactie']['Grootboekrekening'] = $data['Grootboekrekening'];
      $_SESSION['reload']['geld_transactie']['Omschrijving'] = $data['Omschrijving'];

      break;
      
/** BeginBoeking **/      
    case 'beginboeking':
      $validationRules['Fonds'] = false;
      //This field is nog required
//      if ( empty($data['Omschrijving']) ) {
//        $data['Omschrijving'] = ' ';
//      }
      $validationRules['Omschrijving'] = false;
      
      if ( $data['Grootboekrekening'] == 'FONDS' ) {
        $calculateValue = ($data['Aantal'] * $data['fondseenheid'] * $data['Fondskoers']);
      } elseif ( $data['Grootboekrekening'] == 'VERM' ) {
        $data['Fonds'] = '';
        $calculateValue = $data['value_input'];
      }
      
      $debitCredit = abs ($calculateValue);
      if ( $data['RekeningValuta'] === 'EUR' && $data['Valuta'] != 'EUR' ) {
        $calculateValue = $calculateValue * $data['Valutakoers'];
      }
      
      $data['Bedrag'] = $calculateValue;
      
      if ( $data['Grootboekrekening'] == 'FONDS' ) {
        $data['Credit'] = 0;
        $data['Debet'] = $debitCredit;
        $data['Bedrag'] = -abs ($calculateValue);
      } elseif ( $data['Grootboekrekening'] == 'VERM' ) {
        if ( $calculateValue > 0 ) {
          $data['Credit'] = $debitCredit;
          $data['Debet'] = 0;
        } else {
          $data['Credit'] = 0;
          $data['Debet'] = $debitCredit;
        }
      }

      $reopen = 'beginboeking';
      break;
      
      
/** dividendcoupon * */
    case 'dividendcoupon':
      $default = array(
        'Boekdatum'         => $data['Boekdatum'],
        'Datum'             => $data['Boekdatum'],
        'settlementDatum'   => $data['settlementDatum'],
        'Fonds'             => $data['Fonds'],
        'Afschriftnummer'   => $data['Afschriftnummer'],
        'Rekening'          => $data['Rekening'],
        'bankTransactieId'  => $data['bankTransactieId']
      );

      /** first row * */
      $saveArray[0] = array(
        'Omschrijving' => $data['Omschrijving'],
        'Grootboekrekening' => $data['Grootboekrekening'],
        'Valuta' => $data['Valuta'], //Valuta
//        'Bedrag' => $data['value'] * $data['Valutakoers'],
        'Valutakoers' => $data['Valutakoers'],
        'Omschrijving' => $data['Omschrijving'],
        'Volgnummer' => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
        'Debet' => 0,
        'Credit' => 0
        ) + $default;
      
      $saveArray[0]['Bedrag'] = $data['value'];
      if ( $data['RekeningValuta'] === 'EUR' && $data['Valuta'] != 'EUR' ) {
        $saveArray[0]['Bedrag'] = $data['value'] * $data['Valutakoers'];
      }
      
      if ( $data['value'] > 0 ) {
        $saveArray[0]['Credit'] = $data['value'];
        $saveArray[0]['Debet'] = 0;
      } else {
        $saveArray[0]['Credit'] = 0;
        $saveArray[0]['Debet'] = abs($data['value']);
      }
      
//      $saveArray[0]['Credit'] = abs($data['value']);

      

      /** 2nd row * */
      if ( !empty($data['dividend_bedrag']) && $data['dividend_bedrag'] != '0.00' )
      {
        $saveArray[1] = array(
          'Valuta' => $data['dividend_valuta'],
          'Valutakoers' => $data['dividend_Valutakoers'],
          'Omschrijving' => $data['Omschrijving'],
          'Grootboekrekening' => 'DIVBE',
          'Volgnummer' => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
          'Debet' => 0,
          'Credit' => 0
          ) + $default;
        
        $saveArray[1]['Bedrag'] = - abs($data['dividend_value']);
        if ( $data['RekeningValuta'] === 'EUR' && $data['dividend_valuta'] != 'EUR' ) {
          $saveArray[1]['Bedrag'] = - abs($data['dividend_value'] * $data['dividend_Valutakoers']);
        }
        
        $saveArray[1]['Debet'] = abs ($data['dividend_value']);// * $data['dividend_Valutakoers'];
      }

      /** 3rd row * */
      if ( !empty($data['kosten_bedrag']) && $data['kosten_bedrag'] != '0.00' )
      {
        $saveArray[2] = array(
          'Grootboekrekening' => $data['kosten_Grootboekrekening'],
          'Valuta' => $data['kosten_valuta'],
          'Valutakoers' => $data['kosten_Valutakoers'],
//          'Bedrag' => -abs ($data['kosten_value'] * $data['kosten_Valutakoers']),
          'Omschrijving' => $data['Omschrijving'],
          'Volgnummer' => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
          'Debet' => 0,
          'Credit' => 0
          ) + $default;

        $saveArray[2]['Bedrag'] = -abs ($data['kosten_value']);
        if ( $data['RekeningValuta'] === 'EUR' && $data['kosten_valuta'] != 'EUR' ) {
          $saveArray[2]['Bedrag'] = -abs ($data['kosten_value'] * $data['kosten_Valutakoers']);
        }
        
        $saveArray[2]['Debet'] = abs ($data['kosten_value']);// * $data['kosten_Valutakoers'];
      }
      
      
      $validateData = validateData($saveArray[0]);
      if ( isset($saveArray[1])) {$validateData = array_merge_recursive($saveData, validateData($saveArray[1], 'dividend_'));}
      if ( isset($saveArray[2])) {$validateData = array_merge_recursive($saveData, validateData($saveArray[2], 'kosten_'));}
      
      /** if we have errors send them to the frontend **/
      if ( in_array(true, $validateData['error'])) {
        $saveData = $validateData;
      } else {
        /** we have no errors move on to save the data **/
        $saveData = array_merge_recursive($saveData, saveData($saveArray[0]));
        if ( isset($saveArray[1])) {$saveData = array_merge_recursive($saveData, saveData($saveArray[1], 'dividend_'));}
        if ( isset($saveArray[2])) {$saveData = array_merge_recursive($saveData, saveData($saveArray[2], 'kosten_'));}
      }
      


      $reopen = 'dividend_coupons';
      break;
      
/** Aan/Verkoop **/      
    case 'aanverkoop':
      $validationRules['Fonds'] = true; 
      /** 1e opzet  voor fonds.omschrijving 3887   **/
//      if ( ! empty ($data['Fonds']) ) {
//        $fonds = new Fonds();
//        $fondsData = $fonds->parseBySearch(array('Fonds' => $data['Fonds']), array('Fonds', 'omschrijving'));
//      } else {
//        $fondsData = array(
//          'Fonds' => '',
//          'omschrijving' => '',
//        );
//      }
      
      $default = array(
        'Boekdatum'         => $data['Boekdatum'],
        'Datum'             => $data['Boekdatum'],
        'settlementDatum'   => $data['settlementDatum'],
        'Fonds'             => $data['Fonds'],
        'Afschriftnummer'   => $data['Afschriftnummer'],
        'Rekening'          => $data['Rekening'],
        'bankTransactieId'  => $data['bankTransactieId'],
      );

      /** check if we buy or sell **/
      if ( $data['Transactietype'][0] == 'V' || $data['Transactietype'][0] == 'V/S' )
      {
        $data['Aantal'] = -abs($data['Aantal']);
      }

      $saveArray[0] = array(
        'Omschrijving' => $data['Omschrijving'],
        'Grootboekrekening' => 'FONDS',
        'Valuta' => $data['Valuta'], //Valuta
        'Bedrag' => $data['Bedrag'],
        'Valutakoers' => $data['Valutakoers'],
        'Omschrijving' => $data['Omschrijving'],
        'Volgnummer' => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
        'Transactietype' => $data['Transactietype'],
        'Fondskoers' => $data['Fondskoers'],
        'Aantal' => $data['Aantal'],
        'Bewaarder' => $data['Bewaarder'],
        ) + $default;
      
//      $data['value'] = $data['Aantal'] * $data['Fondskoers'] * $data['fondseenheid'];
      $aanVerkoopBedrag = abs($data['Aantal'] * $data['Fondskoers'] * $data['fondseenheid']);
      $debetCreditBedrag = $aanVerkoopBedrag;
      
      if ( $data['RekeningValuta'] === 'EUR' && $data['Valuta'] != 'EUR' ) {
        $aanVerkoopBedrag = $aanVerkoopBedrag * $data['Valutakoers'];
      }
      
      
      if ( $data['Transactietype'][0] == 'A' || $data['Transactietype'][0] == 'A/S' )
      {
        $saveArray[0]['Bedrag']= -abs($aanVerkoopBedrag);
        $saveArray[0]['Debet'] = $debetCreditBedrag ;
        $saveArray[0]['Credit'] = 0;
      }
      else
      {
        $saveArray[0]['Bedrag'] = $aanVerkoopBedrag ;
        $saveArray[0]['Credit'] = $debetCreditBedrag ;
        $saveArray[0]['Debet'] = 0;
      }
      

      //kosten
      if ( ! empty($data['Kosten_Input']) && $data['Kosten_Input']  > 0 ) {
        $saveArray[1] = array(
          'Grootboekrekening' => ( isset($data['Kosten_Grootboekrekening']) && ! empty($data['Kosten_Grootboekrekening']) ? $data['Kosten_Grootboekrekening'] : 'KOST'),
          'Valutakoers' => $data['Kosten_Valutakoers'],
          'Valuta' => $data['Kosten_Valuta'],
          'Fondskoers' => null,
          'Omschrijving' => $data['Omschrijving'],
          'Volgnummer' => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
          ) + $default;
        
        $saveArray[1]['Bedrag'] = -abs($data['Kosten_Input']);
        if ( $data['RekeningValuta'] === 'EUR' && $data['Kosten_Valuta'] != 'EUR' ) {
          $saveArray[1]['Bedrag'] = -abs($data['Kosten_Input'] * $data['Kosten_Valutakoers']);
        }
        
        $saveArray[1]['Debet'] = abs($data['Kosten_Input']);
        $saveArray[1]['Credit'] = 0;
        
      }


      //kosten
      if ( ! empty($data['Kosten1_Input']) && $data['Kosten1_Input']  > 0 ) {
        $saveArray[2] = array(
            'Grootboekrekening' => ( isset($data['Kosten1_Grootboekrekening']) && ! empty($data['Kosten1_Grootboekrekening']) ? $data['Kosten1_Grootboekrekening'] : 'KOST'),
            'Valutakoers' => $data['Kosten1_Valutakoers'],
            'Valuta' => $data['Kosten1_Valuta'],
            'Fondskoers' => null,
            'Omschrijving' => $data['Omschrijving'],
            'Volgnummer' => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
          ) + $default;

        $saveArray[2]['Bedrag'] = -abs($data['Kosten1_Input']);
        if ( $data['RekeningValuta'] === 'EUR' && $data['Kosten1_Valuta'] != 'EUR' ) {
          $saveArray[2]['Bedrag'] = -abs($data['Kosten1_Input'] * $data['Kosten1_Valutakoers']);
        }

        $saveArray[2]['Debet'] = abs($data['Kosten1_Input']);
        $saveArray[2]['Credit'] = 0;

      }



      //kostenBuitenland
      if ( ! empty($data['kostenBuitenland_Input']) && $data['kostenBuitenland_Input']  > 0 ) {
        $saveArray[3] = array(
          'Grootboekrekening' => 'KOBU',
          'Valuta' => $data['kostenBuitenland_Valuta'],
          'Fondskoers' => null,
          'Valutakoers' => $data['kostenBuitenland_Valutakoers'],
          'Volgnummer' => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
          'Omschrijving' => $data['Omschrijving'],
          ) + $default;
        
        $saveArray[3]['Bedrag'] = -abs($data['kostenBuitenland_Input']);
        if ( $data['RekeningValuta'] === 'EUR' && $data['kostenBuitenland_Valuta'] != 'EUR' ) {
          $saveArray[3]['Bedrag'] = -abs($data['kostenBuitenland_Input'] * $data['kostenBuitenland_Valutakoers']);
        }
        
        $saveArray[3]['Debet'] = abs ($data['kostenBuitenland_Input']); //bedrag kosten
        $saveArray[3]['Credit'] = 0;
        
        
      }
      
      //rente
      if ( ! empty($data['rente_Input']) && $data['rente_Input']  > 0 ) {
        $saveArray[4] = array(
          'Grootboekrekening' => ($data['Transactietype'][0] == 'V' ? 'RENOB' : 'RENME'),
          'Valutakoers' => $data['rente_Valutakoers'],
          'Valuta' => $data['rente_Valuta'],
          'Omschrijving' => $data['Omschrijving'],
          'Volgnummer' => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
          ) + $default;
         
          $aanVerkoopRenteBedrag = abs($data['rente_Input']);
          $aanVerkoopRenteBedragDebetCredet = $aanVerkoopRenteBedrag;
          if ( $data['RekeningValuta'] === 'EUR' && $data['Valuta'] != 'EUR' ) {
            $aanVerkoopRenteBedrag = $aanVerkoopRenteBedrag * $data['rente_Valutakoers'];
          }
          
          
          if ($data['Transactietype'][0] == 'A'  || $data['Transactietype'][0] == 'A/S') {
            $saveArray[4]['Bedrag'] = -abs($aanVerkoopRenteBedrag);
            $saveArray[4]['Debet'] = $aanVerkoopRenteBedragDebetCredet;
          } else {
            $saveArray[4]['Bedrag'] = $aanVerkoopRenteBedrag;
            $saveArray[4]['Credit'] = $aanVerkoopRenteBedragDebetCredet;
          }
      }
      
      
      $validateData = validateData($saveArray[0]);
      if ( isset($saveArray[1])) {$validateData = array_merge_recursive($validateData, validateData($saveArray[1], 'Kosten_'));}
      if ( isset($saveArray[2])) {$validateData = array_merge_recursive($validateData, validateData($saveArray[2], 'Kosten1_'));}
      if ( isset($saveArray[3])) {$validateData = array_merge_recursive($validateData, validateData($saveArray[3], 'kostenBuitenland_'));}
      if ( isset($saveArray[4])) {$validateData = array_merge_recursive($validateData, validateData($saveArray[4], 'rente_'));}
      
      /** if we have errors send them to the frontend **/
      if ( in_array(true, $validateData['error'])) {
        $saveData = $validateData;
      } else {
        /** we have no errors move on to save the data **/
        $saveData = array_merge_recursive($saveData, saveData($saveArray[0]));
        if ( isset($saveArray[1])) {$saveData = array_merge_recursive($saveData, saveData($saveArray[1], 'Kosten_'));}
        if ( isset($saveArray[2])) {$saveData = array_merge_recursive($saveData, saveData($saveArray[2], 'Kosten1_'));}
        if ( isset($saveArray[3])) {$saveData = array_merge_recursive($saveData, saveData($saveArray[3], 'kostenBuitenland_'));}
        if ( isset($saveArray[4])) {$saveData = array_merge_recursive($saveData, saveData($saveArray[4], 'rente_'));}
      }
      
      $reopen = 'aan_verkoop';
      
      break;

    
/** 
 * Kostenboeking 
 * Absoluut invoeren
 * Wegschrijven als debet en negatief bedrag
 * 
 */   
    case 'kostenboeking':
//      if ( empty($data['Omschrijving']) ) {$data['Omschrijving'] = ' ';} //fake empty description
      $validationRules['Fonds'] = false;
      $validationRules['Omschrijving'] = false;

      $kostenboekingTotaal = abs($data['value']);
      $kostenboekingTotaalDebet = $kostenboekingTotaal;
      if ( $data['RekeningValuta'] === 'EUR' && $data['Valuta'] != 'EUR' ) {
        $kostenboekingTotaal = $kostenboekingTotaal * $data['Valutakoers'];
      }
      
      $data['Credit'] = 0;
      $data['Debet'] = $kostenboekingTotaalDebet;
      $data['Bedrag'] = -abs ($kostenboekingTotaal);
        
      $reopen = 'kostenboeking';
      break;

    case 'aanverksoop':

      break;
/** Memoriaal **/    
    case 'memoriaal':
      
      $_SESSION['reload']['memoriaal']['Omschrijving'] = $data['Omschrijving'];

      $validationRules['Fonds'] = true;
      $default = array(
        'Boekdatum'           => $data['Boekdatum'],
        'Datum'               => $data['Boekdatum'],
        'settlementDatum'     => $data['settlementDatum'],
        'Afschriftnummer'     => $data['Afschriftnummer'],
        'Rekening'            => $data['Rekening'],
        'Memoriaalboeking'    => 1,
        'bankTransactieId'    => $data['bankTransactieId']
      );

      /** deponering or lichting - **/
      if ( in_array($data['Transactietype'], array('L', 'V/O', 'V/S')) ) {
        $data['Aantal'] = - abs($data['Aantal']);
      }

      $saveArray[0] = array(
        'Omschrijving'        => $data['Omschrijving'],
        'Grootboekrekening'   => 'FONDS',
        'Valuta'              => $data['Valuta'], //Valuta
        'Bedrag'              => $data['Bedrag'],
        'Fonds'               => $data['Fonds'],
        'Valutakoers'         => $data['Valutakoers'],
        'Omschrijving'        => $data['Omschrijving'],
        'Volgnummer'          => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
        'Transactietype'      => $data['Transactietype'],
        'Fondskoers'          => $data['Fondskoers'],
        'Aantal'              => $data['Aantal'],
        'Bewaarder'           => $data['Bewaarder'],
        ) + $default;
      $saveArray[0]['Bedrag'] = $data['Aantal'] * $data['Fondskoers'] * $data['fondseenheid'] * $data['Valutakoers'];
      $data['value'] = $data['Aantal'] * $data['Fondskoers'] * $data['fondseenheid'];
   
      switch ($data['Transactietype'])
      {
        case 'V/O':
        case 'V/S':
        case 'L':
          $saveArray[0]['Bedrag'] = abs ($saveArray[0]['Bedrag']);//Bedrag is in euro 
          $saveArray[0]['Credit'] = abs ($data['value']); //Credit is in geselecteerde valuta
          $saveArray[0]['Debet'] = 0;
          break;
        case 'B':
        case 'D':
        case 'A/O':
        case 'A/S':
          $saveArray[0]['Bedrag'] = -abs ($saveArray[0]['Bedrag']);//Bedrag is in euro 
          $saveArray[0]['Credit'] = 0;
          $saveArray[0]['Debet'] = abs ($data['value']); //Debet is in geselecteerde valuta
          break;
      }
      
      
//      if ( in_array($data['Transactietype'], array('V/O', 'V/S', 'L')) ) { //verkoop
//        $saveArray[0]['Bedrag'] = abs ($saveArray[0]['Bedrag']);//Bedrag is in euro 
//        $saveArray[0]['Credit'] = abs ($data['value']); //Credit is in geselecteerde valuta
//        $saveArray[0]['Debet'] = 0;
//      } elseif ( in_array($data['Transactietype'], array('D', 'A/O', 'A/S')) ) { //aankoop
//        $saveArray[0]['Bedrag'] = -abs ($saveArray[0]['Bedrag']);//Bedrag is in euro 
//        $saveArray[0]['Credit'] = 0;
//        $saveArray[0]['Debet'] = abs ($data['value']); //Debet is in geselecteerde valuta
//      }
     

      //rente
      if ( ! empty($data['rente_Input']) && $data['rente_Input'] != '0.00') {
        $validationRules['Fonds'] = false;
        $saveArray[3] = array(
          'Grootboekrekening'   => (in_array($data['Transactietype'], array('L', 'V/O', 'V/S')) ? 'RENOB' : 'RENME'),
          'Valutakoers'         => $data['rente_Valutakoers'],
          'Valuta'              => $data['rente_Valuta'],
          'Fonds'               => $data['Fonds'],
          'Omschrijving'        => $data['Omschrijving'],
          'Volgnummer'          => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
        ) + $default;
         
        if ( in_array($data['Transactietype'], array('V/O', 'V/S', 'L')) ) { //verkoop
          $saveArray[3]['Bedrag'] = abs ($data['rente_Input'] * $data['rente_Valutakoers']);
          $saveArray[3]['Credit'] = abs ($data['rente_Input'] * $data['rente_Valutakoers']); //bedrag kostenbuitenland
        } elseif ( in_array($data['Transactietype'], array('A/O', 'A/S', 'D', 'B'))   ) { //aankoop
          $saveArray[3]['Bedrag'] = -abs ($data['rente_Input'] * $data['rente_Valutakoers'] );
          $saveArray[3]['Debet'] = $data['rente_Input'] * $data['rente_Valutakoers']; //bedrag kostenbuitenland
        }
      } else {
        /** set empty if input is not filled **/
//        $saveArray[3]['Bedrag'] = 0;
      }
      
      $validateData = validateData($saveArray[0]);
      if ( isset($saveArray[3])) {$validateData = array_merge_recursive($validateData, validateData($saveArray[3], null, $validationRules));}
      /** if we have errors send them to the frontend **/
      if ( in_array(true, $validateData['error'])) {
        $saveData = $validateData;
      } else {
        /** we have no errors move on to save the data **/
        $saveData = array_merge_recursive($saveData, saveData($saveArray[0]));
        if ( isset($saveArray[3])) {$saveData = array_merge_recursive($saveData, saveData($saveArray[3], null, $validationRules));}
      }
      
      
      
      
      
      // - Bij een aankoop is het: mutatiebedrag (is negatief) -  opgelopen rente
      // - Bij een verkoop is het: mutatiebedrag (is positief) + opgelopen rente     
      /** create compensating line **/
      if ( isset ($data['createRule']) && $data['createRule'] == 'true' && ! empty ($data['Transactietype']) && ! in_array(true, $validateData['error'])) {
        
        $data['Valuta'] = 'EUR';
        if ( in_array($data['Transactietype'], array('A/O', 'A/S', 'D'))   ) { //aankoop
          $totaalBoeking = round($data['Aantal'] * $data['Fondskoers'] * $data['fondseenheid'] * $data['Valutakoers'], 2) + round($data['rente_Input'] * $data['rente_Valutakoers'], 2);
          $saveArray[1] = array(
            'Grootboekrekening'      => 'STORT',
            'Credit'        => abs ($totaalBoeking), //som van de deponering en rente
            'Bedrag'        => abs ($totaalBoeking), //+ Credit
            'Omschrijving'  => $data['Omschrijving'],
            'Valutakoers' => 1,
            'Valuta'              => $data['Valuta'], //Valuta
            'Fonds'         => '',
            'Volgnummer' => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
          ) + $default;
        } elseif ( in_array($data['Transactietype'], array('V/O', 'V/S', 'L')) ) { //verkoop
          $totaalBoeking = round($data['Aantal'] * $data['Fondskoers'] * $data['fondseenheid'] * $data['Valutakoers'], 2) - round($data['rente_Input'] * $data['rente_Valutakoers'], 2);
          $saveArray[1] = array(
            'Grootboekrekening'     => 'ONTTR',
            'Debet'         => abs ($totaalBoeking), //som van de lichting en rente
            'Bedrag'        => -abs ($totaalBoeking), //- Debet
            'Omschrijving'  => $data['Omschrijving'],
            'Valutakoers' => 1,
            'Valuta'              => $data['Valuta'], //Valuta
            'Fonds'         => '',
            'Volgnummer' => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
          ) + $default;
        } elseif ( $data['Transactietype'] === 'B' ) {
          $totaalBoeking = round($data['Aantal'] * $data['Fondskoers'] * $data['fondseenheid'] * $data['Valutakoers'], 2) + round($data['rente_Input'] * $data['rente_Valutakoers'], 2);
          $saveArray[1] = array(
            'Grootboekrekening'   => 'VERM',
            'Credit'              => abs ($totaalBoeking), //som van de deponering en rente
            'Bedrag'              => abs ($totaalBoeking), //+ Credit
            'Omschrijving'        => $data['Omschrijving'],
            'Valutakoers'         => 1,
            'Valuta'              => $data['Valuta'], //Valuta
            'Fonds'               => null,
            'Volgnummer'          => $AERekeningmutaties->getVolgnummer($data['Rekening'], $data['Afschriftnummer'], $mutationType),
          ) + $default;
        }
        $validationRules['Fonds'] = false;
        $saveData = array_merge_recursive($saveData, saveData($saveArray[1], null, $validationRules));
      }
      
      
      break;
      
    case 'overige':
      $validationRules['Fonds'] = false;
      $reopen = 'overige';
      
      if ( $data['Debet'] > 0 ) { //verkoop
        $data['Bedrag'] = -abs ($data['Bedrag']);
      } else { //aankoop
        $data['Bedrag'] = abs ($data['Bedrag']);
      }
      
      break;
    case 'editForm':
      $validationRules['Fonds'] = false;
      
      if ( $data['Debet'] > 0 ) { //verkoop
        $data['Bedrag'] = -abs ($data['Bedrag']);
      } else { //aankoop
        $data['Bedrag'] = abs ($data['Bedrag']);
      }
      
      if ( isset($data['id']) && ! empty($data['id']) ) {$return = $data['returnUrl'];}
      break;
      
    default:
      break;
  }
}
$result = false;
$error = '';


//prep error messages
$errorMessage = array();
$fieldErrors = array();
//
if ( empty($saveData) )
{
  /** process data before saving it **/
  $data = processData ($data);

  $object = new $useTable();
  $object->validationRules = $validationRules;
  $editObject = new editObject($object);
  $editObject->__funcvar = $__funcvar;
  $editObject->__appvar = $__appvar;
  $editObject->verzendVermogensbeheerder = $verzendVermogensbeheerder;
  $editObject->controller($action, $data);
  /** check for error messages * */
  $saveData['fieldErrors'] = setFieldErrors($editObject->form->object->data['fields']);
  $message=$editObject->message;
  /** set result * */
  $saveData['result'][] = $editObject->result;
  $saveData[] = $editObject->_error;
}

//$editObject->controller($action,$data);

if ( !in_array(0, $saveData['result']) )
{
  
  echo json_encode(array(
    'success' => 1,
    'message' => 'De door u ingevoerde waarden zijn opgeslagen. '.$message,
    'reopen'  => $reopen,
    'return'  => $return
  ));
}
else
{
  //revert saves
  if ( isset($saveData['ids']) && !empty($saveData['ids']) )
  {
    foreach ($saveData['ids'] as $id)
    {
      removeData($id);
    }
//    $errorMessage = $saveArray;
  }
  else
  {
//    $errorMessage = 'De door u ingevoerde waarde konden niet worden opgeslagen';
  }

  echo json_encode(array(
    'success' => 0,
    'message' => 'De door u ingevoerde waarde konden niet worden opgeslagen. '.$message,
    'errors' => $saveData['fieldErrors']
  ));
}


