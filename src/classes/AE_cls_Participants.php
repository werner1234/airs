<?php
 
class AE_Participants extends AE_AjaxHelper
{
  var $minKoersDecimals = 2;
  var $maxKoersDecimals = 6;

  var $participanten = null;
  var $participantenFondsVerloop = null;
  
  var $transactionTypes = array(
    'B' => 'Begin',
    'A' => 'Aankoop',
    'V' => 'Verkoop',
    'D' => 'Deponering',
    'L' => 'Lichting',
    'BK' => 'Bijkopen',
    'DV' => 'Deelverkoop',
    'H' => 'Herbelegging',
    'U' => 'Uitkering'
  );

  function AE_Participants()
  {
    $this->AETemplate = new AE_template();
    $this->participanten = new Participanten();
    $this->participantenFondsVerloop = new ParticipantenFondsVerloop();
    $this->AENumbers = new AE_Numbers();
    $this->AE_Array = new AE_Array();
    $this->db = new db();
    
    parent::AE_AjaxHelper(array_merge($_POST, $_GET));
  }
  
  
  function getFondsBucketCode () {
     $this->itemsExist(array('fonds'));
     $fonds = new Fonds();
     $bucketCode = $fonds->parseBySearch(array('Fonds' => $this->data['fonds']), array('bucketCode'));
     
     if ( $bucketCode !== false ) {
       echo json_encode($bucketCode);
     } else {
       exit('Fonds kon niet worden gevonden!');
     }
  }
  
  
  /**
   * Get 
   * @param type $clientId
   * @return type
   */
  function getParticipant ($cmrId = null) {
    $clients = array();
    if ( ! empty ($cmrId) ) {
      if ( is_array($cmrId) ) {
        $ids = implode(',', $cmrId);
      } else {
        $ids = $cmrId;
      }
      
      $allParticipantsQuery = $this->participanten->getQueryTemplate('getParticipantByCrmId', array('crm_id' => $ids));
      $this->db->executeQuery($allParticipantsQuery);
      while ( $participantRow = $this->db->nextRecord() ) {
        $clients[$participantRow['registration_number'].'.'.$participantRow['fonds_fonds']] = $participantRow;
      }
    } else {
      $allParticipantsQuery = $this->participanten->getQueryTemplate('getAllParticipants');
      $this->db->executeQuery($allParticipantsQuery);
      while ( $participantRow = $this->db->nextRecord() ) {
        $clients[] = $participantRow;
      }
    }
    return $clients;
  }
  
  

  /**
   * check if combination exists
   * @param class $participantenObject
   * @param type $fonds
   * @param type $registrationNumber
   * @return boolean
   */
  function isDuplicate($participantenObject, $fonds, $registrationNumber)
  {
    $db = new DB();
    $query = 'SELECT id FROM ' . $participantenObject->data['table'] . ' WHERE `fonds_fonds` = "' . $fonds . '" AND `registration_number` = "' . $registrationNumber . '"';

    $db->executeQuery($query);

    $results = $db->records();
    if ($results > 0)
    {
      return $db->nextRecord();
    }
    return false;
  }

  /**
   * calculate total 
   * 
   * @param array $data array of transactions as array(type, amount)
   */
  function calculateTotal($data)
  {
    $totals = 0;
    foreach ($data as $transactietypeKey => $transactietypetotal)
    {
      switch ($transactietypeKey)
      {
        case 'B':
        case 'A':
        case 'D':
        case 'BK':
        case 'H':
          $totals = ($totals + $transactietypetotal);
          break;
        case 'V':
        case 'L':
        case 'DV':
          $totals = ($totals - $transactietypetotal);
          break;
      }
    }
    return $totals;
  }
  

  /**
   * format the waarde
   */
  function formatNumber($value, $type = null)
  {
    if (in_array($type, array('B', 'A', 'D', 'BK', 'H'))) //array('B', 'A', 'D')
    {
      $value = -abs($value);
    }
    else
    {
      $value = abs($value);
    }
    return $value;
//    return number_format((float) $value, 2, '.', ',');
  }

  /**
   * formatAantal
   * if transaction types (V || L) make negative
   * 
   * @param type $amount aantal
   * @param type $type transaction type
   * @return type aantal
   */
  function formatAantal($amount, $type)
  {
    if (in_array($type, array('V', 'L', 'DV')) ) //&& $amount > 0
    {
      $amount = -abs($amount);
    } else {
      $amount = abs($amount);
    }
    return $amount;
    //return $this->formatAantalField($amount);
  }

  function getTransactionTypeList()
  {
    $transactionTypes = array();
    if ( isset ($participantenFondsVerloop->data['fields']['transactietype']['form_options']) ) {
      foreach ($participantenFondsVerloop->data['fields']['transactietype']['form_options'] as $transactietypeKey => $ransactietypeValue)
      {
        $transactionTypes[$transactietypeKey] = 0;
      }
    }
    return $transactionTypes;
  }
  
  /**
   * 
   * 
   */
  function getFondsForClient()
  {
    $this->itemsExist(array('registrationNumber', 'clientId'));
    $where = '';
    if ($this->data['clientId'] != 'null')
    {
      $where .= '`crm_id` = "' . $this->data['clientId'] . '"';
    }
    if ($this->data['registrationNumber'] != 'null')
    {
      $where .= (!empty($where) ? ' AND ' : '') . '`registration_number` = "' . $this->data['registrationNumber'] . '"';
    }
    if (!empty($where))
    {
      $where = 'WHERE ' . $where;
    }

    $query = 'SELECT * FROM `' . $this->participanten->data['table'] . '` ' . $where . '';

    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    while ($row = $DB->NextRecord())
    {
      $fonds[$row['fonds_fonds']] = $row['fonds_fonds'];
    }
    $this->returnData($fonds);
  }

