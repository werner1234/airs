<?php
/*
    AE-ICT sourcemodule created 28 sep. 2022
    Author              : Chris van Santen
    Filename            : updateQueue.php


*/

class UpdateQueue extends Table
{
  var $data = array();

  /*
  * Constructor
  */
  function UpdateQueue()
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
    if ($type == "delete") { return false; }
		return true;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "updates";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Bedrijf',
													array("description"=>"Bedrijf",
													"default_value"=>"",
													"db_size"=>"35",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"35",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('exportId',
													array("description"=>"ID",
													"default_value"=>"",
													"db_size"=>"35",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"35",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('type',
													array("description"=>"type",
													"default_value"=>"",
													"db_size"=>"35",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"35",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('filename',
													array("description"=>"filename",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"100",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('filesize',
													array("description"=>"bytes",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('server',
													array("description"=>"server",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"100",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('username',
													array("description"=>"username",
													"default_value"=>"",
													"db_size"=>"35",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"35",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('password',
													array("description"=>"password",
													"default_value"=>"",
													"db_size"=>"35",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"35",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('complete',
													array("description"=>"compleet",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"select",
                          "form_options" => array(0,1,2,3,99),
                          "form_select_option_notempty" => true,
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('terugmelding',
													array("description"=>"terugmelding",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

   $this->addField('tableDef',
													array("description"=>"tableDef",
													"default_value"=>"",
													"db_size"=>"16000000",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"verzonden",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datumtijd",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"opgehaald",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datumtijd",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
