<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 29 november 2007
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/12/19 08:18:29 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: Beurzen.php,v $
    Revision 1.2  2015/12/19 08:18:29  rvv
    *** empty log message ***

    Revision 1.1  2007/12/14 07:56:59  rvv
    *** empty log message ***

 		
 	
*/

class Beurzen extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Beurzen()
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
		($this->get("beurs")=="")?$this->setError("beurs",vt("Mag niet leeg zijn!")):true;
		//($this->get("Omschrijving")=="")?$this->setError("Omschrijving","Mag niet leeg zijn!"):true;
	
		$query  = "SELECT id FROM Beurzen WHERE beurs = '".$this->get("beurs")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		
		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("beurs",vtb("%s bestaat al", array($this->get("beurs"))));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
		return checkAccess($type);
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "Beurzen";
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

		$this->addField('beurs',
													array("description"=>"beurs",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"key_field"=>true));

		$this->addField('omschrijving',
													array("description"=>"omschrijving",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('beursregio',
													array("description"=>"Beursregio",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_type"=>"selectKeyed",
                          "form_options"=>array('NL'=>'Nederland'),
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



  }
}
?>