  function listRegistrationNumbers()
  {
    $this->itemsExist(array('fonds', 'clientId'));
    $where = '';

    if ($this->data['clientId'] != 'null')
    {
      $where .= '`crm_id` = "' . $this->data['clientId'] . '"';
    }
    if ($this->data['fonds'] != 'null')
    {
      $where .= (!empty($where) ? ' AND ' : '') . '`fonds_fonds` = "' . $this->data['fonds'] . '"';
    }
    if (!empty($where))
    {
      $where = 'WHERE ' . $where;
    }

    $query = 'SELECT * FROM `' . $this->participanten->data['table'] . '`  ' . $where . '';
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    while ($row = $DB->NextRecord())
    {
      $registration[$row['id']] = $row['registration_number'] . ' - ' . $row['fonds_fonds'];
    }

    //if we got more registration numbers for one person list them else send empty
    if (count($registration) == 1)
    {
      $registration = array();
    }

    $this->returnData($registration);
  }

  /**
   * Get the fonds of a registrationnumber
   */
  function getFondsOfRegistrationNumber($registrationNumber)
  {
    $query = 'SELECT * FROM `' . $this->participanten->data['table'] . '` WHERE `registration_number` = "' . $registrationNumber . '"';
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $row = $DB->NextRecord();

    if (isset($row['fonds_fonds']))
    {
      return $row['fonds_fonds'];
    }
    return null;
  }

  function getFondsOfId($id)
  {
    $query = 'SELECT * FROM `' . $this->participanten->data['table'] . '` WHERE `id` = "' . $id . '"';
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $row = $DB->NextRecord();

    if (isset($row['fonds_fonds']))
    {
      return $row['fonds_fonds'];
    }
    return null;
  }
  
  /**
   * Positions 
   */
  
  
  /**
   * positionOneFondsOneClient
   * @param type $client
   * @param type $fonds
   * @param type $date
   * @return type
   */
  function positionOneFondsOneClient ($client, $fonds, $date, $data = null) {
    return $this->positionOneFondsOneClientResult($client, $fonds, $date, $data);
  }
  
  /**
   * positionAllFondsAllClients
   * @param type $date
   */
  function positionAllFondsAllClients ($date, $data = null) {
     /** get used fonds * */
    $orderField = $this->AE_Array->toSqlOrder(array('fonds_fonds' => ( isset ($data['sort']['fonds_fonds']) ? $data['sort']['fonds_fonds']['order'] : 'ASC') ));
    $listFonds = $this->participanten->parseBySearch(null, null, 'GROUP BY `fonds_fonds` ' . $orderField, -1);
    
//    $listFonds = 'SELECT * from `' . $this->participanten->data['table'] . '` GROUP BY `fonds_fonds';

//    $db = new DB();
//    $db->QRecords($listFonds);
//    while ($row = $db->nextRecord())
    foreach ( $listFonds as $row )
    {
      $returnData[$row['fonds_fonds']] = $this->positionOneFondsAllClientResult($row['fonds_fonds'], $date, $data);
    }
    return $returnData;
  }
  
  /**
   * positionOneFondsAllClient
   * @param type $fonds
   * @param type $date
   * @return type
   */
  function positionOneFondsAllClient ($fonds, $date, $data = null) {
    return $this->positionOneFondsAllClientResult($fonds, $date, $data);
  }
  
  /**
   * positionAllFondsOneClient
   * @param type $client
   * @param type $date
   */
  function positionAllFondsOneClient ($client, $date, $data = null) {
    unset($data['sort']['zoekveld']);
    return $this->positionAllFondsOneClientResult($client, $date, $data);
  }

  /**
   * Overview generators for participants
   */
  
  
  /**
   * oneFondsAllClient
   * @param type $fonds
   * @param type $startDate
   * @param type $endDate
   * @return type
   */
  function oneFondsAllClient($fonds, $startDate, $endDate, $data)
  {
    $dataArray = array();

    /** get fonds amount between selected date * */
    $startTrancactions = $this->oneFondsAllClientStartPosition($fonds, $startDate, $endDate);
    $betweenDatesQuery = $this->oneFondsAllClientBetweenQuery($fonds, $startDate, $endDate, $this->AE_Array->formToSqlOrder($data['sort']) );
    $endTrancactions = $this->oneFondsAllClientEndPosition($fonds, $startDate, $endDate);
    $betweenTransaction = $this->getTransactionTypeList();
    $fondsData = $this->getFondskoers($fonds, $endDate);
    $endAmount = $startTrancactions['aantal'] + $endTrancactions['aantal'];

    /** set header row as start date and total amount * */
    $dataArray['head'] = $this->makeHead($startDate, $startTrancactions);
    
    $betweenDates = new DB();
    $betweenDates->QRecords($betweenDatesQuery);
    while ($fondsClientRow = $betweenDates->nextRecord())
    {
      $fondsClientRow['aantal'] = $this->formatAantal($fondsClientRow['aantal'], $fondsClientRow['transactietype']);
//      $fondsClientRow['aantal'] = $this->formatAantalField($fondsClientRow['aantal']);
      
      $fondsClientRow['datum'] = $this->AE_Datum->dbToForm($fondsClientRow['datum']);
      $fondsClientRow['waarde'] = $this->formatNumber($fondsClientRow['aantal'] * $fondsClientRow['koers'], $fondsClientRow['transactietype']);

      $fondsClientRow['koers'] = $this->formatKoersField($fondsClientRow['koers']);

      $fondsClientRow['startDate'] = $startDate;
      $fondsClientRow['endDate'] = $endDate;
      
      $fondsClientRow['endKoers'] = number_format((float) $fondsData['Koers'], 2, '.', '');
      $fondsClientRow['startPosition'] = $startTrancactions['aantal'];
      $fondsClientRow['endPosition'] = $endTrancactions['aantal'];
      
      $fondsClientRow['endValue'] = number_format((float) $endAmount * $fondsData['Koers'], 2, '.', '');
      
      $dataArray[] = $fondsClientRow;
    }

    $dataArray['foot'] = $this->makeFoot($endDate, $endAmount);
    $dataArray['koers'] = $this->makeVerloopKoers($fondsData);
    $dataArray['value'] = $this->makeValue($endAmount, $fondsData);

    if ( $this->AE_Array->hasNumericKeys($dataArray) == false && $startTrancactions['aantal'] == 0 && $endAmount == 0 ) {return array();}
    
    
    return $dataArray;
  }

