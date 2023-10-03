<?php
/*
    AE-ICT CODEX source module versie 1.6, 22 april 2008
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/05/19 15:51:40 $
    File Versie         : $Revision: 1.5 $

    $Log: ActieveFondsen.php,v $
    Revision 1.5  2018/05/19 15:51:40  rvv
    *** empty log message ***

    Revision 1.4  2018/01/03 14:17:55  rvv
    *** empty log message ***

    Revision 1.3  2017/09/18 17:21:54  rvv
    *** empty log message ***

    Revision 1.2  2017/09/02 17:17:46  rvv
    *** empty log message ***

    Revision 1.1  2015/03/07 17:15:16  rvv
    *** empty log message ***

    Revision 1.4  2015/01/03 16:07:20  rvv
    *** empty log message ***

    Revision 1.3  2011/08/31 15:18:50  rvv
    *** empty log message ***

    Revision 1.2  2011/06/13 14:34:04  rvv
    *** empty log message ***

    Revision 1.1  2008/04/23 09:04:32  rvv
    *** empty log message ***



*/

class ActieveFondsen extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function ActieveFondsen()
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
      return checkAccess($type);
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "ActieveFondsen";
    $this->data['identity'] = "id";

		$this->addField('InPositie',
													array("description"=>"In Positie",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"checkbox",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		
		$this->addField('Aantal',
										array("description"=>"Aantal",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('portefeuilleAantal',
										array("description"=>"Portefeuille aantal",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Actief',
										array("description"=>"Actieve Fondsen",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"checkbox",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('laatsteKoers',
										array("description"=>"Laatstekoers",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('laatsteKoersDatum',
										array("description"=>"Koersdatum",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"date",
													"form_type"=>"datum",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
  }
}
?>