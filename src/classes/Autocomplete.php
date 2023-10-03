<?php
$baseDir = realpath(dirname(__FILE__)."/..");

include_once($baseDir . "/config/local_vars.php");
include_once($baseDir . "/config/vars.php");
include_once($baseDir . "/config/applicatie_functies.php");


/**
 * Variant 1 vanuit een Record definitie
 *
 * Vanuit een Record definitie kan de auto complete instellingen worden toegevoegd bij het addField
 * Dit kan door het toevoegen van de autocomplete array met de config zoals onderaan beschreven 'autocomplete' => array()
 * Via getAutoCompleteScript wordt de js script opgehaald
 *
 * Vanuit het php script kan dit als volgt gekoppeld worden getAutoCompleteScript(Record, Veld, Veld ID (html id))
 * $editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('Fonds', 'fonds', 'fonds');
 */

/**
 * Variant 2 vanuit het php script
 *
 * resetVirtualField wordt gebruikt om alle waarde voor dit virtuele veld te resetten
 * Via addVirtuelField wordt een virtueel veld aangemaakt bestaande uit veldnaam en de autocomplete array met de config zoals onderaan beschreven
 *
 * $autocomplete->resetVirtualField('fonds');
 * $editObject->formVars['fonds'] = $autocomplete->addVirtuelField('fonds', array());
 * $editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('fonds');
 * Via getAutoCompleteScript wordt de js script opgehaald
 */




/**
 * @todo replace conditions with $GET values {$get:fieldname} should be replaced with $_GET['fieldname']
 * @todo Add jquery to ajax header it should be posible to add field values to the request so the ajax data tag could be filled with
 * data : {
 *  term : request.term,
 *  field1: $("field").val(),
 * }
 */

/**
 * Basic usage as
 * $autocomplete = new Autocomplete();
 * $autocomplete->resetVirtualField('fieldName');
 * $field = $autocomplete->addVirtuelField('fieldName', array(
 *  'autocomplete' => array(
 *     'table' => 'table1, table2, table2',
 *     'label' => array(
 *       'label1',
 *       'label1'
 *     ),
 *     'searchable' => array(
 *       'field1',
 *       'table2`.`field1'
 *     )
 * ));
 * $script = $autocomplete->getAutoCompleteVirtuelFieldScript('fieldName');
 */

/**
 * @property string table list of table names comma sepperated
 * 
 * @property array label is an array of fields shown as an label
 * Could be the following properties array(fieldname, table.fieldname, combine => "{fieldname} / {fieldname}")
 * Combine seperator is an forward dash / 
 * 
 * @property array searchable Fields that can be searched in
 * Could be the folling array array(fieldname, table.fieldname)
 * search is fieldname LIKE '%search string%'
 * 
 * @property array conditions Search conditions
 * Could be the following array(
 *  'table`.`field' => '`table`.`field`',
 *  'AND' => ' (table.field  >=  NOW() OR table.field = "0000-00-00")')
 *  'field >' => 'value'
 * )
 * 
 * @property array field_value the value displayed in the form field
 * Could be the following array 'field_value' => array('field1', 'table.field1'),
 * 
 * @property string value is set in an hidden field
 * could be the following 'value' => 'key'
 *
 * @property array actions array to manually add javascript actions
 * 'actions' => array('select', 'close', 'open', 'change')
 *
 * 'select' => 'event.preventDefault(); $("#Fonds").val(ui.item.field_value);'
 *
 * Alle data vanuit de lookup zijn toegankelijk vanuit de ui variable
 */




class Autocomplete
{
  var $recordLocation = '';
  var $fieldDelimiter = ' - ';
  var $recordLimit = 30;
  var $minLeng = 2;
  var $autocompleteUrl = 'lookups/autocomplete.php';
  
  var $getFields = '';
  var $getJoin = '';
  var $getSearch = '';
  var $getOrder = '';
  var $getGroup = '';
  
