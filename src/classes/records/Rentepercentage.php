<?php
/* 	
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/02/15 10:15:29 $
 		File Versie					: $Revision: 1.10 $
 				
*/

class Rentepercentage extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Rentepercentage()
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
	
	function validate()
	{
		($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;
		($this->get("Rentepercentage")=="")?$this->setError("Rentepercentage",vt("Mag niet leeg zijn!")):true;
		(!isNumeric($this->get("Rentepercentage")))?$this->setError("Rentepercentage",vt("Moet een getal zijn.")):true;
    
    if(db2jul($this->get("GeldigVanaf")) < db2jul($this->get("Datum")))
      $this->setError("GeldigVanaf",vt("Moet na de datum liggen."));
      
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "Rentepercentages";
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

		$this->addField('Fonds',
													array("description"=>"Fonds",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('Datum',
													array("description"=>"Datum",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"default_value"=>"now()",
													"form_type"=>"calendar",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "form_extra"=>"onchange=\"editForm.GeldigVanaf.value=this.value;\""));

		$this->addField('Rentepercentage',
													array("description"=>"Rentepercentage",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('GeldigVanaf',
													array("description"=>"Record geldig vanaf",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"default_value"=>"now()",
													"form_type"=>"calendar",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
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