  /**
   * allFondsOneClient get alle the fonds for one client
   * 
   * @param type $client
   * @param type $startDate
   * @param type $endDate
   */
  function allFondsOneClient($client, $startDate, $endDate, $data = null)
  {
    $dataArray = array();
    /** get the fonds the client is in * */
    $orderField = $this->AE_Array->toSqlOrder(array('fonds_fonds' => ( isset ($data['sort']['fonds_fonds']) ? $data['sort']['fonds_fonds']['order'] : 'ASC') ));
    /** get used fonds **/
    $listFonds = $this->participanten->parseBySearch(array('crm_id' => $client), null, 'GROUP BY `fonds_fonds` ' . $orderField, -1);
    
    
//    $listFonds = 'SELECT * from `' . $this->participanten->data['table'] . '` WHERE `crm_id` = "' . $client . '" GROUP BY `fonds_fonds`';
//
//    $db = new DB();
//    $db->QRecords($listFonds);
//    while ($row = $db->nextRecord())
    foreach ( $listFonds as $row)
    {
      $startTrancactions = $this->oneFondsOneClientStartPosition($client, $row['fonds_fonds'], $startDate, $endDate);
      $endTrancactions = $this->oneFondsOneClientEndPosition($client, $row['fonds_fonds'], $startDate, $endDate);
      
      /** set header row as start date and total amount * */
      $dataArray[$row['fonds_fonds']]['head'] = $this->makeHead($startDate, $startTrancactions);

      /** get fonds amount between selected date * */

      $oneFondsOneClientQuery = $this->oneFondsOneClientQuery($client, $row['fonds_fonds'], $startDate, $endDate, $this->AE_Array->formToSqlOrder($data['sort']));

      $oneFondsOneClientDB = new DB();
      $oneFondsOneClientDB->QRecords($oneFondsOneClientQuery);

      
      $endAmount = $startTrancactions['aantal'] + $endTrancactions['aantal'];

      $fondsData = $this->getFondskoers($row['fonds_fonds'], $endDate);

      $betweenTransaction = $this->getTransactionTypeList();
      
      while ($fondsClientRow = $oneFondsOneClientDB->nextRecord())
      {
//        if ( $fondsClientRow['aantal'] * $fondsClientRow['koers'] != 0 ) {
          $fondsClientRow['waarde'] = $this->formatNumber($fondsClientRow['aantal'] * $fondsClientRow['koers'], $fondsClientRow['transactietype']);

          $fondsClientRow['aantal'] = $this->formatAantal($fondsClientRow['aantal'], $fondsClientRow['transactietype']);
  //        $fondsClientRow['aantal'] = $this->formatAantalField($fondsClientRow['aantal']);

          $fondsClientRow['datum'] = $this->AE_Datum->dbToForm($fondsClientRow['datum']);

          $fondsClientRow['koers'] = $this->formatKoersField($fondsClientRow['koers']);

          $fondsClientRow['startDate'] = $startDate;
          $fondsClientRow['startPosition'] = $this->formatAantalField($startTrancactions['aantal']);
          $fondsClientRow['startPosition'] = $this->formatAantalField($fondsClientRow['startPosition']);

          $fondsClientRow['endDate'] = $endDate;
          $fondsClientRow['endPosition'] = $this->formatAantalField($endAmount);
          $fondsClientRow['endPosition'] = $this->formatAantalField($fondsClientRow['endPosition']);

          $fondsClientRow['endKoers'] = $this->formatKoersField($fondsData['Koers']);
          $fondsClientRow['endValue'] = number_format((float) $endAmount * $fondsData['Koers'], 2, '.', '');

          $dataArray[$fondsClientRow['fonds_fonds']][] = $fondsClientRow;
//        }
      }
      $dataArray[$row['fonds_fonds']]['foot'] = $this->makeFoot($endDate, $endAmount);
      $dataArray[$row['fonds_fonds']]['koers'] = $this->makeVerloopKoers($fondsData);
      $dataArray[$row['fonds_fonds']]['value'] = $this->makeValue($endAmount, $fondsData);
      
      if ( $this->AE_Array->hasNumericKeys($dataArray[$row['fonds_fonds']]) == false && $startTrancactions['aantal'] == 0 && $endAmount == 0) {$dataArray[$row['fonds_fonds']] = null;}
      
      if ( is_array($dataArray[$row['fonds_fonds']]) && $this->AE_Array->hasNumericKeys($dataArray[$row['fonds_fonds']]) == false ) {
        $this->crmNaw = new Naw ();
//        debug($this->crmNaw->parseByid($client, array('naam', 'zoekveld')));
        $participateIn = $this->participanten->parseBySearch(array('crm_id' => $client, 'fonds_fonds' => $row['fonds_fonds']), null, null, -1);
        $AEParticipantsFonds = new ParticipantenFondsVerloop();
        foreach ( $participateIn as $participage ) {
          $totaalAantalPerRegistration = $AEParticipantsFonds->parseBySearch(
            array(
              'participanten_id' => $participage['id'], 
              '`datum` < "' . $this->AE_Datum->formToDb($endDate) . '"'
            ), 
            array(
              'sum(`aantal`) as totaalAantal'
            )
        );

          if ( $totaalAantalPerRegistration['totaalAantal'] > 0 ) {
            $dataArray[$row['fonds_fonds']]['csv'][] = array(
              'fonds'               => $row['fonds_fonds'],
              'registratieNummer'   => $participage['registration_number'],
              'crm'                 => $this->crmNaw->parseByid($client, array('naam', 'zoekveld'))
            );
          }
        }

      }
    }

    return $dataArray;
  }

