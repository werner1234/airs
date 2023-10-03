<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 29 april 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/05/25 08:54:41 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: CRM_blanco_mutatieQueue.php,v $
    Revision 1.2  2020/05/25 08:54:41  cvs
    call 8380

    Revision 1.1  2020/01/30 07:25:01  cvs
    call 8380

*/

class CRM_blanco_mutatieQueue extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  var $veldArray;
  /*
  * Constructor
  */
  function CRM_blanco_mutatieQueue()
  {

    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
    $this->initModule();

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
		return true;
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
      $tst->tableExist($this->data['table'],true);
      $tst->changeField($this->data['table'],"CRM_id",array("Type"=>"int","Null"=>false));
      $tst->changeField($this->data['table'],"blancoId",array("Type"=>"varchar(100)","Null"=>false));
      $tst->changeField($this->data['table'],"verwerkt",array("Type"=>"tinyint","Null"=>false));
      $tst->changeField($this->data['table'],"afgewerkt",array("Type"=>"tinyint","Null"=>false));
      $tst->changeField($this->data['table'],"jsonData",array("Type"=>"text","Null"=>false));
      $tst->changeField($this->data['table'],'verwerktDoor',array("Type"=>"varchar(25)","Null"=>false));
      $tst->changeField($this->data['table'],'verwerktDatum',array("Type"=>"datetime","Null"=>false));
      $tst->changeField($this->data['table'],'md5',array("Type"=>"varchar(100)","Null"=>false));
  }
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "CRM_blanco_mutatieQueue";
    $this->data['table']  = "CRM_blanco_mutatieQueue";
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
													"form_visible"=>false,
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
													"form_visible"=>false,
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
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('CRM_id',
													array("description"=>"CRM_id",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"int",
                          "form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_search"=>false,
													"list_order"=>true));

		$this->addField('blancoId',
													array("description"=>"blancoId",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"45",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>true));

		$this->addField('verwerkt',
													array("description"=>"verwerkt",
													"default_value"=>"0",
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

    $this->addField('afgewerkt',
                    array("description"=>"afgewerkt",
                          "default_value"=>"0",
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

    $this->addField('jsonData',
                    array("description"=>"jsonData",
                          "default_value"=>"",
                          "db_size"=>"20",
                          "db_type"=>"text",
                          "form_type"=>"textarea",
                          "form_size"=>"20",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('verwerktDoor',
                    array("description"=>"verwerkt door",
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
    $this->addField('verwerktDatum',
                    array("description"=>"verwerkt d.d.",
                          "default_value"=>"",
                          "db_size"=>"20",
                          "db_type"=>"datetime",
                          "form_type"=>"text",
                          "form_size"=>"20",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));


  }
}
