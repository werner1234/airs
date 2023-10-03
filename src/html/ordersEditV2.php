<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
   Laatste aanpassing	: $Date: 2020/07/17 14:52:21 $
   File Versie					: $Revision: 1.228 $

   $Log: ordersEditV2.php,v $
   Revision 1.228  2020/07/17 14:52:21  rm
   8467

   Revision 1.227  2020/05/27 14:14:20  rm
   8627

   Revision 1.226  2020/04/06 14:40:18  rm
   8454 Orders: Tonen indien KID-form aanwezig

   Revision 1.225  2019/12/07 17:45:49  rvv
   *** empty log message ***

   Revision 1.224  2019/10/09 14:26:33  rm
   8143

   Revision 1.223  2019/10/02 14:17:20  rm
   8145 -> vasthouden filter na opslaan en terug

   Revision 1.222  2019/09/11 15:06:18  rm
   8060

   Revision 1.221  2019/08/28 10:28:43  rm
   8027

   Revision 1.220  2019/08/14 07:52:25  rm
   Nominaal meervoudig aanzetten

   Revision 1.219  2019/08/09 14:27:40  rm
   4913

   Revision 1.218  2019/08/09 13:56:12  rm
   4913

   Revision 1.217  2019/08/09 13:21:34  rm
   4913

   Revision 1.216  2019/08/08 08:10:45  rm
   7868

   Revision 1.215  2019/07/03 14:47:38  rm
   7922

   Revision 1.214  2019/06/14 07:56:34  rm
   7871

   Revision 1.213  2019/05/25 16:20:52  rvv
   *** empty log message ***

   Revision 1.212  2019/04/24 14:42:10  rm
   Order logs filteren op speciale tekens

   Revision 1.211  2019/03/06 15:46:59  rm
   Depotbank old vullen met huidige depotbank

   Revision 1.210  2019/01/14 15:36:40  rm
   comma teveel

   Revision 1.209  2019/01/14 11:36:21  rm
   Toevoegen fx fondsen
   FX USD/GBP Spot
   FX GBP/USD Spot

   Revision 1.208  2018/12/03 12:44:29  rm
   Orders

   Revision 1.206  2018/09/19 13:42:20  rm
   4773

   Revision 1.205  2018/08/22 12:47:14  rvv
   Added consolidatie=0 filter

   Revision 1.204  2018/08/18 12:40:14  rvv
   php 5.6 & consolidatie

   Revision 1.203  2018/07/25 15:11:46  rm
   controlle gewijzigd

   Revision 1.202  2018/06/27 09:03:43  rm
   6560

   Revision 1.201  2018/02/14 09:35:10  rm
   Toevoegen fx fondsen
   FX EUR/DKK Spot
   FX DKK/EUR Spot
   FX EUR/AUD Spot
   FX AUD/EUR Spot

   Revision 1.200  2018/02/13 14:10:33  rm
   Toevoegen
   -FX CAD/EUR Spot
   -FX EUR/CAD Spot

   Revision 1.199  2018/02/07 17:15:01  rvv
   *** empty log message ***

   Revision 1.198  2018/02/02 15:31:25  rm
   6540

   Revision 1.197  2018/01/29 10:10:46  rvv
   *** empty log message ***

   Revision 1.196  2018/01/29 07:51:12  rvv
   *** empty log message ***

   Revision 1.195  2018/01/26 10:51:56  rm
   extra velden bij mail

   Revision 1.194  2018/01/15 12:28:57  rm
   Mailbevestiging orders

   Revision 1.193  2018/01/10 15:37:25  rm
   6487 validatie

   Revision 1.192  2017/12/31 09:36:45  rvv
   *** empty log message ***

   Revision 1.191  2017/12/30 09:47:59  rvv
   *** empty log message ***

   Revision 1.190  2017/12/23 18:13:40  rvv
   *** empty log message ***

   Revision 1.189  2017/12/20 16:59:57  rvv
   *** empty log message ***

   Revision 1.188  2017/12/15 15:36:27  rm
   advies relatie

   Revision 1.187  2017/12/08 18:23:43  rm
   no message

   Revision 1.186  2017/10/01 07:27:32  rvv
   *** empty log message ***

   Revision 1.185  2017/09/21 15:01:00  rm
   advies relaties

   Revision 1.184  2017/09/10 14:32:43  rvv
   *** empty log message ***

   Revision 1.183  2017/08/23 12:44:55  rm
   Toevoegen fx fonds SEK/EUR

   Revision 1.182  2017/07/05 14:52:50  rm
   Toevoegen fx fondsen

   Revision 1.181  2017/06/14 13:34:18  rm
   orders opnieuw inleggen en inleggen vanuit rapportage

   Revision 1.180  2017/05/10 12:45:52  rm
   Ordersv2

   Revision 1.178  2017/05/09 11:35:18  rm
   no message

   Revision 1.177  2017/05/04 12:57:54  rm
   javascript problemen

   Revision 1.176  2017/04/13 14:03:25  rm
   5791

   Revision 1.175  2017/03/01 12:03:21  rm
   Ordersv2

   Revision 1.174  2016/12/21 15:23:50  rm
   Toevoeging fx fondsen

   Revision 1.173  2016/12/14 15:13:12  rvv
   *** empty log message ***

   Revision 1.172  2016/12/07 15:14:27  rvv
   *** empty log message ***

   Revision 1.171  2016/11/09 13:31:26  rvv
   *** empty log message ***

   Revision 1.170  2016/11/02 15:44:53  rm
   Verzenden combinatie alleen wanneer status == 0

   Revision 1.169  2016/10/26 14:37:01  rm
   Ordersv2

   Revision 1.168  2016/10/21 14:13:42  rm
   Call:
   5350
   5349
   5348

   Revision 1.167  2016/10/19 10:21:07  rm
   status fix order


*/
include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/editObject.php");
include_once("../classes/AE_cls_MailTemplate.php");

$AEJson = new AE_Json();
$AEMessage = new AE_Message();
$orderLogs = new orderLogs();

//echo '<pre>';print_r(get_defined_vars()); echo '</pre>';

$_SESSION['NAV'] = null;
$forceredirect = '';

$__funcvar['listurl'] = "ordersListV2.php";
$__funcvar['location'] = "ordersEditV2.php";

$AETemplate = new AE_template();
$AEMessage = new AE_Message();

$data = array_merge($_POST, $_GET);
$canSendOrder = true; //Bepalen of we een order mogen verzenden

$_POST = $data;
$_GET = $data;

