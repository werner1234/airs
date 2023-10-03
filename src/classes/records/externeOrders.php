<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/05/09 11:41:07 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: optTransactieCodes.php,v $
    Revision 1.2  2018/05/09 11:41:07  cvs
    call 6572

    naar RVV 20201102

*/

class ExterneOrders extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function ExterneOrders()
  {
    $this->initModule();
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

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
    return true;
	}

  function initModule()
  {
    $tst = new SQLman();
    $tst->tableExist("externeOrders",true);
    $tst->changeField("externeOrders","externOrderId",array("Type"=>" varchar(40)","Null"=>false));
    $tst->changeField("externeOrders","ISIN",array("Type"=>" varchar(12)","Null"=>false));
    $tst->changeField("externeOrders","valuta",array("Type"=>" varchar(3)","Null"=>false));
    $tst->changeField("externeOrders","fonds",array("Type"=>" varchar(25)","Null"=>false));
    $tst->changeField("externeOrders","aantal",array("Type"=>" double","Null"=>false));
    $tst->changeField("externeOrders","datum",array("Type"=>" date","Null"=>false));
    $tst->changeField("externeOrders","settlementdatum",array("Type"=>" date","Null"=>false));
    $tst->changeField("externeOrders","beurs",array("Type"=>" varchar(30)","Null"=>false));
    $tst->changeField("externeOrders","uitvoeringskoers",array("Type"=>" double","Null"=>false));
    $tst->changeField("externeOrders","verwerkt",array("Type"=>" tinyint","Null"=>false));
    $tst->changeField("externeOrders","nettobedrag",array("Type"=>" double","Null"=>false));
    $tst->changeField("externeOrders","executor",array("Type"=>" varchar(15)","Null"=>false ) );
  }


  /*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "externeOrders";
    $this->data['table']  = "externeOrders";
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
													"form_visible"=>false,
													"list_visible"=>false,
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
													"list_visible"=>false,
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
													"list_visible"=>false,
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
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('ISIN',
													array("description"=>"ISIN",
													"default_value"=>"",
													"db_size"=>"12",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('valuta',
													array("description"=>"valuta",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"50",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('fonds',
													array("description"=>"fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
                          "form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('aantal',
                    array("description"=>"aantal",
                          "default_value"=>"",
                          "db_size"=>"12",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_format"=>"%01.6f",
                          "list_format"=>"%01.6f",
                          "form_size"=>"12",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"50",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('datum',
                    array("description"=>"datum",
                          "default_value"=>"",
                          "db_size"=>"12",
                          "db_type"=>"date",
                          "form_type"=>"calendar",
                          "form_size"=>"12",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"50",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('settlementdatum',
                    array("description"=>"settlementdatum",
                          "default_value"=>"",
                          "db_size"=>"12",
                          "db_type"=>"date",
                          "form_type"=>"calendar",
                          "form_size"=>"12",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"50",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('externOrderId',
                    array("description"=>"externOrderId",
                          "default_value"=>"",
                          "db_size"=>"40",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"40",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    $this->addField('beurs',
                    array("description"=>"beurs",
                          "default_value"=>"",
                          "db_size"=>"30",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"30",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('uitvoeringskoers',
                    array("description"=>"uitvoeringskoers",
                          "default_value"=>"",
                          "db_size"=>"12",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_format"=>"%01.6f",
                          "list_format"=>"%01.6f",
                          "form_size"=>"12",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('verwerkt',
                    array("description"=>"verwerkt",
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

    $this->addField('nettobedrag',
                    array("description"=>"nettobedrag",
                          "default_value"=>"",
                          "db_size"=>"12",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_format"=>"%01.6f",
                          "list_format"=>"%01.6f",
                          "form_size"=>"12",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('executor',
                    array("description"=>"executor",
                          "default_value"=>"",
                          "db_size"=>"15",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"15",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));


  }
}
