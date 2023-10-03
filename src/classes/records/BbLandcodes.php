<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 13 december 2007
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2014/11/01 22:08:02 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: BbLandcodes.php,v $
    Revision 1.2  2014/11/01 22:08:02  rvv
    *** empty log message ***

    Revision 1.1  2007/12/14 07:56:59  rvv
    *** empty log message ***

 		
 	
*/

class BbLandcodes extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function BbLandcodes()
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
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
		return checkAccess($type);
	}

	/*
	 * Veldvalidatie
	 */
	function validate()
	{
		($this->get("bbLandcode")=="")?$this->setError("bbLandcode",vt("Mag niet leeg zijn!")):true;
		//($this->get("Omschrijving")=="")?$this->setError("Omschrijving","Mag niet leeg zijn!"):true;
	
		$query  = "SELECT id FROM BbLandcodes WHERE bbLandcode = '".$this->get("bbLandcode")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		
		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("bbLandcode", vtb("%s bestaat al", array($this->get("bbLandcode"))));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	


	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "BbLandcodes";
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

		$this->addField('bbLandcode',
													array("description"=>"BB Landcode",
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
													"list_order"=>"true",
													"key_field"=>true));

		$this->addField('omschrijving',
													array("description"=>"omschrijving",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 		$this->addField('settlementDays',
													array("description"=>"settlement datum T+",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"text",
													"form_size"=>"10",
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