  /**
   * 
   * @param type $client
   * @param type $fonds
   * @param type $registrationNumber
   * @param type $startDate
   * @param type $endDate
   * @param type $data
   * @return type
   */
  function oneFondsOneClient($client, $fonds, $registrationNumber, $startDate, $endDate, $data)
  {
    $betweenDates = new DB();
    if (!empty($registrationNumber))
    {
      /** get fonds amount between selected date * */
      $startTrancactions = $this->oneRegistrationOneClientStartPosition($registrationNumber, $fonds, $startDate, $endDate);
      $betweenDatesQuery = $this->oneRegistrationOneClientQuery($client, $registrationNumber, $startDate, $endDate, $this->AE_Array->formToSqlOrder($data['sort']));
      $endTrancactions = $this->oneRegistrationOneClientEndPosition($client, $registrationNumber, $startDate, $endDate);
    }
    else
    {
      /** get fonds amount between selected date * */
      $startTrancactions = $this->oneFondsOneClientStartPosition($client, $fonds, $startDate, $endDate);
      $betweenDatesQuery = $this->oneFondsOneClientQuery($client, $fonds, $startDate, $endDate, $this->AE_Array->formToSqlOrder($data['sort']));
      $endTrancactions = $this->oneFondsOneClientEndPosition($client, $fonds, $startDate, $endDate);
    }

    /** set header row as start date and total amount * */
    $dataArray['head'] = $this->makeHead($startDate, $startTrancactions); 
    
     $fondsData = $this->getFondskoers($fonds, $endDate);
    $endAmount = $startTrancactions['aantal'] + $endTrancactions['aantal'];

    $betweenTransaction = $this->getTransactionTypeList();

    $betweenDates->QRecords($betweenDatesQuery);
    while ($fondsClientRow = $betweenDates->nextRecord())
    {
//      $fondsClientRow['aantal'] = $this->formatAantal($fondsClientRow['aantal'], $fondsClientRow['transactietype']);
//      $fondsClientRow['aantal'] = $this->formatAantalField($fondsClientRow['aantal']);
      
      $fondsClientRow['datum'] = $this->AE_Datum->dbToForm($fondsClientRow['datum']);
      $fondsClientRow['waarde'] = $this->formatNumber($fondsClientRow['aantal'] * $fondsClientRow['koers'], $fondsClientRow['transactietype']);
      
      $fondsClientRow['startDate'] = $startDate;
      $fondsClientRow['startPosition'] = $this->formatAantalField($startTrancactions['aantal']);
        
      $fondsClientRow['endDate'] = $endDate;
      $fondsClientRow['endPosition'] = $this->formatAantalField($endAmount);

      $fondsClientRow['endKoers'] = $this->formatKoersField($fondsData['Koers']);
      $fondsClientRow['endValue'] = number_format((float) $endAmount * $fondsData['Koers'], 2, '.', '');
      
      $fondsClientRow['koers'] = $this->formatKoersField($fondsClientRow['koers']);
      $dataArray[] = $fondsClientRow;
    }

    $dataArray['foot'] = $this->makeFoot($endDate, $endAmount);
    $dataArray['koers'] = $this->makeVerloopKoers($fondsData);
    $dataArray['value'] = $this->makeValue($endAmount, $fondsData);

    if ( $this->AE_Array->hasNumericKeys($dataArray) == false && $startTrancactions['aantal'] == 0 && $endAmount == 0) {return array();}
    return $dataArray;
  }

  /**
   * allFondsAllClients
   * Get all fonds and all clients
   * 
   * @param type $startDate
   * @param type $endDate
   */
  function allFondsAllClients($startDate, $endDate, $data)
  {
    $dataArray = array();
    $orderField = $this->AE_Array->toSqlOrder(array('fonds_fonds' => ( isset ($data['sort']['fonds_fonds']) ? $data['sort']['fonds_fonds']['order'] : 'ASC') ));
    /** get used fonds **/
    $listFonds = $this->participanten->parseBySearch(null, null, 'GROUP BY `fonds_fonds` ' . $orderField, -1);
//    debug($listFonds);
//    $listFonds = 'SELECT * from `' . $this->participanten->data['table'] . '` GROUP BY `fonds_fonds';

    
//    debug($data['sort']);
//    $db = new DB();
//    $db->QRecords($listFonds);
//    while ($row = $db->nextRecord())
    foreach ( $listFonds as $row )
    {
      $startTrancactions = $this->oneFondsAllClientStartPosition($row['fonds_fonds'], $startDate);
      $endTrancactions = $this->oneFondsAllClientEndPosition($row['fonds_fonds'], $startDate, $endDate);
      
      /** get fonds amount between selected date **/
      $queryParticipanten = $this->AETemplate->parseFile('participanten/sql/allFondsAllClientsBetweenDatesFonds.sql', array(
        'participantenTable'             => $this->participanten->data['table'],
        'participantenFondsVerloopTable' => $this->participantenFondsVerloop->data['table'],
        'startDate'                      => $this->AE_Datum->formToDb($startDate),
        'endDate'                        => $this->AE_Datum->formToDb($endDate),
        'fonds'                          => $row['fonds_fonds'],
        'order'                          => $this->AE_Array->formToSqlOrder($data['sort']) 
      ));
//      debug($queryParticipanten);
      $fondsDB = new DB();
      $fondsDB->QRecords($queryParticipanten);

      /** set header row as start date and total amount * */
      $dataArray[$row['fonds_fonds']]['head'] = $this->makeHead($startDate, $startTrancactions);

      $fondsData = $this->getFondskoers($row['fonds_fonds'], $endDate);
      $endAmount = (double)$startTrancactions['aantal'] + (double)$endTrancactions['aantal'];

      $betweenTransaction = $this->getTransactionTypeList();
      while ($transactionRow = $fondsDB->nextRecord())
      {
//        if ( $transactionRow['aantal'] * $transactionRow['koers'] != 0 ) {
          $transactionRow['waarde'] = $this->formatNumber($transactionRow['aantal'] * $transactionRow['koers'], $transactionRow['transactietype']);

          $betweenTransaction[$transactionRow['transactietype']] += $transactionRow['aantal'];
          $transactionRow['aantal'] = $this->formatAantal($transactionRow['aantal'], $transactionRow['transactietype']);
  //        $transactionRow['aantal'] = $this->formatAantalField($transactionRow['aantal']);

          $transactionRow['datum'] = $this->AE_Datum->dbToForm($transactionRow['datum']);
  //        $transactionRow['koers'] = $this->formatKoersField($transactionRow['koers']);
          $transactionRow['startDate'] = $startDate;
          $transactionRow['endDate'] = $endDate;

          $transactionRow['startPosition'] = $startTrancactions['aantal'];
          $transactionRow['endPosition'] = $endTrancactions['aantal'];

          $transactionRow['endKoers'] = $fondsData['Koers'];//$this->formatKoersField($fondsData['Koers']);
          $transactionRow['endValue'] = $endAmount * $fondsData['Koers'];//number_format((float) $endAmount * $fondsData['Koers'], 2, '.', '');

          $dataArray[$row['fonds_fonds']][] = $transactionRow;
//        }
      }

      $dataArray[$row['fonds_fonds']]['foot'] = $this->makeFoot($endDate, $endAmount);
      $dataArray[$row['fonds_fonds']]['koers'] = $this->makeVerloopKoers($fondsData);
      $dataArray[$row['fonds_fonds']]['value'] = $this->makeValue($endAmount, $fondsData);
      
      if ( $this->AE_Array->hasNumericKeys($dataArray[$row['fonds_fonds']]) == false && $startTrancactions['aantal'] == 0 && $endAmount == 0) {unset($dataArray[$row['fonds_fonds']]);}
    }
    return $dataArray;
  }

