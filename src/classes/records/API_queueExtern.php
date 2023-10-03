<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 18 augustus 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/03/11 13:31:05 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: API_queueExtern.php,v $
    Revision 1.1  2019/03/11 13:31:05  cvs
    call 7364

    Revision 1.1  2018/09/07 10:16:37  cvs
    commit voor robert call 6989

    Revision 1.1  2017/08/18 14:42:18  cvs
    call 5815

 		
 	
*/

class API_queueExtern extends Table
{
  /*
  * Object vars
  */
  var $tableName = "apiQueueExtern";
  var $data  = array();
  
  /*
  * Constructor
  */
  function API_queueExtern()
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
	 
	   /*
	 $level = getMyLevel("Default");
	  switch ($type) 
	  {
	  	case "edit":
	  		return ($level >=3 )?true:false;
	  		break;
	  	case "delete":
	  		return ($level >=7 )?true:false;
	  		break;
	  	default:
	  	  return false;
	  		break;
	  }
	  */
	}

  function initModule()
  {
    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"submitterIp",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"eventCode",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"action",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"dataFields",array("Type"=>" text","Null"=>false));
  }
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "API extern wachtrij";
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

		$this->addField('action',
													array("description"=>"action",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('submitterIp',
													array("description"=>"submitterIp",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('eventCode',
													array("description"=>"eventCode",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"250",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('dataFields',
													array("description"=>"dataFields",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
													"form_rows"=> "20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"400",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('crmId',
													array("description"=>"crmId",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"400",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));


  }
}
