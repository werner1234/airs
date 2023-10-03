<?php
/* 	
 		Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2018/01/10 16:11:44 $
 		File Versie					: $Revision: 1.5 $
 				
*/

class Participanten extends Table
{
  /*
  * Object vars
  */
  var $data = array();
  
  /*
  * Constructor
  */
  function Participanten()
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
		return true;
	}
	
	function validate()
	{
		($this->get('crm_id') == '') ? $this->setError("crm_id",vt("Mag niet leeg zijn!")):true;
		($this->get('fonds_fonds') == '') ? $this->setError("fonds_fonds",vt("Mag niet leeg zijn!")):true;
    ($this->get('fonds_fonds') == '0') ? $this->setError("fonds_fonds",vt("Mag niet leeg zijn!")):true;
    ($this->get('registration_number') == '') ? $this->setError("registration_number",vt("Mag niet leeg zijn!")):true;
    
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "participanten";
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

		$this->addField('fonds_fonds',
													array("description"=>"Fonds",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Fondsen"));
    
		$this->addField('crm_id',
													array("description"=>"Crm id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));    
  
		$this->addField('registration_number',
													array("description"=>"Registratie nummer",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"12",
													"list_width"=>"150",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"categorie"=>"Participant"));
    
    
    $this->addField('memo', array(
                          'description' => 'Memo',
													"db_size"=>"11",
													"db_type"=>"text",
													"form_type"=>"textarea",
                          'form_rows' => 5,
                          'form_size' => 44,
													"form_visible"=>true,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Participant"));
    
    
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