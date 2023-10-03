<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2020/04/21 13:07:39 $
 		File Versie					: $Revision: 1.33 $

 		$Log: mysqlTable.php,v $
 		Revision 1.33  2020/04/21 13:07:39  rm
 		8515
 		
 		Revision 1.32  2019/05/29 15:04:46  rm
 		querybuilder
 		
 		Revision 1.31  2019/05/24 12:59:46  rm
 		Encoding fix
 		
 		Revision 1.30  2019/05/22 14:34:28  rm
 		7752
 		
 		Revision 1.29  2018/08/18 12:40:13  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.28  2018/01/27 17:27:00  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2015/11/08 16:40:38  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2015/06/20 06:01:30  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2015/06/19 16:30:57  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2015/06/05 15:01:12  rm
 		return van alle errors
 		
 		Revision 1.23  2015/03/25 13:33:24  rm
 		setvalue
 		
 		Revision 1.22  2015/03/18 15:41:49  rm
 		extra sql data
 		
 		Revision 1.21  2015/02/11 15:26:59  rm
 		Parse by function
 		
 		Revision 1.20  2015/02/04 13:30:44  rm
 		Bugfix getQuerytemplate bij encoden array_merge($this->data, $data)
 		
 		Revision 1.19  2015/01/30 15:10:10  rm
 		append .sql to getQueryTemplate if only a name is given without .
 		
 		Revision 1.18  2014/12/21 10:56:28  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2014/08/30 16:23:12  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/08/09 14:51:18  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/03/27 17:06:00  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2014/03/26 18:23:43  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/12/18 17:02:55  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/07/24 15:42:51  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2011/09/18 15:47:08  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2011/03/30 11:56:39  cvs
 		multi database aanpassingen

 		Revision 1.9  2005/12/16 15:07:19  jwellner
 		no message

 		Revision 1.8  2005/12/16 14:57:29  jwellner
 		no message



*/

class Table
{
  /*
  * Object vars
  */

  var $data = array();
  var $error;
  var $dbId = 1;
  
  /*
  * Constructor
  */
  function Table($dbId=1)
  {
    $this->defineData();
    $this->dbId = $dbId;

    $this->set($this->data['identity'],0);
  }

  /*
  *  get/set Methodsgit
  */
  function set($field, $value)
  {
    $this->data['fields'][$field]['value'] = $value;
    return true;
  }

	function setOption($field, $option ,$value)
  {
    $this->data['fields'][$field][$option] = $value;
    return true;
  }

  function get($field)
  {
    return $this->data['fields'][$field]['value'];
  }

  function getDescription($field)
  {
    return $this->data['fields'][$field]['description'];
  }

  /*
  * Data storage methods
  */
  function save()
  {
    $db = new MySqlObject($this->dbId);
    return $db->save($this);
  }

  function remove()
  {
    if($this->get($this->data['identity']))
    {
      $db = new MySqlObject($this->dbId);
      return $db->remove($this);
    }
    else
    	return false;
  }


  function getByField($field,$value)
  {
    $db=new DB($this->dbId);
    $db->SQL("SELECT id FROM ".$this->data['table']." WHERE `$field`='".mysql_real_escape_string($value)."'");
    $db->Query();
    $idRecord=$db->lookupRecord();
    $this->getById($idRecord['id']);   
  }
  
  function getById($id)
  {
    // load by identity
    $this->set($this->data['identity'],$id);
    $db = new MysqlObject($this->dbId);
    if($db->getById($this))
    {
    	return true;
    }
    else
    {
    	return false;
    }
  }

  function validate()
  {
  	return true;
  }

  function validateDelete()
  {
  	return true;
  }

  //list_kopregel_data
  function setError($field, $message)
  {
		$this->error  = true;
		$this->data['fields'][$field]['error'] = $message;
    $this->addClass($field, 'input_error');
//		$this->data['fields'][$field]['form_class'] = "input_error";
		return true;
  }

  function getError($field)
  {
		return $this->data['fields'][$field]['error'];
  }
  
  function getErrors ()
  {
    foreach ($this->data['fields'] as $fieldId => $fieldData) {
      if ( isset($fieldData['error']) ) {
        $data[$fieldId] = array('description' => $fieldData['description'], 'message' => $fieldData['error']);
      }
    }
  return $data;
}

  function checkAccess($type)
  {
		return true;
  }

