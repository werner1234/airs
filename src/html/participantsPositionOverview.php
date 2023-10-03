<?php

/*
  AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
  Author              : $Author: rm $
  Laatste aanpassing  : $Date: 2017/12/08 18:23:43 $
  File Versie         : $Revision: 1.7 $

 */
include_once("wwwvars.php");
$AETemplate = new AE_template();

$data = array_merge($_POST, $_GET);
$AETemplate = new AE_template();
$AEParticipanten = new AE_Participants();
$AEParticipantsReport = new AE_ParticipantsReport();
$AENumbers = new AE_Numbers();

/**
 * if one fonds and one client is selected
 */
if (isset($data['type']) && $data['type'] === 'overviewCourse' && !empty($data['client_id']) && (!empty($data['Fonds']) || !empty($data['registration_number'])))
{
  $displayHtml = '';
  if (empty($data['Fonds']))
  {
    $data['Fonds'] = $AEParticipanten->getFondsOfId($data['registration_number']);
  }
  
  if ($data['viewType'] == 'pdf') {
    $data['rapportType'] = 'positie-client-fonds';
    $data['viewType'] = 'bulk_pdf';
    $data['DateEnd'] = $data['date'] ;
    $AEParticipantsReport->makePdfPositionOneFondsOneClient($data);
//    $AEParticipantsReport->makePdfBulk($data);
    exit();
  }
  

  $rows = $AEParticipanten->positionOneFondsOneClient($data['client_id'], $data['Fonds'], $data['date'], $data);

  if ($data['viewType'] != 'csv' && $data['viewType'] != 'xls')
  {
    if (count($rows) > 0)
    {
      $viewData = array(
        'overview_row',
        'Fonds_name' => $data['Fonds'],
        'date'       => $data['date']
      );

      foreach ($rows as $key => $value)
      {
        $value = formatPositionFields($value, $AENumbers); 
        $viewData['overview_row'] .= $AETemplate->parseBlockFromFile('participantenOverzichten/overviewPosition_row.html', $value);
      }
      $displayHtml .= $AETemplate->parseBlockFromFile('participantenOverzichten/overviewPosition_head.html', $viewData);
    }
    else
    {
      $displayHtml .= $AETemplate->parseBlockFromFile('participantenOverzichten/overviewPosition_head.html', array(
        'overview_row' => '<tr><td colspan=8>Er zijn geen gegevens gevonden om de rapportage te kunnen opbouwen.</td></tr>',
        'Fonds_name' => $data['Fonds'],
        'date'       => $data['date']
      ));
    }
  }
  elseif ($data['viewType'] == 'csv')
  {
    makeCsvPosition($rows, 'positionOneFondsOneClient', false);
  }
  elseif ($data['viewType'] == 'xls')
  {
    makeXlsPosition($rows, 'positionOneFondsOneClient', false);
  }

  if ($data['viewType'] == 'pdf') {
//    $AEParticipantsReport->makePdfPositionForClient($data['client_id'], $data['date'],$data);
//    $AEParticipantsReport->makePdfPosition($rows, 'positionOneFondsOneClient',$data);
  }
  else
  {
    echo $displayHtml;
  }
  exit;
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
  if ($data['viewType'] == 'pdf') {
    $data['rapportType'] = 'positie';
    $data['viewType'] = 'bulk_pdf';
    $data['DateEnd'] = $data['date'] ;
    $AEParticipantsReport->makePdfBulk($data);
    exit();
  }
  
  if ($data['viewType'] != 'csv' && $data['viewType'] != 'xls' && $data['viewType'] != 'pdf')
  {
    $rows = $AEParticipanten->positionAllFondsAllClients($data['date'], $data);
    foreach ($rows as $fonds => $dataRows)
    {
      if (count($dataRows) > 3)
      {
        $viewData = array(
          'overview_row',
          'Fonds_name' => $fonds,
          'date'       => $data['date']
        );
        foreach ($dataRows as $value)
        {
          $value = formatPositionFields($value, $AENumbers);
          $viewData['overview_row'] .= $AETemplate->parseBlockFromFile('participantenOverzichten/overviewPosition_row.html', $value);
        }
        echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewPosition_head.html', $viewData);
      }
    }

    if (empty($viewData))
    {
      echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewPosition_head.html', array(
        'overview_row' => '<tr><td colspan=8>Er zijn geen gegevens gevonden om de rapportage te kunnen opbouwen.</td></tr>',
        'Fonds_name' => $data['Fonds'],
        'date'       => $data['date']
      ));
    }
  }
  elseif ($data['viewType'] == 'csv')
  {
    $rows = $AEParticipanten->positionAllFondsAllClients($data['date'], $data);
    makeCsvPosition($rows, 'positionAllFondsAllClients');
  }
  elseif ($data['viewType'] == 'xls')
  {
    $rows = $AEParticipanten->positionAllFondsAllClients($data['date'], $data);
    makeXlsPosition($rows, 'positionAllFondsAllClients');
  }
}