if ($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
{
  $beperktToegankelijk = " (Portefeuilles.Accountmanager='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "' OR Portefeuilles.tweedeAanspreekpunt ='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "') ";
}
else
{
  $joinPortefeuilles = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '" . $USR . "' JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
  $beperktToegankelijk = " (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
}
$query = " SELECT Portefeuilles.id as id FROM (Portefeuilles) $joinPortefeuilles WHERE 1 AND $beperktToegankelijk ORDER BY Portefeuilles.Portefeuille ASC";


$db = new DB();
$db->executeQuery($query);
while ($queryData = $db->nextRecord())
{
  $Portefeuilles[] = $queryData['id'];
}

/**
 * updateFix
 * Bij combinatie orders de mogelijkheid om fix te wijzigen vanuit de order list
 */
if ( isset ($data['updateFix']) && (int) $data['updateFix'] === 1 )
{
  $ordersV2 = new OrdersV2();
  $ordersV2->getById($data['orderId']);
  $curFix = $ordersV2->get('fixOrder');
  $ordersV2->set('fixOrder', (int) $data['fixStatus']);
  if($ordersV2->save()) {
    $orderLogs->addToLog($data['orderId'], null, "fixOrder naar " . $data['fixStatus'] . " " . $curFix . " -> " . $data['fixStatus'] . "");
    echo $AEJson->json_encode(
      array(
        'success' => true,
        'message' => $AEMessage->getMessage()
      )
    );
  } else {
    echo $AEJson->json_encode(
      array(
        'success' => false,
        'message' => $AEMessage->getMessage()
      )
    );
  }
  exit();
}




if ( isset ($data['ignoreAdviceMail']) && (int) $data['ignoreAdviceMail'] === 1 )
{
  $object2 = new OrderRegelsV2();
  $object2->getById($data['orderregelId']);
  $object2->set('mailBevestigingVerzonden', date('Y-m-d H:i:s'));
  $object2->set('mailBevestigingData', 'ignored ');
  $orderLogs->addToLog($data['id'], null, 'E-mail naar advies relatie genegeerd', '','',5,'');

  $object2->save();

  header("Location: ordersEditV2.php?action=edit&id=" . $data['id']);
  exit();
}

if ( isset ($data['sendAdviceMail']) && (int) $data['sendAdviceMail'] === 1 ) {
  
  $object2 = new OrderRegelsV2();
  $object2->getById($data['orderregelId']);
  
  
  
  if (isset($data['portefeuille']) && !empty($data['portefeuille']))
  {
    $thisPortefeuille = $data['portefeuille'];
  }
  else
  {
    $thisPortefeuille = $object2->get('portefeuille');
  }
  
  
  $db = new DB();
  $vermogensBeheerderBccQuery = "
    SELECT Portefeuille, Portefeuilles.Vermogensbeheerder, orderAdviesBcc from Portefeuilles
    LEFT JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder  = Portefeuilles.Vermogensbeheerder
    where Portefeuilles.Portefeuille = '" . mysql_real_escape_string($thisPortefeuille) . "'
  ";
  $db->SQL($vermogensBeheerderBccQuery);
  $db->Query();
  $vermogensBeheerderBcc = $db->nextRecord();
  $vermogensBeheerderBcc = $vermogensBeheerderBcc['orderAdviesBcc'];
  
  
  $AEJson = new AE_Json();
  $mailData = array(
    'senderName'              => $_SESSION['usersession']['gebruiker']['Naam'],
    'senderEmail'             => $_SESSION['usersession']['gebruiker']['emailAdres'],
    'subject'                 => $data['adviseSubject'],
    'orderReden'              => $data['adviseOrderReden'],
    'adviseReceiverName'      => $data['adviseReceiverName'],
    'adviseReceiverEmail'     => $data['adviseReceiverEmail'],
    'adviseReceiverEmailBcc'  => $vermogensBeheerderBcc,
    'body'                    => $data['adviseMail']
  );
  
  $object2->set('mailBevestigingVerzonden', date('Y-m-d H:i:s'));
  $object2->set('mailBevestigingData', serialize($mailData));
  $object2->set('orderReden', $data['adviseOrderReden']);

  
  $mail = new PHPMailer();
  $mail->IsSMTP();

  $mail->From     = $_SESSION['usersession']['gebruiker']['emailAdres'];
  $mail->FromName = $_SESSION['usersession']['gebruiker']['Naam'];

  $mail->Subject = $data['adviseSubject'];
  $mail->Body    = $data['adviseMail'];

  if (isset($_FILES['uploaded_file1']) && $_FILES['uploaded_file1']['error'] == UPLOAD_ERR_OK) {
    $mail->AddAttachment($_FILES['uploaded_file1']['tmp_name'], $_FILES['uploaded_file1']['name']);
  }
  if (isset($_FILES['uploaded_file2']) && $_FILES['uploaded_file2']['error'] == UPLOAD_ERR_OK) {
    $mail->AddAttachment($_FILES['uploaded_file2']['tmp_name'], $_FILES['uploaded_file2']['name']);
  }
  if (isset($_FILES['uploaded_file3']) && $_FILES['uploaded_file3']['error'] == UPLOAD_ERR_OK) {
    $mail->AddAttachment($_FILES['uploaded_file3']['tmp_name'], $_FILES['uploaded_file3']['name']);
  }

  $mail->AltBody = html_entity_decode(strip_tags($data['adviseMail']));
  $mail->AddAddress($data['adviseReceiverEmail'], $data['adviseReceiverName']);

  if ( ! empty ($vermogensBeheerderBcc) ) {
    $mail->AddBCC($vermogensBeheerderBcc, '');
  }

  if( $mail->Send() ) {
    $object2->save();

    $orderLogs->addToLog($data['id'], null, 'E-mail naar advies relatie verzonden', '','',5,'');

    $AEMessage->setFlash('Bericht:' . $data['subject'] .' is verzonden', 'Error');
  } else {
    $AEMessage->setFlash('kan bericht:' . $data['subject'] .' niet versturen', 'Error');
  }

  if ( isset($data['adviseOrderOrderVerzenden']) && $data['adviseOrderOrderVerzenden'] == 1 ) {
    $data['verzenden'] = $data['adviseOrderOrderVerzenden'];
    $data['orderStatus'] = $data['adviseOrderOrderStatus'];
    $data['id'] = $data['orderid'];
    $data['action'] = 'update';
    $data['orderid'] = $data['orderid'];
    $data['orderregelId'] = $data['orderregelId'];

  } else {
    header("Location: ordersEditV2.php?action=edit&id=" . $data['id']);
    exit();
  }

}

/**
 * Functionaliteit voor berekenen notas komt hier
 */
if ( isset($data['recalculateNota']) && (int)$data['recalculateNota'] === 1) {
  echo $AEJson->json_encode(array(
                              'success' => true,
                              'calculatedData'  => updateBrutoWaardeV2($_GET['orderid'],$_GET['orderRegelId'],$_GET['notaValutakoers'],date('Y-m-d',form2jul($_GET['settlementdatum'])),$_GET)));
  exit();
}

if ( isset($data['saveNota']) && (int)$data['saveNota'] === 1) {
  //listarray($data);
  $nota=new orderregelsV2();
  $nota->getById($data['orderRegelId']);
  foreach($data as $variable=>$value)
    if(isset($nota->data['fields'][$variable]))
      $nota->set($variable, $value);
  if($nota->save())
    echo $AEJson->json_encode(array( 'success' => true ));
  exit();
}

/**
 * Einde functionaliteit voor berekenen notas
 */

/**
 * Conbinatie van in aanmaak naar ingevoerd
 */
if ( isset ($data['checkForStatusInAanmaak']) ) {
  $batchId = (int) $data['batchId'];
  if ( ! $batchId ) {echo $AEJson->json_encode(array('success' => false)); exit();}
  $orderDB = new DB();
  $fetchOrders = "select OrdersV2.id FROM OrdersV2 WHERE OrdersV2.batchId = '".$batchId."' AND OrdersV2.orderStatus = '-1'";
  echo $AEJson->json_encode(array('success' => true, 'hasStatus' => $orderDB->Qrecords($fetchOrders)));
  exit();
}

if ( isset($data['klaarMetInAanmaak']) && $data['klaarMetInAanmaak'] === 'combinationBatch' ) {
  klaarMetInAanmaak ($data);
  exit();
}

function klaarMetInAanmaak ($data) {
  $batchId = (int) $data['batchId'];
  if ( ! $batchId ) {return false;}
  $orderDB = new DB();
  $fetchOrders = "select OrdersV2.id, OrdersV2.orderStatus, OrdersV2.batchId, OrdersV2.fixOrder FROM OrdersV2  WHERE OrdersV2.batchId = '".$batchId."' AND OrdersV2.orderStatus = '-1'";
  $orderDB->executeQuery($fetchOrders);
  $orderIds=array();
  while ($orderData = $orderDB->nextRecord())
  {
    $orderIds[] = $orderData['id'];
  }
  $db = new DB();
  foreach($orderIds as $orderId)
  {
    $order = new OrdersV2();
    $order->forceLog = true;
    $order->getById($orderId);
    $order->set('orderStatus', 0);
    $order->save();

    $updateOrderRegelQuery = "UPDATE OrderRegelsV2 SET orderregelStatus = '0' WHERE orderid = '" . $orderId . "' ";
    $db->SQL($updateOrderRegelQuery);
    $db->Query();
  }
}



/**
 * Conbinatie van in aanmaak naar ingevoerd
 */



/**
 * Combinatie naar fix verzenden
 */
if ((isset ($data['sendToFix']) && (int)$data['sendToFix'] === 1)) //&& requestType('ajax')
{
  if (isset($data['batchId']) && !empty($data['batchId']))
  {
    $orderDB = new DB();
    $fetchOrders = 'select OrdersV2.id, OrdersV2.batchId, OrdersV2.fixOrder FROM OrdersV2 WHERE OrdersV2.batchId = "' . $data['batchId'] . '" AND OrdersV2.orderStatus = 0 ORDER BY OrdersV2.id';
    $orderDB->executeQuery($fetchOrders);
    $orderCounter = array('fix' => 0, 'noFix' => 0, 'total' => 0);

    while ($orderData = $orderDB->nextRecord())
    {
      $orderCounter['total']++;
      if ( intval($orderData['fixOrder']) === 1 )
      {
        $orderCounter['fix']++;
        $object = new OrdersV2();
        $object->getById($orderData['id']);
        $object->verzendFix();
      }
    }

    if ($orderCounter['total'] === 0)
    {
      $AEMessage->setFlash(vt('Geen orders gevonden.'), 'error');
    }
    else
    {
      if ($orderCounter['fix'] === 0)
      {
        $AEMessage->setFlash(vt('Geen fix orders in geselecteerde batch gevonden.'), 'info');
      }
      else
      {
        $AEMessage->setFlash($orderCounter['fix'] . ' orders naar fix verzonden', 'success');
      }
    }
  }
  else
  {
    $AEMessage->setFlash(vt('Geen geldige order.'), 'error');
  }

  header("Location: " . $__funcvar['listurl']);
  exit();
}
/**
 * Bij het annuleren
 */
if ((isset ($data['cancel']) && (int)$data['cancel'] === 1) && requestType('ajax'))
{
  $fixOrder = new FixOrders();
  $fixOrder->getByField('AIRSorderReference', $data['orderId']);
  
  $message = '';
  $cancel = false;
  if ($fixOrder->get('orderid') != 0)
  {
    $cancel = true;
  }
  else
  {
    $AEMessage->setMessage(vt('Annuleren niet mogelijk (nog geen fix order id aanwezig)'), 'info');
  }
  
  echo $AEJson->json_encode(
    array(
      'success' => true,
      'cancel'  => $cancel,
      'message' => $AEMessage->getMessage()
    )
  );
  exit();
}

/**
 * Change nota
 */
if (isset ($data['changeNota']))
{
  $saveNotaObj = OrdersV2();
  $saveNotaEditObject = new editObject($saveNotaObj);

  $saveNotaEditObject->__funcvar = $__funcvar;
  $saveNotaEditObject->__appvar = $__appvar;
  $objectData = $data;

  if ( ($data['orderSelectieType'] == 'M' || $data['orderSelectieType'] == 'O' || $data['orderSelectieType'] == 'X') && $validateObjectName === 'OrderRegelsV2' && $data['id'] > 0  && $data['orderregelId'] > 0  )
  {
    $validateObject->getByField('orderid', $data['orderregelId']);
    $objectData['id'] = $data['orderregelId'];
  }
  elseif ( ($data['orderSelectieType'] != 'M' && $data['orderSelectieType'] != 'O' && $data['orderSelectieType'] != 'X') && $data['id'] > 0 && $validateObjectName === 'OrderRegelsV2')
  {
    $validateObject->getByField('orderid', $data['id']);
    $objectData['id'] = $data['orderregelId'];
  }
  elseif ($data['id'] > 0 && $validateObjectName === 'OrdersV2')
  {
    $validateObject->getById($data['id']);
  }

  echo $AEJson->json_encode(
    array(
      'success'     => true,
      'saved'       => false,
    )
  );

  exit();
}


if (isset ($data['type']) && $data['type'] === 'clientInrows')
{
  $orderId = (int)$data['orderId'];
  $portefeuille = (isset($data['portefeuille'])?$data['portefeuille']:null);
  if (!$orderId || !$portefeuille)
  {
    echo $AEJson->json_encode(array('success' => false));
    exit();
  }
  $orderDB = new DB();
  $fetchOrders = "select OrderRegelsV2.id FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid = '" . $orderId . "' AND OrderRegelsV2.portefeuille = '" . $portefeuille . "'";
  echo $AEJson->json_encode(array(
                              'success'      => true,
                              'clientInrows' => $orderDB->Qrecords($fetchOrders)
                            ));
  exit();
}


/**
 *
 *
 */
if (isset ($data['changeStatus']))
{
  $order=new OrdersV2();
  if($data['orderSelectieType']=='C' && !isset($data['orderid']) && isset($data['id']) )
    $data['orderid']=$data['id'];
  $order->getById($data['orderid']);
  $order->set('orderStatus', $data['orderStatus']);
  if($order->validate(true))
  {
    $order->save();
    $db = new DB();
    $updateOrderRegelQuery = "UPDATE OrderRegelsV2 SET orderregelStatus = '" . $data['orderStatus'] . "' WHERE orderid = '" . $data['orderid'] . "' ";
    $db->SQL($updateOrderRegelQuery);
    $savedLine = $db->Query();
    if ( $savedLine === true )
    {
      echo $AEJson->json_encode(
        array(
          'success'     => true,
          'saved'       => true,
        )
      );
      exit();
    }
  }
  echo $AEJson->json_encode(
    array(
      'success'     => true,
      'saved'       => false,
    )
  );

  exit();
}


/**
 * het formulier valideren voordat het gesubmit wordt
 */
if (isset ($data['validate']))
{
  $AEJson = new AE_Json();
  $validateObjects = array('OrdersV2', 'OrderRegelsV2');
  
  
  
  
  
  /** @todo is dit nodig? */
  /** bij meervoudig hoeft orderRegels niet **/
  if ( ($data['orderSelectieType'] == 'M' || $data['orderSelectieType'] == 'O' || $data['orderSelectieType'] == 'X') && empty($data['portefeuille']))
  {
    unset($validateObjects[1]);
  }

  $ordersObject = null;

  $validationJson = array(
    'fieldErrors' => array(),
    'result'      => array(),
    'error'       => array()
  );

  /** valuta ophalen als een rekening is geselecteerd en valuta niet is ingevoerd **/
  if (empty($data['valuta']) && (isset($data) && !empty($data['rekening'])))
  {
    $rekeningen = new Rekeningen ();
    $data['valuta'] = $rekeningen->parseBySearch(array('Rekening' => $data['rekening']), 'Valuta');
  }

  $_SESSION['orderData'] = $data;
  foreach ($validateObjects as $validateObjectName)
  {
    $validateObject = new $validateObjectName();

    $validateEditObject = new editObject($validateObject);
    $validateEditObject->__funcvar = $__funcvar;
    $validateEditObject->__appvar = $__appvar;
    $objectData = $data;

    if ( ($data['orderSelectieType'] == 'M' || $data['orderSelectieType'] == 'O' || $data['orderSelectieType'] == 'X' ) && $validateObjectName === 'OrderRegelsV2' && $data['id'] > 0  && $data['orderregelId'] > 0  )
    {
      $validateObject->getByField('id', $data['orderregelId']);
      $objectData['id'] = $data['orderregelId'];
    }
    elseif ( ($data['orderSelectieType'] != 'M' && $data['orderSelectieType'] != 'O' && $data['orderSelectieType'] != 'X' ) && $data['id'] > 0 && $validateObjectName === 'OrderRegelsV2')
    {
      $validateObject->getByField('orderid', $data['id']);
      $objectData['id'] = $data['orderregelId'];
    }
    elseif ($data['id'] > 0 && $validateObjectName === 'OrdersV2')
    {
      $validateObject->getById($data['id']);
    }

    if ($validateObjectName === 'OrdersV2')
    {
      $ordersObject = $validateObject;
    }
  
    /*
     * Bij een meervoudige order gaat de validatie uitvoeren met order status 1 inplaats van 0 ivm de overige order regels
     * Dit is alleen bij fix.
     */
    if ( $data['orderSelectieType'] == 'M' && (int) $objectData['orderStatus'] === 0 && (int) $data['verzenden'] === 1 ) {
      $objectData['orderStatus']++;
    }
    
    if ((isset($data['orderid']) && $data['orderid'] > 0) || $data['id'] > 0)
    {
      $validateEditObject->dataBegin = $validateEditObject->object->data;
    }
    $validateEditObject->data = $objectData;
    $validateEditObject->setFields();

    if ($validateObjectName === 'OrderRegelsV2')
    {
      $validateObject->setOrderOject($ordersObject);
    }
  
    /**
     * reset de controles
     **/
    if (isset($data['resetControle']) && $data['resetControle'] == 1 ) {
      $validateEditObject->object->forceOrdercheck = true;
//      $_SESSION['orderData'] = null;
    }
    
    
    $validateEditObject->object->validate();


    if (is_array($validateObject->getErrors()) || $validateObject->error == true)
    {
      $validationJson['fieldErrors'] = array_merge($validationJson['fieldErrors'], $validateObject->getErrors());
      $validationJson['saved'][] = false;
    }
    else
    {
      $validationJson['saved'][] = true;
    }
  }

  if (isset($_SESSION['orderData']))
  {
    $_SESSION['orderData'] = '';
  }
  echo $AEJson->json_encode(
    array(
      'success'     => true,
      'saved'       => in_array(false, $validationJson['saved'])?false:true,
      'message'     => $validateEditObject->_error,
      'error'       => $validationJson['fieldErrors'],
      'CheckResult' => (isset($validateEditObject->object->data['orderData']['orderCheckHtml'])?$validateEditObject->object->data['orderData']['orderCheckHtml']:'')
    )
  );
  unset($_SESSION['orderData']);
  exit();
}
/**
 * einde van de validatie
 */

//annuleren meervoudige regel
if ( isset ($data['cancelOrderregel']) && (int) $data['cancelOrderregel'] === 1) {
  $orderRegelData = $data;
  unset($orderRegelData['id']);
  $cancelOrderid = $data['id'];
  $orderRegelData['id'] = $orderRegelData['orderregelId'];
  $orderRegelObj = new OrderRegelsV2();
  $orderEditObj = new editObject($orderRegelObj);

  $orderRegelDb = new DB();
  $orderRegelQuery = "SELECT * FROM `OrderRegelsV2` WHERE `id`= '" . $orderRegelData['id'] . "';";
  $orderRegelDb->executeQuery($orderRegelQuery);
  $orderRegelData = $orderRegelDb->nextRecord();

  $orderLogs = new orderLogs();
  $orderLogs->addToLog($cancelOrderid, null, 'Order regel ' . $orderRegelData['positie'] . ' Portefeuille:' . $orderRegelData['portefeuille'] . ' Aantal:'.$orderRegelData['aantal'].' verwijderd', '','',5,'');

  $orderEditObj->controller('delete', $orderRegelData);

  unset($data['orderregelId']);
  $data['action'] = 'edit';

}

$editcontent['javascript'] = '';
//laad objecten
$object = new OrdersV2();
$object2 = new OrderRegelsV2();

$editObject = new editObject($object);
$editObject->addExtraObject($object2);
$editObject->addExtraObject($object2);
$editObject->__funcvar = $__funcvar;
//$editObject->skipStripAll=true;





$standaard = $object->getStandaard();
$object->setStandaard($standaard);

$noInsert = false;
$db = new DB();

/** controlleer of er een batch id is meegestuurd **/
$currentBatch = isset($_GET['batchId'])?$_GET['batchId']:null;

/** controlleren of het order id is meegezonden **/
$thisOrderId = null;
if (isset ($data['id']) && $data['id'] > 0)
{
  $thisOrderId = $data['id'];
}
elseif (isset ($data['orderid']) && $data['orderid'] > 0)
{
  $thisOrderId = $data['orderid'];
}

if ($thisOrderId)
{
  $data['id'] = $thisOrderId;
  $object->set('id', $thisOrderId);
  $object->getById($thisOrderId);
}

$db = new DB();
$query = "SELECT Layout FROM Vermogensbeheerders Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";
$db->SQL($query);
$beheerderRec = $db->lookupRecord();
if(file_exists('ordersV2PDF_L'.$beheerderRec['Layout'].'.php'))
  $pdfScript='ordersV2PDF_L'.$beheerderRec['Layout'].'.php';
else
  $pdfScript='ordersPrint.php';

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("Order print",$pdfScript."?uitvoer=pdf&orderid=".$thisOrderId);
/** einde order id **/

/** controlleren of er een order regel id is meegestuurd **/
$editObject->formVars['orderregelId'] = '';
$thisOrderRegelId = null;
if (isset($data['orderregelId']) && $data['orderregelId'] > 0)
{
  $thisOrderRegelId = $data['orderregelId'];
  $noInsert = true;
  $object2->getById($data['orderregelId']);
  $editObject->formVars['orderregelId'] = $data['orderregelId'];
}
/** einde order regel id **/



/**
 * orderAdviesNotificatie
 * Controlleren of het een advies relatie
 * Bepalen of we een order mogen verzenden of niet ivm de advies check
 */
if ( isset($data['portefeuille']) && ! empty ($data['portefeuille']) )
{
  $adviesStatus = checkOrderAcces ('orderAdviesNotificatie');
  $isAdviceRelation = isAdviesRelatie($data['portefeuille']);

  // Bepalen of het een advies relatie is
  // bij advies 5 en 0 mag alles gewoon verzonden worden
  // geld alleen voor e, c en n norders
  if (
    (
      $isAdviceRelation === true
      && ( $adviesStatus != 5 && $adviesStatus != 0 )
    )
    && ($data['orderSelectieType'] === 'E' || $data['orderSelectieType'] === 'C' || $data['orderSelectieType'] === 'N' ) )
  {
    //Bepalen of we de advies relatie mail hebben verzonden wanneer dit niet het geval is mogen we geen order verzenden
    if ( ( ! isset ($data['orderregelId']) || empty ($data['orderregelId']) ) || $object2->get('mailBevestigingVerzonden') === '0000-00-00 00:00:00' ) {
      $canSendOrder = false;
    }
  }
}



if (isset($data['verzenden']) && $data['verzenden'] == 1 && $canSendOrder === true )
{
  $data['orderStatus'] = 1;
  $forceOrdercheck=true;
}
else {
  $forceOrdercheck=false;
}




if ($thisOrderId && ($object->get('OrderSoort') != 'M' && $object->get('OrderSoort') != 'O' && $object->get('OrderSoort') != 'X') && !$thisOrderRegelId)
{
  $query = "SELECT OrderRegelsV2.id,OrdersV2.orderSoort FROM OrdersV2 JOIN OrderRegelsV2 ON OrderRegelsV2.orderid = OrdersV2.id WHERE OrdersV2.id = '" . $thisOrderId . "'";

  /** wanneer maar 1 record ook de order regel ophalen **/
  if ($db->QRecords($query) == 1)
  {
    $noInsert = true;
    $regelId = $db->nextRecord();
    $object2->getById($regelId['id']);
    $editObject->formVars['orderregelId'] = $regelId['id'];
  }
  else
  {
    echo "(" . $db->QRecords($query) . ") orderregels gevonden bij order (" . $_GET['id'] . ")";
    exit;
  }
}
elseif ($_GET['action'] == 'new' && $currentBatch > 0)
{
  $query = "SELECT orderSoort,OrderRegelsV2.id,OrderRegelsV2.orderid FROM OrdersV2 JOIN OrderRegelsV2 ON OrdersV2.id=OrderRegelsV2.orderid WHERE batchId='$currentBatch' limit 1";
  $db->SQL($query);
  $batchOrder = $db->lookupRecord();
  if ($batchOrder['orderSoort'] == 'C')
  {
    $object2->getById($batchOrder['id']);
  }
  elseif ($batchOrder['orderSoort'] == 'M' || $batchOrder['orderSoort'] == 'O')
  {
    $object->getById($batchOrder['orderid']);
  }
}

/**
 * Bepalen van batch id
 */
/** batch id wanneer er een order geopend is **/
$orderBatchId = $object->get('batchId');

if ($orderBatchId > 0)
{
  $editObject->formVars["batchId"] = $orderBatchId;
}
elseif ($currentBatch > 0)
{
  $editObject->formVars["batchId"] = $currentBatch;
}
/** er is geen batch id herkend we gaan een nieuwe ophalen **/
else
{// echo "Komen we hier ooit? Kan denk ik weg?";exit;
  $cfg = new AE_config();
  $newBatchId = $cfg->getData('lastOrderBatchId') + 1;
  $cfg->addItem('lastOrderBatchId', $newBatchId);
  $editObject->formVars["batchId"] = $newBatchId;
}

//javascript bankdepotcodes
$editObject->formVars["BankDepotCodes"] = $AEJson->json_encode($__fixVars['BankDepotCodes']);


$editObject->__appvar = $__appvar;
$action = $data['action'];
$redirectUrl = "orderregelsList.php";
$editObject->usetemplate = true;
$editObject->formVars['fondsData'] = '[]';

$editObject->formVars['UitvoeringOngelijk'] = '';
if ($object->get('orderStatus') == 2 && $object->get('fixOrder') == 0 && $data['orderStatus']<3)
{
  $query = "SELECT sum(uitvoeringsAantal) AS totaal FROM OrderUitvoeringV2 WHERE orderid='" . $object->get("id") . "' ";
  $db->SQL($query);
  $regelsRec = $db->lookupRecord();
  $query="SELECT SUM(OrderRegelsV2.aantal) as aantal FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid = '".$object->get("id") ."'";
  $db->SQL($query);
  $orderregels=$db->lookupRecord();
  if (round($orderregels["aantal"], 4) != round(($regelsRec["totaal"]), 4) && $regelsRec["totaal"] > 0 && $orderregels["aantal"] > 0)
  {
    $editObject->formVars['UitvoeringOngelijk']= vt('Uitvoerings aantal ongelijk aan order aantal.') . " (".round($orderregels["aantal"], 4)." <> ".round($regelsRec["totaal"],4).")";
  }
}
if (requestType('ajax'))
  $editObject->object->forceLog=true;


$editObject->controller($action, $data);



if ( isset($data['orderSelectieType']) ) {
  $soort = $data['orderSelectieType'];
} else {
  $soort = $object->get('OrderSoort'); // haal order soort op
}

/**
 * Wanneer er een order opnieuw willen inleggen
 */
if (isset($data['copyid']) && ($data['copyid'] > 0 || $data['copyid'] == -1))
{
  $cfg = new AE_config();
  $copyNewBatchId = $cfg->getData('lastOrderBatchId') + 1;
  $cfg->addItem('lastOrderBatchId', $copyNewBatchId);

  if ( isset ($data['copyBatch']) && $data['copyBatch'] > 0 ) {
    $withStatus = '';
    if ( isset ($data['withStatus']) && ! empty($data['withStatus']) ) {
      $withStatus = 'AND orderStatus = ' . $data['withStatus'];
    }
    $getBatchQuery = "SELECT id FROM OrdersV2 WHERE batchId = " . $data['copyBatch'] . " " . $withStatus;
    $batchDb = new DB();
    $batchDb->executeQuery($getBatchQuery);
    $numRows = $batchDb->records();
    $firstId = null;

    while ( $batchData = $batchDb->nextRecord() ) {
      $batchData['copyid'] = $data['copyid'];
      if ( $numRows == 1 ) {$batchData['OrderSoort'] = 'E';}
      $cloneId = $object->cloneOrder($batchData, $copyNewBatchId);
      if ( ! $firstId ) {
        $firstId = $cloneId;
      }
    }
  } else {
    $copyOrderSoort = $object->get('OrderSoort');

    if ( $copyOrderSoort == 'C' ) {
      $data['OrderSoort'] = 'E';
    }

    $firstId = $object->cloneOrder($data, $copyNewBatchId);
    if($data['copyid'] > 0)
    {
      $editObject->formVars['copyFrom'] = $data['copyid'];
      $editObject->formVars['batchId'] = 0;
      $editObject->formVars['orderregelId'] = 0;
    }
  }

  // altijd naar nieuwe order ook bij c bij
  header('Location: ordersEditV2.php?action=edit&id=' . $firstId);
  exit();

}
/**
 * Einde opnieuw inleggen
 */
//
$editObject->formVars['fromRapport'] = 0;
if ( isset ($data['from_rapport'])) {
  /**
   * inleggen vanuit rapportage
   */
  $fromFondsDb = new Fonds();
  $fromFondsData = $fromFondsDb->parseBySearch(array('fonds' => $data['from_fonds']));

  $editObject->formVars['fondsData'] = $AEJson->json_encode($fromFondsData);

  $object->set('ISINCode', $fromFondsData['ISINCode']);
  $object->set('fonds', $fromFondsData['Fonds']);
  $object->set('fondseenheid', $fromFondsData['Fondseenheid']);
  $object->set('fondsValuta', $fromFondsData['Valuta']);
  $object->set('fondsOmschrijving', $fromFondsData['Omschrijving']);
  $object->set('beurs', $fromFondsData['beurs']);
  $editObject->formVars['fonds_id'] = $fromFondsData['id'];

  $fromClient = new Portefeuilles();
  $fromClientData = $fromClient->parseBySearch(array('Portefeuille' => $data['from_portefeuille']));

  $object->set('client', $fromClientData['Client']);
  $object->set('depotbank', $fromClientData['Depotbank']);
  $object->set('fondssoort', $fromFondsData['fondssoort']);
  $object2->set('client', $fromClientData['Client']);

  if ( $data['from_transactie'] === 'V' ) {
    if ( $fromFondsData['fondssoort']  == 'AAND' || $fromFondsData['fondssoort']  == 'OBL' ) {
      $object->set('transactieSoort', 'V');
    } else {
      $object->set('transactieSoort', 'VO');
      if ( isset ($data['from_aantal']) && $data['from_aantal'] > 0 ) {
        $object->set('transactieSoort', 'VS');
      }
    }
  } elseif ( $data['from_transactie'] === 'A' ) {

    if ( $fromFondsData['fondssoort']  == 'AAND' || $fromFondsData['fondssoort']  == 'OBL' ) {
      $object->set('transactieSoort', 'A');
    } else {
      $object->set('transactieSoort', 'AS');
      if ( isset ($data['from_aantal']) && $data['from_aantal'] > 0 || ( isset($data['has_aantal']) && $data['has_aantal'] > 0) ) {
        $object->set('transactieSoort', 'AO');
      }
    }
  }

  if ( isset ($data['from_aantal']) ) {
    $object2->set('aantal', $data['from_aantal']);
  }


  $soort = 'E';
  if ( isset ($data['soort']) && $data['soort'] === 'N' ) {
    $soort = 'N';
  }
  if ( isset ($data['soort']) && $data['soort'] === 'O' ) {
    $soort = 'O';
  }

  if ( isset ($data['from_bedrag']) ) {
    $object2->set('bedrag', $data['from_bedrag']);
  }

//
  $object2->set('portefeuille', $data['from_portefeuille']);
//  $object->set('transactieSoort', 'A');
  $object->set("tijdsLimiet", jul2sql(time()));
  $editObject->formVars['portefeuille_id'] = $fromClientData['id'];
  $editObject->formVars['fromRapport'] = 1;
  $editObject->formVars['return'] = 'test';
  $object->set('copyFrom', '0999999990');
  $editObject->formVars['copyFrom'] = '0999999990';
  $data['copyid'] = '0999999990';

  $forceredirect = $_SESSION['currentHtmlRapportUrl'];

  //Memo en tijdslimiet vullen
  $object->set("memo", $standaard['OrderStandaardMemo']);
  $object->set("tijdsLimiet", jul2sql(time()));

  $fixDepotbankenPerVermogensbeheerderObj = new FixDepotbankenPerVermogensbeheerder();
  $fixDepotbankenPerVermogensbeheerderData = $fixDepotbankenPerVermogensbeheerderObj->parseBySearch( array('depotbank'=> $fromClientData['Depotbank'],'vermogensbeheerder' => $fromClientData['Vermogensbeheerder']));

  $object->set('fixOrder', intval($fixDepotbankenPerVermogensbeheerderData['fixDefaultAan']));
  if ( intval($fixDepotbankenPerVermogensbeheerderData['fixDefaultAan']) === 1 ) {
    $object->set('careOrder', intval($fixDepotbankenPerVermogensbeheerderData['careOrderVerplicht']));
  }
}



/**
 * Voor restant naar nieuwe order
 */
if ( isset($data['toAdd']) && intval($data['toAdd']) > 0 )
{
  $object2->set('aantal', $data['toAdd']);
}

$editObject->formVars['rekeningNrTonen'] = '';
$editObject->formVars['fixTonen'] = '';
$editObject->formVars['OrderuitvoerBewaarder'] = 0;

if (isset($data['getRekeningFieldStatus']) || ($action != 'update' && $object->get('id') > 0) || $editObject->formVars['fromRapport'] == 1)
{
//  Depotbanken->orderRekeningTonen
  $thisPortefeuille = '';
  $rekeningNrTonen = 0;
  $fixTonen = 0;

  if (isset($data['portefeuille']) && !empty($data['portefeuille']))
  {
    $thisPortefeuille = $data['portefeuille'];
  }
  else
  {
    $thisPortefeuille = $object2->get('portefeuille');
  }

  if ( empty ($thisPortefeuille) && ($object->get('OrderSoort') === 'M' || $object->get('OrderSoort') === 'X' || $object->get('OrderSoort') === 'O') )
  {
    $orderRegelObj = new orderRegelsV2();
    $thisPortefeuille = $orderRegelObj->parseBySearch(array('orderid' => $object->get('id')),'portefeuille',null,1);
  }

  if (empty($thisPortefeuille))
  {
    if (requestType('ajax'))
    {
      echo $AEJson->json_encode(
        array(
          'success' => false,
          'message' => 'Portefeuille gevevens konden niet worden opgehaald'
        )
      );
    }
    else
    {
//      exit('Portefeuille gevevens konden niet worden opgehaald');
    }
  }
  /** ophalen vermogensbeheerder voor geselecteerde Portefeuilles **/
  $portefeuilleObject = new Portefeuilles ();
  $portefeuilleData = $portefeuilleObject->parseBySearch(
    array('Portefeuille' => $thisPortefeuille),
    array('Vermogensbeheerder', 'Depotbank')
  );

  //rekeningnrtonen op depotbank niveau
  $depotbankObj = new Depotbank ();
  $depotbankData = $depotbankObj->parseBySearch(
    array('Depotbank' => $portefeuilleData['Depotbank']),
    array('orderRekeningTonen')
  );
  $rekeningNrTonen = $depotbankData['orderRekeningTonen'];
  
  $vermogensbeheerderObj = new Vermogensbeheerder ();
  $vermogensbeheerderData = $vermogensbeheerderObj->parseBySearch(
    array('vermogensbeheerder' => $portefeuilleData['Vermogensbeheerder']),
    array('OrderuitvoerBewaarder')
  );
  $orderuitvoerBewaarder = $vermogensbeheerderData['OrderuitvoerBewaarder'];
 
  //rekeningnrtonen op fixdepotbankpervermogensbeheerder niveau
  $fixDepotbankenPerVermogensbeheerderObj = new FixDepotbankenPerVermogensbeheerder();
  $fixDepotbankenPerVermogensbeheerderData = $fixDepotbankenPerVermogensbeheerderObj->parseBySearch( array('depotbank'=> $portefeuilleData['Depotbank'],'vermogensbeheerder' => $portefeuilleData['Vermogensbeheerder']));
  if (!empty ($fixDepotbankenPerVermogensbeheerderData))
  {
    $rekeningNrTonen = $fixDepotbankenPerVermogensbeheerderData['rekeningNrTonen'];
  }


  if (isset($fixDepotbankenPerVermogensbeheerderData['depotbank']))
  {
    $fixTonen = 1;
  }

  if ( ($soort == 'M' || $soort == 'X') ) {
    if ( ( isset ($fixDepotbankenPerVermogensbeheerderData['meervoudigViaFix']) && (int)$fixDepotbankenPerVermogensbeheerderData['meervoudigViaFix'] === 0 ) ) {
      $fixTonen = 0;
    }


    // Bij order per bewaarder de 1e orderregel ophalen en een extra check doen of we fix mogen gebruiken
    if ( (int) $orderuitvoerBewaarder === 1 && $object2->get('id') == 0) {
      /** Haal in een van de order regels het rekening nummer op. **/
      $orderRegelObj = new orderRegelsV2();
      $rekeningnr = $orderRegelObj->parseBySearch(
        array('orderid' => $object->get('id')),
        'rekening'
      );

      /** ophalen vermogensbeheerder voor geselecteerde Portefeuilles **/
      $portefeuilleObject = new Portefeuilles ();
      $portefeuilleVermogensbeheerder = $portefeuilleObject->parseBySearch(
        array('Portefeuille' => $thisPortefeuille),
        'Vermogensbeheerder'
      );

      /** ophalen rekeningDepotbank **/
      $rekeningenObj = new Rekeningen ();
      $rekeningDepotbank = $rekeningenObj->parseBySearch(
        array('Rekening' => $rekeningnr, 'Portefeuille' => $thisPortefeuille),
        'Depotbank'
      );

      /** Ophalen van de fix gegevens */
      $fixDepotbankObj = new FixDepotbankenPerVermogensbeheerder ();
      $fixDepotbankData = $fixDepotbankObj->parseBySearch(
        array('Vermogensbeheerder' => $portefeuilleVermogensbeheerder, 'depotbank' => $rekeningDepotbank)
        ,array('fixDefaultAan', 'meervoudigViaFix', 'nominaalViaFix', 'meervNominaalFIX', 'careOrderVerplicht')
      );

      if ( ! empty($fixDepotbankData) ) {
        $fixDepotbankData['showfix'] = 1;
        if ( (int) $fixDepotbankData['meervoudigViaFix'] === 1 ) {
          $fixTonen = 1;
        }
      }
    }

  }



  if ( ( $soort == 'N'  ) && ( isset ($fixDepotbankenPerVermogensbeheerderData['nominaalViaFix']) && (int)$fixDepotbankenPerVermogensbeheerderData['nominaalViaFix'] === 0 ) ) {
    $fixTonen = 0;
  }

  if (  $soort == 'O' && ( isset ($fixDepotbankenPerVermogensbeheerderData['meervNominaalFIX']) && (int)$fixDepotbankenPerVermogensbeheerderData['meervNominaalFIX'] === 0 ) )
  {
    $fixTonen = 0;
  }

    //resultaat terug geven bij ajax
  if (requestType('ajax'))
  {
    echo $AEJson->json_encode(
      array(
        'success'         => true,
        'rekeningNrTonen' => $rekeningNrTonen,
        'fixTonen'        => $fixTonen,
        'fixDefaultAan'   => ($fixDepotbankenPerVermogensbeheerderData['fixDefaultAan'] == 1 ? true : false),
        'adviesRelatie'   => isAdviesRelatie ($thisPortefeuille),
      )
    );
    exit();
  }

  $editObject->formVars['rekeningNrTonen'] = $rekeningNrTonen;
  $editObject->formVars['OrderuitvoerBewaarder'] = $orderuitvoerBewaarder;
  $editObject->formVars['fixTonen'] = $fixTonen;
}



//OrderuitvoerBewaarderFix
if (isset($data['OrderuitvoerBewaarderFix']) ) {
  
  $returnData = array(
    'showfix'             => 0,
    'fixDefaultAan'       => 0,
    'meervoudigViaFix'    => 0,
    'nominaalViaFix'      => 0,
    'careOrderVerplicht'  => 0
  );
  
  if (isset($data['portefeuille']) && !empty($data['portefeuille']))
  {
    $thisPortefeuille = $data['portefeuille'];
  }
  else
  {
    $thisPortefeuille = $object2->get('portefeuille');
  }
  
  /** ophalen vermogensbeheerder voor geselecteerde Portefeuilles **/
  $portefeuilleObject = new Portefeuilles ();
  $portefeuilleData = $portefeuilleObject->parseBySearch(
    array('Portefeuille' => $thisPortefeuille),
    array('Vermogensbeheerder')
  );
  
  /** ophalen rekeningDepotbank **/
  $rekeningenObj = new Rekeningen ();
  $rekeningData = $rekeningenObj->parseBySearch(
    array('Rekening' => $data['rekening'], 'Portefeuille' => $thisPortefeuille),
    array('Depotbank')
  );
  
  $fixDepotbankObj = new FixDepotbankenPerVermogensbeheerder ();
  $fixDepotbankData = $fixDepotbankObj->parseBySearch(
    array('Vermogensbeheerder' => $portefeuilleData['Vermogensbeheerder'], 'depotbank' => $rekeningData['Depotbank'])
    ,array('fixDefaultAan', 'meervoudigViaFix', 'nominaalViaFix', 'careOrderVerplicht')
  );
  
  if ( ! empty($fixDepotbankData) ) {
    $fixDepotbankData['showfix'] = 1;
    $returnData = array_merge($returnData, $fixDepotbankData);
  }
  
  echo $AEJson->json_encode($returnData);
  exit();
}




/**
 * Optie expiratie datum opmaken
 */
$OptieExpDatum = $object->data['fields']['optieExpDatum']['value'];
$expJaarDb = substr($OptieExpDatum, 0, 4);
$expMaandDb = substr($OptieExpDatum, 4, 2);

$huidigeJaar = date('Y') - 1; //get current year minus one for history

$i = 0;
$OptieExpJaar = '';
for ($i; $i < 10; $i++)
{
  $expJaar = $huidigeJaar + $i;
  if ($expJaar == $expJaarDb)
  {
    $OptieExpJaar .= "<option value=\"" . $expJaar . "\" SELECTED>" . $expJaar . "</option>";
  }
  else
  {
    $OptieExpJaar .= "<option value=\"" . $expJaar . "\" >" . $expJaar . "</option>";
  }
}
$editObject->formVars["OptieExpJaar"] = $OptieExpJaar;

$OptieExpMaand = '';
$huidigeMaand = date('n');
for ($i = 1; $i < 13; $i++)
{
  if ($i < 10)
  {
    $maandString = '0' . $i;
  }
  else
  {
    $maandString = $i;
  }

  if ($i == $expMaandDb)
  {
    $OptieExpMaand .= "<option value=\"$maandString\" SELECTED>" . $__appvar["Maanden"][$i] . " </option>";
  }
  else
  {
    $OptieExpMaand .= "<option value=\"$maandString\" >" . $__appvar["Maanden"][$i] . " </option>";
  }
}
$editObject->formVars["OptieExpMaand"] = $OptieExpMaand;

$vermogensbeheerderKeuze = unserialize($standaard['OrderStatusKeuze']);

//listarray($vermogensbeheerderKeuze);
//listarray($selectStatus);
//$object->set("vermogensBeheerder",$__appvar['bedrijf']);
if ($action == "new" || $action == "edit")
{
  /** copieren form voor nieuw fonds **/
  $object->data['fields']['fondsISINCode'] = $object->data['fields']['ISINCode'];
  $object->data['fields']['fondsISINCode']['form_extra'] = 'oninput="let p=this.selectionStart;this.value=this.value.toUpperCase();this.setSelectionRange(p, p);"';
  
  $object->data['fields']['fondsFonds'] = $object->data['fields']['fonds'];
  $object->data['fields']['fondsFonds']['form_visible'] = true;
  $object->set('fondsFonds', $object->get('fondsOmschrijving'));

  $object->data['fields']['fondsFondsOmschrijving'] = $object->data['fields']['fondsOmschrijving'];
  $object->data['fields']['fondsFondseenheid'] = $object->data['fields']['fondseenheid'];
//  $object->data['fields']['fondsFondsBeurscode'] = $object->data['fields']['fondsBeurscode'];
  $object->data['fields']['fondsFondsValuta'] = $object->data['fields']['fondsValuta'];

  /** copieren form voor nieuw optie **/
  $object->data['fields']['optieISINCode'] = $object->data['fields']['ISINCode'];
  $object->data['fields']['optieFonds'] = $object->data['fields']['fonds'];
  $object->data['fields']['optieFonds']['form_visible'] = true;

  $object->set('optieFonds', $object->get('fondsOmschrijving'));
  $object->data['fields']['optieFondsOmschrijving'] = $object->data['fields']['fondsOmschrijving'];

  $object->data['fields']['optieFondseenheid'] = $object->data['fields']['fondseenheid'];
  $object->data['fields']['optieBeurs'] = $object->data['fields']['beurs'];
  $object->data['fields']['optieFondsValuta'] = $object->data['fields']['fondsValuta'];
  $object->data['fields']['optieOptieSymbool'] = $object->data['fields']['optieSymbool'];
  $object->data['fields']['optieOptieType'] = $object->data['fields']['optieType'];
  $object->data['fields']['optieOptieUitoefenprijs'] = $object->data['fields']['optieUitoefenprijs'];
  $object->data['fields']['optieOptieExpDatum'] = $object->data['fields']['optieExpDatum'];

  $object->data['fields']['fondsFondsOmschrijving']['form_extra'] = '';
  $object->data['fields']['optieFondsOmschrijving']['form_extra'] = '';

  /** set fields to hidden **/
//    $object->data['fields']['ISINCode']['form_type'] = 'hidden';
//    $object->data['fields']['fondsOmschrijving']['form_type'] = 'hidden';
  $object->data['fields']['fondseenheid']['form_type'] = 'hidden';
  $object->data['fields']['fondsBeurscode']['form_type'] = 'hidden';
  $object->data['fields']['fondsValuta']['form_type'] = 'hidden';

  $object->data['fields']['optieSymbool']['form_type'] = 'hidden';
  $object->data['fields']['optieType']['form_type'] = 'hidden';
  $object->data['fields']['optieUitoefenprijs']['form_type'] = 'hidden';
  $object->data['fields']['optieExpDatum']['form_type'] = 'hidden';

  $object->set("status", date("Ymd_Hi") . "/$USR ** aanmaken order");
  if($action == "new")
  {
    $object->set("memo", $standaard['OrderStandaardMemo']);

    $object->set("tijdsLimiet", jul2sql(time()));
  }
}


$editObject->formVars['addFonds'] = $AETemplate->parseBlockFromFileWithForm('classTemplates/orders/newOrderSelectie.html',$editObject->formVars, $object);

/** laad template gebaseerd op geselecteerde soort **/
$editObject->formTemplate = "classTemplates/orders/ordersEdit" . $soort . "Template.html";

/** laden van javascript en css **/
$editcontent['javascript'] = str_replace(
  'document.editForm.submit();',
  'if(checkPage())document.editForm.submit();',
  $editcontent['javascript']
);



/**
 * opmaken van soort order selectie
 */
$disabled = array('M' => '', 'E' => '', 'C' => '', 'N' => '', 'O' => '', 'F' => '', 'X' => '');
if ($action <> 'new')
{
  if (isset ($_GET['OrderSoort']) && $_GET['OrderSoort'] <> '')
  {
    unset($disabled[$_GET['OrderSoort']]);
  }
}
$selected = array('M' => '', 'E' => '', 'C' => '', 'N' => '', 'O' => '', 'F' => '', 'X' => '');
$selected[$soort] = 'checked ';
$orderTypen = array(
  'M' => vt('Meervoudig (1 fonds; meerdere portefeuilles)'),
  'E' => vt('Enkelvoudig (1 portefeuille; 1 fonds)'),
  'C' => vt('Combinatie (1 portefeuille; meerdere fondsen)'),
  'N' => vt('Nominaal Enkelvoudig- Bel. fondsen (1 portefeuille; 1 fonds)'),
  'O' => vt('Nominaal Meervoudig - Bel. fondsen (1 fonds; meerdere portefeuilles)')
);


if ( checkOrderAcces('orderFxToestaan') === true ) {
  $orderTypen['F'] = vt('FX-transacties Enkelvoudig (1 portefeuille; 1 fonds)');
  $orderTypen['X'] = vt('FX-transacties Meervoudig (1 fonds; meerdere portefeuilles)');
}


$editObject->formVars["newOrder"] = '
    <div class="row"><div class="formHolder box box12">
      <div class="formTitle textB">' . vt('Soort order') . '</div>
      <div class="padded-10 formContent"><table>
      ';

//  $editObject->formVars["newOrder"]='<span><fieldset><legend><b>Soort order</b></legend><table>';
foreach ($orderTypen as $key => $value)
{
  $editObject->formVars["newOrder"] .= '<tr><td><input type="radio" name="orderSelectieType" value="' . $key . '" ' . $selected[$key] . " " . $disabled[$key] . ' onClick="document.location=\'?action=new&orderSelectieType=' . $key . '\'"> ' . $value . ' </td></tr>';
}
//  $editObject->formVars["newOrder"].='</table></fieldset></span><br>';
$editObject->formVars["newOrder"] .= '</table></div></div></div>';
$editObject->formVars['orderIdentificatie'] = $__appvar["bedrijf"] . $object->get('id');
$editObject->formVars['orderRegelPositie'] = $object2->get('positie');

/** einde soort order **/

$editcontent['script_voet'] .= "$('#uitvoeringen').load(encodeURI('orderuitvoeringListV2.php?orderid=' + $('#id').val()));";
if ($action <> 'new' && $soort <> 'E')
{
  $editcontent['script_voet'] .= "$('#orderregels').load(encodeURI('orderregelsListV2.php?listonly=1&action=new&orderid=' + $('#id').val()+'&batchId='+ $('#batchId').val() ));";
}




/**
 * Ophalen van extra gegevens bij de volgende acties:
 * Wijzigen en opnieuw inleggen van een order
 */
$editObject->formVars['showNotas'] = 0;
$editObject->formVars['addNota'] = '';
$editObject->formVars['notaModule'] = 0;

$editObject->formVars['depobankValue'] = '';
$editObject->formVars['profileValue'] = '';
if ($object->get('id') > 0 || (isset($data['copyid']) && $data['copyid'] > 0))
{

  $portefeuilleObject = new Portefeuilles ();
  $portefeuilleData = $portefeuilleObject->parseBySearch(
    array(
      'Portefeuille' => $object2->get('portefeuille')
    ),
    array(
      'Depotbank',
      'Risicoklasse',
      'Vermogensbeheerder'
    )
  );

  $editObject->formVars['depobankValue'] = $portefeuilleData['Depotbank'];
  $editObject->formVars['profileValue'] = $portefeuilleData['Risicoklasse'];


  /** controlleren of we notas mogen maken  */
  $editObject->formVars['fondsValutaKoers']=1;
  $editObject->formVars['memo_value']=$object->get('memo');
  $editObject->formVars['fondsValuta']=$object->get('fondsValuta');
  $rekening=new Rekeningen();
  $rekening->getByField('Rekening',$object2->get('rekening'));
  $editObject->formVars['rekValuta']=$rekening->get('Valuta');
  $uitvoeringen = new OrderUitvoeringV2();
  $uitvoeringsValutakoers=$uitvoeringen->uitvoeringsValutakoers($object->get('id'));
  if($uitvoeringsValutakoers <> 0)
    $editObject->formVars['fondsValutaKoers']=$uitvoeringsValutakoers;


  if (  checkOrderAcces('notaModule') === true ) {
    $huidigeStatus = intval($object->get("orderStatus"));

    $editObject->formVars['notaModule'] = 1;

    if ( ( $soort === 'M' || $soort === 'O' || $soort === 'N' ) && $huidigeStatus >=2 ) {
     $editObject->formVars['notaHerrekenKnop'] = '<span style="float: right; margin-left: 10px;" id="btnRecalculateNota" class="btn-new btn-default">' . vt('Herberekenen') . '</span> <span id="orderNotaMelding">  </span>';

      $editObject->formVars['showNotas'] = 0;
      $editObject->formVars['addNota'] = $AETemplate->parseBlockFromFileWithForm(
        'classTemplates/orders/orderEditNotasTemplate.html',
        $editObject->formVars,
        $object2
      );
    }

    if ($object2->get('id') > 0)
    {
      if ($object2->get('notaDefinitief') == 0 ) {
        $editObject->formVars['notaHerrekenKnop'] = '<span style="float: right; margin-left: 10px;" id="btnRecalculateNota" class="btn-new btn-default">' . vt('Herberekenen') . '</span> <span id="orderNotaMelding">  </span>';

        if ( $soort === 'E' || $soort === 'C' || $soort === 'N' ) {
          $editObject->formVars['notaFormHerrekenKnop'] = '<span style="float: right; margin-left: 10px;" id="btnRecalculateNota" class="btn-new btn-default">' . vt('Herberekenen') . '</span> <span id="orderNotaMelding">  </span>';
        }
      }
      $editObject->formVars['showNotas'] = 1;
      $editObject->formVars['addNota'] = $AETemplate->parseBlockFromFileWithForm(
        'classTemplates/orders/orderEditNotasTemplate.html',
        $editObject->formVars,
        $object2
      );
    }

    $notaOrderregels=$object2;

    if($object->get('depotbank') == 'KAS')
      $notaOrderregels->set('PSET','KASBANK');

    $query="SELECT code,type FROM PSAFperFonds WHERE fonds='".mysql_real_escape_string($object->get('fonds'))."'";
    $db->SQL($query);
    $db->Query();
    while($dbData=$db->nextRecord())
    {
      if($notaOrderregels->get($dbData['type']) == '')
        $notaOrderregels->set($dbData['type'],$dbData['code']);
    }
    $form=new form($notaOrderregels);

    $editObject->formVars['voorkeursOrderReden'] = str_replace('"orderReden"','"voorkeursOrderReden"',$form->makeInput('orderReden'));
    $editObject->formVars['voorkeursPSAF'] = str_replace('"PSAF"','"voorkeursPSAF"',$form->makeInput('PSAF'));
    $editObject->formVars['voorkeursPSET'] = str_replace('"PSET"','"voorkeursPSET"',$form->makeInput('PSET'));

  }

  /** Einde controlleren of we notas mogen maken */

}
/** einde ophalen extra gegevens **/


$adviesRelatie = false;
$adviesStatus = 0;
$adviesBericht = '';

$editObject->formVars["annuleerKnop"] = '';
/** wanneer we een order editen of opnieuw gaan inleggen **/

$editObject->formVars['orderdepotbank'] = '';


if ($object->get('id') > 0)
{
  $adviesStatus = checkOrderAcces ('orderAdviesNotificatie');

  /** fondsdepotbankcode ophalen */
  $fondsObj = new Fonds();
  $fondsData = $fondsObj->parseBySearch(array('fonds' => $object->get('fonds')));
  $editObject->formVars['fondsData'] = $AEJson->json_encode($fondsData);

  $editObject->formVars['orderdepotbank'] = $object->get('depotbank');
  $editObject->formVars['isFixOrder'] = $object->get('fixOrder');


  $object->setOrderregelObject($object2);
  $editObject->template['eigenFocus'] = 'return;';

  if ( $soort === 'M' || $soort === 'O' ) {
    $adviesRelatie = false;
    $orderRegelObj = new OrderRegelsV2();
    $orderRegelsDatas = $orderRegelObj->parseBySearch(array('orderid' => $object->get('id')), 'all', null, -1);
    $adviesBericht = '';
    foreach ( $orderRegelsDatas as $orderRegelsData ) {
      $adviesBericht = '';
      if ( isAdviesRelatie ($orderRegelsData['portefeuille']) === true ) {
        $adviesRelatie = true;
        $adviesBericht .= ( ! empty ($adviesRelatieMMessage) ? '<br />':'') . 'Let op: portefeuille ' . $orderRegelsData['portefeuille'] . ' betreft een adviesrelatie';
      }
    }
    if ( ! empty ($adviesRelatieMMessage) ) {
      $AEMessage->setMessage($adviesBericht, 'info');
    }
  } else {
    $adviesRelatie = isAdviesRelatie ($object->orderregelObject->data['fields']['portefeuille']['value']);

    $object->adviesRelatie = $adviesRelatie;
    $object->adviesStatus = $adviesStatus;

    if ( $adviesRelatie === true ) {
      $adviesBericht .= vt('LET OP: Dit is een advies relatie');
    }


    if ( $adviesStatus > 0 ) {
      $crmObj = new naw();
      $crmNawEmail = $crmObj->parseBySearch(array ('portefeuille' => $thisPortefeuille), 'email');

      if ( $adviesRelatie === true && ! $crmNawEmail && ( $adviesStatus === 1 || $adviesStatus === 3 ) ) {
        $adviesBericht .= '<br />' . vt('Order kan niet worden doorgezet E-mail adres van relatie ontbreekt');
      } elseif ( ! $crmNawEmail && ( $adviesStatus === 2 || $adviesStatus === 4 ) ) {
        $adviesBericht .= '<br />' . vt('E-mail adres van relatie ontbreekt');
      } elseif ( ! $crmNawEmail && $adviesRelatie === true && $adviesStatus === 2 ) {
        $adviesBericht .= '<br />' . vt('Order kan niet worden doorgezet E-mail adres van relatie ontbreekt');
      }  elseif ( ! $crmNawEmail && $adviesRelatie === false && $adviesStatus === 2 ) {
        $adviesBericht .= vt('E-mail adres van relatie ontbreekt');
      }
    }

    if ( ! empty ($adviesBericht) ) {
      $AEMessage->setMessage($adviesBericht, 'info');
    }
  }

  /** ophalen van de logs **/
  $orderLogs = new orderLogs();
  $logData = $orderLogs->getForOrder($object->get('id'));
  $editObject->formVars['orderLogs'] = '';

  foreach ($logData as $log)
  {
    $log['message'] = preg_replace('/[^a-zA-Z0-9_>\. -]/s','',$log['message']);
    $log['message'] = str_replace('__', '', $log['message']);
    
    $editObject->formVars['orderLogs'] .= date(
        'd-m-Y H:i:s',
        db2jul($log['change_date'])
      ) . $log['timeOffset'] . '/' . $log['add_user'] . ' - ' . (($log['fixOrderId'] != 0)?$log['fixOrderId']:"") . '' . $log['message'] . '<br />';
  }

  /** bepaal wat we mogen wijzigen **/
  $huidigeStatus = intval($object->get("orderStatus"));


  if ( (int) $huidigeStatus <= 2 && ! isset ($editObject->object->locked) || $editObject->object->locked !== true)
  {
    $uitvoeringen = new OrderUitvoeringV2();
    $uitvoeringenVerschil = $uitvoeringen->uitvoeringenVerschil($object->get('id'));

    $annuleerTime = db2jul($object->get('fixAnnuleerdatum'));

    /**
     * Tijdelijke fix voor Nominaal orders
     * Bij order soort = Nominaal
     * Status === doorgegeven === 1
     *
     * Zet het uitvoeringsverschil op 1
     */
    if ( ($soort === 'N' || $soort === 'O') && $huidigeStatus === 1 ) {
      $uitvoeringenVerschil = 1;
    }
    /**
     * Annuleer knoppen & meldingen
     */
    if ($object->get('fixAnnuleerdatum') == '0000-00-00 00:00:00' && $uitvoeringenVerschil > 0)
    {
      if (
        //if fix order
        (
          $object->get('fixOrder') == 1
          && (
            ($huidigeStatus < 1 && $object->get("add_user") === $USR)
            || checkOrderAcces ('handmatig_verzenden') === true
          )
        )
        // geen fix order
        || (
          $object->get('fixOrder') == 0
          && (
            ($huidigeStatus < 1 && $object->get("add_user") === $USR)
            || checkOrderAcces ('handmatig_volgendeStatus') === true
          )
        )
      ) {
        if ($huidigeStatus == 2 && $uitvoeringenVerschil > 0)
        {
          $editObject->formVars["annuleerKnop"] = '<div class="box "><button tabindex="0" class="btn-new btn-delete" id=\'cancelOrder\'>' . vt('Annuleer restant') . '</button></div>';
        }
        else
        {
          if ( (intval($object->get('orderStatus')) !== 6 && intval($object->get('orderStatus')) !== 7) ) {
            $editObject->formVars["annuleerKnop"] = '<div class="box "><button tabindex="0" class="btn-new btn-delete" id="cancelOrder">' . vt('Annuleren') . '</button></div>';
          }

          if ( $soort === 'M' || $soort === 'O') {
            $editObject->formVars["annuleerRegelKnop"] = '<div class="box "><button tabindex="0" class="btn-new btn-delete" id=\'cancelOrderRegel\'>' . vt('Verwijderen') . '</button></div>';
          }
        }
      }

      if ($object->get("orderSubStatus") > 3)
      {
        $editObject->formVars["annuleerKnop"] = 'Fix status > 3, geen annulering nodig.';
      }
    }
    elseif ( ($soort <> 'N' && $soort <> 'O' ) && $uitvoeringenVerschil < 0)
    {
      $editObject->formVars["annuleerKnop"] = vt('Meer uitvoeringen dan stukken in orderregels!');
    }
    
    if ( $object->get('fixAnnuleerdatum') != '0000-00-00 00:00:00' )
  {
    $editObject->formVars["annuleerKnop"] = vt('Annuleer verzoek op') . ' ' . date(
        'd-m-Y \o\m H:i:s',
        $annuleerTime
      ) . ' ' . vt('verzonden.');
  }

  }

  if ($huidigeStatus >= 1)
  {
    $ISINCodeClass = 'notEditable';
    $PortefeuilleSelectieClass = 'notEditable';
    $object2->addClass('portefeuille', 'notEditable');
    $object2->addClass('rekening', 'notEditable');
    $object->addClass('fondsOmschrijving', 'notEditable');
    $object->addClass('transactieSoort', 'notEditable');
    $object->addClass('transactieType', 'notEditable');

    // Bij fix mag het koerslimier nooit gewijzigd worden
    if ( (int) $object->get('fixOrder') === 1 || (int) $object->get('orderStatus') >= 3 ) {
      $object->addClass('koersLimiet', 'notEditable');
    }

    $object->addClass('tijdsSoort', 'notEditable');
    $object->addClass('tijdsLimiet', 'notEditable');
    $object->addClass('beurs', 'notEditable');

    $object2->addClass('aantal', 'notEditable');

    /** @todo Volgens mij kan dit weg * */
//      $object->setOption("orderStatus","form_extra"," onChange='orderStatusChange()' ");
  }

  if ($huidigeStatus > 2)
  {
    $object->setOption('transactieType', 'form_extra', "DISABLED ");
    $object->setOption('transactieSoort', 'form_extra', "DISABLED ");
  }
}
else
{
  /** wanneer we een order aanmaken **/
  // $object->data['fields']['orderStatus']['form_type'] = 'hidden';
  // $object->data['fields']['orderStatus']['form_value'] = '0';

  $editObject->formVars['statusClass'] = 'hideElement';
  $editObject->formVars['orderIdentificatie'] = '';
}

if($thisOrderId=='' && $action != "update" )
{
  if (($soort == 'M' || $soort == 'O' || $soort == 'C') && checkOrderAcces('VermogensbeheerderOrderOrderdesk') )  //
  {
    $object->setOption("orderStatus", "form_options", array(-1 => $__ORDERvar["orderStatus"][-1]));
    $object->set('orderStatus',-1);
  }
  else
  {
    $object->setOption("orderStatus", "form_options", array(0 => $__ORDERvar["orderStatus"][0]));
    $object->set('orderStatus',0);
  }
}
else
{
  $selectStatus = $object->getOrderStatusOpties($vermogensbeheerderKeuze, $object->get('orderStatus'));
  $object->setOption("orderStatus", "form_options", $selectStatus);
}


/**
 * advies relatie tonen/opbouwen
 */
$editObject->formVars['relationHasEmail'] = '0';
$editObject->formVars['addadvise'] = '';

$adviesEmailVerzonden = false;
$adviesEmailNegeerd = false;

$editObject->formVars['adviesEmailVerzonden'] = 0;

$editObject->formVars['viewAdviceMail'] = ( isset($data['viewAdviceMail']) ? $data['viewAdviceMail']:0);
$editObject->formVars['orderredenVerplicht'] = 0;



$editObject->formVars['adviesStatus'] = $adviesStatus;

if ( ($adviesRelatie === true || ($adviesStatus === 3 || $adviesStatus === 4 || $adviesStatus === 5) ) && $object2->get('id') > 0 ) {
  $AEMailTemplate = new AE_MailTemplate();
  $AETemplate->cleanupTags = false;
  $aeconfig = new AE_config();
  $editObject->formVars['orderredenVerplicht'] = $standaard['orderredenVerplicht'];

  $newTemplateEdit = '';

  $crmObj = new naw();
  $crmNawEmail = $crmObj->parseBySearch(array ('portefeuille' => $thisPortefeuille), 'email');
  $editObject->formVars['relationHasEmail'] = ( $crmNawEmail ? '1':'0');


  $editObject->formVars['adviesEmailVerzonden'] = 0;
  if ( $object2->get('mailBevestigingVerzonden') !== '0000-00-00 00:00:00' ) {
    if ( $object2->get('mailBevestigingData') === 'ignored ' ) {
      $adviesEmailGenegeerd = true;
    } else {
      $adviesEmailVerzonden = true;
      $editObject->formVars['adviesEmailVerzonden'] = '1';
    }
    $editObject->formVars['adviseOrderSendDate'] = dbdate2form($object2->get('mailBevestigingVerzonden'));
  }

  $editObject->formVars['checkOrderVierOgen'] = (checkOrderAcces ('orderVierOgen') === true ? '1':'0');

  $crmObj = new Naw();
  $crmNawData = $crmObj->parseBySearch(array ('portefeuille' => $thisPortefeuille));
  
  
  $db = new DB();
  $vermogensBeheerderBccQuery = "
    SELECT Portefeuille, Portefeuilles.Vermogensbeheerder, orderAdviesBcc from Portefeuilles
    LEFT JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder  = Portefeuilles.Vermogensbeheerder
    where Portefeuilles.Portefeuille = '" . mysql_real_escape_string($thisPortefeuille) . "'
  ";
  $db->SQL($vermogensBeheerderBccQuery);
  $db->Query();
  $vermogensBeheerderBcc = $db->nextRecord();
  $vermogensBeheerderBcc = $vermogensBeheerderBcc['orderAdviesBcc'];
  
  $fondsen = new Fonds();
  // bij de preview ook de data verwerken
  $editObject->formVars['adviseSenderName'] = $_SESSION['usersession']['gebruiker']['Naam'];
  $editObject->formVars['adviseSenderEmail'] = $_SESSION['usersession']['gebruiker']['emailAdres'];

  if ( isset($data['viewAdviceMail']) && (int) $data['viewAdviceMail'] === 1 ) {
    
    $orderRedenen = new Orderredenen();

    //CRM gegevens ophalen
    foreach ($crmNawData as $key => $value) {
      $ordersConformMailData[$key] = $value;
    }

    //portefeuille gegevens ophalen
    $portefeuilleObj = new Portefeuilles();
    $portefeuilleData = $portefeuilleObj->parseBySearch(array ('Portefeuille' => $thisPortefeuille));
    foreach ($portefeuilleData as $key => $value) {
      $ordersConformMailData[$key] = $value;
    }

    //Fonds gegevens ophalen
    $fondsData = $fondsen->parseBySearch(array ('Fonds' => $object->get('fonds')));
    foreach ($fondsData as $key => $value) {
      $ordersConformMailData[$key] = $value;
    }

    foreach ($object->data['fields'] as $key => $values) {
      $ordersConformMailData[$key] = $values['value'];
      $ordersConformMailData[$object->data['table'] . '.' . $key] = $value;
    }
    foreach ($object2->data['fields'] as $key => $values) {
      $ordersConformMailData[$key] = $values['value'];
      $ordersConformMailData[$object2->data['table'] . '.' . $key] = $value;
    }

    //Order reden ophalen
    $objOrderReden = $object2->get('orderReden');
    $orderRedenData = $orderRedenen->parseBySearch(array( 'orderreden' => $data['voorkeursOrderReden']), 'all', null, 1);
    $ordersConformMailData['orderReden'] = ( ! empty ($objOrderReden) ? $objOrderReden :  ( isset($orderRedenData['omschrijving']) ? $orderRedenData['omschrijving'] : '' ) ) ;

    $editObject->formVars['adviseSubject'] = ( isset($data['subject']) ? $data['subject'] : '' );
    $editObject->formVars['adviseOrderReden'] = ( ! empty ($objOrderReden) ? $objOrderReden :  ( isset($data['voorkeursOrderReden']) ? $data['voorkeursOrderReden'] : '' ) ) ;

    //mail gegevens ophalen
    $controleRegelArray=$object2->createCheckHtml(unserialize($ordersConformMailData['controleRegels']),true,true);
      $ordersConformMailData['controleRegels'] = '<table class="table table-boxed" id="">' .$controleRegelArray['html']. '</table>';
    $actieveChecks=getActieveControles();
    foreach($actieveChecks as $key=>$value)
    {
      $ordersConformMailData['controle' . ucfirst($key)] = '';
      $ordersConformMailData['controle' . ucfirst($key) . '2'] = '';
    }
    foreach($controleRegelArray['htmlMailLos'] as $key=>$htmlValue)
      $ordersConformMailData['controle'.ucfirst($key)] = '' .$htmlValue.'';
    foreach($controleRegelArray['htmlMailLos2'] as $key=>$htmlValue)
      $ordersConformMailData['controle'.ucfirst($key).'2'] = '' .$htmlValue. '';

    $ordersConformMailData['emailHandtekening'] = $_SESSION['usersession']['gebruiker']['emailHandtekening'];
    $ordersConformMailData['transactieSoort'] = $__ORDERvar['transactieSoort'][$object->get('transactieSoort')];
    $ordersConformMailData['transactieType'] = $__ORDERvar["transactieType"][$object->get('transactieType')];
    $ordersConformMailData['huidigeDatum'] = date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
    $ordersConformMailData['huidigeGebruiker'] = $USR;

    $ordersConformMailData['add_date'] = $ordersConformMailData['OrderRegelsV2.add_date'];
    $ordersConformMailData['change_date'] = $ordersConformMailData['OrderRegelsV2.change_date'];

    $query = "SELECT Naam,titel FROM Gebruikers WHERE Gebruiker='" . $USR . "'";
    $db = new DB();
    $db->SQL($query);
    $dataGebr=$db->lookupRecord();
    $ordersConformMailData['GebruikerNaam'] = $dataGebr['Naam'];
    $ordersConformMailData['GebruikerTitel'] = $dataGebr['titel'];

    $ordersConformMailData = $AEMailTemplate->getExtraFields($ordersConformMailData);
  
    $emailBody = $data['emailBody'];
    $AEMailTemplate->setData($ordersConformMailData);
    $emailBody = $AEMailTemplate->ParseData($emailBody);
    $editObject->formVars['adviseSubject'] = $AEMailTemplate->ParseData($editObject->formVars['adviseSubject']);
    
    
    
//    foreach ( $ordersConformMailData as $key => $val ) {
//      $editObject->formVars['adviseSubject']  = str_replace("[" . $key . "]", $val, $editObject->formVars['adviseSubject'] );
//      $emailBody = str_replace("[" . $key . "]", $val, $emailBody);
//    }

    $AETemplate->loadTemplateFromString($data['emailBody'], 'ordersConformMail');
    $editObject->formVars['adviseMail'] = $emailBody;
    $editObject->formVars['adviseMailHtml'] = htmlspecialchars($emailBody);
  } elseif ( $adviesEmailVerzonden === true ) {
    $mailBevestigingData = $object2->get('mailBevestigingData');
    $mailBevestigingData = unserialize($mailBevestigingData);

    $editObject->formVars['adviseSenderName'] = $mailBevestigingData['senderName'];
    $editObject->formVars['adviseSenderEmail'] = $mailBevestigingData['senderEmail'];
//    $editObject->formVars['adviseReceiverName'] = $mailBevestigingData[''];
//    $editObject->formVars['adviseReceiverEmail'] = $mailBevestigingData[''];
    $editObject->formVars['adviseSubject'] = $mailBevestigingData['subject'];
    $editObject->formVars['adviseOrderReden'] = $mailBevestigingData['orderReden'];
    $editObject->formVars['adviseMail'] = $mailBevestigingData['body'];

    $editObject->formVars['adviesEmailVerzonden'] = 1;

  } else {
    $ordersConformMailBody = $aeconfig->getData('ordersConformMail');
    $ordersConformMailSubject = $aeconfig->getData('ordersConformMailSubject');
    $AETemplate->loadTemplateFromString($ordersConformMailBody, 'ordersConformMail');
    $newTemplateEdit = $AETemplate->parseBlock('ordersConformMail', array());
  }

  $adviseForm = new Form($object2);
  $emailVars = array(
    'id'                        => $object->get('id'),
    'orderregelId'              => $object2->get('id'),
    'senderName'                => $_SESSION['usersession']['gebruiker']['Naam'],
    'senderEmail'               => $_SESSION['usersession']['gebruiker']['emailAdres'],
    'orderAdviesOrderReden'     => str_replace('"orderReden"','"voorkeursOrderReden"', $adviseForm->makeInput('orderReden')),
    'receiverName'              => $crmNawData['naam'],
    'receiverEmail'             => $crmNawData['email'],
    'subject'                   => $ordersConformMailSubject,
//  (isset($mailBevestigingData->subject) ? $mailBevestigingData->subject : 'Uw ' . $__ORDERvar['transactieSoort'][$object->get('transactieSoort')] . ' in ' . $object->get('fondsOmschrijving')),//'Bevestiging effectenorder '.$this->order['orderid'],
    //              'bodyHtml'=>$data['notaEmail']
    'emailBody'                 => $newTemplateEdit
    //$ordersConformMail . <br/>' . ,
  );
  
  
  //KidControlle
  $kidFormulier = $fondsen->parseBySearch(array ('Fonds' => $object->get('fonds')), 'KIDformulier');

  if (strpos($newTemplateEdit, '[link|KIDformulier|') !== false) {
    if ( empty ($kidFormulier) || $kidFormulier == 'Niet aanwezig' ) {
      $editObject->formVars['KIDEmptyMessage'] = '<div class="alert alert-info" style="margin-top: 5px; margin-bottom: 5px;">' . vt('Let op: KIDformulier link is leeg, maar wel aanwezig in template.') . '</div>';
    }
  }
  
//

  $editObject->formVars['adviseReceiverName'] = $crmNawData['naam'];
  $editObject->formVars['adviseReceiverEmail'] = $crmNawData['email'];
  $editObject->formVars['adviseSenderBccEmail'] = $vermogensBeheerderBcc;
  $editObject->formVars['addadvise'] = $AETemplate->parseBlockFromFile('./classTemplates/orders/addadvise.html', $emailVars);


  $editObject->formVars['adviesEmailGenegeerd'] = ($adviesEmailGenegeerd === true ? 1:0);

}


$editObject->formVars['adviesRelatie'] = ($adviesRelatie===true?1:0);


//blahblah
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/ordersEditV2.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask');
$editcontent['jsincludes'] .= $AETemplate->loadJs('jquery.isloading.min');
$editcontent['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask-masks');
$editcontent['jsincludes'] .= $AETemplate->loadJs('ckeditor4/ckeditor');
//$editcontent['style'] .= $AETemplate->loadCss('dataTables/jquery.dataTables.min');
$editcontent['script_voet'] .= $AETemplate->parseFile('/javascript/jquery-input-mask-masks.js');
$editcontent['script_voet'] .= $AETemplate->parseFile('orders/js/ordersEdit.js', $editObject->formVars);
$editcontent['style'] .= $AETemplate->loadCss('rekeningmutaties');


/** laad dingen op basis van geselecteerde soort **/
$editObject->formVars['tempFixOrder_inputfield'] = '';
$editObject->formVars['tempRekening_inputfield'] = '';
switch ($soort)
{
  case 'M':
    /** controlleren of alle regels gecontrolleerd zijn */
    $controlleValidated = false;
    if ( $object->get('id') > 0 ) {
      $controleStatusArr = $object2->parseBySearch(array('orderid' => $object->get('id'), 'controleStatus' => 2), "all", null, -1);
      if ( empty($controleStatusArr) ) {
        $controlleValidated = true;
      } else {
        $AEMessage->setMessage('Nog niet alle controles zijn correct!', 'error');
      }
    }

    $editcontent['script_voet'] .= $AETemplate->parseFile('orders/js/ordersMTemplate.js', $editObject->formVars);
    break;
  case 'E':
    $editcontent['script_voet'] .= $AETemplate->parseFile('orders/js/ordersETemplate.js', $editObject->formVars);
    break;
  case 'F':
    $editcontent['script_voet'] .= $AETemplate->parseFile('orders/js/ordersFTemplate.js', $editObject->formVars);
    break;
  case 'X':
    $editcontent['script_voet'] .= $AETemplate->parseFile('orders/js/ordersXTemplate.js', $editObject->formVars);
    break;
  case 'C':

    /** clone fields **/
    if (isset($action) && $action == 'new') //$action == 'edit' ||
    {
      $object->data['fields']['tempFixOrder'] = $object->data['fields']['fixOrder'];
      $object->data['fields']['tempBeurs'] = $object->data['fields']['beurs'];
      $object2->data['fields']['tempRekening'] = $object2->data['fields']['rekening'];
    } else {
      $editObject->formVars['tempRekening_inputfield'] = '<input type="hidden" id="tempRekening" name="tempRekening" value="'.$object2->data['fields']['rekening']['value'].'" />';
      $object->data['fields']['tempFixOrder'] = $object->data['fields']['fixOrder'];
      $object->data['fields']['tempFixOrder']['form_extra'] = 'disabled';
    }
    $editcontent['script_voet'] .= $AETemplate->parseFile('orders/js/ordersCTemplate.js', $editObject->formVars);
    break;
  case 'N':
    $editcontent['script_voet'] .= $AETemplate->parseFile('orders/js/ordersNTemplate.js', $editObject->formVars);
    break;
  case 'O':
    /** controlleren of alle regels gecontrolleerd zijn */
    $controlleValidated = false;
    if ( $object->get('id') > 0 ) {
      $controleStatusArr = $object2->parseBySearch(array('orderid' => $object->get('id'), 'controleStatus' => 2), "all", null, -1);
      if ( empty($controleStatusArr) ) {
        $controlleValidated = true;
      } else {
        $AEMessage->setMessage('Nog niet alle controles zijn correct!', 'error');
      }
    }
    
    $editcontent['script_voet'] .= $AETemplate->parseFile('orders/js/ordersOTemplate.js', $editObject->formVars);
    break;
  default:
    break;
}

if ( checkOrderAcces ('handmatig_opslaan') === false )
{
  $ISINCodeClass = 'notEditable';
  $PortefeuilleSelectieClass = 'notEditable';
  $object2->addClass('portefeuille', 'notEditable');
  $object2->addClass('rekening', 'notEditable');
  $object->addClass('fondsOmschrijving', 'notEditable');
  $object->addClass('transactieSoort', 'notEditable');
  $object->addClass('memo', 'notEditable');
  $object->addClass('transactieType', 'notEditable');

  $object->addClass('koersLimiet', 'notEditable');
  $object->addClass('tijdsSoort', 'notEditable');
  $object->addClass('tijdsLimiet', 'notEditable');

  $object->addClass('beurs', 'notEditable');

  $object2->addClass('aantal', 'notEditable');

  $object->setOption('transactieType', 'form_extra', "DISABLED ");
  $object->setOption('transactieSoort', 'form_extra', "DISABLED ");
}

$object->setOption('transactieType', 'form_extra', " DISABLED ");
$object->addClass('koersLimiet', 'maskValutaKoers');
$object->addClass('notaValutakoers', 'maskValutaKoers');
$object2->addClass('aantal', 'maskAmountPN');



/**
 * Autocomplete velden aanmaken voor fonds en client
 */
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('ISINCode');



/** wijzig velden voor fx transactie */
$fxAnd = null;
if ( $soort === 'F' || $soort === 'X') {
  $autocomplete->minLeng = 0;
  $fxAnd = '`Fondsen`.`Fonds` IN (
    "FX USD/EUR Spot",
    "FX GBP/EUR Spot",
    "FX SGD/EUR Spot",
    "FX NOK/EUR Spot",
    "FX JPY/EUR Spot",
    "FX CHF/EUR Spot",
    
    "FX EUR/USD Spot",
    "FX EUR/GBP Spot",
    "FX EUR/SGD Spot",
    "FX EUR/NOK Spot",
    "FX EUR/JPY Spot",
    "FX EUR/CHF Spot",
    
    "FX EUR/HKD Spot",
    "FX HKD/EUR Spot",
    "FX HKD/USD Spot",
    
    "FX SEK/EUR Spot",
    "FX EUR/SEK Spot",
    
    "FX CAD/EUR Spot",
    "FX EUR/CAD Spot",
    
    "FX EUR/DKK Spot",
    "FX DKK/EUR Spot",
    "FX EUR/AUD Spot",
    "FX AUD/EUR Spot",
    
    "FX USD/GBP Spot",
    "FX GBP/USD Spot",
    
    "FX ZAR/EUR Spot"
    
  ) ';
}

