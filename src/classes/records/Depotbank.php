<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/02/27 13:47:06 $
 		File Versie					: $Revision: 1.17 $

*/

class Depotbank extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Depotbank()
  {
    $this->defineData();
    $this->setDefaults();
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

	/*
	 * Veldvalidatie
	 */
	function validate()
	{
		($this->get("Depotbank")=="")?$this->setError("Depotbank",vt("Mag niet leeg zijn!")):true;
		//($this->get("Omschrijving")=="")?$this->setError("Omschrijving","Mag niet leeg zijn!"):true;

		$query  = "SELECT id FROM Depotbanken WHERE Depotbank = '".$this->get("Depotbank")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Depotbank", vtb("%s bestaat al", array($this->get("Depotbank"))));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "Depotbanken";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Depotbank',
													array("description"=>"Depotbank",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"key_field"=>true));

		$this->addField('Omschrijving',
													array("description"=>"Omschrijving",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('IbanVoorloop',
													array("description"=>"IBAN-voorloop",
													"default_value"=>"",
													"db_size"=>"32",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
 
 		$this->addField('BICcode',
													array("description"=>"BICcode",
													"default_value"=>"",
													"db_size"=>"32",
													"db_type"=>"varchar",
													"select_query"=>"",
													"form_type"=>"varchar",
													"form_size"=>"32",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('landCode',
                    array("description"=>"Land depotbank",
                          "default_value"=>"",
                          "db_size"=>"3",
                          "select_query"=>"SELECT landCode, concat(landCode,' - ',omschrijvingNL) as omschrijving FROM ISOLanden ORDER BY landCode ",
                          "form_type"=>"selectKeyed",
                          "form_size"=>"3",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "keyIn"        => "ISOLanden"));
    
  	$this->addField('orderLayout',
													array("description"=>"Layout (orders)",
													"default_value"=>"",
													"db_size"=>"32",
													"db_type"=>"tinyint",
													"select_query"=>"",
													"form_type"=>"varchar",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

  	$this->addField('orderRekeningTonen',
													array("description"=>"Rekening tonen (orders)",
													"default_value"=>"",
													"db_size"=>"32",
													"db_type"=>"tinyint",
													"select_query"=>"",
													"form_type"=>"checkbox",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('LEInrDepBank',
										array("description"=>"LEI nr.",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"select_query"=>"",
													"form_type"=>"varchar",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                                                                            
		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
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
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
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
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>