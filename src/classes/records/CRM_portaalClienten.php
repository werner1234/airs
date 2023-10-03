<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 31 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/05/07 05:39:18 $
    File Versie         : $Revision: 1.2 $

    $Log: CRM_portaalClienten.php,v $
    Revision 1.2  2020/05/07 05:39:18  rvv
    *** empty log message ***

    Revision 1.1  2020/05/06 14:55:04  rvv
    *** empty log message ***

    Revision 1.2  2018/07/13 14:30:08  cvs
    *** empty log message ***

    Revision 1.1  2018/07/11 09:36:37  cvs
    eerste commit V3

    Revision 1.1  2017/08/18 15:00:28  cvs
    *** empty log message ***

    Revision 1.1  2012/06/13 09:20:28  cvs
    *** empty log message ***



*/

class CRM_portaalClienten extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function CRM_portaalClienten()
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
		($this->get("portefeuille")=="")?$this->setError("portefeuille",vt("Mag niet leeg zijn!")):true;
		($this->get("name")=="")?$this->setError("name",vt("Mag niet leeg zijn!")):true;
		($this->get("email")=="")?$this->setError("email",vt("Mag niet leeg zijn!")):true;
		($this->get("password")=="")?$this->setError("password",vt("Mag niet leeg zijn!")):true;

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

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "CRM_portaalClienten";
    $this->data['table']  = "CRM_portaalClienten";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"db_extra"=>"auto increment",
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
													"db_extra"=>"",
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
													"db_extra"=>"",
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
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"toegevoegd",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"db_extra"=>"",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('portefeuille',
													array("description"=>"portefeuille",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"65",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('name',
													array("description"=>"naam",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"275",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('email',
													array("description"=>"E-mail",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"200",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('password',
													array("description"=>"Wachtwoord",
													"default_value"=>"",
													"db_size"=>"200",
													"db_type"=>"varchar",
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('clientWW',
													array("description"=>"clientWW",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
                          

		$this->addField('passwordChange',
													array("description"=>"WW wijzig datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"db_extra"=>"",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('passwordTimes',
													array("description"=>"WW wijzig aantal",												"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('loginTimes',
													array("description"=>"logins",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('loginLast',
													array("description"=>"login datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"db_extra"=>"",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



    $this->addField('verzendAanhef',
													array("description"=>"aanhef",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
    $this->addField('accountmanagerNaam',
													array("description"=>"accountmanager",
													"default_value"=>"",
													"db_size"=>"75",
													"db_type"=>"varchar",
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"75",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"200",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
    
    $this->addField('accountmanagerEmail',
													array("description"=>"E-mail acc.man",
													"default_value"=>"",
													"db_size"=>"75",
													"db_type"=>"varchar",
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"75",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"200",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
    
    $this->addField('accountmanagerGebruikerNaam',
													array("description"=>"gebrNaam acc.man",
													"default_value"=>"",
													"db_size"=>"75",
													"db_type"=>"varchar",
													"db_extra"=>"",
													"form_type"=>"text",
													"form_size"=>"75",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
    $this->addField('geblokkeerd',
													array("description"=>"geblokkeerd",
													"default_value"=>"0",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"db_extra"=>"",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>false));

    $this->addField('depotbank',
                    array("description"=>"depotbank",
                          "default_value"=>"0",
                          "db_size"=>"10",
                          "db_type"=>"varchar",
                          "db_extra"=>"",
                          "form_type"=>"text",
                          "form_size"=>"10",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"60",
                          "list_align"=>"center",
                          "list_search"=>false,
                          "list_order"=>false));

    $this->addField('overRide2factor',
                    array("description"=>"2 factor uitgeschakeld",
                          "default_value"=>"0",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "db_extra"=>"",
                          "form_type"=>"checkbox",
                          "form_size"=>"4",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"60",
                          "list_align"=>"center",
                          "list_search"=>false,
                          "list_order"=>false));


  }
}
?>