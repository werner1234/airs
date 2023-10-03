<?php
/*
    AE-ICT CODEX source module versie 1.6, 11 augustus 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2013/03/09 16:18:19 $
    File Versie         : $Revision: 1.3 $

    $Log: historischeTenaamstelling.php,v $
    Revision 1.3  2013/03/09 16:18:19  rvv
    *** empty log message ***

    Revision 1.2  2011/12/24 16:31:00  rvv
    *** empty log message ***

    Revision 1.1  2011/08/11 15:40:36  rvv
    *** empty log message ***



*/

class HistorischeTenaamstelling extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function HistorischeTenaamstelling()
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
   // return checkAccess();
	}

	/*
  * Table definition
  */
  function defineData()
  {
    global $__appvar;
    $this->data['name']  = "";
    $this->data['table']  = "historischeTenaamstelling";
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

		$this->addField('clientId',
													array("description"=>"Client",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT id, concat(Client,' - ',id)  FROM Clienten ORDER BY Client ",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('crmId',
													array("description"=>"CRM naam",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT id, concat(Naam,' - ',id)  FROM CRM_naw ORDER BY Zoekveld,Naam ",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Naam',
													array("description"=>"Naam",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"100",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Naam1',
													array("description"=>"Naam1",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"100",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		if($__appvar["bedrijf"]=='RCN')
		{
		  $this->addField('adres',
													array("description"=>"Adres",
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
		  $this->addField('pc',
													array("description"=>"Postcode",
													"default_value"=>"",
													"db_size"=>"17",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"17",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		  $this->addField('woonplaats',
													array("description"=>"Woonplaats",
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
		}

		$this->addField('geldigTot',
													array("description"=>"Geldig tot",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
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
													"form_type"=>"calendar",
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

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
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