  function setDefaults()
  {
		$fields = array_keys($this->data['fields']);
    $db=new DB();
    $query="SELECT tabel,veld,waarde FROM StandaardVeldVulling WHERE veld IN('".implode("','",$fields)."') AND tabel='".get_class($this)."'";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $this->data['fields'][$data['veld']]['default_value']=$data['waarde'];
    }
      $getalFormatType=array('0d'=>'%01.0f','2d'=>'%01.2f');
      $uitlijningtType=array('L'=>'style="text-align:left;"','R'=>'style="text-align:right;"','C'=>'style="text-align:center;"');
      $uitlijningtTypeList=array('L'=>'Left','R'=>'Right','C'=>'Center');

    $query="SELECT tabel,veld,uitlijning,getalformat,headerBreedte,weergaveBreedte,aantalRegels,formExtra,nietLeeg FROM veldopmaak WHERE veld IN('".implode("','",$fields)."') AND tabel='".get_class($this)."'";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    { 
      if($data['weergaveBreedte']<>'')
      	$this->data['fields'][$data['veld']]["form_size"]=$data['weergaveBreedte'];
        
      if($data['aantalRegels']<>'')
      	$this->data['fields'][$data['veld']]["form_rows"]=$data['aantalRegels'];
        
      if($data['headerBreedte']<>'')
      	$this->data['fields'][$data['veld']]["list_size"]=$data['headerBreedte'];                

      if($data['nietLeeg'] > 0)
      	$this->data['fields'][$data['veld']]["validate_notEmpty"]=$data['nietLeeg'];                
                    
      if($data['uitlijning']<>'')
      {
         $this->data['fields'][$data['veld']]['form_extra'].=$uitlijningtType[$data['uitlijning']];
         $this->data['fields'][$data['veld']]['list_align']=$uitlijningtTypeList[$data['uitlijning']];
      }
      if($data['getalformat']<>'')
      { 
        $this->data['fields'][$data['veld']]['list_format']=$getalFormatType[$data['getalformat']];
        $this->data['fields'][$data['veld']]['form_format']=$getalFormatType[$data['getalformat']];
      }
      if($data['formExtra']<>'')
      { 
        $this->data['fields'][$data['veld']]['form_extra'].=' '.$data['formExtra'];
      }
    }
    
