<?php

/*
  AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
  Author              : $Author: rm $
  Laatste aanpassing  : $Date: 2017/12/08 18:23:43 $
  File Versie         : $Revision: 1.7 $

 */
include_once("wwwvars.php");


$data = array_merge($_POST, $_GET);



if ( isset($data['sort']) ) {
//  debug($data);
  
}

$AETemplate = new AE_template();
$AEParticipanten = new AE_Participants();
$AEParticipantsReport = new AE_ParticipantsReport();
$AENumbers = new AE_Numbers();
$AEArray = new AE_Array();
$crmNaw = new Naw();
$minNumOfRecords = 4;//how many rows do we need to display (start position, end position, koers, waarde)



if ( $data['viewType'] === 'bulk_pdf') {
  $client = null;
  $client['crm_id'] = $data['client_id'];
//  debug($client);
  $rows[$client['crm_id']]['CRM'] = $crmNaw->parseById($client['crm_id']);
  $rows[$client['crm_id']]['participate'] = $AEParticipanten->getParticipant($client['crm_id']);
  $rows[$client['crm_id']]['rows'] = $AEParticipanten->allFondsOneClient($client['crm_id'], $data['DateStart'], $data['DateEnd']);
  $AEParticipantsReport->makePdfBulk($rows, 'naam',$data);
  exit();
}

/**
 * if only a client is selected
 */
if (
  isset($data['type']) &&
  $data['type'] === 'overviewCourse' &&
  ! empty($data['client_id']) &&
  empty($data['Fonds']) &&
  empty($data['registration_number'])
)
{
  $rows = $AEParticipanten->allFondsOneClient($data['client_id'], $data['DateStart'], $data['DateEnd'], $data);
  if ($data['viewType'] != 'csv' && $data['viewType'] != 'xls' && $data['viewType'] != 'pdf')
  {
    
    if ( $AEArray->is_null( array_unique (array_values($rows)) ) === true) {
        echo '<table><tr><td colspan=8><p><br />Er zijn geen gegevens gevonden om de rapportage te kunnen opbouwen.</p></td></tr></table>';
    }
    
    foreach ($rows as $fonds => $dataRows)
    {
      if (count($dataRows) >= $minNumOfRecords)
      {
        $viewData = array(
          'overview_row',
          'Fonds_name' => $fonds
        );
        
        foreach ($dataRows as $key => $value)
        {
          if ( $key === 'csv') {break;}
          $value = formatVerloopFields($value, $AENumbers, $AEParticipanten);
          $viewData['overview_row'] .= $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourse_row.html', $value);
        }
        echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourse_head.html', $viewData);
      }
    }
  }elseif ($data['viewType'] == 'csv')
  {
    makeCsvOverview($rows, 'allFondsOneClient');
  }
  elseif ($data['viewType'] == 'xls')
  {
    makeXlsOverview($rows, 'allFondsOneClient');
  }
  elseif ($data['viewType'] == 'pdf') {
    $AEParticipantsReport->makePdfCourse($rows, 'allFondsOneClient',$data);
  }
}

/**
 * if no fonds and no client is selected
 */