  var $defaultJoinType = 'inner';
  var $defaultJoinsTypes = array(
    'inner' => 'INNER JOIN',
    'left'  => 'LEFT JOIN',
    'right' => 'RIGHT JOIN',
    'full'  => 'FULL JOIN'
  );
  var $defaultJoin = array(
    'on'          => '',
    'alias'       => '',
    'type'        => '',
    'conditions'  => '',
    'fields'      => '',
  );
  
  
  
  var $defaultInput = array(
    'form_type' => 'autocomplete',
    'form_visible'  => true
  );
  
  var $default = array (
    'url_addon'     => array(), //addon to the url - array to http_build_query
    'query'         => '',
    'record'        => '',
    'table'         => '',
    'object'         => '',
    'order'         => '',
    'label'         => array(),
    'searchable'    => array(),
    'field_value'   => array('id'),
    'extra_fields'  => array(),
    'value'         => 'id',
    'join'          => array(),
    'prefix'        => false,//standaard geen velden prefixen
    'returnType'    => 'flatten'
  );
  
  /**
   * Sets the appvar to this class
   * set the recordlocation for including
   * 
   * @global type $__appvar
   */
  function Autocomplete ()
  {
    GLOBAL $__appvar;
    $this->recordLocation = $__appvar['basedir'] . '/classes/records';
    $this->AEArray = new AE_Array();
//    if ( ! isset($_SESSION['virtual_field']) ) {$_SESSION['virtual_field'] = array();}
  }
  
  function getRecordLimit() {
    return $this->recordLimit;
  }
  
  /**
   * Fake get function to use with The form object
   * @return null
   * 
   * @since 21-8-2014
   * @author RM
   */
  function get () {return null;}

  /**
   * include the file needed for the object
   * 
   * @param array $object
   * @return boolean
   */
  function loadClassFile ($object)
  {
    if(class_exists($object))
    {
      return true;
    }
    //check normal record
    if ( file_exists($this->recordLocation . DIRECTORY_SEPARATOR . $object .'.php') )
    {
      include_once $this->recordLocation . DIRECTORY_SEPARATOR . $object .'.php';
      return true;
    }
    //check record with first letter lowercase
    $object[0] = strtolower($object[0]); //sets first letter to lowercase
    if ( file_exists($this->recordLocation . DIRECTORY_SEPARATOR . $object .'.php') )
    {
      include_once $this->recordLocation . DIRECTORY_SEPARATOR . $object .'.php';
      return true;
    }
    return false;
  }
  
  /**
   * Get the url to the autocomplete script
   * 
   * @param type $object
   * @param type $field
   * @param array $autoCompeteField
   * @return null|the url
   */
  function getUrl ($object = '', $field = '', $autoCompeteField = array())
  {
    if ( isset($autoCompeteField['url']) && ! empty($autoCompeteField['url']) ) {
      $delimiter = '&';
      if (strpos($autoCompeteField['url'],'?') === false) {
        $delimiter = '?';
      }
      return $autoCompeteField['url'] . $delimiter . ($autoCompeteField['url_addon']);
    } elseif ( ! empty ($object) && ! empty ($field) ) {
      return $this->autocompleteUrl . '?object=' . $object . '&field=' . $field .  ( ! empty($autoCompeteField['url_addon']) ? '&' . $autoCompeteField['url_addon'] : '');
    }
    return null;
  }
  