$nominaalAnd = null;
if ( $soort === 'N' || $soort === 'O' )
{
  $nominaalAnd = 'Fondsen.orderinlegInBedrag > 0';
}
/** set autocomplete velden **/

$editObject->formVars['Fonds'] = $autocomplete->addVirtuelField(
  'ISINCode',
  array(
    'autocomplete' => array(
      'table'        => 'Fondsen',
//    'order' => 'Fondskoersen.Datum DESC',
      'label'        => array(
        'Fondsen.Fonds',
        'Fondsen.ISINCode',
        'combine' => '({Valuta})'
      ),
      'searchable'   => array('Fondsen.Fonds', 'Fondsen.ISINCode', 'Fondsen.Omschrijving', 'Fondsen.FondsImportCode'),
      'field_value'  => array('Fondsen.ISINCode'),
      'extra_fields' => array('*'),
      'value'        => 'ISINCode',
      'actions'      => array(

        'select' => '
        event.preventDefault();
        
        $("#ISINCode").val(ui.item.field_value);
        $("input[name=fonds_id]").val(ui.item.data.id);
        $("input[name=fonds]").val(ui.item.data.Fonds);
        $("#fondsOmschrijving").val(ui.item.data.Omschrijving);
        $("#fondsOmschrijvingHidden").val(ui.item.data.Omschrijving);
        
        $("#beurs").val(ui.item.data.beurs);

        $("#fondseenheid").val(ui.item.data.Fondseenheid);
        $("#fondsBeurscode").val(ui.item.data.beurs);
        $("#fondsValuta").val(ui.item.data.Valuta);

        $("#optieType").val(ui.item.data.OptieType);
        $("#optieUitoefenprijs").val(ui.item.data.OptieUitoefenPrijs);
        $("#optieExpDatum").val(ui.item.data.OptieExpDatum);

        $("#fondsOmschrijving").prop("readonly", true);
        $("#fondssoort").val(ui.item.data.fondssoort);
        
        
        fondsChanged("fonds");
        valutaChanged(ui.item.data.Valuta);
        
        
        fillTransactionType(ui.item.data.fondssoort);
        console.log(ui.item);
        isinChanged (ui.item.data);
        aantalChanged();
        
        clearControlle();

      '
      ),
      'conditions'   => array(
        'AND' => array(
          '(Fondsen.EindDatum >= now() OR Fondsen.EindDatum = "0000-00-00")',
          $nominaalAnd,
          $fxAnd
        )
      )
    ),
    'form_size'    => '15',
    'validate'     => $object->data['fields']['fonds']['validate'],
    'form_value'   => $object->get('ISINCode'),
    'form_class'   => (isset($ISINCodeClass)?$ISINCodeClass:'')
  )
);

$editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('ISINCode');

$autocomplete->minLeng = 2;
$autocomplete->resetVirtualField('PortefeuilleSelectie');
$editObject->formVars['PortefeuilleSelectie'] = $autocomplete->addVirtuelField(
  'PortefeuilleSelectie',
  array(
    'autocomplete' => array(
      'table'        => 'Portefeuilles',
      'prefix'       => true,
      'returnType'   => 'expanded',
      'join'         => array(
        'fixDepotbankenPerVermogensbeheerder' => array(
          'type' => 'left',
          'on'   => array(
            'Portefeuilles.Vermogensbeheerder' => 'vermogensbeheerder',
            'Portefeuilles.depotbank'          => 'depotbank'
          )
        ),
        'CRM_naw' => array(
          'type' => 'left',
          'on'   => array(
            'Portefeuilles.Portefeuille'          => 'Portefeuille'
          )
        ),
        'Vermogensbeheerders' => array(
          'type' => 'inner',
          'on'   => array(
            'Portefeuilles.Vermogensbeheerder'          => 'Vermogensbeheerder'
          )
        )
      ),
      'extra_fields' => array(
        'Portefeuille',
        'Client',
        'Vermogensbeheerder',
        'id',

        'Risicoklasse',
        'Depotbank',
        'fixDepotbankenPerVermogensbeheerder.depotbank',
        'Vermogensbeheerders.OrderuitvoerBewaarder'
        //'`Portefeuille` AS subPortefeuille'
      ),
      'label'        => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client', 'CRM_naw.naam'),
      'searchable'   => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
//    'extra_fields'  => array(),
      'field_value'  => array('Portefeuilles.Client'),
      'value'        => 'Portefeuilles.Client',
      'actions'      => array(
        'select' => 'event.preventDefault();
        $("#PortefeuilleSelectie").val(ui.item.field_value);
        $("#client").val(ui.item.field_value);
        $("#portefeuille").val(ui.item.data.Portefeuilles.Portefeuille);
        $("input[name=portefeuille_id]").val(ui.item.data.Portefeuilles.id);
        var fixDefaultAan=false;
        if ( ui.item.data.fixDepotbankenPerVermogensbeheerder.fixDefaultAan != null )
        {
          if(ui.item.data.fixDepotbankenPerVermogensbeheerder.fixDefaultAan==1)
            fixDefaultAan=true;
        }

        if ( ui.item.data.fixDepotbankenPerVermogensbeheerder.depotbank != null  && ui.item.data.fixDepotbankenPerVermogensbeheerder.depotbank != "") {
          fixOrder(true,fixDefaultAan);
          
          if ( ui.item.data.fixDepotbankenPerVermogensbeheerder.careOrderVerplicht != null  && ui.item.data.fixDepotbankenPerVermogensbeheerder.careOrderVerplicht != "") {
            careOrder(ui.item.data.fixDepotbankenPerVermogensbeheerder.careOrderVerplicht);
          }
        } else {
          fixOrder(false,fixDefaultAan);
        }
        
        //console.log($("input[name=orderSelectieType]:checked", ".orderForm").val());
        if ( $("input[name=orderSelectieType]:checked", ".orderForm").val() === "M" ) {
          if ( ui.item.data.fixDepotbankenPerVermogensbeheerder.meervoudigViaFix == null
          || ui.item.data.fixDepotbankenPerVermogensbeheerder.meervoudigViaFix == 0 ) {
            fixOrder(false,fixDefaultAan);
          }
        }
        
        if ( $("input[name=orderSelectieType]:checked", ".orderForm").val() === "O" ) {
          if ( ui.item.data.fixDepotbankenPerVermogensbeheerder.meervNominaalFIX == null
          || ui.item.data.fixDepotbankenPerVermogensbeheerder.meervNominaalFIX == 0 ) {
            fixOrder(false,fixDefaultAan);
          }
        }
        
        // || $("input[name=orderSelectieType]:checked", ".orderForm").val() === "O"
        if ( $("input[name=orderSelectieType]:checked", ".orderForm").val() === "N" ) {
          if ( ui.item.data.fixDepotbankenPerVermogensbeheerder.nominaalViaFix == null
          || ui.item.data.fixDepotbankenPerVermogensbeheerder.nominaalViaFix == 0 ) {
            fixOrder(false,fixDefaultAan);
          }
        }
        //meervoudigViaFix
        //nominaalViaFix
        
        $("#fixOrder").change();
        $(".rekeningField").hide();

        $("#OrderuitvoerBewaarder").val(ui.item.data.Vermogensbeheerders.OrderuitvoerBewaarder);


        $("#depobankValue").html(ui.item.data.Portefeuilles.Depotbank);
        $("#DepotbankOld").val($("#Depotbank").val());
        $("#Depotbank").val(ui.item.data.Portefeuilles.Depotbank);
        
        $("#profileValue").html(ui.item.data.Portefeuilles.Risicoklasse);
        
        
        
        $("#Risicoklasse").val(ui.item.data.Portefeuilles.Risicoklasse);

        aantalChanged();
        rekeningNrTonen(ui.item.data.Portefeuilles.Portefeuille);
        
        
        
        

        clientChanged();

        portefeuilleChanged (ui.item);
        fondsChanged("fonds");
        
      '
      ),
      'conditions'   => array(
        'AND' => array(
          'Portefeuilles.consolidatie=0',
          '(Portefeuilles.EindDatum >= now() OR Portefeuilles.EindDatum = "0000-00-00")',
          'Portefeuilles.id' => $Portefeuilles,
          '(SELECT COUNT(*) FROM `Rekeningen` WHERE Portefeuille = Portefeuilles.Portefeuille AND inactief = 0) > 0'
        )
      )
    ),
    'form_size'    => '15',
    'form_value'   => $object2->get('client'),
    'form_class'   => (isset($PortefeuilleSelectieClass)?$PortefeuilleSelectieClass:'')
  )
);

$editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('PortefeuilleSelectie');
/**
 * Einde auto complete velden
 */

/** Uitvoeringen berichten popup */
//Wanneer we uitvoeringen moegen muteren
if ( checkOrderAcces('handmatig_uitvoeringenMuteren') === true)
{
  $editObject->formVars['handmatig_uitvoeringenMuteren'] = 1;
}
else
{
  $editObject->formVars['handmatig_uitvoeringenMuteren'] = 0;
}
//$editObject->formVars['handmatig_uitvoeringenMuteren'] = 0;
/** einde Uitvoeringen berichten popup */

$editcontent['script_voet'] .= $object->testForSpeedup();


$editObject->formVars['return'] = 'ordersListV2.php';

if ( ! empty ($forceredirect) ) {
  $editObject->formVars['return'] = $forceredirect;

}

$editObject->formVars["controlle_chk"] = '';
if ($object2->get('id') > 0 && $object->get('orderStatus') > 0)
{
  //$object2->orderStatus = $object->get('orderStatus');
  $object2->orderControlle();
  $editObject->formVars["controlle_chk"] = $object2->data['orderData']['orderCheckHtml'];
}
elseif ($action == "edit") //&& $object->get('ISINCode') <> '' //Waarom filter op ISIN?
{
  if ($object2->get('id') != null)
  {
    $object2->orderControlle();
    $editObject->formVars["controlle_chk"] = isset($object2->data['orderData']['orderCheckHtml'])?$object2->data['orderData']['orderCheckHtml']:'';
  }
}


