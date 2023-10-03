<?php
include_once("wwwvars.php");
// @ToDo: Controleren of dit bestand encoding problemen heeft.
$AEMessage = new AE_Message();

$__funcvar['listurl']  = "ordersListV2.php";
$__funcvar['location'] = "ordersStatusEditV2.php";

$data = array_merge($_POST, $_GET);

$object = new OrdersV2();
$object2 = new OrderRegelsV2();

$editObject = new editObject($object);

$editObject->__appvar = $__appvar;
$action = $data['action'];
$editObject->usetemplate = true;
//isFixMessage
$editObject->formVars['orderInfoDisplay'] = 'hidden';

if ( isset($data['orderStatus']) && (int)$data['orderStatus'] >= 0 ) {
  $fixSet = '';
  $orderLogs = new orderLogs();
  
  $orderRegels = $object2->parseBySearch(array('orderid' => $data['huidigeOrderId']), "all", null, -1);
  if ( ! empty($orderRegels) )
  {
    $editObject->formVars['orderInfoDisplay'] = '';
    $orderData = $object->parseById($data['huidigeOrderId']);
    
    // Bij fix orders die nog niet verzonden zijn een melding tonen
    if (
      $orderData['fixOrder'] == 1
      && ($orderData['fixVerzenddatum'] == '' || $orderData['fixVerzenddatum'] == '0000-00-00 00:00:00')
      && $orderData['orderStatus'] == 0
    ) {
      $fixSet = ', `fixOrder` = 0';
      $orderLogs->addToLog($data['huidigeOrderId'], null, "Order beheer: fixOrder 1 -> 0");
    }
  }
  
  $updateOrderStaturQuery =  'UPDATE `OrdersV2` SET `orderStatus` = "'.$data['orderStatus'].'" ' . $fixSet . ' WHERE `id` = "'.$data['huidigeOrderId'].'" ';
  $updateOrderRegelStaturQuery =  'UPDATE `OrderRegelsV2` SET `orderregelStatus` = "'.$data['orderStatus'].'" WHERE `orderid` = "'.$data['huidigeOrderId'].'" ';
  
  $db = new DB();
  if ($db->executeQuery($updateOrderStaturQuery) && $db->executeQuery($updateOrderRegelStaturQuery)) {
    
    $orderLogs->addToLog($data['huidigeOrderId'], null, 'Order beheer: Status is aangepast van '.$__ORDERvar['orderStatus'][$data['huidigeOrderStatus']].' naar '.$__ORDERvar['orderStatus'][$data['orderStatus']].' !');
    
    $AEMessage->setMessage('Status is aangepast van '.$__ORDERvar['orderStatus'][$data['huidigeOrderStatus']].' naar '.$__ORDERvar['orderStatus'][$data['orderStatus']].' !', 'success');
  } else {
    $AEMessage->setMessage('Status kon niet worden aangepast!', 'error');
  }
  
  $data['orderId'] = $data['huidigeOrderId'];
  echo $AEMessage->getMessage();
}


$autocomplete = new Autocomplete();

$autocomplete->resetVirtualField('orderId');
$editObject->formVars['orderId'] = $autocomplete->addVirtuelField(
  'orderId',
  array(
    'autocomplete' => array(
      'table'       => 'OrdersV2',
      'label'       => array(
        'OrdersV2.id',
        'OrdersV2.ISINCode',
      ),
      'searchable'  => array('OrdersV2.id'),
      'field_value' => array('OrdersV2.id', 'OrdersV2.ISINCode'),
      'value'       => 'OrdersV2.id',
      'actions'     => array(
        'select' => '
          event.preventDefault();
          $("#orderId").val(ui.item.value);
        '
      )
    ),
    'form_size'    => '15'
  )
);
$editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('orderId');

