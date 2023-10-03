<?php

/*
  Author  						: $Author: rm $
  Laatste aanpassing	: $Date: 2015/05/01 14:11:24 $
  File Versie					: $Revision: 1.1 $
 */

class fondsTurboSymbolen extends Table
{
  
  var $issuer = array(
    'ING'     => 'ING Bank',
    'AAB'     => 'ABN AMRO',
    'CB'      => 'Commerzbank',
    'BNP'     => 'BNP Paribas',
    'BIN'     => 'Binck Banck',
    'RBS'     => 'Royal Bank of Scotland',
  );
  
  var $kindOfTurbo = array (
    
  );
  
  
  /*
   * Object vars
   */

  var $data = array();

  /*
   * Constructor
   */

  function fondsTurboSymbolen()
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
    ($this->get("short")=="")?$this->setError("short",vt("Mag niet leeg zijn!")):true;
    ($this->get("long")=="")?$this->setError("long",vt("Mag niet leeg zijn!")):true;

    $fonds = new Fonds();
    $fondsExists = $fonds->parseBySearch(array('Fonds' => $this->get('Fonds'), 'HeeftOptie' => 1), array('Fonds','id'));

    if( $this->get('Fonds') !== $fondsExists['Fonds'] || ($this->get('Fonds') !== '' && $fondsExists === false)) {
      $this->setError('Fonds', vtb(' Fonds "%s" Is niet bekend', array($this->get('Fonds'))));
    }
//    
//    /** if we do an edit check if we didnt change the fonds of symbol **/
//    $checkCombination = false;
//    if ( $this->get('id') ) {
//      $query = "SELECT * FROM `fondsOptieSymbolen` WHERE `id` = '" . $this->get('id') . "' ;";
//      if ( $DB->QRecords($query) > 0 ) {
//        $currentData = $DB->nextRecord();
//        if (
//                $this->get('key') != $currentData['key'] || 
//                $this->get('Fonds') != $currentData['Fonds'] ||
//                $this->get('aantal') != $currentData['aantal'] ||
//                $this->get('optieBeurs') != $currentData['optieBeurs'] 
//        ) {
//          $checkCombination = true;
//        }
//      }
//    }
//    
//    /** if we add or changed the fonds/symbol/aantal check if they are unique **/
//    if ( ! $this->get('id') || $checkCombination === true ) {
//      $query = "SELECT * FROM `fondsOptieSymbolen`
//        WHERE `key` = '" . $this->get('key') . "'
//        AND `Fonds` = '" . $this->get('Fonds') . "'
//        AND `aantal` = '" . $this->get('aantal') . "'
//        AND `optieBeurs` = '" . $this->get('optieBeurs') . "'    
//      ;";
////      debug($query);
////      exit();
//      if ( $DB->QRecords($query) > 0 ) {
//        $this->setError("key","Ingevoerde combinatie bestaat al (Symbool/Fonds/Aantal/Beurs).");
//      }
//    }

    $valid = ($this->error==false)?true:false;
		return $valid;
  }

  /*
   * Table definition
   */

  function defineData()
  {
    global $__appvar;
    $this->data['table'] = "fondsTurboSymbolen";
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

 
    
    $this->addField('short', array(
      "description"  => "Kort",
      "db_size"      => "15",
      "db_type"      => "varchar",
      "form_size"    => "15",
      "form_type"    => "text",
      "form_visible" => true,
      "list_width"   => "150",
      "list_width"   => "150",
      "list_visible" => true,
      "list_align"   => "left",
      "list_search"  => true,
      "list_order"   => "true",
    ));
    
    $this->addField('long', array(
      "description"  => "Lang",
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
    ));
    

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