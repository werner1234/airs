<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 12 oktober 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/03/17 18:42:13 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: emailLog.php,v $
    Revision 1.4  2018/03/17 18:42:13  rvv
    *** empty log message ***

    Revision 1.3  2016/01/30 16:18:40  rvv
    *** empty log message ***

    Revision 1.2  2015/04/19 08:41:19  rvv
    *** empty log message ***

    Revision 1.1  2013/10/12 15:47:12  rvv
    *** empty log message ***

 		
 	
*/

class EmailLog extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function EmailLog()
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

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	   return false;
     //return checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "emailLog";
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

		$this->addField('zender',
													array("description"=>"zender",
													"default_value"=>"",
													"db_size"=>"150",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"150",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('ontvangers',
													array("description"=>"ontvangers",
													"default_value"=>"",
													"db_size"=>"150",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"150",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('cc',
										array("description"=>"cc",
													"default_value"=>"",
													"db_size"=>"150",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"150",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('bcc',
										array("description"=>"bcc",
													"default_value"=>"",
													"db_size"=>"150",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"150",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('onderwerp',
													array("description"=>"onderwerp",
													"default_value"=>"",
													"db_size"=>"150",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"150",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('body',
													array("description"=>"body",
													"default_value"=>"",
													"db_size"=>"150",
													"db_type"=>"text",
													"form_type"=>"htmlarea",
                          "form_rows"=>'8',
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('bijlagen',
													array("description"=>"bijlagen",
													"default_value"=>"",
													"db_size"=>"150",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"150",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true")); 
                                              
		$this->addField('verzonden',
													array("description"=>"verzonden",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('vanaf',
													array("description"=>"vanaf",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('foutmelding',
													array("description"=>"foutmelding",
													"default_value"=>"",
													"db_size"=>"200",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"100",
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
													"form_type"=>"datetime",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
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
													"form_type"=>"datetime",
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