		for($a=0;$a < count($fields); $a++)
		{
			// nog checken op speciale waarden in $this->data['fields'][$fields[$a]]['value']
			switch (strtoupper($this->data['fields'][$fields[$a]]['default_value']))
			{
				case strtoupper("now()") :
					$value = jul2db(mktime());
				break;
				case"LASTWORKDAY" :
          $value = lastWorkday("Y-m-d");
				break;
				default :
					$value = $this->data['fields'][$fields[$a]]['default_value'];
				break;
			}
			$this->set($fields[$a], $value);
		}
  	return true;
  }

  /*
  * Table definition
  */
  function defineData()
  {

  }
  
  
  /**
   * 
   * @param array,string $queryStr where
   * @param string $part field or fields
   * @param type $extra last lines in query
   * @param type $limit
   * @return array
   */
  function parseBySearch($queryStr, $part = "all", $extra = null, $limit = 1)
  {
    if ( is_array ($queryStr) ) {
      $queryStr = $this->__makeConditions($queryStr);
    } elseif ( ! empty ($queryStr) ) {
      $queryStr =  ' WHERE ' . $queryStr;
    }
    
    $fields = '*';
    if ( is_array ($part) ) {
      $AE_Array = new AE_Array();
      $fields = $AE_Array->toSqlFields($part);
      $part = 'all';
    }
    
    $db = new DB();
    $q = 'SELECT ' . $fields . ' FROM `' . $this->data['table'] . '` ' . $queryStr . ' ' . $extra;

    if ( $limit == 1 ) {
      if ($rec = $db->lookupRecordByQuery($q) )
      {
        if ($part == "all")
          return $rec;
        else
          return $rec[$part];
      }
      else {
        return false;
      }
    } else {
      $resultData = array();
      $db->executeQuery($q);
      while ( $data = $db->nextRecord() ) {
        $resultData[] = $data;
      }
      return $resultData;
    }
  }
  
  function getList ($queryStr, $key, $value, $extra = '') {
    if ( is_array ($queryStr) ) {
      $queryStr = $this->__makeConditions($queryStr);
    } elseif ( ! empty ($queryStr) ) {
      $queryStr =  ' WHERE ' . $queryStr;
    }
  
    $db = new DB();
    $q = 'SELECT `' . $key . '`, `' . $value . '` FROM `' . $this->data['table'] . '` ' . $queryStr . ' ' . $extra;
  
    $resultData = array();
    $db->executeQuery($q);
    while ( $data = $db->nextRecord() ) {
      $resultData[$data[$key]] = $data[$value];
    }
    return $resultData;
  }
  
  
  
  
  
  function getTable()
  {
    return $this->data['table'];
  }
  
  
  
  function parseByArray ($type = 'first', $queryData = array())
  {
  
    $queryParts = array(
      'table' => $this->getTable(),
        'conditions' => null, 'fields' => null, 'joins' => null, 'limit' => null,
        'offset' => null, 'order' => null, 'page' => 1, 'group' => null,
    );
  
    $queryData = array_merge(
      array(
        'conditions' => null, 'fields' => null, 'joins' => array(), 'limit' => null,
        'offset' => null, 'order' => null, 'page' => 1, 'group' => null,
      ),
      (array)$queryData
    );

    foreach ( $queryData as $key => $value ) {
    
        switch ($key) {
          case 'limit':
            $queryParts['limit'] = $value;
            break;
          case 'conditions':
            if ( ! empty ($value) )
            {
              if (is_array($value))
              {
                $queryParts['conditions'] .= $this->parseConditions($value);
              }
              else
              {
                $queryParts['conditions'] .= $value;
              }
            }
            break;
          case 'fields':
            if ( empty ($value) ) {
              $queryParts['fields'] .= '*';
            } else {
              
              $first = true;
              foreach ( $value as $searchField ) {
  
                if (strpos($searchField, '`') === false && strpos($searchField, '*') === false) {
                  if (strpos($searchField, '.') !== false ) {
                    $searchField = str_replace('.', '`.`', $searchField);
                  }
                  $searchField = '`' . $searchField . '`';
                }
                
                $queryParts['fields'] .= ( $first === false ? ', ':'') . '' . $searchField . ' ';
                $first = false;
              }
            }
            
            break;
          case 'joins':
            if ( ! empty ($value) ) {
              foreach ( $value as $key => $join ) {
                $queryParts['joins'] .= ' LEFT JOIN ' . $key . ' ON ' .  $join;
              }
            }
    
            break;
        }
    }
    
    $db = new DB();
    $buildQuery = 'SELECT ' . $queryParts['fields'] . ' FROM ' . $queryParts['table'] . '' . $queryParts['joins'] . '' . $queryParts['conditions'];
    if ( $type === 'list' ) {
      $resultData = array();
      $db->executeQuery($buildQuery);
      while ( $data = $db->nextRecord() ) {
        if ( isset($queryData['fields'][0]) && isset ($queryData['fields'][1]) ) {
          $resultData[$data[$queryData['fields'][0]]] = $data[$queryData['fields'][1]];
        } else {
          if ( isset ($data['id']) ) {
          
          }
          $resultData[$data['id']] = $data['id'];
        }
      }
      return $resultData;
    } elseif ( $queryParts['limit'] == 1 ) {
      if ($rec = $db->lookupRecordByQuery($buildQuery) )
      {
          return $rec;
      }
      else {
        return false;
      }
    } else {
      $resultData = array();
      $db->executeQuery($buildQuery);
      while ( $data = $db->nextRecord() ) {
        $resultData[] = $data;
      }
      return $resultData;
    }
    
    
    
  }
  
  
  function parseConditions ($conditions) {
    $statement = ' WHERE';
    $queryString = '';
  
    $operatorMatch = '/^(((' . implode(')|(', $this->_sqlOps);
    $operatorMatch .= ')\\x20?)|<[>=]?(?![^>]+>)\\x20?|[>=!]{1,3}(?!<)\\x20?)/is';
  
    foreach ( $conditions as $key => $value )
    {
      $key = trim($key);
      if (strpos($key, ' ') === false)
      {
        $operator = '=';
      }
      else
      {
        list($key, $operator) = explode(' ', $key, 2);
        if (!preg_match($operatorMatch, trim($operator)) && strpos($operator, ' ') !== false)
        {
          $key = $key . ' ' . $operator;
          $split = strrpos($key, ' ');
          $operator = substr($key, $split);
          $key = substr($key, 0, $split);
        }
      }
  
      // plaats ` om veldnamen
      if (strpos($key, '`') === false) {
        if (strpos($key, '.') !== false) {
          $key = str_replace('.', '`.`', $key);
        }
        $key = '`' . $key . '`';
      }
      
      
      $null = $value === null || (is_array($value) && empty($value));
  
      $value = $this->quoteValue($value);
      if (is_array($value)) {
        $value = implode(', ', $value);
        switch ($operator) {
          case '=':
            $operator = 'IN';
            break;
          case '!=':
          case '<>':
            $operator = 'NOT IN';
            break;
        }
        $value = "({$value})";
      } elseif ($null || $value === 'NULL') {
        switch ($operator) {
          case '=':
            $operator = 'IS';
            break;
          case '!=':
          case '<>':
            $operator = 'IS NOT';
            break;
        }
      }
  
      $queryString .= ''.$statement . ' ' . $key . ' ' . $operator . ' ' . $value;
      $statement = ' AND';
    }
    return $queryString;
    }
  
  
  function quoteValue($data) {
    
    if (is_array($data) && !empty($data)) {
      foreach ( $data as $key => $value ) {
        $data[$key] = $this->quoteValue($value);
      }
      return $data;
    }
    
    if ($data === null || (is_array($data) && empty($data))) {
      return 'NULL';
    }
  
    if ($data === '') {
      return '""';
    }
    if (is_float($data)) {
      return str_replace(',', '.', strval($data));
    }
    
    if ((is_int($data) || $data === '0') || (
        is_numeric($data) &&
        strpos($data, ',') === false &&
        $data[0] != '0' &&
        strpos($data, 'e') === false)
    ) {
      return $data;
    }
    
    return '"' . mysql_escape_string($data) . '"';
    
  }
  
  
  function parseById($id,$part="all")
  {
    $queryStr = "id = ".(int) mysql_escape_string($id);
    return $this->parseBySearch($queryStr,$part);
  }
  
  function __makeConditions ($conditions) {
    $statement = 'WHERE';
    $queryString = '';

    foreach ( $conditions as $key => $value ) {
      if ( is_numeric($key) ) {
        $value = mysql_real_escape_string($value);
        $queryString .= $statement . ' ' . $value;
        $statement = ' AND';
      } elseif ( ! is_array($value) ) {
        $value = mysql_real_escape_string($value);
        $queryString .= $statement . ' `' . $key . '` = "' . $value . '"';
        $statement = ' AND';
      } else {
        $queryString .= $statement . ' `' . $key . '` IN ("' . implode('", "', $value) . '")';
        $statement = ' AND';
      }
    }
    return $queryString;
  }

  
  
  
  
  
  /**
   * Add a class to an record field
   * 
   * @author RM
   * @param type $field
   * @param type $class
   * @todo add an check of the class is already set
   */
  function addClass ($field, $class)
  {
    /** if array key not exists or empty set it else append it **/
    if ( ! isset ($this->data['fields'][$field]['form_class']) || empty ($this->data['fields'][$field]['form_class']) )
    {
      $this->setOption($field, 'form_class', $class);
    } else {
      $this->setOption($field, 'form_class', $this->data['fields'][$field]['form_class'] . ' ' . $class);
    }
  }
  
  /**
   * set value of a field
   * @param type $field
   * @param type $newValue
   */
  function setvalue ($field, $newValue)
  {
    $this->setPropertie($field, 'value', $newValue);
  }
  
  /**
   * Set property of field
   * @param type $field
   * @param type $key
   * @param type $newValue
   */
  function setPropertie ($field, $key, $newValue)
  {
    $this->data['fields'][$field][$key] = $newValue;
  }
  
  var $recordSqlFolder = 'recordSql';
  function getQueryTemplate ($statement, $data = array()) {
    if ( strpos ($statement, '.') === false ) {$statement .= '.sql';}
    $template = new AE_template();
    $template->appendSubdirToTemplatePath($this->recordSqlFolder . DIRECTORY_SEPARATOR . ( isset ($data['templatePath']) ? $data['templatePath'] : strtolower(get_class($this)) ));
    $data['table'] = $this->data['table'];
    return $template->parseFile($statement, $data);
  }
  
  
}
?>