  /**
   * Queries
   */

  /** get start position * */
  function oneRegistrationOneClientStartPosition($client, $registration, $startDate, $endDate)
  {
    $listBeforeDateQuery = $this->AETemplate->parseFile('participanten/sql/oneregistrationOneClientStartPosition.sql', array(
              'participantenTable'             => $this->participanten->data['table'],
              'participantenFondsVerloopTable' => $this->participantenFondsVerloop->data['table'],
              'startDate'                      => $this->AE_Datum->formToDb($startDate),
              'registration'                   => $registration,
              'crm_id'                         => $client
    ));
    $listBeforeDate = new DB();
    $listBeforeDate->QRecords($listBeforeDateQuery);
    return $listBeforeDate->nextRecord();
  }

  function oneRegistrationOneClientQuery($client, $registration, $startDate, $endDate, $order = null)
  {
    return $this->AETemplate->parseFile('participanten/sql/oneRegistrationOneClientBetweenDates.sql', array(
              'participantenTable'             => $this->participanten->data['table'],
              'participantenFondsVerloopTable' => $this->participantenFondsVerloop->data['table'],
              'startDate'                      => $this->AE_Datum->formToDb($startDate),
              'endDate'                        => $this->AE_Datum->formToDb($endDate),
              'registration'                   => $registration,
              'crm_id'                         => $client,
              'order'                          => $order
    ));
  }

  function oneRegistrationOneClientEndPosition($client, $registration, $startDate, $endDate)
  {
    $listBeforeDateQuery = $this->AETemplate->parseFile('participanten/sql/oneRegistrationOneClientEndPosition.sql', array(
              'participantenTable'             => $this->participanten->data['table'],
              'participantenFondsVerloopTable' => $this->participantenFondsVerloop->data['table'],
              'startDate'                      => $this->AE_Datum->formToDb($startDate),
              'endDate'                        => $this->AE_Datum->formToDb($endDate),
              'registration'                   => $registration,
              'crm_id'                         => $client
    ));
    $listBeforeDate = new DB();
    $listBeforeDate->QRecords($listBeforeDateQuery);
    return $listBeforeDate->nextRecord();
  }
  
  /** get positions **/
  
  
  /**
   * positionAllFondsOneClientResult
   * @param type $client
   * @param type $date
   * @return type
   */
  function positionAllFondsOneClientResult ($client, $date, $data = null) {
    $getPositionQuery = $this->AETemplate->parseFile('participanten/sql/positionAllFondsOneClient.sql', array(
      'participantenTable'                => $this->participanten->data['table'],
      'participantenFondsVerloopTable'    => $this->participantenFondsVerloop->data['table'],
      'date'                              => $this->AE_Datum->formToDb($date),
      'client'                            => $client,
      'order'                             => $this->AE_Array->formToSqlOrder($data['sort'])
    ));
//debug($getPositionQuery);
    $db = new DB();
    $db->QRecords($getPositionQuery);
    
    while ($rowData = $db->nextRecord()) {
//      debug($rowData);
      if ( $rowData['aantal'] != 0 ) {
        $rowData['fonds'] = $rowData['fonds_fonds'];
        $rowData['datum'] = $date;
        $fondsData = $this->getFondskoers($rowData['fonds_fonds'], $date);
        $returnData[] = $this->formatPositionRowData($rowData, $fondsData);
      }
    }
    return $returnData;
  }
  
  /**
   * positionOneFondsAllClientResult
   * @param type $fonds
   * @param type $date
   * @return type
   */
  function positionOneFondsAllClientResult($fonds, $date, $data) {
    $getPositionQuery = $this->AETemplate->parseFile('participanten/sql/positionOneFondsAllClient.sql', array(
      'participantenTable'              => $this->participanten->data['table'],
      'participantenFondsVerloopTable'  => $this->participantenFondsVerloop->data['table'],
      'date'                            => $this->AE_Datum->formToDb($date),
      'fonds'                           => $fonds,
      'order'                           => $this->AE_Array->formToSqlOrder($data['sort'])
    ));

    $db = new DB();
    $db->QRecords($getPositionQuery);
    $fondsData = $this->getFondskoers($fonds, $date);
    
    $total = 0;
    while ($rowData = $db->nextRecord()) {
      if ( $rowData['aantal'] != 0) {
        $rowData['datum'] = $date;
        $returnData[] = $this->formatPositionRowData($rowData, $fondsData);
        $total = $total + $rowData['aantal'];
      }
    }
    
    if ( empty ($returnData) ) {return array();}
    
    $totalValue = (float) $total * $fondsData['Koers'];
    /** add values to array for csv or pdf **/
    foreach ($returnData as $key => $data) {
      $returnData[$key]['fonds'] = $fonds;
      $returnData[$key]['total_aantal'] = $total;//$this->formatAantalField($total);
      $returnData[$key]['total_waarde'] = $totalValue;//$this->formatNumber($totalValue);//number_format((float) $total * $fondsData['Koers'], 2, '.', '');
      $returnData[$key]['total_koers'] = $fondsData['Koers'];//$this->formatKoersField($fondsData['Koers']);
    }
    
    $returnData['total'] = $this->makeTotal($total);
    $returnData['koers'] = $this->makePositieKoers($fondsData);
    $returnData['waarde'] = array(
      'registration_number' => '<strong>Waarde</strong>',
      'aantal'  => $totalValue//$this->formatNumber($totalValue)//number_format((float) $total * $fondsData['Koers'], 2, '.', '')
    );
    
    return $returnData;
  }
  