if ( isset ($data['orderId']) && (int) $data['orderId'] > 0 ) {
  $editObject->formVars['orderInfoDisplay'] = '';
  $orderRegels = $object2->parseBySearch(array('orderid' => $data['orderId']), "all", null, -1);
  $orderData = $object->parseById($data['orderId']);
  
  if (
    $orderData['fixOrder'] == 1
    && ($orderData['fixVerzenddatum'] == '' || $orderData['fixVerzenddatum'] == '0000-00-00 00:00:00')
    && $orderData['orderStatus'] == 0
  ) {
    $editObject->formVars['isFixMessage'] = $AEMessage->makeMessage(vt('Let op! Dit is een fix order, bij het wijzigen van de status wordt het fix vinkje uitgezet.'), 'info');
    
    $editObject->formVars['confirmEvent'] = "
      AEConfirm(
          '" . vt('Let op!') . " <br />" . vt('Dit is een fix order, bij het wijzigen van de status wordt het fix vinkje uitgezet.') . " <br />" . vt('Weet u zeker dat u de status wilt wijzigen?') . "',
          '" . vt('Order status wijzigen - Fix order') . "',
          function () {
            $('#changeorderStatusForm').submit();
          },
          function () {
            return false;
          }
        );
    ";
    
  } else {
    $editObject->formVars['confirmEvent'] = "AEConfirm('" . vt('Weet u zeker dat u de status wilt wijzigen?') . "', '" . vt('Order status wijzigen') . "', function () {\$('#changeorderStatusForm').submit();});";
  }
  
  $orderStatus = $__ORDERvar['orderStatus'];
  if ( $orderData['fixOrder'] == 1 ) {
    if ( $orderData['orderStatus'] > 2 ) {
      foreach ( $orderStatus as $key => $value ) {
        if ( $key <= 2 ) {
          unset($orderStatus[$key]);
        }
      }
    } else {
      foreach ( $orderStatus as $key => $value ) {
        if ( $key <= $orderData['orderStatus'] ) {
          unset($orderStatus[$key]);
        }
      }
    }
    $object->data['fields']['orderStatus']['form_options'] = $orderStatus;
  }
  
  $action = 'edit';
  $data['id'] = $data['orderId'];
  $orderData['transactieSoort'] = $__ORDERvar['transactieSoort'][$orderData['transactieSoort']];
  
  $editObject->formVars['orderInfo'] = '<div class="row">';
  $editObject->formVars['orderInfo'] .= '
          <div class="box box5">
            <div class="formblock">
              <div class="formlinks">' . vt('Order') . '</div>
              <div class="formrechts">' . $orderData['id'] . '</div>
            </div>
            <div class="formblock">
              <div class="formlinks">' . vt('Fonds') . '</div>
              <div class="formrechts">' . $orderData['fonds'] . '</div>
            </div>
            <div class="formblock">
              <div class="formlinks">' . vt('Omschrijving') . '</div>
              <div class="formrechts">' . $orderData['fondsOmschrijving'] . '</div>
            </div>
            <div class="formblock">
              <div class="formlinks">' . vt('ISINCode') . '</div>
              <div class="formrechts">' . $orderData['ISINCode'] . '</div>
            </div>
            <div class="formblock">
              <div class="formlinks">' . vt('transactieSoort') . '</div>
              <div class="formrechts">' . $orderData['transactieSoort'] . '</div>
            </div>
            <div class="formblock" style="' . ($orderData['fixOrder'] == 0 ? '':'color:red; font-weight:bold;') . '">
              <div class="formlinks">' . vt('Fix order') . '</div>
              <div class="formrechts">' . ($orderData['fixOrder'] == 0 ? 'Nee':'Ja') . '</div>
            </div>
            <div class="formblock">
              <div class="formlinks">' . vt('orderStatus') . '</div>
              <div class="formrechts">' . $__ORDERvar['orderStatus'][$orderData['orderStatus']] . '</div>
            </div>
          </div>
      ';
  
  if ( ! empty($orderRegels) )
  {
    // Bij fix orders die nog niet verzonden zijn een melding tonen
    
    $editObject->formVars['orderInfo'] .= '<div class="box box5"><table>';
    foreach ($orderRegels as $orderRegelData)
    {
      $editObject->formVars['orderInfo'] .= '
            <tr class="list_kopregel">
              <td class="list_kopregel_data">' . vt('Portefeuille') . '</td>
              <td class="list_kopregel_data">' . vt('Rekening') . '</td>
              <td class="list_kopregel_data">' . vt('Aantal') . '</td>
            </tr>
            <tr class="list_dataregel">
              <td class="listTableData">' . $orderRegelData['portefeuille'] . '</td>
              <td class="listTableData">' . $orderRegelData['rekening'] . '</td>
              <td class="listTableData">' . $orderRegelData['aantal'] . '</td>
            </tr>

          ';
    }
  }
  $editObject->formVars['orderInfo'] .= '</table></div>';
  $editObject->formVars['orderInfo'] .= '</div>';
}


$editObject->controller($action, $data);

$editObject->formTemplate = "classTemplates/orders/ordersStatusEditV2.html";

$editObject->template = $editcontent;

echo $editObject->getOutput();


if ($result = $editObject->result) {

}

$_SESSION['NAV'] = null;