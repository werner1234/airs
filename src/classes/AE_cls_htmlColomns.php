<?php



class AE_cls_htmlColomns
{
  
  var $data = array();
  var $sortData = array();
  var $error;
  var $dbId = 1;
  var $user;
  var $mainObject;
  
  function AE_cls_htmlColomns($dbId=1)
  {
    global $USR;
    $this->defineData();
    $this->dbId = $dbId;
    $this->user = $USR;
  }
  
  
  function addColomnDef($name, $colProperties)
	{
		$this->data[$name] = array_merge($this->properties, $colProperties);
	}
  
  function addSortDef($name, $sortProperties)
	{
		$this->sortData[$name] = array_merge($this->sortProperties, $sortProperties);
	}
  
  function get($field)
  {
    return $this->data[$field]['value'];
  }
  
  function showColomns($showValues=false)
  {
    foreach($this->data as $k=>$v)
    {
      if ($showValues)
      {
        echo "<li>".$k." -- >".$v["value"]."</li>";
      }
      else
      {
        echo "<li>".$k."</li>";
      }
      
    }
  }
}