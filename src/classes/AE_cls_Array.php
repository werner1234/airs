<?php

class AE_Array
{

  function AE_Array()
  {
    $this->AE_Sanitize = new AE_Sanitize();
  }

  /**
   * Check if an array has numeric keys 
   * array('test', 'field1') = true
   * array('field1' => 'field1', 'field2' => 'field2) = false
   * array('field1' => 'field1', 'field2) = true
   * @param type $array
   * @return Bool true/false
   */
  function hasNumericKeys($array)
  {
    return (bool) count(array_filter(array_keys($array), 'is_numeric'));
  }

  /**
   * Check if an array contains only NULL values
   * @param type $array array (key => null, 0 => null, key1 => array(key => value)
   * @return boolean 
   */
  function is_null($array)
  {
    $nullValues = array();

    foreach ($array as $key => $value)
    {
      if ($value === NULL)
      {
        $nullValues[] = true;
      }
      else
      {
        $nullValues[] = false;
      }
    }

    if (empty($nullValues) || in_array(false, $nullValues))
    {
      return false;
    }
    return true;
  }

  /**
   * extractByKeyValue
   * @param type $data search array
   * @param type $search search for array as key value array('key' => 'value', 'key' => 'value')
   * @return array 
   */
  function extractByKeyValue($data, $search, $retainKey = false)
  {
    $return = array();

    foreach ($data as $key => $value)
    {
      foreach ($search as $searchKey => $searchValue)
      {
        if (isset($value[$searchKey]) && $value[$searchKey] === $searchValue)
        {
          if ( $retainKey === true ) {
            $return[$key] = $value;
          } else {
            $return[$searchValue][] = $value;
          }

        }
      }
    }
    return $return;
  }

  /**
   * extractValuesByKey
   * @param type $data
   * @param type $searchKey
   * @return type
   */
  function extractValuesByKey($data, $searchKey, $keepIndex = false)
  {
    $return = array();

    foreach ($data as $key => $value)
    {
      if (isset($value[$searchKey]))
      {
        if ( $keepIndex === true ) {
          $return[$key] = $value[$searchKey];
        } else {
          $return[] = $value[$searchKey];
        }

      }
    }
    return $return;
  }

  function sortMultiOnField($dataArray, $sortField, $sortDirection)
  {
    if ($sortDirection === 'asc')
    {
      uasort($dataArray, array($this, '__sort_by_order_asc'));
    }
    elseif ($sortDirection === 'desc')
    {
      uasort($dataArray, array($this, '__sort_by_order_desc'));
    }
    return $dataArray;
  }

  function __sort_by_order_asc($a, $b)
  {
    return $a['aantal'] - $b['aantal'];
  }

  function __sort_by_order_desc($a, $b)
  {
    return $b['aantal'] - $a['aantal'];
  }

  /** start array to sql functions * */

  /**
   * Convert an array to fields
   * array(id, date, field1, field2) = `id`, `date`, `field1`, `field2`
   * array(id, date => startdate, field1) = id, `date` as `startdate`, field1
   * @param type $fieldArray
   * @return type
   */
  function toSqlFields($fieldArray)
  {
    $queryString = array();
    foreach ($fieldArray as $key => $value)
    {
      if (!is_numeric($key))
      {
        $queryString[] = '`' . $this->AE_Sanitize->escape($key) . '` AS `' . $this->AE_Sanitize->escape($value) . '`';
      }
      else
      {
        $queryString[] = $this->AE_Sanitize->escape($value);
      }
    }
    return implode(',', $queryString);
  }

  /**
   * sort form to order by query part if active is one
   * array('field_name' => array ( 'active' => 1, 'order' => 'asc' )
   * @param type $orderArray
   * @return string ORDER BY `key` value
   */
  function formToSqlOrder($orderArray)
  {
    if (!empty($orderArray))
    {
      $orderString = array();
      foreach ($orderArray as $orderField => $data)
      {
        if (isset($data['active']) && $data['active'] == 1)
        {
          $orderString[$orderField] = $data['order'];
        }
      }
      if (empty($orderString))
      {
        return null;
      }
      return $this->toSqlOrder($orderString);
    }
  }

  /**
   * key value array to order by query part
   * array( 'field' => 'asc', 'field2' => 'DESC' )
   * @param type $orderArray
   * @return string ORDER BY `key` value
   */
  function toSqlOrder($orderArray)
  {
    if (!empty($orderArray))
    {
      $orderString = array();
      foreach ($orderArray as $field => $order)
      {
        $orderString[] = '`' . $field . '` ' . strtoupper($order);
      }
      if (empty($orderString))
      {
        return null;
      }
      return 'ORDER BY ' . implode(',', $orderString);
    }

    return '';
  }

  function toSqlKeyValue(&$value, $key)
  {
    $value = sprintf("`%s` = '%s'", $key, $this->AE_Sanitize->escape($value));
  }

  function __toSqlFields(&$value)
  {
    $value = sprintf("`%s`", $this->AE_Sanitize->escape($value));
  }

  function toSqlInsert($data)
  {
    $arrayKeys = array_keys($data);
    $last = end($arrayKeys);

    $insert = '';
    foreach ($data as $insertkey => $insertValue)
    {
      $insert .= sprintf("`%s` = '%s'", $insertkey, $this->AE_Sanitize->escape($insertValue)) . ( $last !== $insertkey ? ', ' : '');
    }
    return $insert;
  }

  function expand($data = array(), $delimiter = '.')
  {
    $unflattenedArray = array();
		foreach ($data as $key => $value) {
			$keyList = explode($delimiter, $key);
			$firstKey = array_shift($keyList);
			if (count($keyList) > 0) { //does it go deeper, or was that the last key?
				$subarray = $this->expand(array(implode($delimiter, $keyList) => $value), $delimiter);
				foreach ($subarray as $subarrayKey => $subarrayValue) {
					$unflattenedArray[$firstKey][$subarrayKey] = $subarrayValue;
				}
			} else {
				$unflattenedArray[$firstKey] = $value;
			}
		}
		return $unflattenedArray;
  }



}
