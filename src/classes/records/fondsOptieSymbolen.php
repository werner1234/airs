<?php

/*
  Author  						: $Author: rm $
  Laatste aanpassing	: $Date: 2017/01/18 16:07:36 $
  File Versie					: $Revision: 1.5 $
 */

class fondsOptieSymbolen extends Table
{
  /*
   * Object vars
   */

  var $data = array();

  /*
   * Constructor
   */

  function fondsOptieSymbolen()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'], 0);
  }

  function addField($name, $properties)
  {
    $this->data['fields'][$name] = $properties;
  }

  function checkAccess($type)
  {
    global $__appvar,$USR;
   	if($_SESSION['usersession']['superuser'])
    {
      if(isset($__appvar["homeAdmins"]) && $type=='delete')
      {
        if(in_array($USR,$__appvar["homeAdmins"]))
          return true;
      }
      else
		    return true;
	  }
    return false;
  }

  function validate()
  {
    global $__appvar;
    
    ($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;
    ($this->get("key")=="")?$this->setError("key",vt("Mag niet leeg zijn!")):true;
    (strlen($this->get("key")) > 5)?$this->setError("key",vt("Veld mag niet meer dan 5 karakters bevatten!")):true;
    ($this->get("optieValuta")=="")?$this->setError("optieValuta",vt("Mag niet leeg zijn!")):true;
    ($this->get("aantal")=="")?$this->setError("aantal",vt("Mag niet leeg zijn!")):true;
    ($this->get("optieBeurs")=='keuze')?$this->setError("optieBeurs",vt("Maak een keuze!")):true;
//    ($this->get("optieBeurs")=="")?$this->setError("optieBeurs","Mag niet leeg zijn!"):true;
//    ($this->get("optieVWD")=="")?$this->setError("optieVWD","Mag niet leeg zijn!"):true;
    
    if ($this->get("optieVWD") != "") {
      if ( ! preg_match('/^[A-Za-z0-9]{1,}\.[A-Za-z0-9]{1,}$/', $this->get("optieVWD")) ) {
        $this->setError("optieVWD",vt("Opbouw is niet correct (text.text)!"));
      }
    }
    
    $DB = new DB();

		$query  = "SELECT id FROM Fondsen WHERE Fonds = '" . $this->get('Fonds') . "' AND HeeftOptie = 1;";

		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() <= 0) {
			$this->setError('Fonds', vtb('%s Is niet bekend of mag geen optie bevatten', array($this->get('Fonds'))));
    }
    
    /** if we do an edit check if we didnt change the fonds of symbol **/
    $checkCombination = false;
    if ( $this->get('id') ) {
      $query = "SELECT * FROM `fondsOptieSymbolen` WHERE `id` = '" . $this->get('id') . "' ;";
      if ( $DB->QRecords($query) > 0 ) {
        $currentData = $DB->nextRecord();
        if (
                $this->get('key') != $currentData['key'] || 
                $this->get('Fonds') != $currentData['Fonds'] ||
                $this->get('aantal') != $currentData['aantal'] ||
                $this->get('optieBeurs') != $currentData['optieBeurs'] 
        ) {
          $checkCombination = true;
        }
      }
    }
    
    /** if we add or changed the fonds/symbol/aantal check if they are unique **/
    if ( ! $this->get('id') || $checkCombination === true ) {
      $query = "SELECT * FROM `fondsOptieSymbolen`
        WHERE `key` = '" . $this->get('key') . "'
        AND `Fonds` = '" . $this->get('Fonds') . "'
        AND `aantal` = '" . $this->get('aantal') . "'
        AND `optieBeurs` = '" . $this->get('optieBeurs') . "'    
      ;";
//      debug($query);
//      exit();
      if ( $DB->QRecords($query) > 0 ) {
        $this->setError("key", vt("Ingevoerde combinatie bestaat al (Symbool/Fonds/Aantal/Beurs)."));
      }
    }

    $valid = ($this->error==false)?true:false;
		return $valid;
  }

  /*
   * Table definition
   */

  function defineData()
  {
    global $__appvar;
    $this->data['table'] = "fondsOptieSymbolen";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

    $this->addField('id', array("description"  => "id",
      "db_size"      => "11",
      "db_type"      => "int",
      "form_type"    => "text",
      "form_visible" => false,
      "list_visible" => true,
      "list_align"   => "right",
      "list_search"  => false,
      "list_order"   => "true"
    ));

    $this->addField('key', array(
      "description"  => "Symbool",
      "db_size"      => "45",
      "db_type"      => "varchar",
      "form_size"    => "20",
      "form_type"    => "text",
      "form_visible" => true,
      "list_width"   => "150",
      "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => true,
      "list_order"   => "true",
      'form_extra'   => 'maxlength="5"'
    ));

    $this->addField('Fonds', array(
      "description"  => "Fonds",
      "db_size"      => "25",
      "db_type"      => "varchar",
      "form_size"    => "25",
      "form_type"    => "text",
      "form_visible" => true,
      "list_width"   => "150",
      "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => true,
      "list_order"   => "true",
      "keyIn"        => "Fondsen"
    ));

    $this->addField('aantal', array(
      "description"  => "Aantal",
      "db_size"      => "25",
      "db_type"      => "varchar",
      "form_size"    => "25",
      "form_type"    => "text",
      "form_visible" => true,
      "list_width"   => "150",
      "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => true,
      "list_order"   => "true",
      'form_class'   => 'maskNumeric'
    ));

    $this->addField('optieValuta', array(
      "description"  => "Valuta",
      "db_size"      => "4",
      "db_type"      => "char",
      "form_size"    => "4",
      "form_type"    => "select",
      "form_visible" => true, "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "right",
      "list_search"  => false,
      "list_order"   => "true",
      "keyIn"        => "Valutas"
    ));

    $this->addField('optieBeurs', array(
      "description"   => "Beurs",
      "db_size"       => "45",
      "db_type"       => "varchar",
      "form_size"     => "12",
      "form_type"     => "selectKeyed",
      "select_query"  => "(SELECT 'keuze','keuze') UNION (SELECT Beurs,Omschrijving FROM Beurzen) ",
      "form_visible"  => true, "list_width"    => "150",
      "default_value" => 'keuze',
      "list_visible"  => true,
      "list_align"    => "left",
      "list_search"   => false,
      "list_order"    => "true",
      "keyIn"         => "Beurzen"
    ));

    $this->addField('optieVWD', array(
      "description"  => "Identifier VWD",
      "db_size"      => "80",
      "db_type"      => "varchar",
      "form_type"    => "text",
      "form_visible" => true, 
      "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => false,
      "list_order"   => "true"
    ));
    
    $this->addField('optieVWDSuffix', array(
      "description"  => "VWD achtervoegsel",
      "db_size"      => "5",
      "db_type"      => "varchar",
      "form_type"    => "text",
      "form_visible" => true, 
      "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => false,
      "list_order"   => "true"
    ));
    
    $this->addField('optieVWDFactor', array(
      "description"  => "VWD-factor",
      "db_size"      => "5",
      "db_type"      => "int",
      "form_type"    => "text",
      "form_visible" => true, 
      "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => false,
      "list_order"   => "true",
      'form_class'   => 'maskNumeric'
    ));
    

    $this->addField('optieAABCode', array("description"  => "AAB-Code",
      "db_size"      => "26",
      "db_type"      => "varchar",
      "form_type"    => "text",
      "form_visible" => true, "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => false,
      "list_order"   => "true"));

    $this->addField('optieaabbeCode', array("description"  => "AABBE-Code",
      "db_size"      => "30",
      "db_type"      => "varchar",
      "form_type"    => "text",
      "form_visible" => true, "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => false,
      "list_order"   => "true"));

    $this->addField('optiestroeveCode', array("description"  => "Stroeve-Code",
      "db_size"      => "25",
      "db_type"      => "varchar",
      "form_type"    => "text",
      "form_visible" => true, "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => false,
      "list_order"   => "true"));

    $this->addField('optiekasbankCode', array("description"  => "Kasbank-Code",
      "db_size"      => "35",
      "db_type"      => "varchar",
      "form_type"    => "text",
      "form_visible" => true, "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => false,
      "list_order"   => "true"));

    $this->addField('optiebinckCode', array("description"  => "Binck-Code",
      "db_size"      => "26",
      "db_type"      => "varchar",
      "form_type"    => "text",
      "form_visible" => true, "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => false,
      "list_order"   => "true"));

    $this->addField('optiesnsSecCode', array("description"  => "SNSSEC-Code",
      "db_size"      => "30",
      "db_type"      => "varchar",
      "form_type"    => "text",
      "form_visible" => true, "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => false,
      "list_order"   => "true"));
  
    $this->addField('optieSAXOcode', array("description"  => "SAXO-Code",
                                             "db_size"      => "26",
                                             "db_type"      => "varchar",
                                             "form_type"    => "text",
                                             "form_visible" => true, "list_width"   => "150",
                                             "list_visible" => true,
                                             "list_align"   => "left",
                                             "list_search"  => false,
                                             "list_order"   => "true"));

    $this->addField('add_date', array(
      "description"  => "add_date",
      "db_size"      => "0",
      "db_type"      => "datetime",
      "form_type"    => "datum",
      "form_visible" => true,
      "list_visible" => true,
      "list_align"   => "right",
      "list_search"  => false,
      "list_order"   => "true"
    ));

    $this->addField('add_user', array(
      "description"  => "add_user",
      "db_size"      => "10",
      "db_type"      => "varchar",
      "form_type"    => "text",
      "form_visible" => true,
      "list_visible" => true,
      "list_align"   => "right",
      "list_search"  => false,
      "list_order"   => "true"
    ));

    $this->addField('change_date', array(
      "description"  => "change_date",
      "db_size"      => "0",
      "db_type"      => "datetime",
      "form_type"    => "datum",
      "form_visible" => true,
      "list_visible" => true,
      "list_align"   => "right",
      "list_search"  => false,
      "list_order"   => "true"
    ));

    $this->addField('change_user', array(
      "description"  => "change_user",
      "db_size"      => "10",
      "db_type"      => "varchar",
      "form_type"    => "text",
      "form_visible" => true,
      "list_visible" => true,
      "list_align"   => "right",
      "list_search"  => false,
      "list_order"   => "true"
    ));
  }

}

?>