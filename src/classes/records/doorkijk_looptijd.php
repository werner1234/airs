<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 september 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/03/07 16:48:06 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: doorkijk_looptijd.php,v $
    Revision 1.3  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.2  2017/12/04 15:09:19  cvs
    vt( verwijderen

    Revision 1.1  2017/12/04 10:39:16  cvs
    Update van Ben ingelezen dd 4-12-2017

 		
 	
*/

class doorkijk_looptijd extends Table
{
  /*
  * Object vars
  */
  var $tableName = "doorkijk_looptijd";
  var $data = array();
  
  /*
  * Constructor
  */
  function doorkijk_looptijd()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
	  //$this->initModule();
  }

	function addField($name, $properties)
	{
		$properties["description"] = $properties["description"];
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
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = $this->tableName;
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
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

		$this->addField('code',
													array("description"=>"code",
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

		$this->addField('van',
													array("description"=>"van",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('tot',
													array("description"=>"tot",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


  }
	function initModule()
	{
		$tst = new SQLman();
		$tst->tableExist($this->tableName,true);
		$tst->changeField($this->tableName,"code",array("Type"=>"varchar(10)","Null"=>true));
		$tst->changeField($this->tableName,"van",array("Type"=>"int","Null"=>true));
		$tst->changeField($this->tableName,"tot",array("Type"=>"int","Null"=>true));

	}
}
?>