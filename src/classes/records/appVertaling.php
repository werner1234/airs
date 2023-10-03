<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 21 juli 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/07/29 10:58:16 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: appVertaling.php,v $
branche vertaling_updateMaster
 		
 	
*/

class AppVertaling extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function AppVertaling()
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
		($this->get("veld")=="")?$this->setError("veld",vt("Mag niet leeg zijn!")):true;
		($this->get("nl")=="")?$this->setError("nl",vt("Mag niet leeg zijn!")):true;
		($this->get("en")=="")?$this->setError("en",vt("Mag niet leeg zijn!")):true;

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	  return true;
	 
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
    $this->data['name']   = "Applicatie vertaling";
    $this->data['table']  = "appVertaling";
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
													"list_width"=>"",
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
													"form_visible"=>false,
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
													"form_visible"=>false,
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
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('veld',
													array("description"=>(vt("veld")),
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"100",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"20%",
													"list_align"=>"left",
													"list_search"=>"true",
													"list_nobreak" => true,
													"list_order"=>"true"));

		$this->addField('nl',
													array("description"=>("nl"),
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"20%",
													"list_align"=>"left",
													"list_search"=>"true",
                          "list_nobreak" => true,
													"list_order"=>"true"));

		$this->addField('en',
													array("description"=>("en"),
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"20%",
													"list_align"=>"left",
													"list_search"=>"true",
                          "list_nobreak" => true,
													"list_order"=>"true"));

		$this->addField('fr',
													array("description"=>("fr"),
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"20%",
													"list_align"=>"left",
													"list_search"=>false,
                          "list_nobreak" => true,
													"list_order"=>"true"));

		$this->addField('du',
													array("description"=>("du"),
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
                          "list_nobreak" => true,
													"list_order"=>"true"));
		$this->addField('orgin',
													array("description"=>("orgin"),
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"100",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"20%",
													"list_align"=>"left",
													"list_search"=>false,
                          "list_nobreak" => true,
													"list_order"=>"true"));



  }
}
?>