/**
 * if one fonds is selected and no client
 */
if (
        isset($data['type']) &&
        $data['type'] === 'overviewCourse' &&
        empty($data['client_id']) &&
        empty($data['registration_number']) &&
        !empty($data['Fonds'])
)
{
//  debug('oneFondsAllClient');
  $rows = $AEParticipanten->positionOneFondsAllClient($data['Fonds'], $data['date'], $data);
  /** we expect more than 4 rows (head, foot, koers, value) * */
  
  if ($data['viewType'] == 'csv')
    {
      makeCsvPosition($rows, 'positionOneFondsAllClient');
    }
    elseif ($data['viewType'] == 'xls')
    {
      makeXlsPosition($rows, 'positionOneFondsAllClient');
    }
    elseif ($data['viewType'] == 'pdf') {
      $AEParticipantsReport->makePdfPositionOneFondsAllClient(array($data['Fonds'] => $rows), 'positionOneFondsAllClient',$data);
    }
  
  if (count($rows) > 3)
  {
    $viewData = array(
      'overview_row',
      'Fonds_name' => $data['Fonds'],
      'date'       => $data['date']
    );
    
    if ($data['viewType'] != 'csv' && $data['viewType'] != 'pdf')
    {

      foreach ($rows as $key => $value)
      {
        $value = formatPositionFields($value, $AENumbers); 
        $viewData['overview_row'] .= $AETemplate->parseBlockFromFile('participantenOverzichten/overviewPosition_row.html', $value);
      }

      echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewPosition_head.html', $viewData);
    }
  }
  else
  {
    echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewPosition_head.html', array(
      'overview_row' => '<tr><td colspan=8>Er zijn geen gegevens gevonden om de rapportage te kunnen opbouwen.</td></tr>',
      'Fonds_name' => $data['Fonds'],
      'date'       => $data['date']
    ));
  }
  exit;
}

/**
 * if only a client is selected
 */
if (
        isset($data['type']) &&
        $data['type'] === 'overviewCourse' &&
        !empty($data['client_id']) &&
        empty($data['Fonds']) &&
        empty($data['registration_number'])
)
{
  
  if ($data['viewType'] == 'pdf') {
    $data['rapportType'] = 'positie-client';
    $data['viewType'] = 'bulk_pdf';
    $data['DateEnd'] = $data['date'];
//    $AEParticipantsReport->makePdfPositionForClient($data['client_id'], $data['date'], $data);
    $AEParticipantsReport->makePdfBulk($data);
    exit();
  }
  
  
  $rows = $AEParticipanten->positionAllFondsOneClient($data['client_id'], $data['date'], $data);
  
  if ($data['viewType'] == 'csv')
  {
    makeCsvPosition($rows, 'positionAllFondsOneClient', false);
  }
  elseif ($data['viewType'] == 'xls')
  {
    makeXlsPosition($rows, 'positionAllFondsOneClient', false);
  }
  
  if ( count($rows) == 0 ) {
    echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewPosition_head.html', array(
      'overview_row' => '<tr><td colspan=8>Er zijn geen gegevens gevonden om de rapportage te kunnen opbouwen.</td></tr>',
      'Fonds_name' => $data['Fonds'],
      'date'       => $data['date']
    ));
  } elseif ($data['viewType'] != 'csv' && $data['viewType'] != 'pdf')
  {
    $viewData = array(
      'overview_row',
      'Fonds_name' => $fonds,
      'date'       => $data['date']
    );

    foreach ($rows as $dataRows)
    {
      $dataRows = formatPositionFields($dataRows, $AENumbers); 
      $viewData['overview_row'] .= '<tr>
          <td>' . $dataRows['registration_number'] . '</td>
          <td>' . $dataRows['fonds_fonds'] . '</td>
          <td style="text-align: right">' . $dataRows['aantal'] . '</td>
          <td style="text-align: right">' . $dataRows['koers'] . '</td>
          <td style="text-align: right">' . $dataRows['waarde'] . '</td>
        </tr>';
    }
    $viewData['client'] = $data['client'];
    echo $AETemplate->parseBlockFromFile('participantenOverzichten/overviewPosition_clean_head.html', $viewData);
  }
}

