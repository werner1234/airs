<?php
/*
    AE-ICT CODEX source module versie 1.6, 11 november 2009
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/01/22 15:58:51 $
    File Versie         : $Revision: 1.8 $

    $Log: CRM_evenementen.php,v $
    Revision 1.8  2020/01/22 15:58:51  rvv
    *** empty log message ***

    Revision 1.7  2017/12/14 18:26:41  rvv
    *** empty log message ***

    Revision 1.5  2016/11/19 18:58:47  rvv
    *** empty log message ***

    Revision 1.4  2011/11/12 18:29:17  rvv
    *** empty log message ***

    Revision 1.3  2011/09/28 18:40:59  rvv
    *** empty log message ***

    Revision 1.2  2009/11/15 16:46:35  rvv
    *** empty log message ***

    Revision 1.1  2009/11/11 17:36:18  rvv
    *** empty log message ***



*/

class CRM_evenementen extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function CRM_evenementen()
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

	 $level = GetCRMAccess();
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
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "CRM_evenementen";
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

		$this->addField('rel_id',
													array("description"=>"rel_id",
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

		$this->addField('evenement',
													array("description"=>"evenement",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"select_query"=>"SELECT omschrijving,omschrijving  FROM CRM_selectievelden WHERE module = 'evenementen' ORDER BY omschrijving",
													"form_type"=>"selectKeyed",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('Aanwezig',
                    array("description"=>"Aanwezig",
                          "default_value"=>"",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "list_width"=>"150",
                          "form_type"=>"checkbox",
                          "form_size"=>"4",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    $this->addField('Afwezig',
                    array("description"=>"Afwezig",
                          "default_value"=>"",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "list_width"=>"150",
                          "form_type"=>"checkbox",
                          "form_size"=>"4",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    $this->addField('Opmerking',
                    array("description"=>"Opmerking",
                          "default_value"=>"",
                          "db_size"=>"60",
                          "db_type"=>"text",
                          "list_width"=>"150",
                          "form_type"=>"textarea",
                          "form_size"=>"50",
                          "form_rows"=>"3",
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
													"form_type"=>"datetime",
													"form_size"=>"0",
													"form_visible"=>false,
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
													"form_type"=>"datetime",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"150",
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



  }
}
?>