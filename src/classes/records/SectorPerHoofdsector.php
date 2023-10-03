<?php
/* 	
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/08/02 14:14:04 $
 		File Versie					: $Revision: 1.5 $
 				
*/

class SectorPerHoofdsector extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function SectorPerHoofdsector()
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
		($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		($this->get("Hoofdsector")=="")?$this->setError("Hoofdsector",vt("Mag niet leeg zijn!")):true;
		($this->get("Beleggingssector")=="")?$this->setError("Beleggingssector",vt("Mag niet leeg zijn!")):true;

		$query  = "SELECT id FROM SectorenPerHoofdsector WHERE Vermogensbeheerder = '".$this->get("Vermogensbeheerder")."' AND Hoofdsector = '".$this->get("Hoofdsector")."' AND Beleggingssector = '".$this->get("Beleggingssector")."'";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();
		
		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Vermogensbeheerder",vt("combinatie bestaat al"));
			$this->setError("Hoofdsector",vt("combinatie bestaat al"));
			$this->setError("Beleggingssector",vt("combinatie bestaat al"));
		}
		
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "SectorenPerHoofdsector";
    $this->data['identity'] = "id";

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

		$this->addField('Vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));

		$this->addField('Hoofdsector',
													array("description"=>"Hoofdsector",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('Beleggingssector',
													array("description"=>"Beleggingssector",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Beleggingssectoren"));

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