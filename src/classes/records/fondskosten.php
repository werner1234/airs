<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 19 november 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/01/24 17:05:06 $
    File Versie         : $Revision: 1.5 $
 		
    $Log: fondskosten.php,v $
    Revision 1.5  2018/01/24 17:05:06  rvv
    *** empty log message ***

    Revision 1.4  2017/12/08 14:12:01  rm
    6413 Fondskosten toevoegen autocomplete aan fonds veld

    Revision 1.3  2016/10/24 06:46:05  rvv
    *** empty log message ***

    Revision 1.2  2014/11/23 14:03:38  rvv
    *** empty log message ***

    Revision 1.1  2014/11/19 16:43:28  rvv
    *** empty log message ***

 		
 	
*/

class Fondskosten extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Fondskosten()
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
		$DB = new DB();

		$DB->SQL("SELECT Fonds FROM Fondsen WHERE Fonds = '".$this->get("fonds")."' ");
		$DB->Query();
		if($DB->records() <= 0) {
			$this->setError("fonds", vtb("%s is een onbekend fonds", array($this->get("fonds"))));
		}
		$query  = "SELECT id FROM fondskosten WHERE fonds = '".$this->get("fonds")."' AND datum = '".$this->get("datum")."' ";

		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("fonds", vtb("Fonds '%s' met datum '%s' bestaat al.", array($this->get("fonds"), date('d-m-Y',db2jul($this->get("datum"))))));
		}
    
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
     return checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "fondskosten";
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

		$this->addField('fonds',
													array("description"=>"fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"autocomplete",
													'select_query'=>"SELECT Fonds,Fonds FROM Fondsen ORDER BY Fonds",
													'select_query_ajax'=>"SELECT Fonds,Fonds FROM Fondsen WHERE Fonds='{value}'",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",

													'autocomplete' => array(
														'table' => 'Fondsen',
														'label'        => array(
															'Fondsen.Fonds',
															'Fondsen.ISINCode',
															'combine' => '({Valuta})'
														),
														'searchable'   => array('Fondsen.Fonds', 'Fondsen.ISINCode', 'Fondsen.Omschrijving'),
														'field_value'  => array('Fondsen.Fonds'),
														'extra_fields' => array('*'),
														'value'        => 'Fonds',
													),

                          "keyIn"=>"Fondsen"));

		$this->addField('datum',
													array("description"=>"datum",
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

		$this->addField('percentage',
													array("description"=>"TotCostFund %",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.3f",
                          "form_format"=>"%01.3f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('transCostFund',
										array("description"=>"transCostFund %",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.3f",
													"form_format"=>"%01.3f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('perfFeeFund',
										array("description"=>"perfFeeFund %",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.3f",
													"form_format"=>"%01.3f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('datumProvider',
										array("description"=>"datum provider",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_size"=>"25",
													"form_type"=>"calendar",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('handmatig',
										array("description"=>"handmatig",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_options"=>array('0'=>'Nee','1'=>'Ja'),
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('opmerking',
										array("description"=>"opmerking",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"textarea",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
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



  }
}
?>