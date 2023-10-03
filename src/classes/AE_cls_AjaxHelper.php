<?php

/*
  AE-ICT source module
  Author  						: $Author: rm $
  Laatste aanpassing	: $Date: 2015/01/28 13:16:42 $
  File Versie					: $Revision: 1.1 $
 */

/**
 * Ajax helper
 * 
 * @author RM
 * @since 16-10-2014
 * 
 * Commonly used ajax functionality
 * 
 */
class AE_AjaxHelper
{
  var $AE_Datum = array();
  var $data = array();
  var $__appvar = array();

  function AE_AjaxHelper($data)
  {
    global $__appvar;
    $this->AE_Datum = new AE_datum;
    $this->__appvar = $__appvar;
    $this->data = $data;
    if ( empty ($data) ) {
      $this->data = array_merge($_POST, $_GET);
    }
    
  }

  /**
   * checks if items exist in the data array exits the script is a item is not found
   * @param type $itemArray array of required items
   * 
   * exit with message
   * 
   * @author RM
   * @since 5-8-2014
   */
  function itemsExist($itemArray)
  {
    foreach ($itemArray as $item)
    {
      $appdata = $this->data;
      $path = explode('.', $item);
      foreach ($path as $part)
      {
        if ( ! isset($appdata[$part]))
        {
          exit('Niet alle benodigde waarden zijn gevonden: ' . $item);
        }
        $appdata = $appdata[$part];
      }
    }
  }
  
  /**
   * 
   */
  function returnData ($data)
  {
    if (! isset($this->data['ajaxClassCall'])) {
      return $data;
    }
    echo json_encode($data);
  }
  
  function returnJson ($data)
  {
    echo json_encode($data);
  }

}