  /**
   * Returns the autocomplete script
   * 
   * @param type $fieldID
   * @param type $toUrl
   * @param array $actions
   * @return type
   */
  function getScript ($fieldID, $toUrl, $actions = array(), $sourceData = '')
  {
    return '
      $("#' . $fieldID . '").autocomplete(
      {
        source : function(request, response) {
            $.ajax({
                url: "' . $toUrl . '",
                dataType: "json",
                data: {
                  term : request.term,
                  ' . $sourceData . '
                },
                success: function(data) {
                    response(data);
                }
            });
        }, // link naar lookup script
        autoFocus: true,
        open: function() { 
            $(".ui-menu").width(500);
//          $("#' . $fieldID . '").autocomplete("widget").width(500);
          '. ( isset($actions['open']) ? $actions['open'] : '').'
        },
        create : function(event, ui)// onCreate sla oude waardes op om te kunnen resetten in onClose bij geen selectie
        {
          event.preventDefault();
        },
        close : function(event, ui)// controle of ID gevuld is anders reset naar onCreate waarden
        {
          '. ( isset($actions['close']) ? $actions['close'] : '').'
        },
        search : function(event, ui)// als zoeken gestart het ID veld leegmaken
        {
        },
        select : function(event, ui)// bij selectie clientside vars updaten
        {
          '. ( isset($actions['select']) ? $actions['select'] : '').'
          '. ( isset($actions['select_addon']) ? $actions['select_addon'] : '').'
        },
        change: function(event, ui) {
            '. ( isset($actions['change']) ? $actions['change'] : '').'
        },
        //autoFocus: true,
        minLength : "' . $this->minLeng . '", // pas na de tweede letter starten met zoeken
        delay : 0,
        html: true
      });
    ';
  }



  /**
   * Returns the source autocomplete script
   *
   * @param type $fieldID
   * @param type $toUrl
   * @param array $actions
   * @return type
   */
  function getSourceScript ($fieldID, $toUrl, $actions = array(), $sourceData = '')
  {
    return '
      var ' . $fieldID . '_autocomp_opt = {
        source : function(request, response) {
            $.ajax({
                url: "' . $toUrl . '",
                dataType: "json",
                data: {
                  term : request.term,
                  ' . $sourceData . '
                },
                success: function(data) {
                    response(data);
                }
            });
        }, // link naar lookup script
        autoFocus: true,
        open: function() { 
            $(".ui-menu").width(500);
//          $("#' . $fieldID . '").autocomplete("widget").width(500);
          '. ( isset($actions['open']) ? $actions['open'] : '').'
        },
        create : function(event, ui)// onCreate sla oude waardes op om te kunnen resetten in onClose bij geen selectie
        {
          event.preventDefault();
        },
        close : function(event, ui)// controle of ID gevuld is anders reset naar onCreate waarden
        {
          '. ( isset($actions['close']) ? $actions['close'] : '').'
        },
        change : function(event, ui)
        {
        console.log(ui);
          '. ( isset($actions['change']) ? $actions['change'] : '').'
        },
        search : function(event, ui)// als zoeken gestart het ID veld leegmaken
        {
        },
        select : function(event, ui)// bij selectie clientside vars updaten
        {
          '. ( isset($actions['select']) ? $actions['select'] : '').'
          '. ( isset($actions['select_addon']) ? $actions['select_addon'] : '').'
        },
        //autoFocus: true,
        minLength : "' . $this->minLeng . '", // pas na de tweede letter starten met zoeken
        delay : 0,
        html: true
      };
    ';
  }


  
  function resetVirtualField ($field) {unset($_SESSION['virtual_field'][$field]);}
  
  function getVirtualField ($field, $fieldOptions = null)
  {
//    echo '--virtuel-->'; print_r($_SESSION['virtual_field']); echo ' <-- virtuel';
    if ( ! isset ($_SESSION['virtual_field'][$field]) && ! empty($fieldOptions) ) {
      $_SESSION['virtual_field'][$field] = $fieldOptions;
      return $_SESSION['virtual_field'][$field];
    } elseif ( isset ($_SESSION['virtual_field'][$field]) ) {
      return $_SESSION['virtual_field'][$field];
    }
    
    return null;
  }
  
  
  function addVirtuelField ($field, $fieldOptions)
  {
    GLOBAL $__appvar;
    include_once($__appvar['basedir'] . '/classes/formObject.php');
    $virtualField = $this->getVirtualField($field, $fieldOptions);
    
    //merge autocomplete with simple input array
    $this->data['fields'][$field] = array_merge($virtualField, $this->defaultInput);
    //build input field
    
    $form = new Form($this);
    $inputfield = $form->makeInput($field, null);
    
    if ( ! empty($inputfield) ) {
      return $inputfield;
    }
    return null;
  }
  
