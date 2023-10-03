<?php

/**
 * Airs notificaties
 */

class AIRS_Notify extends AE_AjaxHelper {
  var $returnJson = true;
  var $data = array();
  var $__appvar = array();
  
  var $defaultOptions = array(
    'ttl'         => '2u',
    'usr'         => '',
    'type'        => 'info',
    'message'     => '',
    'module'      => '',
    'module_id'   => '',
//    'status'      => ''
  );
  
  var $defaultTypes = array(
    'info', 
    'error', 
    'success'
  );
  var $changeBoxTypeOn = array('error');
  
  function AIRS_Notify ()
  {
    global $__appvar;
    
    $this->__appvar = $__appvar;
    $this->data = array_merge($_POST, $_GET);
    
    if ( ! class_exists('DB')) {
      include_once("../../classes/AE_cls_mysql.php");
    }
  }
  /**
   * Omzetten van ttl naar werkelijke php code
   * @param type $TTL array('+2 u', '2 u')
   * @return type
   */
  function __ConvertTTL ($TTL)
  {
    if (strpos($TTL,'u') !== false) {
        $TTL = str_replace('u', ' hours', $TTL);
    }
    
    if (strpos($TTL,'d') !== false) {
      $TTL = str_replace('d', ' days', $TTL);
    }
    if (strpos($TTL,'w') !== false) {
      $TTL = str_replace('w', ' weeks', $TTL);
    }
    
    if (strpos($TTL,'m') !== false) {
      $TTL = str_replace('m', ' minutes', $TTL);
    }
    
    if (strpos($TTL,'-') === false && strpos($TTL,'+') === false) {
      $TTL = '+'.$TTL;
    }
    return date('Y-m-d H:i:s', strtotime($TTL));
  }

  function getNotifier ()
  {
    $this->returnJson = false;

    echo json_encode(array(
      'status'              => $this->getStatus(),
      'groupedResults'      => $this->fetchAllGrouped(),
      'fetchAll'            => $this->fetchAll(),
      'fetchAllError'       => $this->fetchAll(array('notificationType' => 'error'))
    ));
  }
  
  
  /**
   * Een nieuwe notificatie toevoegen
   * @param str $module => bij welke functionaliteit hoort deze notificatie
   * @param str $message => het bericht
   * @param array $options
   * @return insert true/false
   */
  function addRow ($module, $moduleId, $message, $options = array())
  {
    $addData = array_merge($this->defaultOptions, $options, array('module' => $module, 'message' => $message, 'module_id' => $moduleId));
    $addData['ttl_date'] = $this->__ConvertTTL($addData['ttl']);

    $insert = '';
    foreach ( $addData as $insertkey => $insertValue ) {
      $insert .= sprintf( "`%s` = '%s'" , $insertkey , $insertValue ) . ', ';
    }
    $sql = "INSERT INTO `notifications` SET " . $insert . " `change_user` = '', `change_date` = NOW(), `add_user` = '', `add_date` = NOW();";

    $db = new DB();
    return $db->executeQuery($sql);
  }
  
  /**
   * Haal alle notifications op met een ttl_data groter dan de huidige datum en tijd
   * @param type $seen [1 => gezien, 0 => not niet gezien]
   * @return array ( alle records )
   */
  function getAll($seen = 0)
  {
    $getAllData = array();
    $db = new DB();
    $getAllQuery = 'SELECT * FROM `notifications` 
      WHERE `seen` = "' . $seen . '" 
      AND `ttl_date` > NOW() 
      AND `ttl_date` != "0000-00-00 00:00:00"
      ORDER BY `add_date`  DESC, `id` DESC';
    
    $db->executeQuery($getAllQuery);
    
    while ( $getAllRecord = $db->nextRecord() ) {
      $getAllData[] = $getAllRecord;
    }
    
    return $getAllData;
  }
  
  /**
   * return higest notification priority based on $this->changeBoxTypeOn
   * @param type $notificationa
   * @return type
   */
  function getHighestState ($notificationa) {
    if ( empty($notificationa) ) {return null;}
    foreach ( $notificationa as $value ) {
      if ( isset ($value['type'])) {
        $currentNotificationTypes[] = $value['type'];
      }
    }
    $currentNotificationTypes = array_flip($currentNotificationTypes);
    $currentNotificationTypes = array_flip(array_intersect_key(array_flip($this->defaultTypes), $currentNotificationTypes));
    foreach ($this->changeBoxTypeOn as $showOnType) {
      if ( in_array ($showOnType, $currentNotificationTypes) ) {
        return $showOnType;
      }
    }
    return null;
  }
  