if (
        isset($data['type']) &&
        $data['type'] === 'overviewCourse' &&
        empty($data['client_id']) &&
        (empty($data['Fonds']) && empty($data['registration_number']))
)
{
  $rows = $AEParticipanten->allFondsAllClients($data['DateStart'], $data['DateEnd'], $data);
  
  if ($data['viewType'] != 'xls' && $data['viewType'] != 'csv' && $data['viewType'] != 'pdf')
  {
    if ( empty ($rows) ) {
      echo '<table><tr><td colspan=8><p><br />Er zijn geen gegevens gevonden om de rapportage te kunnen opbouwen.</p></td></tr></table>';
    }
    foreach ($rows as $fonds => $dataRows)
    {
      
      if (count($dataRows) >= $minNumOfRecords)
      {
        $viewData = array(
          'overview_row',
          'Fonds_name' => $fonds
        );
        foreach ($dataRows as $key => $value)
        {
          $value = formatVerloopFields($value, $AENumbers, $AEParticipanten);
          $viewData['overview_row'] .= $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourse_row.html', $value);
        }
        echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourse_head.html', $viewData);
      }
    }
  }elseif ($data['viewType'] == 'csv')
  {
    makeCsvOverview($rows, 'allFondsAllClients');
  }
  elseif ($data['viewType'] == 'xls')
  {
    makeXlsOverview($rows, 'allFondsAllClients');
  }
  elseif ($data['viewType'] == 'pdf')
  {
    $AEParticipantsReport->makePdfCourse($rows, 'overviewOneFondsOneClient',$data);
//     makePdfPosition($rows, 'overviewOneFondsOneClient',$data);
  }elseif ( empty ($rows) ) {
    echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourse_head.html', array(
      'overview_row' => '<tr><td colspan=8>Er zijn geen gegevens gevonden om de rapportage te kunnen opbouwen.</td></tr>'
    ));
  }
}





/**
 * if one fonds and one client is selected
 */
if (isset($data['type']) && $data['type'] === 'overviewCourse' && !empty($data['client_id']) && (!empty($data['Fonds']) || !empty($data['registration_number'])))
{
//  debug('oneFondsOneClient');
  if (empty($data['Fonds']))
  {
    $data['Fonds'] = $AEParticipanten->getFondsOfId($data['registration_number']);
  }

  $rows = $AEParticipanten->oneFondsOneClient($data['client_id'], $data['Fonds'], $data['registration_number'], $data['DateStart'], $data['DateEnd'], $data);
  if ($data['viewType'] == 'csv') {
    makeCsvOverview(array($data['Fonds'] => $rows), 'oneFondsOneClient');
  }
  elseif ($data['viewType'] == 'xls')
  {
    makeXlsOverview($rows, 'oneFondsOneClient');
  }
  elseif ($data['viewType'] == 'pdf') {
    $AEParticipantsReport->makePdfCourse(array($data['Fonds'] => $rows), 'oneFondsOneClient',$data);
  }

  if ( count ($rows) > 3 ) {
    $viewData = array(
      'overview_row',
      'Fonds_name' => $data['Fonds']
    );

    if ($data['viewType'] != 'csv' && $data['viewType'] != 'pdf')
    {
      foreach ($rows as $key => $value)
      {
        $value = formatVerloopFields($value, $AENumbers, $AEParticipanten);
        $viewData['overview_row'] .= $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourse_row.html', $value);
      }

      echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourse_head.html', $viewData);
    }
    
  } else {
    echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourse_head.html', array(
      'Fonds_name' => $data['Fonds'],
      'overview_row' => '<tr><td colspan=8>Er zijn geen gegevens gevonden om de rapportage te kunnen opbouwen.</td></tr>'
    ));
  }
  exit;
}

/**
 * if one fonds is selected and no client
 */
if (
        isset($data['type']) &&
        $data['type'] === 'overviewCourse' &&
        empty($data['client_id']) &&
        empty($data['registration_number']) &&
       ! empty($data['Fonds'])
){
//  debug('oneFondsAllClient');
  $rows = $AEParticipanten->oneFondsAllClient($data['Fonds'], $data['DateStart'], $data['DateEnd'], $data);

  /** we expect more than 4 rows (head, foot, koers, value) **/
  
  
  if ($data['viewType'] == 'csv')
  {
    makeCsvOverview(array($data['Fonds'] => $rows), 'oneFondsAllClient');
  }
  elseif ($data['viewType'] == 'xls')
  {
    makeXlsOverview($rows, 'oneFondsAllClient');
  }
  elseif ($data['viewType'] == 'pdf') {
    $AEParticipantsReport->makePdfCourse(array($data['Fonds'] => $rows), 'oneFondsAllClient',$data);
  }
  
  
  if ( count ($rows) > 3 ) {
    $viewData = array(
      'overview_row',
      'Fonds_name' => $data['Fonds']
    );

    if ($data['viewType'] != 'csv' && $data['viewType'] != 'pdf')
    {
      foreach ($rows as $key => $value)
      {
        $value = formatVerloopFields($value, $AENumbers, $AEParticipanten);
        $viewData['overview_row'] .= $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourse_row.html', $value);
      }
      echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourse_head.html', $viewData);
    }
  } else {
    echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourse_head.html', array(
      'overview_row'  => '<tr><td colspan=8>Er zijn geen gegevens gevonden om de rapportage te kunnen opbouwen.</td></tr>',
      'Fonds_name'    => $data['Fonds']
    ));
  }
  exit;
}



