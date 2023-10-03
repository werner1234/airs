<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 23 februari 2019
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/02/23 18:28:21 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: ISOLanden.php,v $
    Revision 1.1  2019/02/23 18:28:21  rvv
    *** empty log message ***

 		
 	
*/

class ISOLanden extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function ISOLanden()
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
    ($this->get("landCode")=="")?$this->setError("landCode",vt("Mag niet leeg zijn!")):true;
    
    $DB = new DB();
    $DB->SQL("SELECT id FROM ISOLanden WHERE landCode = '".$this->get("landCode")."'");
    $DB->Query();
    $data = $DB->nextRecord();
    
    if($DB->records() >0 && $this->get("id") <> $data[id])
    {
      $this->setError("landCode",vt("%s bestaat al", array($this->get("landCode"))));
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
    $this->data['table']  = "ISOLanden";
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

		$this->addField('landCode',
													array("description"=>"Code 3-tekens",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "key_field"=>true));

		$this->addField('landCodeKort',
													array("description"=>"Code 2-tekens",
													"default_value"=>"",
													"db_size"=>"2",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('landISOnr',
													array("description"=>"ISO nr",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('omschrijvingNL',
													array("description"=>"NL",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('omschrijvingEN',
													array("description"=>"EN",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
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
    $this->addField('add_date',
                    array("description"=>"change_date",
                          "db_size"=>"0",
                          "db_type"=>"datetime",
                          "form_type"=>"datum",
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