  function positionOneFondsOneClientResult ($client, $fonds, $date, $data = null) {
    $getPositionQuery = $this->AETemplate->parseFile('participanten/sql/positionOneFondsOneClient.sql', array(
      'participantenTable'                => $this->participanten->data['table'],
      'participantenFondsVerloopTable'    => $this->participantenFondsVerloop->data['table'],
      'date'                              => $this->AE_Datum->formToDb($date),
      'fonds'                             => $fonds,
      'client'                            => $client,
      'order'                           => $this->AE_Array->formToSqlOrder($data['sort'])
    ));
    $db = new DB();
    $db->QRecords($getPositionQuery);
    
    
    $totalAmount = 0;
    $totalValue = 0;
    $fondsData = $this->getFondskoers($fonds, $date);

    while ($rowData = $db->nextRecord()) {
      if ( $rowData['aantal'] != 0 ) {
        //EUR
        $rowData['currentValutaCourse'] = 1;
        if ($fondsData['Valuta'] !== 'EUR') {
          $currentValutaCourse = $this->getExchangeRate($fondsData['Valuta'], $this->AE_Datum->formToDb($date));
          $rowData['currentValutaCourse'] = $currentValutaCourse['Koers'];
        }
        $totalAmount += $rowData['aantal'];
        $rowData['fonds'] = $fonds;
        $rowData['aantal'] = $rowData['aantal'];//$this->formatAantalField($rowData['aantal']);
        $rowData['datum'] = $date;
        $rowData['waarde'] = $rowData['aantal'] * $fondsData['Koers'];//number_format($rowData['aantal'] * (float) $fondsData['Koers'], 2, '.', ''); //$this->formatNumber($rowData['aantal'] * (float) $fondsData['Koers'], $rowData['transactietype']);
        $rowData['koers'] = $fondsData['Koers'];//$this->formatKoersField($fondsData['Koers']);
        $rowData['Valuta'] = $fondsData['Valuta'];
        $returnData[] = $rowData;
      }
    }
    
    foreach ($returnData as $key => $values) {
      $returnData[$key]['total_aantal'] = $this->formatAantalField($totalAmount);//number_format((float) $totalAmount, 2, '.', '');
      $returnData[$key]['total_waarde'] = $totalAmount * $fondsData['Koers'];//number_format((float) $totalAmount * $fondsData['Koers'], 2, '.', '');
      $returnData[$key]['total_koers'] = $this->formatKoersField($fondsData['Koers']);        
    }
    
    return $returnData;
  }

  /** get start position * */

  /**
   * get start position oneFondsAllClientStartPosition
   * @param type $fonds
   * @param type $startDate
   * @param type $endDate
   * @return type
   */
  function oneFondsAllClientStartPosition($fonds, $startDate)
  {
    $listBeforeDateQuery = $this->AETemplate->parseFile('participanten/sql/oneFondsAllClientStartPosition.sql', array(
              'participantenTable'             => $this->participanten->data['table'],
              'participantenFondsVerloopTable' => $this->participantenFondsVerloop->data['table'],
              'startDate'                      => $this->AE_Datum->formToDb($startDate),
              'fonds'                          => $fonds,
    ));
    $listBeforeDate = new DB();
    $listBeforeDate->QRecords($listBeforeDateQuery);
    $returnData = $listBeforeDate->nextRecord();
    if (empty($returnData['aantal']) || $returnData['aantal'] < 1)
    {
      $returnData['aantal'] = 0;
    }
    return $returnData;
  }

  /**
   * get start position oneFondsOneClientStartPosition
   * @param type $client
   * @param type $fonds
   * @param type $startDate
   * @param type $endDate
   * @return type
   */
  function oneFondsOneClientStartPosition($client, $fonds, $startDate, $endDate)
  {
    $listBeforeDateQuery = $this->AETemplate->parseFile('participanten/sql/oneFondsOneClientStartPosition.sql', array(
              'participantenTable'             => $this->participanten->data['table'],
              'participantenFondsVerloopTable' => $this->participantenFondsVerloop->data['table'],
              'startDate'                      => $this->AE_Datum->formToDb($startDate),
              'fonds'                          => $fonds,
              'crm_id'                         => $client
    ));
    $listBeforeDate = new DB();
    $listBeforeDate->QRecords($listBeforeDateQuery);
    
    $returnData = $listBeforeDate->nextRecord();
    if (empty($returnData['aantal']) || $returnData['aantal'] < 1)
    {
      $returnData['aantal'] = 0;
    }
    return $returnData;
  }

  /**
   * get end position for oneFondsOneClientEndPosition
   * @param type $client
   * @param type $fonds
   * @param type $startDate
   * @param type $endDate
   * @return type
   */
  function oneFondsOneClientEndPosition($client, $fonds, $startDate, $endDate)
  {
    $listBeforeDateQuery = $this->AETemplate->parseFile('participanten/sql/oneFondsOneClientEndPosition.sql', array(
              'participantenTable'             => $this->participanten->data['table'],
              'participantenFondsVerloopTable' => $this->participantenFondsVerloop->data['table'],
              'startDate'                      => $this->AE_Datum->formToDb($startDate),
              'endDate'                        => $this->AE_Datum->formToDb($endDate),
              'fonds'                          => $fonds,
              'crm_id'                         => $client
    ));
    $listBeforeDate = new DB();
    $listBeforeDate->QRecords($listBeforeDateQuery);
    return $listBeforeDate->nextRecord();
  }

  /**
   * get end position for oneFondsAllClientEndPosition
   * @param type $fonds
   * @param type $startDate
   * @param type $endDate
   * @return type
   */
  function oneFondsAllClientEndPosition($fonds, $startDate, $endDate)
  {
    $listBeforeDateQuery = $this->AETemplate->parseFile('participanten/sql/oneFondsAllClientEndPosition.sql', array(
              'participantenTable'             => $this->participanten->data['table'],
              'participantenFondsVerloopTable' => $this->participantenFondsVerloop->data['table'],
              'startDate'                      => $this->AE_Datum->formToDb($startDate),
              'endDate'                        => $this->AE_Datum->formToDb($endDate),
              'fonds'                          => $fonds,
    ));
    $listBeforeDate = new DB();
    $listBeforeDate->QRecords($listBeforeDateQuery);
    return $listBeforeDate->nextRecord();
  }

