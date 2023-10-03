<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/11/19 14:30:33 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: modulezTijdelijkeBatch.php,v $
    Revision 1.2  2018/11/19 14:30:33  cvs
    update naar VRY omgeving

    Revision 1.1  2018/11/07 12:20:23  cvs
    call 7300





 		
 	
*/

class modulezTijdelijkeBatch extends Table
{
  /*
  * Object vars
  */
  var $tableName = "modulezTijdelijkeBatch";
  var $data = array();
  
  /*
  * Constructor
  */
  function modulezTijdelijkeBatch()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
    $this->initModule();
  }


  function getSaldo($portefeuille, $batch, $allData=false)
  {
    $db = new DB();
    $query = "SELECT * FROM ".$this->tableName." WHERE `batch` = '{$batch}'  AND `portefeuille` = '{$portefeuille}'";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      if ($all)
      {
        return $rec;
      }
      else
      {
        return $rec["bedragVrijmaken"];
      }
    }
    else
    {
      return false;
    }
  }

  function addRecord($batch,$record)
  {
//    debug($record);
    global $USR;
    $db = new DB();
    $query = "INSERT INTO ".$this->tableName." SET 
     `add_date` = NOW(),
     `add_user` = '$USR',
     `change_date` = NOW(),
     `change_user` = '$USR',
     `batch` = '{$batch}',
     `portefeuille` = '{$record["portefeuille"]}',
     `bedragVrijmaken` = '{$record["berekendSaldo"]}',
     `record` = '".json_encode($record)."'
     ";
//    debug($query);
    $db->executeQuery($query);

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
    $this->data['name']  = "ModuleZ transactiecodes";
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

		$this->addField('batch',
													array("description"=>"batch",
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

		$this->addField('portefeuille',
													array("description"=>"portefeuille",
													"default_value"=>"",
													"db_size"=>"35",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"35",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bedragVrijmaken',
													array("description"=>"bedragVrijmaken",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('record',
													array("description"=>"record",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"80",
													"form_rows"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));



  }

  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);

    $tst->changeField($this->tableName,"batch",array("Type"=>"varchar(25)","Null"=>false));
    $tst->changeField($this->tableName,"portefeuille",array("Type"=>"varchar(35)","Null"=>false));
    $tst->changeField($this->tableName,"bedragVrijmaken",array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,"record",array("Type"=>"text","Null"=>false));

  }
}

