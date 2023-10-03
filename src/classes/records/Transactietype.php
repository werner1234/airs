<?php
/* 	
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/04/19 16:18:34 $
 		File Versie					: $Revision: 1.8 $
 				
*/

class Transactietype extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Transactietype()
  {
    $this->defineData();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}
	
	function checkAccess($type)
	{
		return checkAccess($type);
	}
	
	/*
	 * Veldvalidatie
	 */
	function validate()
	{
		($this->get("Transactietype")=="")?$this->setError("Transactietype",vt("Mag niet leeg zijn!")):true;
		
		$query  = "SELECT id FROM Transactietypes WHERE Transactietype = '".$this->get("Transactietype")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();
		
		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Transactietype",vtb("%s bestaat al", array($this->get("Transactietype"))));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
  
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "Transactietypes";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Transactietype',
													array("description"=>"Transactietype",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_size"=>"5",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"key_field"=>true));

		$this->addField('Omschrijving',
													array("description"=>"Omschrijving",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('transactievorm',
													array("description"=>"transactievorm",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "form_options"=>array('o'=>'openen','s'=>'sluiten'),
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('Liquiditeit',
													array("description"=>"Liquiditeit",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>