<?php

/*
  AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
  Author              : $Author: rm $
  Laatste aanpassing  : $Date: 2017/12/15 15:37:47 $
  File Versie         : $Revision: 1.3 $

 */
include_once("wwwvars.php");
$AEMessage = new AE_Message();
$AEParticipanten = new AE_Participants();
$AETemplate = new AE_template();

$data = array_merge($_GET, $_POST);/** merge data * */
$data['action'] = 'edit';
$action = $data['action'];
$clearDataButton = '';

$participantenFondsVerloop = new ParticipantenFondsVerloop();

/** expected fields **/
  $expectedHeaders = array(
    'registratienummer',
    'fonds',
    'datum',
    'transCode',
    'aantal',
    'koers',
    'waarde',
    'omschrijving',
  );
//  error_reporting(E_ALL);
//ini_set('display_errors', 1);
$example = '';

if ( isset ($data['clearData']) ) {
  unset($_SESSION['tempParticipantsImport']);
  $AEMessage->setFlash(vt('Regels zijn gewist.'), 'info');
  header("Location: participants_bulkImportMutations.php");
  exit();
}
//debug($data);
if ( $data['importRows'] == 'true' && $_SESSION['tempParticipantsImport'] ) {
//  debug($participantenFondsVerloop->data);
  foreach ( $_SESSION['tempParticipantsImport'] as $rowId => $importData ) {
    /** check the row the same way we did before **/
    $AEParticipanten->processRow($importData, $rowId, $AEParticipanten);
    $AEDatum = new AE_datum();
    $importData['datum'] = $AEDatum->formToDb($importData['datum']);
    $row['transCode'] = trim($row['transCode']);
    $importData['transCode'] = strtoupper($importData['transCode']);
    
    //make  values for sql
    $importData['aantal'] = str_replace(array('.', ','), array('', '.'), $importData['aantal']);
    $importData['koers'] = str_replace(array('.', ','), array('', '.'), $importData['koers']);

    if ( $importData['transCode'] == 'U') {
      $importData['waarde'] = str_replace(array('.', ','), array('', '.'), $importData['waarde']);
      $importData['koers'] = 0;
      $importData['aantal'] = 0;
    } else {
      $importData['waarde'] = null;
    }
    /** create insert statement **/
//    debug($importData);
    $participant = $AEParticipanten->isDuplicate( new Participanten(), trim ($importData['fonds']), trim ($importData['registratienummer']));
    $AETemplate = new AE_template();
    $newParticipantSql[] = $AETemplate->parseBlockFromFile('participantenBulkImport/sql/newParticipantRow.sql', array_merge(
      array (
        'table'               => $participantenFondsVerloop->data['table'],
        'participanten_id'    => $participant['id'],
        'add_user'            => $_SESSION['usersession']['gebruiker']['Gebruiker'],
        'add_date'            => date('Y-m-d H:i:s')
      ),
      $importData
    ));
  }
  /** double check for no errors then save rows one by one **/
  if ($AEParticipanten->error != true ) {
    $db = new DB();
    foreach ( $newParticipantSql as $insertQuery ) {
      $db->executeQuery($insertQuery);
    }
    unset($_SESSION['tempParticipantsImport']);
    $AEMessage->setMessage(vt('Uw invoer is geimporteerd.'), 'success');
  } else {
    $AEMessage->setMessage(vt('Uw invoer komt niet overeen met de verwachten gegevens.'), 'error');
  }
  
  unset($_GET['importRows']);
}