  function addToField ($object, $field, $data)
  {
    $_SESSION['autocomplete_field'][$object][$field] = $data;
  }

  
  /**
   * Get the script needed for the autocomplete
   * @param type $field
   * @return type
   */
  function getAutoCompleteVirtuelFieldScript ($field, $fieldOptions = array(), $source = false)
  {
    if ($virtualField = $this->getVirtualField($field, $fieldOptions) ) {
      $toUrl = $this->getUrl('virtual', $field, $virtualField['autocomplete']);
      
      $actions['select_addon'] = (isset($virtualField['autocomplete']['actions']['select_addon']) ? $virtualField['autocomplete']['actions']['select_addon'] : '');
      if ( isset($virtualField['autocomplete']['actions']['close'])) {
        $actions['close'] = $virtualField['autocomplete']['actions']['close'];
      }
      
      if ( isset($virtualField['autocomplete']['actions']['open'])) {
        $actions['open'] = $virtualField['autocomplete']['actions']['open'];
      }
      if ( isset($virtualField['autocomplete']['actions']['change'])) {
        $actions['change'] = $virtualField['autocomplete']['actions']['change'];
      }
      
      if ( isset($virtualField['autocomplete']['actions']['select'])) {
        $actions['select'] = $virtualField['autocomplete']['actions']['select'];
      } else {
        $actions['select'] = ' 
          event.preventDefault();
          $("#' . $field . '").val(ui.item.field_value);
          $("#' . $field . '_hidden").val(ui.item.value).trigger("change");  
        ';
      }
      
      $sourceData = '';
      if ( isset ($virtualField['autocomplete']['source_data']) ) {
        $autocompleteSource = $virtualField['autocomplete']['source_data'];
        if ( isset ($autocompleteSource['name']) ) {
          foreach ($autocompleteSource['name'] as $key => $fieldName ) {
            if (is_numeric($key) ) {$key = $fieldName;}
            $sourceData .= $key . ' : $("[name=\'' . $fieldName . '\']").val(),';
          }
        }
        
      }
      
      if ( $source === true  ) {
        return $this->getSourceScript($field, $toUrl, $actions, $sourceData);
      }
      return $this->getScript($field, $toUrl, $actions, $sourceData);

    }
  }
  
  function getAutoCompleteScript ($object, $field, $fieldID)
  {
    if ( $this->loadClassFile($object) )
    {
      $autocompleteObject = new $object();
      if ( isset($autocompleteObject->data['fields'][$field]['autocomplete']) )
      {
        $autoCompeteField = array_merge($this->default, $autocompleteObject->data['fields'][$field]['autocomplete']);
        $toUrl = $this->getUrl($object, $field, $autoCompeteField);

        if ( isset ($autocompleteObject->data['fields'][$field]['autocomplete']['actions']['select']) ) {
          $actions['select'] = $autocompleteObject->data['fields'][$field]['autocomplete']['actions']['select'];
        } else {
          $actions['select'] = ' 
            event.preventDefault();
            $("#' . $fieldID . '").val(ui.item.field_value);
            $("#' . $fieldID . '_hidden").val(ui.item.data.' . (!empty($autoCompeteField['value'])?$autoCompeteField['value']:'id') . ').trigger("change");  
          ';
        }
        return $this->getScript($fieldID, $toUrl, $actions);
      }
    }
  }

