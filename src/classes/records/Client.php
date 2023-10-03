<?php
/* 	
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/08/12 17:38:43 $
 		File Versie					: $Revision: 1.18 $
 				
*/

class Client extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Client()
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
	  if($this->get("Client")<>preg_replace("/[`']/", "", $this->get("Client")))
    {
      $this->setError("Client", vtb(" %s bevat ongewenste tekens.", array($this->get("Client"))));
    }
		($this->get("Client")=="")?$this->setError("Client",vt("Mag niet leeg zijn!")):true;
		($this->get("Wachtwoord") !="" && strlen($this->get("Wachtwoord"))<6)?$this->setError("Wachtwoord",vt("Moet minimaal 6 tekens zijn.")):true;
		

		$query  = "SELECT id FROM Clienten WHERE Client = '".mysql_real_escape_string($this->get("Client"))."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("Client",vtb("%s bestaat al", array($this->get("Client"))));
		}
		
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "Clienten";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

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

		$this->addField('Client',
													array("description"=>"Client",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"key_field"=>true));

		$this->addField('Naam',
													array("description"=>"Naam 1",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"list_width"=>"150",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('Naam1',
													array("description"=>"Naam 2",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"list_width"=>"150",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Adres',
													array("description"=>"Adres",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"list_width"=>"150",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('pc',
													array("description"=>"Postcode",
													"default_value"=>"",
													"db_size"=>"17",
													"list_width"=>"150",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"17",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
													
		$this->addField('Woonplaats',
													array("description"=>"Woonplaats",
													"db_size"=>"50",
													"list_width"=>"150",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
													
		$this->addField('Land',
													array("description"=>"Land",
													"db_size"=>"50",
													"list_width"=>"150",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('Telefoon',
													array("description"=>"Telefoon",
													"db_size"=>"15",
													"list_width"=>"150",
													"db_type"=>"varchar",
													"form_size"=>"15",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Fax',
													array("description"=>"Fax",
													"db_size"=>"15",
													"list_width"=>"150",
													"db_type"=>"varchar",
													"form_size"=>"15",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
/*
		$this->addField('Wachtwoord',
													array("description"=>"Wachtwoord",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_size"=>"15",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
*/																										
		$this->addField('Email',
													array("description"=>"Email",
													"db_size"=>"50",
													"list_width"=>"150",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"text",
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

    $this->addField('extraInfo',
                    array("description"=>"extraInfo",
                          "db_size"=>"255",
                          "list_width"=>"150",
                          "db_type"=>"varchar",
                          "form_size"=>"80",
                          "form_type"=>"text",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));



  }
}
?>