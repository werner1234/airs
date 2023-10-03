<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 11 september 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/09/20 06:10:31 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: millogic_fondsparameters.php,v $
    Revision 1.1  2017/09/20 06:10:31  cvs
    megaupdate 2722

 		
 	
*/

class Millogic_fondsparameters extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Millogic_fondsparameters()
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
		($this->get("fonds")=="")?$this->setError("fonds",vt("Mag niet leeg zijn!")):true;

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
    $this->data['name']  = "Millogic fondsparameters";
    $this->data['table']  = "millogic_fondsparameters";
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

		$this->addField('fonds',
													array("description"=>"fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"300",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('isShare',
													array("description"=>"aandeel",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"60",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('nlFonds',
													array("description"=>"NL fonds",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>