  /**
   * getAutoCompleteList
   * The part that returns the json result
   * 
   * @param type $object
   * @param type $field
   * @param type $term
   * @return type
   */
  function getAutoCompleteList ($object, $field, $term)
  {
    $output = array();
    $term = iconv("UTF-8", "Windows-1252", $term);
    $term = mysql_real_escape_string($term);

    if ( $object == 'virtual') {
      $autocompleteObject->data['fields'][$field] = $this->getVirtualField ($field);
    } 
    elseif ( $this->loadClassFile($object) ) {
      $autocompleteObject = new $object();
    }

    if ( isset($autocompleteObject->data['fields'][$field]['autocomplete']) )
    {
      $autoCompeteField = array_merge($this->default, $autocompleteObject->data['fields'][$field]['autocomplete']);

      if ( isset ($_SESSION['autocomplete_field'][$object][$field]) ) {
        $autoCompeteField = array_merge($autoCompeteField, $_SESSION['autocomplete_field'][$object][$field]);
      }

      if ( $autoCompeteField['query'] )
      {
        $query = str_replace('{find}', $term, $autoCompeteField['query'], $term);
        if ( strpos($query,'limit') === false && strpos($query,'LIMIT') === false ) {
          $query = $query . ' LIMIT ' . $this->getRecordLimit($autoCompeteField);
        }
      }
      else
      {
        $this->getFields = $this->formatGetFields($autoCompeteField);
        $this->getJoin = $this->formatGetJoins($autoCompeteField);
        $this->getSearch = $this->formatSearch($autoCompeteField, $term);
        $this->getOrder = $this->formatOrder($autoCompeteField);
        $this->getGroup = $this->formatGroup($autoCompeteField);

        $table = $object;
        if ( $autoCompeteField['table'] )
        {
          $table = $autoCompeteField['table'];
        }
        $query = "SELECT " . $this->getFields . " FROM " . $table . " " . $this->getJoin . " " . $this->getSearch . " " . $this->getGroup . " " . $this->getOrder . "  LIMIT " . $this->getRecordLimit($autoCompeteField);
      }


      // Standaard waarde aan list toevoegen alleen als key=>value
      if ( isset ($autoCompeteField['default_values']) && ! empty ($autoCompeteField['default_values']) ) {
        foreach ( $autoCompeteField['default_values'] as $defaultValueKey => $defaultValueValue ) {
          $output[] = array(
            'label'       => $defaultValueValue,
            'field_value' => $defaultValueValue,
            'value'       => $defaultValueKey,
            'data'        => null
          );
        }
      }


      
      $originalValue = $autoCompeteField['value'];
      if ( strpos($autoCompeteField['value'], '.') !== false ) {
        list($table, $autoCompeteField['value']) = explode('.', $autoCompeteField['value']);
      }
      
      $db = new DB();
      $db->executeQuery($query);
      while ($rec = $db->nextRecord())
      {
        $rec = array_map('htmlspecialchars', $rec);
        $value = '';
        if ( isset ($rec[$autoCompeteField['value']]) ) {
          $value = $rec[$autoCompeteField['value']];
        } elseif (isset ($rec[$originalValue] )) {
          $value = $rec[$originalValue];
        }
        
        $returnData = $rec;
        switch ($autoCompeteField['returnType'])
        {
          case 'expanded':
            $returnData = $this->AEArray->expand($rec);
            break;
        }

        // rijen markeren wanneer deze in de mark list staan
        $labelAddon = '';
        if ( isset ($autoCompeteField['markRecords']) && isset ($autoCompeteField['markRecords']['markOn']) && isset($autoCompeteField['markRecords']['markList']) ) {
          if ( in_array($returnData[$autoCompeteField['markRecords']['markOn']], $autoCompeteField['markRecords']['markList'])) {
            $labelAddon = ' (*) ';
          }
        }
        // einde rijen markeren
        
        $output[] = array(
          'label'       => $this->formatField($autoCompeteField['label'], $rec) . $labelAddon,
          'field_value' => $this->formatField($autoCompeteField['field_value'], $rec),
          'value'       => $value,
          'data'        => $returnData
        );
      }
    }
    if ( isset ($autoCompeteField['default_sort']) && ! empty ($autoCompeteField['default_sort']) ) {
      if ( strtolower($autoCompeteField['default_sort']) == 'asc') {
        usort($output, array( $this, 'sortAsc' ));
      } else {

      }
    }

    if ( isset($_GET['airsDebug']) && $_GET['airsDebug'] === 'airs' ) {
      debug($query);
      debug($output);
    }
    
    return $output;
  }