/**
 * Format induvidual fields
 * @param type $values
 * @param type $AENumbers
 * @return type
 */
function formatPositionFields($values, $AENumbers)
{
  /** if only 2 in an array it is an head or footer * */
  if (count($values) == 2)
  {
    switch ( strip_tags ($values['registration_number']) )
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
    $values['zoekveld'] = strlen($values['zoekveld']) > 60 ? substr($values['zoekveld'],0,60)."..." : $values['zoekveld'];
    $values['waarde'] = $AENumbers->viewFormat2Decimals($values['waarde']);
    $values['aantal'] = $AENumbers->viewFormatMinMaxDecimals($values['aantal']);
    $values['koers'] = $AENumbers->viewFormatMinMaxDecimals($values['koers']);

    $values['total_waarde'] = $AENumbers->viewFormat2Decimals($values['total_waarde']);
    $values['total_aantal'] = $AENumbers->viewFormatMinMaxDecimals($values['total_aantal']);
    $values['total_koers'] = $AENumbers->viewFormatMinMaxDecimals($values['total_koers']);
  }
  return $values;
}

function makePdfPosition($displayHtml, $fileName)
{
  require_once('../classes/AE_cls_html2fpdf.php');
  $pdf = new HTML2FPDF();
  $pdf->AddPage();
  if ($displayHtml)
  {
//    $css = $AETemplate->loadCss('workspace');
//    $css = '<style>
//    td {
//      background-color:red;
//    }
//
//    </style>';
    $pdf->WriteHTML($css . $displayHtml);
    $pdf->Output($fileName . '.pdf', 'D');
  }
  else
  {
    echo '<p>Pdf kon niet worden gemaakt.</p>';
  }
}

function makeCsvPosition($rows, $fileName, $totals = true, $crmData = false)
{
  $showCrmData = false;
  if ( isset($_POST['crmTonen']) && (int) $_POST['crmTonen'] === 1 ) {
    $showCrmData = true;
  }

  $addCrmFields = array('zoekveld');
  $addCrmHeaders = array();

  if ( $showCrmData === true ) {
    $addCrmFields = array('adres', 'pc', 'plaats', 'land', 'verzendAdres', 'verzendPc', 'verzendPlaats', 'verzendLand', 'tel1', 'email', 'zoekveld');
    $addCrmHeaders = array('adres', 'pc', 'plaats', 'land', 'verzendAdres', 'verzendPc', 'verzendPlaats', 'verzendLand', 'tel1', 'email');
  }


  if ($totals == true)
  {
    $csvOrder = array_merge(array('registration_number', 'fonds', 'zoekveld', 'datum', 'aantal', 'koers', 'waarde', 'total_aantal', 'total_koers', 'total_waarde'), $addCrmFields);
  }
  else
  {
    $csvOrder = array_merge(array('registration_number', 'fonds', 'zoekveld', 'datum', 'aantal', 'koers', 'waarde'), $addCrmFields);
  }
  $output = fopen("php://output", 'w') or die("Can't open php://output");
  header("Content-Type:application/csv");
  header("Content-Disposition:attachment;filename=" . $fileName . ".csv");


  if ($totals == true)
  {
    fputcsv($output, array_merge(array('Registratienummer', 'Fonds', 'Zoekveld', 'Datum', 'Aantal', 'Koers', 'Waarde', 'Totaal aantal', 'Eindkoers', 'Totaal waarde'), $addCrmHeaders));
  }
  else
  {
    fputcsv($output, array_merge(array('Registratienummer', 'Fonds', 'Zoekveld', 'Datum', 'Aantal', 'Koers', 'Waarde'), $addCrmHeaders));
  }
  foreach ($rows as $fonds => $values)
  {
    if (!is_numeric($fonds))
    {
      foreach ($values as $type => $positionData)
      {
        if (is_numeric($type))
        {
          $crmData = new Naw();
          $crmData = $crmData->parseByArray('first', array(
            'fields' => $addCrmFields,
            'conditions' => array(
              'id' => $positionData['crm_id']
            )
          ));

          if ( isset ($crmData[0]) && ! empty ($crmData[0]) ) {
            $positionData = array_merge($positionData, $crmData[0]);
          }

          $requeredItems = array_intersect_key($positionData, array_flip($csvOrder));
          $requeredItems = array_merge(array_flip($csvOrder), $requeredItems);
          fputcsv($output, $requeredItems);
        }
      }
    }
    else
    {
      $crmData = new Naw();
      $crmData = $crmData->parseByArray('first', array(
        'fields' => $addCrmFields,
        'conditions' => array(
          'id' => $values['crm_id']
        )
      ));

      if ( isset ($crmData[0]) && ! empty ($crmData[0]) ) {
        $values = array_merge($values, $crmData[0]);
      }

      $requeredItems = array_intersect_key($values, array_flip($csvOrder));
      $requeredItems = array_merge(array_flip($csvOrder), $requeredItems);
      fputcsv($output, $requeredItems);
    }
  }
  fclose($output) or die("Can't close php://output");
}

