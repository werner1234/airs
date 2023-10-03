<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/10/24 15:41:59 $
 		File Versie					: $Revision: 1.10 $

*/

class Vertaling extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Vertaling()
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
	  return GetCRMAccess(2);
		//return checkAccess($type);
	}

	function validate()
	{
		($this->get("Taal")=="")?$this->setError("Taal",vt("Mag niet leeg zijn!")):true;
		($this->get("Term")=="")?$this->setError("Term",vt("Mag niet leeg zijn!")):true;

		$query  = "SELECT id FROM Vertalingen WHERE Taal = '".$this->get("Taal")."' AND Term = '".$this->get("Term")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();

		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Taal",vt("combinatie bestaat al"));
			$this->setError("Term",vt("combinatie bestaat al"));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "Vertalingen";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Taal',
													array("description"=>"Taal",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('Term',
													array("description"=>"Term",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_size"=>"100",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('Vertaling',
													array("description"=>"Vertaling",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_size"=>"100",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>