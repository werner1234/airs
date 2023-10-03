<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/11/07 13:05:25 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: MONITOR_bedrijfDepot.php,v $
    Revision 1.1  2018/11/07 13:05:25  cvs
    call 7245

    Revision 1.2  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.1  2017/09/20 06:09:01  cvs
    megaupdate 2722

    Revision 1.1  2016/04/04 08:22:13  cvs
    call 4712

    Revision 1.1  2015/12/01 08:56:03  cvs
    update 2540, call 4352



 		
 	
*/

class MONITOR_bedrijfDepot extends Table
{
  /*
  * Object vars
  */
  var $tableName = "MONITOR_bedrijfDepot";
  var $data = array();
  
  /*
  * Constructor
  */
  function MONITOR_bedrijfDepot()
  {
    $this->defineData();
    $this->initModule();
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
		($this->get("bedrijf")=="")?$this->setError("bedrijf",vt("Mag niet leeg zijn!")):true;
		($this->get("depotbank")=="")?$this->setError("depotbank",vt("Mag niet leeg zijn!")):true;

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
    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"bedrijf",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField($this->tableName,"depotbank",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"bestanden",array("Type"=>" tinyint","Null"=>false));
  }

  /*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "bedrijf-depotbank";
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

		$this->addField('bedrijf',
													array("description"=>"bedrijf",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_size"=>"10",

                         "select_query" => "SELECT `Bedrijf`, `Bedrijf` FROM `Bedrijfsgegevens` ORDER BY `Bedrijf`",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"200",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('depotbank',
													array("description"=>"depotbank",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query" => "SELECT `Depotbank`,`Depotbank` FROM `Depotbanken` ORDER BY `Depotbank`",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"200",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('bestanden',
                    array("description"=>"bestanden",
                          "default_value"=>"",
                          "db_size"=>"10",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"10",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"80",
                          "list_align"=>"center",
                          "list_search"=>false,
                          "list_order"=>"true"));

  }

}