$object->setOption("tijdsLimiet", "form_size", "10");
if ($object->get('tijdsLimiet') == '0000-00-00')
{
  $object->set('tijdsLimiet', '');
}

$object->setOption('tijdsLimiet', 'date_format', 'd-m-Y');

if ($object->get('orderStatus') < 1)
{
  $object->setOption("transactieType", "form_extra", "onChange='tijdsSoortChanged()' ");
  $object->setOption("tijdsSoort", "form_extra", "onChange='tijdsSoortChanged()' ");
  $object->setOption("tijdsLimiet", "form_extra", "onChange='date_complete(this);tijdslimietChange();' ");
  $object->setOption("koersLimiet", "form_extra", "onChange='koersLimietChange()' ");
  $object->setOption("transactieSoort", "form_extra", " id='transactieSoort' onChange='setClass()' ");
}

if ($object->get('orderStatus') > 0 || checkOrderAcces ('handmatig_opslaan') === false)
{
  $object->setOption("fixOrder", "form_extra", "DISABLED");
  $object->setOption("careOrder", "form_extra", "DISABLED");
}



if ($object->get('id') > 0) {// && $object->get('orderStatus') > 0
  $editObject->formVars['currentStatus'] = $selectStatus[$object->get('orderStatus')];
}

/** order identificatie */
$editObject->formVars['order_identificatie'] = '';
if ($object->get('id') > 0) {
  $orderRegelIdentificatie = '';
  if ($object2->get('id') > 0 && ($soort === 'M' || $soort === 'O') )
  {
    $positie = $object2->get('positie');
    $orderRegelIdentificatie = '-' . sprintf("%03d", $positie);
  }
  $editObject->formVars['order_identificatie'] = $__appvar["bedrijf"] . $object->get('id') . $orderRegelIdentificatie  . ' - ' . $selectStatus[$object->get('orderStatus')];
}

