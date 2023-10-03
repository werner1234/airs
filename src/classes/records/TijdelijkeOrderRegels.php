<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 28 maart 2009
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2009/04/05 09:22:52 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: TijdelijkeOrderRegels.php,v $
    Revision 1.2  2009/04/05 09:22:52  rvv
    *** empty log message ***

    Revision 1.1  2009/03/29 14:38:46  rvv
    *** empty log message ***

 		
 	
*/

class TijdelijkeOrderRegels extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function TijdelijkeOrderRegels()
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
     return  checkAccess($type);
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "TijdelijkeOrderRegels";
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

		$this->addField('fonds',
													array("description"=>"fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('portefeuille',
													array("description"=>"portefeuille",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('modelPercentage',
													array("description"=>"modelPercentage",
													"default_value"=>"",
													"db_size"=>"8,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('portefeuillePercentage',
													array("description"=>"portefeuillePercentage",
													"default_value"=>"",
													"db_size"=>"8,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('afwijking',
													array("description"=>"afwijking",
													"default_value"=>"",
													"db_size"=>"8,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('valuta',
													array("description"=>"valuta",
													"default_value"=>"",
													"db_size"=>"6",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"6",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('kopen',
													array("description"=>"kopen",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,4",
													"form_visible"=>true,
													"list_visible"=>true,
						//							"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('verkopen',
													array("description"=>"verkopen",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,4",
													"form_visible"=>true,
													"list_visible"=>true,
							//						"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('overschrijding',
													array("description"=>"overschrijding",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('modelWaarde',
													array("description"=>"modelWaarde",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('koers',
													array("description"=>"koers",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
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