  function sortAsc ($a, $b) {
    return strcasecmp ($a['label'], $b['label']);
  }
  
  function formatOrder ($field)
  {
    if ( ! empty($field['order']) ) {
      return 'ORDER BY ' . $field['order'];
    }
    return null;
  }
  
  function formatGroup ($field)
  {
    if ( ! empty($field['group']) ) {
      return 'GROUP BY ' . $field['group'];
    }
    return null;
  }
  
  /**
   * Format the search string where / or field like
   * 
   * @author RM
   * @since 7-7-2014
   * 
   * @param type $field
   * @param type $term
   * @return string
   */
  function formatSearch ($field, $term)
  {
    $search = '';
    foreach ( $field['searchable'] as $key => $value )
    {
      $type = ' OR';
      if ( $key == 0 ) {
         $type = 'WHERE (';
      }
      /** check for dotted notation table.field replace . with `.` **/
      if ( strpos($value, '`.`') === false && strpos($value, '.') > 0 ) { $value = str_replace('.', '`.`', $value);}
      if ($value[0] != '`') {$value = '`' . $value . '`';}
      $search .= $type." " . $value . " LIKE '%" . $term . "%'";
    }
    $search .= ')';
    
    if ( ! empty ($field['conditions']) ) {
      foreach ( $field['conditions'] as $key => $value) {
        if ( is_array($value) ) {
          foreach ( $value as $statementKey => $statement ) {
            if ( is_null ($statement) || ( empty ($statement) && empty($statementKey) )  ) {continue;}
            if (is_numeric($statementKey)) {$statementKey = $key;}
            $search .= $this->__makeIfStatement($statementKey, $statement);
          }
        } else {
          $search .= $this->__makeIfStatement($key, $value);
        }
      }
    }

    return $search;
  }
  
  function __makeIfStatement ($key, $value) {
    $AEDatum = new AE_datum();
    $search = '';
    /** set special variables here {$get:variableName} **/
      if ( is_string($value) ) {
        preg_match_all('/{\$get:+(.*?)}/',$value, $replaceValues);
        if ( ! empty ($replaceValues) ) {
          foreach ( $replaceValues[1] as $replaceKey => $replaceString ) {
            if ( isset ($_GET[$replaceString]) ) {

              /** probleem met escapen van quotes **/
              if ( $_GET[$replaceString] === "\' \'" || $_GET[$replaceString] === '\" \"' ) {
                $_GET[$replaceString] = '""';
              }
              if ( strpos($_GET[$replaceString], "\\") !== false ) {
              	$_GET[$replaceString] = str_replace('\\', "", $_GET[$replaceString] );
              }

              $replaceVariable = $_GET[$replaceString];
              if ( $replaceVariable === "1" || $replaceVariable === "0" ) {
                $replaceVariable = (bool) $replaceVariable;
              } else {
                if( preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $AEDatum->formToDb($replaceVariable)) && $AEDatum->formToDb($replaceVariable) != '1970-01-01'){
                  $replaceVariable = jul2sql(form2jul($replaceVariable));
                }
              }
              $value = str_replace($replaceValues[0][$replaceKey], $replaceVariable, $value);
            }
            $value = str_replace($replaceValues[0][$replaceKey], '', $value);
          }
        }
      }
        /** end replace variables **/
        
        
        if ( is_numeric($key) && isset( $_GET[$value]) ) {
          $condition = $value;
          if (false !== strtotime($_GET[$condition])) {
            $_GET[$condition] = jul2sql(form2jul($_GET[$condition]));
          }
          $search .= " AND `" . $condition . "` = '" . $_GET[$condition] . "'";
        } elseif ( ! is_numeric($key)) {
          if (is_array($value)) {
            if ( strpos($key, '`.`') === false && strpos($key, '.') > 0 ) { $key = str_replace('.', '`.`', $key);}
            if ($key[0] != '`') {$key = '`' . $key . '`';}
      
            $search .= " AND " . $key . " IN ('" . implode("','", $value) . "')";
          } elseif ( strpos($key, '>') !== false) {
            if ($value[0] != '`') {$value = '\'' . $value . '\'';}
            $key = str_replace('>', '', $key);
            $search .= " AND `" . trim($key) . "` > " . $value . "";
          }elseif ( $key === 'AND') {
            $search .= " AND " . $value . "";
          } else {
            if ( strpos($key, '`.`') === false && strpos($key, '.') > 0 ) { $key = str_replace('.', '`.`', $key);}
            if ($key[0] != '`') {$key = '`' . $key . '`';}
            
            if ($value[0] != '`') {$value = '\'' . $value . '\'';}
            $search .= " AND " . $key . " = " . $value . "";
          }
        }
        