/** wijzig velden voor fx transactie */
if ( $soort === 'F' || $soort === 'X') {
  /** Zorgen dat alleen aan/verkoop getoond wordt   */
  foreach ( $object->data['fields']['transactieSoort']['form_options'] as $tskey => $tsvalue) {
    if ( $tskey !== 'A' && $tskey !== 'V' ) {
      unset($object->data['fields']['transactieSoort']['form_options'][$tskey]);
    }
  }
}

/** maak knoppen **/
$editObject->formVars['copyOrder'] = '';
$editObject->formVars['formMessage'] = '';
$editObject->formVars['continueInput'] = '';
$editObject->formVars["verzendenKnop"] = '';
if (!isset ($editObject->object->locked) || $editObject->object->locked !== true)
{
  //bij status rejected
  if (
    (intval($object->get('orderStatus')) === 6 || intval($object->get('orderStatus')) === 7 || intval($object->get('orderStatus')) === 5 )
    && checkOrderAcces ('handmatig_opslaan') === true
  )
  {
    $editObject->formVars['copyOrder'] = '<a href="ordersEditV2.php?action=edit&id='.$object->get('id').'&copyid='.$object->get('id').'" tabindex="0" class="btn-new btn-save" id=\'copyOrder\'>' . vt('Opnieuw inleggen') . '</a>';
  }

  if (
    (checkOrderAcces ('handmatig_opslaan') === true && $object->get('orderStatus') >= 2 && (int) $object->get('fixOrder') === 1)
    || (checkOrderAcces ('handmatig_opslaan') === true && ($object->get('orderStatus') < 1 || $object->get('fixOrder') == 0))
    || (checkOrderAcces ('handmatig_opslaan') === false && checkOrderAcces ('handmatig_volgendeStatus') === true)
  ) {
    //echo "opslaanKnop ";
    $editObject->formVars["opslaanKnop"] = '
        <button tabindex="0" class="btn-new btn-save" id=\'saveOrderBack\'>' . vt('Opslaan en terug') . '</button>
        <button style="margin-left:20px" tabindex="0" class="btn-new btn-save" id=\'saveOrder\'>' . vt('Opslaan') . '</button>
        <button style="margin-left:20px" tabindex="0" class="btn-new btn-save" id=\'saveOrderNew\'>' . vt('Opslaan en nieuw') . '</button>';
//    $editObject->formVars["opslaanNieuw"] = '<button tabindex="0" class="btn-new btn-save" id=\'saveOrderNew\'>Opslaan en nieuw</button>';

    if ( $editObject->formVars['fromRapport'] == 1 ) {
      $editObject->formVars["opslaanKnop"] = '
        <button tabindex="0" class="btn-new btn-save" id="saveOrderBackReport">' . vt('Opslaan en terug naar rapport') . '</button>
        ';
    }
  }

  if (
    checkOrderAcces ('handmatig_verzenden') === true
    && $object->get('orderStatus') < 1
    && ($object->get('fixVerzenddatum') == '0000-00-00 00:00:00' || $object->get('fixVerzenddatum') == '')
  )
  {
    if (
      checkOrderAcces ('orderVierOgen') === false
      || (
        checkOrderAcces ('orderVierOgen') === true
        && ($object->get('id') > 0 && $object->get('add_user') != $USR)
      )
    ) {
      $editObject->formVars["verzendenKnop"] = '
          <button style="" tabindex="0" class="btn-new btn-save" id="sendOrder">Verzenden </button>
          <button style="margin-left:20px" tabindex="0" class="btn-new btn-save" id="sendOrderNew">' . vt('Verzenden en nieuw') .'</button>
      ';

      if ( $editObject->formVars['fromRapport'] == 1 ) {
        $editObject->formVars["verzendenKnop"] = '
        <button style="" tabindex="0" class="btn-new btn-save" id="sendOrderBackReport">' . vt('Verzenden en terug naar rapport') . '</button>
        ';
      }
    }
//    else
//    {
    //$editObject->formVars["verzendenKnop"] = '<button class="btn-new">Eigen order</button>';
//    }

  }

  /** doorgaan met invoeren meervoudig en combinatie **/
  if ($object->get('orderStatus') < 1 && ($soort == 'O' || $soort == 'M' || $soort == 'C' || soort == 'X' ))
  {
    $editObject->formVars['continueInput'] = '<div class="box box2 box3-sm"><button tabindex="0" class="btn-new btn-default" id=\'continueOrder\'>' . vt('Verder met invoeren') . '</button></div>';
  }
}
/** record is locked **/
else
{
  /** haal het lock bericht op en strip de script code omdat we deze onder formMessage nogmaals gebruiken met script code **/
  $AEMessage->setMessage(
    preg_replace('#<script>(.*?)</script>#is', '', $_SESSION['NAV']->items['navedit']->message),
    'info'
  );
  $editObject->formVars['formMessage'] = $_SESSION['NAV']->items['navedit']->message . '<br />';
}
$editObject->formVars['AEMessage'] = $AEMessage->getMessage();
/** einde maak knoppen **/
$form = new Form($object2);
$editObject->formVars['orderReden'] = $form->makeInput('orderReden');