/**
 * Format induvidual fields
 * @param type $values
 * @param type $AENumbers
 * @return type
 */
function formatVerloopFields($values, $AENumbers, $AEParticipanten)
{
//  debug($values);
  /** if only 2 in an array it is an head or footer * */
  if (count($values) == 2)
  {
    if ( strpos($values['datum'],'Positie op') !== false ) {
      $values['aantal'] = $AENumbers->viewFormatMinMaxDecimals($values['aantal']);
    }
    
    switch ( strip_tags ($values['transactietype']) )
    {
      case 'Totaal':
        $values['aantal'] = $AENumbers->viewFormatMinMaxDecimals($values['aantal']);
        break;
      case 'Koers':
        $values['aantal'] = $AENumbers->viewFormatMinMaxDecimals($values['aantal']);
        break;
      case 'Waarde':
        $values['aantal'] = $AENumbers->viewFormat2Decimals($values['aantal']);
        break;
    }
  }
  else
  {
    $values['zoekveld'] = strlen($values['zoekveld']) > 32 ? substr($values['zoekveld'],0,32)."..." : $values['zoekveld'];
    $values['waarde'] = $AENumbers->viewFormat2Decimals($values['waarde']);
    $values['aantal'] = $AENumbers->viewFormatMinMaxDecimals($values['aantal']);
    $values['koers'] = $AENumbers->viewFormatMinMaxDecimals($values['koers']);

    $values['total_waarde'] = $AENumbers->viewFormat2Decimals($values['total_waarde']);
    $values['total_aantal'] = $AENumbers->viewFormatMinMaxDecimals($values['total_aantal']);
    $values['total_koers'] = $AENumbers->viewFormatMinMaxDecimals($values['total_koers']);
//    $values['transactietype'] = $AEParticipanten->transactionTypes[$values['transactietype']];
  }

//  debug($values);
  return $values;
}


function makeXlsOverview($rows, $fileName)
{
  include_once("AE_cls_xls.php");
  $xls = new AE_xls();


  $csvOrder = array('datum', 'registration_number', 'fonds_fonds', 'zoekveld', 'transactietype', 'aantal', 'koers', 'waarde', 'omschrijving', 'startDate', 'startPosition', 'endDate', 'endPosition', 'endKoers', 'endValue');
  $xlsData[] = array(
    'Datum',
    'Registratienummer',
      'Fonds',
      'Zoekveld',
      'Transactietype',
      'Aantal',
      'Koers',
      'Waarde',
      'Omschrijving',
      'Start datum',
      'start positie',
      'Eind datum',
      'Eind positie',
      'Eind koers',
      'Totaal waarde'
  );

  foreach ($rows as $fonds => $values)
  {
    if ( ! is_numeric ($fonds) )
    {
      $temp = null;
      foreach ($values as $type => $overviewData)
      {
        if (is_numeric($type))
        {
          $requeredItems = null;
          $requeredItems = array_intersect_key($overviewData, array_flip($csvOrder));
          $requeredItems = array_merge(array_flip($csvOrder), $requeredItems);

          $requeredItems['aantal'] = sprintf ("%f", $requeredItems['aantal']);
          $requeredItems['waarde'] = sprintf ("%f", $requeredItems['waarde']);
          $xlsData[] = array_values($requeredItems);
        }
      }
      if ( isset ($values['csv']) ) {
        foreach ( $values['csv'] as $csvData ) {
          $xlsData[] = array(
            '',
            $csvData['registratieNummer'],
            $csvData['fonds'],
            $csvData['crm']['zoekveld'],
            '',
            '',
            '',
            '',
            '',
            strip_tags(str_replace('Positie op: ','',$values['head']['datum'])),
            $values['head']['aantal'],
            strip_tags(str_replace('Positie op: ','',$values['foot']['datum'])),
            $values['foot']['aantal'],
            $values['koers']['aantal'],
            $values['value']['aantal'],
            '',
            '',
            '',
            '',
            ''
          );
        }
      }
    }

    if ( is_numeric ($fonds) )
    {
      $requeredItems = array_intersect_key($values, array_flip($csvOrder));
      $requeredItems = array_merge(array_flip($csvOrder), $requeredItems);
      $xlsData[] = array_values($requeredItems);
    }
  }

  $xls->setData($xlsData);
  $xls->OutputXls($filename=$fileName.'.xls');
}


