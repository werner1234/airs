<?php
/*
    AE-ICT CODEX source module versie 2.0 (simbis), 09-06-2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/08/26 15:44:29 $
    File Versie         : $Revision: 1.1 $

    $Log: fixOrders.php,v $
    Revision 1.1  2015/08/26 15:44:29  rvv
    *** empty log message ***



*/

class FixOrders extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function FixOrders()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields']["$name"] = $properties;
	}

	/*
	 * Veldvalidatie
	 */
	function validate()
	{

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	 return checkAccess();  // override accessControl
   /*
	 $level = getMyLevel("Default");
	  switch ($type)
	  {
	  	case "edit":
	  		return ($level >=3 )?true:false;
	  		break;
	  	case "delete":
	  		return ($level >=7 )?true:false;
	  		break;
	  	default:
	  	  return false;
	  		break;
	  }
   */ 
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']     = 'FIX orders';
    $this->data['table']    = 'fixOrders';
    $this->data['identity'] = 'id';

// eigenschap definitie voor veld id
	$this->addField('id', array(
		'description'  => 'id',
		'default_value'=> '',
		'db_size'      => '11',
		'db_type'      => 'int',
		'db_extra'     => 'auto increment',
		'form_type'    => 'text',
		'form_size'    => '11',
		'form_visible' => false,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld change_user
	$this->addField('change_user', array(
		'description'  => 'change_user',
		'default_value'=> '',
		'db_size'      => '10',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '10',
		'form_visible' => false,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld change_date
	$this->addField('change_date', array(
		'description'  => 'change_date',
		'default_value'=> '',
		'db_size'      => '0',
		'db_type'      => 'datetime',
		'db_extra'     => '',
		'form_type'    => 'calendar',
		'form_size'    => '0',
		'form_visible' => false,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld add_user
	$this->addField('add_user', array(
		'description'  => 'add_user',
		'default_value'=> '',
		'db_size'      => '10',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '10',
		'form_visible' => false,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld add_date
	$this->addField('add_date', array(
		'description'  => 'add_date',
		'default_value'=> '',
		'db_size'      => '0',
		'db_type'      => 'datetime',
		'db_extra'     => '',
		'form_type'    => 'calendar',
		'form_size'    => '0',
		'form_visible' => false,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld portefeuille
	$this->addField('portefeuille', array(
		'description'  => 'portefeuille',
		'default_value'=> '',
		'db_size'      => '25',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '25',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'center',
		'list_search'  => true,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld client
	$this->addField('client', array(
		'description'  => 'client',
		'default_value'=> '',
		'db_size'      => '50',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '50',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));
  
// eigenschap definitie voor veld client
	$this->addField('AIRSorderReference', array(
		'description'  => 'AIRSorderReference',
		'default_value'=> '',
		'db_size'      => '10',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '10',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld rekeningnr
	$this->addField('rekeningnr', array(
		'description'  => 'rekeningnr',
		'default_value'=> '',
		'db_size'      => '30',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '30',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'center',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld vermogensBeheerder
	$this->addField('vermogensBeheerder', array(
		'description'  => 'vermogensBeheerder',
		'default_value'=> '',
		'db_size'      => '20',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '20',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'center',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld orderid
	$this->addField('orderid', array(
		'description'  => 'orderid',
		'default_value'=> '',
		'db_size'      => '20',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '20',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'center',
		'list_search'  => true,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld aantal
	$this->addField('aantal', array(
		'description'  => 'aantal',
		'default_value'=> '',
		'db_size'      => '0',
		'db_type'      => 'double',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '0',
		'form_visible' => true,
		'list_visible' => true,
		'list_format'  => '%01.2f',
		'list_width'   => '',
		'list_align'   => 'right',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld fondsCode
	$this->addField('fondsCode', array(
		'description'  => 'fondsCode',
		'default_value'=> '',
		'db_size'      => '20',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '20',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));
  
	$this->addField('bankfondsCode', array(
		'description'  => 'bankfondsCode',
		'default_value'=> '',
		'db_size'      => '20',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '20',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'center',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld fonds
	$this->addField('fonds', array(
		'description'  => 'fonds',
		'default_value'=> '',
		'db_size'      => '30',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '30',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld fondsOmschrijving
	$this->addField('fondsOmschrijving', array(
		'description'  => 'fondsOmschrijving',
		'default_value'=> '',
		'db_size'      => '70',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '70',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '200',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld transactieType
	$this->addField('transactieType', array(
		'description'  => 'transactieType',
		'default_value'=> '',
		'db_size'      => '6',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '6',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'center',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld transactieSoort
	$this->addField('transactieSoort', array(
		'description'  => 'transactieSoort',
		'default_value'=> '',
		'db_size'      => '6',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '6',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'center',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld tijdsLimiet
	$this->addField('tijdsLimiet', array(
		'description'  => 'tijdsLimiet',
		'default_value'=> '',
		'db_size'      => '0',
		'db_type'      => 'date',
		'db_extra'     => '',
		'form_type'    => 'calendar',
		'form_size'    => '0',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'right',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld tijdsSoort
	$this->addField('tijdsSoort', array(
		'description'  => 'tijdsSoort',
		'default_value'=> '',
		'db_size'      => '6',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '6',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld koersLimiet
	$this->addField('koersLimiet', array(
		'description'  => 'koersLimiet',
		'default_value'=> '',
		'db_size'      => '0',
		'db_type'      => 'double',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '0',
		'form_visible' => true,
		'list_visible' => true,
		'list_format'  => '%01.2f',
		'list_width'   => '80',
		'list_align'   => 'right',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld status
	$this->addField('status', array(
		'description'  => 'status',
		'default_value'=> '',
		'db_size'      => '60',
		'db_type'      => 'text',
		'db_extra'     => '',
		'form_type'    => 'textarea',
		'form_rows'    => '5',
		'form_size'    => '60',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld laatsteStatus
	$this->addField('laatsteStatus', array(
		'description'  => 'laatsteStatus',
		'default_value'=> '',
		'db_size'      => '25',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '25',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'center',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld Depotbank
	$this->addField('Depotbank', array(
		'description'  => 'Depotbank',
		'default_value'=> '',
		'db_size'      => '10',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '10',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'center',
		'list_search'  => false,
		'list_order'   => true ));

	$this->addField('DepotbankOrderId', array(
		'description'  => 'DepotbankOrderId',
		'default_value'=> '',
		'db_size'      => '20',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '20',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld uitvoeringsPrijs
	$this->addField('uitvoeringsPrijs', array(
		'description'  => 'uitvoeringsPrijs',
		'default_value'=> '',
		'db_size'      => '0',
		'db_type'      => 'double',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '0',
		'form_visible' => true,
		'list_visible' => true,
		'list_format'  => '%01.2f',
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld uitvoeringsDatum
	$this->addField('uitvoeringsDatum', array(
		'description'  => 'uitvoeringsDatum',
		'default_value'=> '',
		'db_size'      => '0',
		'db_type'      => 'date',
		'db_extra'     => '',
		'form_type'    => 'calendar',
		'form_size'    => '0',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld aantalUitgeveord
	$this->addField('aantalUitgevoerd', array(
		'description'  => 'aantalUitgevoerd',
		'default_value'=> '',
		'db_size'      => '0',
		'db_type'      => 'double',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '0',
		'form_visible' => true,
		'list_visible' => true,
		'list_format'  => '%01.2f',
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld meldingen
	$this->addField('meldingen', array(
		'description'  => 'meldingen',
		'default_value'=> '',
		'db_size'      => '60',
		'db_type'      => 'text',
		'db_extra'     => '',
		'form_type'    => 'textarea',
		'form_rows'    => '5',
		'form_size'    => '60',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld verwerkt
	$this->addField('verwerkt', array(
		'description'  => 'verwerkt',
		'default_value'=> '',
		'db_size'      => '4',
		'db_type'      => 'tinyint',
		'db_extra'     => '',
		'form_type'    => 'checkbox',
		'form_size'    => '4',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld verwerktStamp
	$this->addField('verwerktStamp', array(
		'description'  => 'verwerktStamp',
		'default_value'=> '',
		'db_size'      => '0',
		'db_type'      => 'datetime',
		'db_extra'     => '',
		'form_type'    => 'calendar',
		'form_size'    => '0',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


// eigenschap definitie voor veld verwerktResult
	$this->addField('verwerktResult', array(
		'description'  => 'verwerktResult',
		'default_value'=> '',
		'db_size'      => '20',
		'db_type'      => 'varchar',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '20',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));

// extra beschikbare tags..
//		'form_class' => '',
//		'form_extra' => '',
//		'form_options' => '',
//		'form_select_option_notempty' => true,
//		'list_numberformat'  => '',
//		'list_invisible'  => '',
//		'list_tdcode'  => '',


	$this->addField('no_legs', array(
		'description'  => 'no_legs',
		'default_value'=> '',
		'db_size'      => '11',
		'db_type'      => 'int',
		'db_extra'     => '',
		'form_type'    => 'text',
		'form_size'    => '20',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));
    
  	$this->addField('legs', array(
		'description'  => 'legs',
		'default_value'=> '',
		'db_size'      => '11',
		'db_type'      => 'int',
		'db_extra'     => '',
		'form_type'    => 'textarea',
		'form_size'    => '20',
		'form_visible' => true,
		'list_visible' => true,
		'list_width'   => '',
		'list_align'   => 'left',
		'list_search'  => false,
		'list_order'   => true ));  

  }
}
?>