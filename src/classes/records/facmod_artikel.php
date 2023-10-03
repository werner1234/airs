<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/11/13 15:14:20 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: facmod_artikel.php,v $
    Revision 1.2  2019/11/13 15:14:20  cvs
    call 7675

    Revision 1.1  2019/07/22 09:11:00  cvs
    call 7675

    Revision 1.2  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.1  2017/09/20 06:09:01  cvs
    megaupdate 2722

    Revision 1.1  2016/04/04 08:22:13  cvs
    call 4712

    Revision 1.1  2015/12/01 08:56:03  cvs
    update 2540, call 4352



 		
 	
*/


class facmod_artikel extends Table
{
  /*
  * Object vars
  */
  var $tableName = "facmod_artikel";
  var $data = array();
  
  /*
  * Constructor
  */
  function facmod_artikel()
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
//		($this->get("BILcode")=="")?$this->setError("BILcode","Mag niet leeg zijn!"):true;
//		($this->get("omschrijving")=="")?$this->setError("omschrijving","Mag niet leeg zijn!"):true;

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
    $tst->changeField($this->tableName,"artnr",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"omschrijving",array("Type"=>" tinytext","Null"=>false));
    $tst->changeField($this->tableName,"eenheid",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField($this->tableName,"btw",array("Type"=>" varchar(2)","Null"=>false));
    $tst->changeField($this->tableName,"stuksprijs",array("Type"=>" double","Null"=>false));
    $tst->changeField($this->tableName,"rubriek",array("Type"=>" varchar(50)","Null"=>false));
  }

  /*
  * Table definition
  */
  function defineData()
  {
  	global $__facmod;
    $this->data['name']  = "BIL transactiecodes";
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

		$this->addField('artnr',
													array("description"=>"artikel code",
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

		$this->addField('omschrijving',
													array("description"=>"omschrijving",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"127",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"500",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('stuksprijs',
												array("description"=>"stuksprijs",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('eenheid',
													array("description"=>"eenheid",
													"default_value"=>"uur",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"select",
                          "form_options" => $__facmod["eenheden"],
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('rubriek',
													array("description"=>"rubriek",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"select",
                          "form_options" => $__facmod["rubriek"],
													"form_select_option_notempty"=>true,
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
$this->addField('btw',
													array("description"=>"btw",
													"default_value"=>"H",
													"db_size"=>"2",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "form_options" => $__facmod["btw"],
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));



  }



}



?>