$editObject->formVars['orderOptionsBlock'] = $AETemplate->parseBlockFromFileWithForm(
  'classTemplates/orders/ordersOptionsBlock.html',
  $editObject->formVars,
  $object
);

$editObject->template = $editcontent;




echo $editObject->getOutput();


if ($result = $editObject->result)
{

  $orderLogs = new orderLogs();
  if ($data['id'] == '' && $object->get('id') > 0)
  {
    if ($soort != 'O' || $soort != 'M' || $soort != 'X')
      $orderLogs->addToLog($object->get('id'), null, 'aanmaken order', '','',5,'');
  }

  $data['OrdersV2.id'] = $object->get('id'); /** order id meegeven naar de order regel */

  $object2->set('orderregelStatus', $object->get('orderStatus'));
  if ($soort == 'O' || $soort == 'M' || $soort == 'X')
  {
    if ( ! empty($data['PortefeuilleSelectie']) && ! empty($data['client']))
    {
      $object2->orderRegelUpdate($data);
      $orderLogs->addToLog($object->get('id'), null, 'aanmaken order', '','',5,'');
    }
  }
  else {
    $object2->orderRegelUpdate($data,$forceOrdercheck);
  }

  /** wanneer we een fix order hebben **/
  if ($object->get('fixOrder') == 1)
  {
    if (($object->get('fixVerzenddatum') == '' || $object->get('fixVerzenddatum') == '0000-00-00 00:00:00') && $huidigeStatus == 1)
      $object->verzendFix();
    elseif ((isset($data['cancel']) && $data['cancel'] == 1) && $object->get('fixAnnuleerdatum') == '0000-00-00 00:00:00')
      $object->annuleerFix();
  }
}

/** als request type = ajax return json voor jquery bij update of verwijderen **/
if (requestType('ajax'))
{
  if ($action == 'update' || $action == 'delete')
  {
    if ($editObject->object->error == false)
    {
      if ( isset($data['klaarMetInAanmaak']) ) {
        klaarMetInAanmaak ($data);
      }


      echo $AEJson->json_encode(
        array(
          'success'  => true,
          'saved'    => true,
//          'message'   => urlencode($editObject->message),
          'id'       => $editObject->object->get('id'),
          'orderId'  => $editObject->object->get('orderid'),
          'testdata' => $data,
          'object'   => $object,
          'object2'  => $object2
        )
      ); //let ajax know the request ended in success
    }
    else
    {
      echo $AEJson->json_encode(
        array(
          'success'  => true,
          'saved'    => false,
          'message'  => $editObject->_error,
          'errors'   => $object->getErrors(),
          'testdata' => $data
//          'orderId'               => $editObject->object->get('id')
        )
      ); //let ajax know the request ended in failure
    }

  }
  /** if ajax disable header and footer **/

  $editObject->includeHeaderInOutput = false;
  exit();
}

//listarray($data); echo " $return ";
//$data["redirect"]=3;
if (!isset($data["redirect"]))
{
  $data["redirect"] = 9999;
}
if (!isset($return))
{
  $return = null;
}

if ($action === 'update')
{
  /** opslaan en nieuw **/
  if (isset ($data['redirect']) && $data['redirect'] === 'saveNew')
  {
    header("Location: ordersEditV2.php?action=new&orderSelectieType=" . $soort);
    exit();
  }

  /** opslaan en nieuw **/
  if (isset ($data['redirect']) && $data['redirect'] === 'saveBack')
  {
    header("Location: ordersListV2.php");
    exit();
  }

  /** opslaan en nieuw **/
  if (isset ($data['redirect']) && $data['redirect'] === 'saveStay')
  {
    header("Location: ordersEditV2.php?action=edit&id=" . $object->get("id"));
    exit();
  }

  if ($soort == 'O' ||$soort == 'M' || $soort == 'X' || $soort == 'C')
  {
    header("Location: ordersEditV2.php?action=edit&id=" . $object->get("id") . "&orderid=" . $object->get("id")."&batchId=" . $_POST['batchId'] . "&orderSelectieType=" . $soort . "&portefeuille=" . $_POST['portefeuille'] . "&rekening=" . $_POST['rekening'] . "&valuta=" . $_POST['valuta'] . "&client=" . $_POST['client']);
  }
  else
  {
    header("Location: " . $return);
  }
}


$_SESSION['NAV'] = null;
//$_SESSION['NAV']['customHtml'] = '<span onClick="parent.content.toOrderList();" class="btn btn-new btn-xs"><i class="fa fa-reply" aria-hidden="true"></i> Terug</span>';