function makeCsvOverview($rows, $fileName)
{
  $csvOrder = array('datum', 'registration_number', 'fonds_fonds', 'zoekveld', 'transactietype', 'aantal', 'koers', 'waarde', 'omschrijving', 'startDate', 'startPosition', 'endDate', 'endPosition', 'endKoers', 'endValue');
  $output = fopen("php://output", 'w') or die("Can't open php://output");
  header("Content-Type:application/csv");
  header("Content-Disposition:attachment;filename=" . $fileName . ".csv");
  fputcsv($output, array('Datum', 'Registratienummer', 'Fonds', 'Zoekveld', 'Transactietype', 'Aantal', 'Koers', 'Waarde', 'Omschrijving', 'Start datum', 'start positie', 'Eind datum', 'Eind positie', 'Eind koers', 'Totaal waarde'));

  $requeredItems = null;
  
  $temp = null;
  foreach ($rows as $fonds => $values)
  {
    if ( ! is_numeric ($fonds) )
    {
      $temp = null;
      foreach ($values as $type => $overviewData)
      {
        if (is_numeric($type))
        {
          $requeredItems = null;
          $requeredItems = array_intersect_key($overviewData, array_flip($csvOrder));
          $requeredItems = array_merge(array_flip($csvOrder), $requeredItems);

          $requeredItems['aantal'] = sprintf ("%f", $requeredItems['aantal']);
          $requeredItems['waarde'] = sprintf ("%f", $requeredItems['waarde']);

          fputcsv($output, $requeredItems);
        }
      }
//      if ( count($values) > 2 && (bool)count(array_filter(array_keys($values), 'is_numeric')) === false ) {
      if ( isset ($values['csv']) ) {
        foreach ( $values['csv'] as $csvData ) {
          $dataArray = array (
            'datum' => '',
            'registration_number' => $csvData['registratieNummer'],
            'fonds_fonds' => $csvData['fonds'],
            'zoekveld' => $csvData['crm']['zoekveld'],
            'transactietype' => '',
            'aantal' => null,
            'koers' => null,
            'waarde' => null,
            'omschrijving' => null,
            'startDate' => strip_tags(str_replace('Positie op: ','',$values['head']['datum'])),
            'startPosition' => $values['head']['aantal'],
            'endDate' => strip_tags(str_replace('Positie op: ','',$values['foot']['datum'])),
            'endPosition' => $values['foot']['aantal'],
            'endKoers' => $values['koers']['aantal'],
            'endValue' => $values['value']['aantal']
          );
           fputcsv($output, $dataArray);
        }
      }
    }
    
    
    if ( is_numeric ($fonds) )
    {
      $requeredItems = array_intersect_key($values, array_flip($csvOrder));
      $requeredItems = array_merge(array_flip($csvOrder), $requeredItems);
      fputcsv($output, $requeredItems);
    }
  }
  
  fclose($output) or die("Can't close php://output");
}