function makeXlsPosition($rows, $fileName, $totals = true)
{
  $showCrmData = false;
  if ( isset($_POST['crmTonen']) && (int) $_POST['crmTonen'] === 1 ) {
    $showCrmData = true;
  }

  $addCrmFields = array('zoekveld');
  $addCrmHeaders = array();

  if ( $showCrmData === true ) {
    $addCrmFields = array('adres', 'pc', 'plaats', 'land', 'verzendAdres', 'verzendPc', 'verzendPlaats', 'verzendLand', 'tel1', 'email', 'zoekveld');
    $addCrmHeaders = array('adres', 'pc', 'plaats', 'land', 'verzendAdres', 'verzendPc', 'verzendPlaats', 'verzendLand', 'tel1', 'email');
  }


  include_once("AE_cls_xls.php");
  $xls = new AE_xls();

  if ($totals == true)
  {
    $csvOrder = array_merge(array('registration_number', 'fonds', 'zoekveld', 'datum', 'aantal', 'koers', 'waarde', 'total_aantal', 'total_koers', 'total_waarde'), $addCrmFields);
  }
  else
  {
    $csvOrder = array_merge(array('registration_number', 'fonds', 'zoekveld', 'datum', 'aantal', 'koers', 'waarde'), $addCrmFields);
  }


  if ($totals == true)
  {
    $xlsData[] = array_merge(array('Registratienummer', 'Fonds', 'Zoekveld', 'Datum', 'Aantal', 'Koers', 'Waarde', 'Totaal aantal', 'Eindkoers', 'Totaal waarde'), $addCrmHeaders);
  }
  else
  {
    $xlsData[] = array_merge(array('Registratienummer', 'Fonds', 'Zoekveld', 'Datum', 'Aantal', 'Koers', 'Waarde'), $addCrmHeaders);
  }

  foreach ($rows as $fonds => $values)
  {
    if (!is_numeric($fonds))
    {
      foreach ($values as $type => $positionData)
      {
        if (is_numeric($type))
        {
          $crmData = new Naw();
          $crmData = $crmData->parseByArray('first', array(
            'fields' => $addCrmFields,
            'conditions' => array(
              'id' => $positionData['crm_id']
            )
          ));

          if ( isset ($crmData[0]) && ! empty ($crmData[0]) ) {
            $positionData = array_merge($positionData, $crmData[0]);
          }
          $requeredItems = array_intersect_key($positionData, array_flip($csvOrder));
          $requeredItems = array_merge(array_flip($csvOrder), $requeredItems);

          $requeredItems['waarde'] = sprintf ("%f", $requeredItems['waarde']);
          $xlsData[] = array_values($requeredItems);
        }
      }
    }
    else
    {
      $crmData = new Naw();
      $crmData = $crmData->parseByArray('first', array(
        'fields' => $addCrmFields,
        'conditions' => array(
          'id' => $values['crm_id']
        )
      ));

      if ( isset ($crmData[0]) && ! empty ($crmData[0]) ) {
        $values = array_merge($values, $crmData[0]);
      }

      $requeredItems = array_intersect_key($values, array_flip($csvOrder));
      $requeredItems = array_merge(array_flip($csvOrder), $requeredItems);

      $requeredItems['waarde'] = sprintf ("%f", $requeredItems['waarde']);
      $xlsData[] = array_values($requeredItems);
    }
  }

  $xls->setData($xlsData);
  $xls->OutputXls($filename=$fileName.'.xls');
}