  /**
   * Wijzig de gezien status van een notificatie
   */
  function seenNotification ()
  {
    $data = array_merge($_GET, $_POST);
    if ( is_array($data['notificationId']) ) {
      $where = '`id` IN (' . implode(',', $data['notificationId']) . ')';
    } else {
      $where = '`id` = "' . $data['notificationId'] . '"';
    }
    
    $updateQuery = "UPDATE `notifications` SET `seen` = '" . $data['seen'] . "' WHERE " . $where;
    $db = new DB();
    if ($db->executeQuery($updateQuery) ) {
      echo json_encode(array('success' => true, 'saved' => true));
    } else {
       echo json_encode(array('success' => true, 'saved' => false));
    }
  }
  
  
  
  
  
  
  
  /**
   * Haal alle notifications op met een ttl_data groter dan de huidige datum en tijd
   * @param type $seen [1 => gezien, 0 => not niet gezien]
   * @return array ( alle records )
   */
  function fetchAll($options = array() )
  {
    $seen = 0;
    if ( ! empty ($options) ) {
      foreach ( $options as $option => $optionValue ) {
        $this->data[$option] = $optionValue;
      }
    }

    if ( isset($this->data['seen']) ) {$seen = $this->data['seen'];}
    
    $type = null;
    if ( isset($this->data['notificationType']) ) {$type = 'AND `type` = "' . $this->data['notificationType'] . '"';}
    
    $getAllData = array();
    $db = new DB();
    $getAllQuery = 'SELECT * FROM `notifications` 
      WHERE `seen` = "' . $seen . '" 
      ' . $type . '  
      AND `ttl_date` > NOW() 
      AND `ttl_date` != "0000-00-00 00:00:00"
      ORDER BY `add_date`  DESC, `id` DESC';
    
    $db->executeQuery($getAllQuery);
    
    while ( $getAllRecord = $db->nextRecord() ) {
      $getAllData[] = $getAllRecord;
    }

    if ( $this->returnJson === true ) {
      echo $this->returnJson($getAllData);
    } else {
      return $getAllData;
    }
  }
  
  function fetchAllGrouped ()
  {
    $seen = 0;
    if ( isset($this->data['seen']) ) {$seen = $this->data['seen'];}
    
    $type = null;
    if ( isset($this->data['notificationType']) ) {$type = 'AND `type` = "' . $this->data['notificationType'] . '"';}
    
    $getAllData = array();
    $db = new DB();
    $getAllQuery = 'SELECT * FROM `notifications` 
      WHERE `seen` = "' . $seen . '" 
      ' . $type . '  
      AND `ttl_date` > NOW() 
      AND `ttl_date` != "0000-00-00 00:00:00"
      ORDER BY `add_date`  DESC, `id` DESC';
    
    $db->executeQuery($getAllQuery);
    
    while ( $getAllRecord = $db->nextRecord() ) {
      $getAllData[$getAllRecord['module'] . '-' . $getAllRecord['module_id']]['module'] = array(
        'module'  => $getAllRecord['module'],
        'id'      => $getAllRecord['module_id']
      );
      $getAllData[$getAllRecord['module'] . '-' . $getAllRecord['module_id']]['data'][] = $getAllRecord;
    }

    if ( $this->returnJson === true ) {
      echo $this->returnJson($getAllData);
    } else {
      return $getAllData;
    }
    

  }
  
  
  function getStatus ()
  {
    $getAllData = array(
      'error'     => 0,
      'warning'   => 0,
      'info'      => 0,
      'success'   => 0
    );
    $db = new DB();
    $getAllQuery = 'SELECT type, count(type) as `counter` FROM `notifications` 
      WHERE `seen` = 0
      AND `ttl_date` > NOW() 
      AND `ttl_date` != "0000-00-00 00:00:00"
      GROUP BY `type`
    ';
    
    $db->executeQuery($getAllQuery);
    
    while ( $getAllRecord = $db->nextRecord() ) {
      $getAllData[$getAllRecord['type']] = $getAllRecord['counter'];
    }

    if ( $this->returnJson === true ) {
      echo $this->returnJson($getAllData);
    } else {
      return $getAllData;
    }

    
  }
  
  
  
  
}