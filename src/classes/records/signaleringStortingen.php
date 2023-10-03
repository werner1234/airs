<?php
/*
    AE-ICT sourcemodule created 04 jul. 2022
    Author              : Chris van Santen
    Filename            : signaleringStortingen.php


*/

class signaleringStortingen extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function signaleringStortingen()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
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
		return true;
    return checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "signaleringStortingen";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



		$this->addField('portefeuille',
													array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"24",
													"form_visible"=>true,
													"form_extra"=>'readonly',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Portefeuilles"));


		$this->addField('bedrag',
													array("description"=>"bedrag",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"form_extra"=>'readonly',
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('datum',
													array("description"=>"Boekdatum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"form_extra"=>'disabled',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('rekeningmutatieId',
                    array("description"=>"rekeningmutatieId",
                          "default_value"=>"",
                          "db_size"=>"11",
                          "db_type"=>"int",
                          "form_type"=>"text",
                          "form_size"=>"11",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>true,
                          "list_order"=>"true"));

		$this->addField('status',
													array("description"=>"Status",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"tinyint",
											  	"form_type"=>"selectKeyed",
													"form_extra"=>'',//disabled
												  "form_options"=>array(0=>'Nieuw',1=>'Bevestigd',2=>'Verwijderd'),
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('toelichting',
										array("description"=>"Toelichting",
													"db_size"=>"255",
													"db_type"=>"text",
                          "form_type"=>"selectKeyed",
													"form_options"=>array(),
													"form_size"=>"50",
													"form_rows"=>"4",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"toelichtingStortOnttr"));

		$this->addField('passend',
										array("description"=>"Passend",
													"default_value"=>"0",
//													"db_size"=>"3",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
//													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
//													"list_width"=>"100",
//													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('opmerking',
										array("description"=>"Opmerking",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"50",
													"form_rows"=>"4",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>