  function oneFondsAllClientBetweenQuery($fonds, $startDate, $endDate, $order = null)
  {
    return $this->AETemplate->parseFile('participanten/sql/oneFondsAllClientBetweenDates.sql', array(
      'participantenTable'             => $this->participanten->data['table'],
      'participantenFondsVerloopTable' => $this->participantenFondsVerloop->data['table'],
      'startDate'                      => $this->AE_Datum->formToDb($startDate),
      'endDate'                        => $this->AE_Datum->formToDb($endDate),
      'fonds'                          => $fonds,
      'order'                          => $order
    ));
  }

  function oneFondsOneClientQuery($client, $fonds, $startDate, $endDate, $order = null)
  {
    return $this->AETemplate->parseFile('participanten/sql/oneFondsOneClientBetweenDates.sql', array(
      'participantenTable'             => $this->participanten->data['table'],
      'participantenFondsVerloopTable' => $this->participantenFondsVerloop->data['table'],
      'startDate'                      => $this->AE_Datum->formToDb($startDate),
      'endDate'                        => $this->AE_Datum->formToDb($endDate),
      'fonds'                          => $fonds,
      'crm_id'                         => $client,
      'order'                          => $order
    ));
  }

  /**
   * getFondskoers
   */
  function getFondskoers($fonds, $date)
  {
    $DB = new DB();
    $query = "SELECT Fondskoersen.Koers, Fondsen.Valuta, Fondsen.Fondseenheid, Fondskoersen.datum FROM Fondsen  LEFT JOIN Fondskoersen ON Fondsen.Fonds = Fondskoersen.Fonds AND Fondskoersen.datum <=  '" . $this->AE_Datum->formToDb($date) . "' WHERE Fondsen.Fonds = '" . $fonds . "' ORDER BY Fondskoersen.datum DESC LIMIT 1";
    $DB->SQL($query);
    $DB->Query();
    if ($DB->Records() > 0)
    {
      $data = $DB->NextRecord();
    }
    else
    {
      $DB = new DB();
      $DB->SQL("SELECT Fondskoersen.Koers, Fondsen.Valuta, Fondsen.Fondseenheid , Fondskoersen.datum FROM Fondskoersen LEFT JOIN Fondsen ON Fondskoersen.Fonds = Fondsen.Fonds WHERE Fondskoersen.Fonds = '" . $fonds . "' ORDER BY Fondskoersen.datum DESC LIMIT 1");
      $DB->Query();
      $data = $DB->NextRecord();
    }
    return $data;
  }
  
  function getExchangeRate ($valuta, $datum = null)
  {
    $DB = new DB();
    $query = "SELECT * 
      FROM Valutakoersen 
      WHERE Valutakoersen.Valuta = '" . $valuta . "' 
        AND Valutakoersen.datum <= '" . $datum . "' 
          ORDER BY Valutakoersen.datum DESC LIMIT 1";
    $DB->SQL($query);
    $DB->Query();
    return $DB->NextRecord();
  }

  /**
   * makeHead universal overview header
   * @param type $startDate
   * @param type $startTrancactions
   * @return type
   */
  function makeHead ($startDate, $startTrancactions) {
    return array(
      'datum'  => '<strong>Positie op: ' . $startDate . '</strong>',
      'aantal' => $startTrancactions['aantal']//$this->formatAantalField($startTrancactions['aantal'])//(strlen(substr(strrchr((float) $startTrancactions['aantal'], "."), 1)) > 2 ? (float) $startTrancactions['aantal'] : number_format((float) $startTrancactions['aantal'], 2, '.', '')) 
    );
  }
  
  function makeTotal ($total) {
    return array(
      'registration_number' => '<strong>Totaal</strong>',
      'aantal' => $total//$this->formatAantalField($total)//number_format((float) $total, 2, '.', '')
    );
  }
  
  /**
   * makeFoot universal overview foot
   * @param type $endDate
   * @param type $endAmount
   * @return type
   */
  function makeFoot ($endDate, $endAmount) {
    return array(
        'datum'  => '<strong>Positie op: ' . $endDate .'</strong>',
        'aantal' => $endAmount//$this->formatAantalField($endAmount)
      );
  }
  
  /**
   * makeVerloopKoers universal overview foot
   * @param type $fondsData
   * @return type
   */
  function makeVerloopKoers ($fondsData) {
    return array(
      'transactietype' => '<strong>Koers</strong>',
      'aantal'         => $fondsData['Koers']//$this->formatKoersField($fondsData['Koers'])
    );
  }
  
  /**
   * makeVerloopKoers universal overview foot
   * @param type $fondsData
   * @return type
   */
  function makePositieKoers ($fondsData) {
    return array(
      'registration_number' => '<strong>Koers</strong>',
      'aantal'              => $fondsData['Koers']//$this->formatKoersField($fondsData['Koers'])
    );
  }
  
  /**
   * makeValue
   * @param type $endAmount
   * @param type $fondsData
   * @return type
   */
  function makeValue ($endAmount, $fondsData) {
    return array(
        'transactietype' => '<strong>Waarde</strong>',
        'aantal'         => abs($endAmount * $fondsData['Koers'])//number_format((float) abs($endAmount * $fondsData['Koers']), 2, '.', ',')
      );
  }
  
  /**
   * formatKoersField
   * @param type $koers
   * @return type
   */
  function formatKoersField($koers) {
    $fieldDecimals = strlen(substr(strrchr((float) $koers, '.'), 1));
    if ( $fieldDecimals < 2 ) {
      $fieldDecimals = $this->minKoersDecimals;
    } elseif ( $fieldDecimals > 2 && $fieldDecimals > $this->maxKoersDecimals) {
      $fieldDecimals = $this->maxKoersDecimals;
    }
    return number_format((float) $koers, $fieldDecimals, '.', ',');
    
//    return (strlen(substr(strrchr((float) $koers, "."), 1)) > 2 ? number_format((float) $koers, strlen(substr(strrchr((float) $koers, "."), 1)), '.', ',') : number_format((float) $koers, 2, '.', ','));
  }
  