        return $search;
  }

  function formatField ($field, $record)
  {
    $label = array();
    foreach ($field as $key => $fieldName)
    {
      $originalFieldName = $fieldName;
      if ( $key === 'combine' ) {
        $combineField = $fieldName;
        foreach ( $record as $recordKey => $recordValue ) {
          $combineField = str_replace('{' . $recordKey . '}', $recordValue, $combineField);
        }
        
        /** if we have an delimiter / check if the first and second variable are set and clear delimiter if empty **/
        $pieces = explode('/', $combineField);
        if ( empty ($pieces[0]) || substr($fieldName, 0, 1) == str_replace(' ', '', $pieces[0]) ) {
          $combineField = str_replace('/', '', $combineField);
          $combineField = str_replace(' ', '', $combineField);
        }
        if ( empty ($pieces[1]) || substr($fieldName, -1) == str_replace(' ', '', $pieces[1]) ) {
          $combineField = str_replace('/', '', $combineField);
          $combineField = str_replace(' ', '', $combineField);
        }
        
        $label[] = $combineField;
      }
      
      if ( strpos($fieldName, '.') !== false ) {
        list($table, $fieldName) = explode('.', $fieldName);
      }
      if ( isset($record[$fieldName]) ) {
        $label[] = $record[$fieldName];
      } elseif ( isset($record[$originalFieldName]) ) {
        $label[] = $record[$originalFieldName];
      }
    }
    return implode($this->fieldDelimiter, $label);
  }

  function formatGetFields ($field)
  {
    unset($field['label']['combine']); /** unset combine veld zodat deze niet als select wordt opgenomen **/
    
    /** voeg alle velden samen **/
    $fieldsArray = array_merge($field['searchable'], array($field['value']), $field['label'], $field['field_value'], $field['extra_fields']);
    $fieldsArray = array_unique($fieldsArray);
    
    /** wanneer we een * (alle velden ophalen) **/
    if (in_array('*', $fieldsArray) ) {
      /** if we need to prefix the fields **/
      if ( $field['prefix'] === true ) {
        return $this->__prefixFields(null, ( ! empty($field['object']) ? $field['object'] : $field['table']));
      }
      return '*';
    }
    
    /** if we need to prefix the fields **/
    if ( $field['prefix'] === true ) {
      return $this->__prefixFields($fieldsArray, ( ! empty($field['object']) ? $field['object'] : $field['table']));
    }
    
    /** convert table.field to table`.`field for the implode **/
    $fieldsArray = str_replace('.', '`.`', $fieldsArray);
    
    return '`' . implode('`,`', $fieldsArray) . '`';
  }
  
  function formatGetJoins ($field)
  {
    $join = '';
    if ( ! empty($field['join']) && is_array($field['join']) ) {
      foreach ( $field['join'] as $object => $conditions ) {
        
        if ( $field['prefix'] === true ) {
          if ( class_exists ($object) ) {
            $joinObject = new $object;
            $joinTable = $joinObject->data['table'];
            if ( ! isset($conditions['fields']) ) {$conditions['fields'] = array();}
            if ( ! empty($conditions['alias']) ) {
              $this->getFields .= ', ' .$this->__prefixFields($conditions['fields'], $object, $conditions['alias']);
            } else {
              $this->getFields .= ', ' .$this->__prefixFields($conditions['fields'], $object);
            }
          } else {
            $joinTable = $object;
            if ( ! empty ($conditions['fields']) ) {
              if ( ! empty($conditions['alias']) ) {
                $this->getFields .= ', ' .$this->__prefixFields($conditions['fields'], null);
              }
            }
          }
        }
        
        $conditions = array_merge($this->defaultJoin , $conditions);
        /** set join type **/
        if ( ! empty($conditions['type']) ) {
          $join .= isset($this->defaultJoinsTypes[$conditions['type']]) ? $this->defaultJoinsTypes[$conditions['type']] : exit('join not found');
        } else {
          $join .= isset($this->defaultJoinsTypes[$this->defaultJoinsType]) ? $this->defaultJoinsTypes[$this->defaultJoinsType] : exit('join not found');
        }
        
        /** add Table to join **/
        $join .= ' ' . $this->__encapsulateField($joinTable);
        if ( ! empty ($conditions['alias']) ) {
          $join .= ' AS ' . $this->__encapsulateField($conditions['alias']);
          $joinTable = $conditions['alias'];
        }
        
        $join .=  ' ON ';
        
        /** set on statement **/
        $joinCount = 0;
        if ( isset ($conditions['on']) ) {
          if ( is_array($conditions['on']) ) {
            foreach ( $conditions['on'] as $joinFrom => $joinOn ) {
              if ( $joinCount > 0) {$join .= ' AND ';}
              $join .= ((strpos($joinFrom,'.') === false) ? $this->__encapsulateField($field['table'] . '.' . $joinFrom) : $this->__encapsulateField($joinFrom));
              $join .= ' = ';
              $join .= ((strpos($joinOn,'.') === false) ? $this->__encapsulateField($joinTable . '.' . $joinOn) : $this->__encapsulateField($joinOn));
              $joinCount++;
            }
          }
        }
      }
    }
    
    return $join;
  }
  
  /**
   * encapsulate fields with ` caracters
   * @param type $field
   * @return type
   */
  function __encapsulateField($field)
  {
    $field = str_replace('`', '', $field);
    $field = str_replace('.', '`.`', $field);
    return '`' . $field . '`';
  }
  
  function __prefixFields ($fields, $object, $alias = null) 
  {
    $fieldsString = '';
    $firstItem = 0;

    if ( ! $object ) {
      foreach ( $fields as $field ) {
        if ( $firstItem > 0 ) {$fieldsString .= ', ';}
        if ( strpos ($field, '*') !== false ) {
          $fieldsString .= $field;
        } else {
          $fieldsString .= $this->__encapsulateField($field);
        }
        $firstItem++;
      }
    } else {
      $prefix = ( ! empty ($alias) ? $alias : $object);

      if ( ! $fields ) {
        $prefixObject = new $object;
        $fields = array_keys($prefixObject->data['fields']);
      }

      foreach ( $fields as $field ) {
        if ( $firstItem > 0 ) {$fieldsString .= ', ';}
        if ( strpos ($field, '*') !== false ) {
          $fieldsString .= $prefix.'.'.$field;
        } 
        elseif ( strpos ($field, 'MAX') !== false ) {
          $asValue = $field;
          $asValue = str_replace(array('MAX', '(', ')', '`'), '', $asValue);
          $fieldsString .= $field . ' AS \'' . $asValue . '\'';
        }
        elseif ( strpos ($field, '.') !== false ) {
          $fieldsString .= $this->__encapsulateField($field) . ' AS \'' . $field . '\'';
        } else {
          $fieldsString .= $this->__encapsulateField($prefix . '.' .$field) . ' AS \'' . $prefix . '.' .$field . '\'';
        }

        $firstItem++;
      }
    }
    return $fieldsString;
  }

}