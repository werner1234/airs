<?php
/*
    AE-ICT CODEX source module versie 1.6, 6 augustus 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/08/10 17:23:57 $
    File Versie         : $Revision: 1.8 $

    $Log: CRM_naw_templates.php,v $
    Revision 1.8  2019/08/10 17:23:57  rvv
    *** empty log message ***

    Revision 1.7  2018/09/23 12:56:04  rvv
    *** empty log message ***

    Revision 1.6  2014/08/30 16:23:34  rvv
    *** empty log message ***

    Revision 1.5  2014/07/02 16:02:10  rvv
    *** empty log message ***

    Revision 1.4  2014/06/29 15:36:41  rvv
    *** empty log message ***

    Revision 1.3  2011/09/26 11:43:25  rvv
    *** empty log message ***

    Revision 1.2  2011/09/14 18:43:16  rvv
    *** empty log message ***

    Revision 1.1  2011/08/07 09:11:54  rvv
    *** empty log message ***



*/

class CRM_naw_templates extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function CRM_naw_templates()
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
	  return GetCRMAccess(2);
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "CRM_naw_templates";
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

		$this->addField('tabs',
													array("description"=>"tabs",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"mediumtext",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('veldenPerTab',
													array("description"=>"veldenPerTab",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"mediumtext",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('memo',
													array("description"=>"Memo",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"120",
													"form_rows"=>"2",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
 
 		$this->addField('intake',
													array("description"=>"intake template",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
                          "form_extra"=>"onchange=\"javascript:editForm.action.value='new';editForm.submit();\"",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('intakeOmschrijving',
                    array("description"=>"Intake omschrijving",
                          "default_value"=>"",
                          "db_size"=>"24",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"24",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

		$this->addField('pdfMaken',
										array("description"=>"pdf opslaan bij documenten",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
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
													"form_type"=>"text",
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

		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>