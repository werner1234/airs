<?php

class AE_Message {
  /**
   * class veriables
   */
  var $messages = array();
  var $messageTypes = array('info', 'error', 'success');
  var $messageWrapper = '<div class="alert alert-%s">%s</div>';
  
  function AE_Message ()
  {
    if ( ! isset($_SESSION['flash_message']) )
    {
      $_SESSION['flash_message'] = array();
    }
  }
  
  /**
   * 
   * @param type $message Bericht
   * @param type $messageTypes array('info', 'error', 'success')
   */
  function setMessage ($message, $type = null)
  {
    if ( $type = $this->checkFlashType ($type) )
    {
      $this->messages[$type][] = $message;
    }
  }
  
  /**
   *
   * @param type $message Bericht
   * @param type $messageTypes array('info', 'error', 'success')
   */
  function makeMessage ($message, $type = null)
  {
    if ( $type = $this->checkFlashType ($type) ) {
      return sprintf($this->messageWrapper, $type, $message);
    }
    return '';
  }
  
  function getMessage ($messageType = null, $type = null)
  {
    if ( empty($this->messages) ) {
      return null;
    }
    $returnMessage = '';
    if ( ! empty($type) )
    {
      if ( ! empty($this->messages[$type]) )
      {
        $returnMessage .= $this->returnMessages($this->messages, $type);
        $this->messages[$type] = array();
      }
    }
    else
    {
      $returnMessage .= $this->returnMessages($this->messages);
      $this->messages = array();
    }
    
    return $returnMessage;
  }
  
  
  /**
   * Flash messages
   * @param type $type
   * @return type
   * @author RM
   * @since 30-6-2014
   */
  function setFlash ($message, $type = 'info')
  {
    if ( $type = $this->checkFlashType ($type) )
    {
      $_SESSION['flash_message'][$type][] = $message;
    }
  }
  
  /**
   * Redirect to page with flash message set
   * @param type $message
   * @param type $location
   * @param type $type
   * @author RM
   * @since 30-6-2014
   */
  function redirectWithFlash ($message, $type = null, $location = 'index.php')
  {
    $this->setFlash($message, $type);
    header("Location: " . $location);
    exit;
  }
  
  /**
   * Get flash messages
   * @param type $type
   * @return messages
   * 
   * @author RM
   * @since 30-6-2014
   */
  function getFlash ($type = null)
  {
    if ( ! isset($_SESSION['flash_message']) || empty($_SESSION['flash_message']) ) {
      return null;
    }
    $returnMessage = '';
    if ( ! empty($type) )
    {
      if ( ! empty($_SESSION['flash_message'][$type]) )
      {
        $returnMessage .= $this->returnMessages($_SESSION['flash_message'], $type);
        unset($_SESSION['flash_message'][$type]);
      }
    }
    else
    {
      $returnMessage .= $this->returnMessages($_SESSION['flash_message']);
      unset($_SESSION['flash_message']);
    }
    
    return $returnMessage;
  }
  
  
  /**
   * Global function to get messages
   * @param array $messageArray
   * @param type $type
   * 
   * @author RM
   * @since 30-6-2014
   */
  function returnMessages ($messageArray, $type = null)
  {
    $returnMessage = '';
    if ( ! empty($type) )
    {
      $messageArray = array($type => $messageArray[$type]);
    }
    foreach ( $messageArray as $type => $messages)
    {
      foreach ( $messages as $messageKey => $message )
      {
        $returnMessage .= sprintf($this->messageWrapper, $type, $message);
      }
    }
    
    return $returnMessage;
  }
  
    
  function checkFlashType ($type, $messageType = null)
  {
    if ( ! in_array($type, $this->messageTypes) ) {
      $type = $this->messageTypes[0];
    }
    return $type;
  }
  
}