if ( ! isset ($data['saveRow'])) {
  
  
  if ( $_FILES['fileToUpload']['name'] ) {
    $all_rows = array();
    $header = null;

//    $counter = 0;
    if(($handle = fopen($_FILES['fileToUpload']['tmp_name'], 'r')) !== FALSE) {
      
      
      $tr = '';
      while ($row = fgetcsv($handle, null , ';')) {
        if ($header === null) {
          $requeredItems = array_intersect_key(array_flip(array_map('strtolower', $row)), array_flip(array_map('strtolower', $expectedHeaders)));
          if ( array_map('strtolower', $expectedHeaders) != array_keys($requeredItems) ) {
            $AEMessage->setFlash(vt('Uw csv bestand komt niet overeen met de verwachten gegevens.'), 'error');
            header("Location: participants_bulkImportMutations.php");
            exit();
          }
          $header = $row;
//          $th = '<tr class="list_kopregel"><th>' . implode("</th><th>",$expectedHeaders) . '</th></tr>';
          continue;
        }
        $currentRow = array_combine($header, $row);
//        $tr .= $AEParticipanten->processRow($currentRow,$counter , $AEParticipanten);//'<tr class="' . $rowClass . '"><td>' . implode("</td><td>",$row) . '</td></tr>';
//        $counter++;
        $all_rows[] = $currentRow;
      }

    }
    $AEMessage->setFlash(vt('Uw csv is geimporteerd.'), 'success');
    $_SESSION['tempParticipantsImport'] = $all_rows;
    
    header("Location: participants_bulkImportMutations.php");
    exit;

  } elseif ( isset ($_SESSION['tempParticipantsImport']) && ! empty ($_SESSION['tempParticipantsImport']) ) {
//    $rowClass = '';
    $tr = null;
    foreach ( $_SESSION['tempParticipantsImport'] as $rowId => $importData ) {
      $tr .= $AEParticipanten->processRow($importData, $rowId, $AEParticipanten);
    }

    $th = '<tr  class="list_kopregel"><th>' . implode("</th><th>",$expectedHeaders) . '</th><th>' . vt('Error(s)') . '</th><th></th></tr>';
    $clearDataButton = '<form><button type="submit" value="true" name="clearData" class="btn btn-gray">'.maakKnop('delete.png').' ' . vt('Gegevens wissen') . '</button></form>';

  } else {
    /**
     * example code
     */
//    $example = '<br /><strong>Voorbeeldregel</strong>';
    $th = '';//<tr class="list_kopregel">
//      <th>registratienummer</th>
//      <th>fonds</th>
//      <th>datum</th>
//      <th>aantal</th>
//      <th>koers</th>
//      <th>waarde</th>
//      <th>omschrijving</th>
//    <tr>';
    $tr = '';
//    <tr>
//      <td>Reg1102</td>
//      <td>Fonds naam</td>
//      <td>22-11-2014</td>
//      <td>2500</td>
//      <td>11.50</td>
//      <td>V</td>
//      <td>28750</td>
//    </tr>
//  ';
  }
//  
  $overviewTable = '<table id="dataTable" class="list_tabel table"><thead>' . $th . '</thead><tbody>' . $tr . '</tbody></table>';
  
} else {
  $rowId = str_replace('row-', '', $data['rowId']);
  unset($data['rowId']);
  unset($data['action']);
  unset($data['saveRow']);
  $requeredItems = array_intersect_key(array_flip(array_map('strtolower', array_keys($data))), array_flip(array_map('strtolower', $expectedHeaders)));
  
  if ( array_map('strtolower', $expectedHeaders) == array_keys($requeredItems) ) {
    /** data is ok lets store it **/
    $diff = array_diff($data, $_SESSION['tempParticipantsImport'][$rowId]);
    $_SESSION['tempParticipantsImport'][$rowId] = array_merge($_SESSION['tempParticipantsImport'][$rowId], $diff);
    echo $AEParticipanten->processRow($_SESSION['tempParticipantsImport'][$rowId], $rowId, $AEParticipanten);
  }
  exit();
}

if ($AEParticipanten->error === true ) {
  $AEMessage->setMessage(vt('Let op: er zitten fouten in de invoer regels, u dient deze op te lossen voordat u verder kunt.'), 'error');
  $importDataButton = '<form><button type="submit" value="true" name="reload" class="btn btn-gray saveRow">'.maakKnop('refresh.png').' ' . vt('Hercontroleren') . '</button></form>';
} elseif (isset ($_SESSION['tempParticipantsImport']) && ! empty ($_SESSION['tempParticipantsImport'])) {
  $importDataButton = '<form><button type="submit" value="true"  name="importRows" class="btn btn-gray saveRow">'.maakKnop('disk_blue.png').' ' . vt('Regels importeren') . '</button></form>';
}

/**
 * setup fonds selection
 */
$editObject = new editObject($participantenFondsVerloop);
$editObject->template = $editcontent;
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->formTemplate = "participants_bulkImportMutationsTemplate.html";
$editObject->usetemplate = true;

$editObject->controller($action, $data);

$editObject->formVars['aeMessage'] = $AEMessage->getMessage() . $AEMessage->getFlash();
$editObject->formVars['overviewTable'] = $overviewTable;
$editObject->formVars['importDataButton'] = $importDataButton;
$editObject->formVars['clearData'] = $clearDataButton;
$editObject->formVars['example'] = $AETemplate->parseBlockFromFile('participantenBulkImport/examples.html', array());

$editObject->template['script_voet'] = "
  var button = 'change';
  $('#dataTable').on('click','.changeRow',function() {
  
    var top = $(this).parent().parent();
    var td = $(this).parent();
    $(td).siblings().each(
      function(){
        if ($(this).find('input').length) {
          button = 'change';
//          //lets save stuff to database
          $(this).text($(this).find('input').val());
        }
        else {
          button = 'save';
          field = $(this).data('name');
          var t = $(this).text();
          $(this).text('').append($('<input name=\"'+field+'\" />',{'value' : t}).val(t));
        }
      });
      if ( button == 'save' ) {
        $(top).find('.changeRow').hide();
        $(top).find('.saveRow').show();
      } else {
        $(top).find('.changeRow').show();
        $(top).find('.saveRow').hide();
      }
  });
  
$('#dataTable').on('click','.saveRow',function() {
    var top = $(this).parent().parent();
   var fields = $( top).find('input').serialize();
   $.ajax({
        url: 'participants_bulkImportMutations.php?' + fields,
        type: 'POST',
        data: {
          saveRow: 'true',
          rowId: $(top).attr('id')
        },
        success: function (data) {
          $(top).replaceWith(data);
        },
        error: function (data) {
        },
        cache: false
      });
  });
";   


echo $editObject->getOutput();
$_SESSION['NAV'] = '';