  function formatAantalField($aantal) {
    $fieldDecimals = strlen(substr(strrchr((float) $aantal, '.'), 1));
    if ( $fieldDecimals < 2 ) {
      $fieldDecimals = $this->minKoersDecimals;
    } elseif ( $fieldDecimals > 2 && $fieldDecimals > $this->maxKoersDecimals) {
      $fieldDecimals = $this->maxKoersDecimals;
    }
    return number_format((float) $aantal, $fieldDecimals, '.', ',');
    
    //return (strlen(substr(strrchr((float) $aantal, '.'), 1)) > 2 ? number_format((float) $aantal, strlen(substr(strrchr((float) $aantal, '.'), 1)), '.', ',') : number_format((float) $aantal, 2, '.', ','));
  }
  
  
  /**
   * Format the array of one position row
   * @param type $rowData
   * @param type $fondsData
   * @return type
   */
  function formatPositionRowData ($rowData, $fondsData) {
    $currentRow = $rowData; //save a copy for calculations
    $rowData['koers'] = $fondsData['Koers'];//$this->formatKoersField($fondsData['Koers']);
    $rowData['waarde'] = $currentRow['aantal'] * $fondsData['Koers'];//number_format(((float) $currentRow['aantal'] * (float) $currentRow['Koers']), 2, '.', ',');
//    $rowData['aantal'] = $this->formatAantalField($rowData['aantal']);
    return $rowData;
  }
  
  
  /**
   *Bulk import voor participanten
   * @var type 
   */
  var $error = false;
  var $AEParticipanten = null;
  function processRow ($row, $rowId, $AEParticipanten) {
    $AEDatum = new AE_datum();
    $row['transCode'] = strtolower(trim($row['transCode']));
    $rowClass = '';
    $row['error'] = '';
    $row['waarde'] = str_replace(' ', '', $row['waarde']);
    
    //make temp values for calculation
    $row['temp_aantal'] = str_replace(array('.', ','), array('', '.'), $row['aantal']);
    $row['temp_koers'] = str_replace(array('.', ','), array('', '.'), $row['koers']);
    $row['temp_waarde'] = str_replace(array('.', ','), array('', '.'), $row['waarde']);
    
    /** check if combination registration and fonds exist **/
    if ( $AEParticipanten->isDuplicate( new Participanten(), trim ($row['fonds']), trim ($row['registratienummer'])) == false ) {
//      $row['fonds'] = maakKnop('bullet_red.png') . ' ' . $row['fonds'];
//      $row['registratienummer'] = maakKnop('bullet_red.png') . ' ' . $row['registratienummer'];
      $rowClass = 'list_dataregel_rose';
      $row['error'] .=  '/1';
      $this->error = true;
    }


    if ( ! in_array($row['transCode'], array('b', 'a', 'v', 'd', 'l', 'bk', 'dv', 'h', 'u')) ) {
      $rowClass = 'list_dataregel_geel';
      $row['error'] .=  '/2';
      $this->error = true;
    }

    /** Controlleer of waarde correct is **/
    $reCalculatedValue = $this->formatNumber(abs($row['temp_aantal'] * $row['temp_koers']), strtoupper($row['transCode']));//($row['temp_aantal'] * $row['temp_koers']) - $row['waarde'];
    
    $tocheckValue = $this->formatNumber($row['temp_waarde']);
    if ( $row['temp_waarde'] < 0 ) {$tocheckValue = '-'.$tocheckValue;}

    if ( $row['transCode'] == 'u' ) {
      if ( $row['temp_waarde'] <= 0 ) {
        $rowClass = 'list_dataregel_oranje';
        $row['error'] .=  '/3';
        $this->error = true;
      }
    } else {
      if ( (int)$reCalculatedValue != $tocheckValue && round($reCalculatedValue, 2) != round($tocheckValue, 2) ) {
  //      $row['waarde'] = maakKnop('bullet_red.png') . ' ' . $row['waarde'];
        $rowClass = 'list_dataregel_oranje';
        $row['error'] .=  '/3';
        $this->error = true;
      }
    }
    /** Controlleer datum **/
    if( ! preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $AEDatum->formToDb($row['datum'])) || $AEDatum->formToDb($row['datum']) == '1970-01-01'){
//      $row['datum'] = maakKnop('bullet_red.png') . ' ' . $row['datum'];
      $rowClass = 'list_dataregel_groen';
      $row['error'] .=  '/4';
      $this->error = true;
    }
    //formatAantal
    
    /** Controlleer of de transcode overeenkomt met de aantal **/
    if ( $this->formatAantal($row['temp_aantal'], strtoupper($row['transCode'])) != $row['temp_aantal'] ) {
//      $row['transCode'] = maakKnop('bullet_red.png') . ' ' . $row['transCode'];
//      $row['aantal'] = maakKnop('bullet_red.png') . ' ' . $row['aantal'];
      $rowClass = 'list_dataregel_cyaan';
      $row['error'] .=  '/5';
      $this->error = true;
    }

    if ( $row['transCode'] == 'u' ) {
      if ( $row['temp_aantal'] != 0 ) {
        $rowClass = 'list_dataregel_cyaan';
        $row['error'] .=  '/5';
        $this->error = true;
      }
    }
    
    unset($row['temp_aantal']);
    unset($row['temp_koers']);
    unset($row['temp_waarde']);
    
    
    $td = '';
    foreach ( $row as $fieldName => $fieldValue ) {
      $td .= '<td data-name="' . $fieldName . '">' . $fieldValue . '</td>';
    }
    $td .= '<td><span class="btn btn-gray changeRow">'.drawButton('edit').' ' . vt('Wijzigen') . '</span> <span style="display:none;" class="btn btn-gray saveRow">'.drawButton('save').' ' . vt('Opslaan') . '</span></td>';

   return '<tr id="row-' . $rowId . '" class="' . $rowClass . '">' . $td . '</tr>'; 
  }
  
  
  
  
  
}
