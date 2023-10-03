<?php
/*
    AE-ICT CODEX source module versie 1.6, 4 augustus 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/08/21 12:43:31 $
    File Versie         : $Revision: 1.4 $

    $Log: ZorgplichtPerBeleggingscategorie.php,v $
    Revision 1.4  2015/08/21 12:43:31  rvv
    *** empty log message ***

    Revision 1.3  2014/03/16 11:15:48  rvv
    *** empty log message ***

    Revision 1.2  2011/08/31 15:18:50  rvv
    *** empty log message ***

    Revision 1.1  2010/08/06 16:30:28  rvv
    *** empty log message ***



*/

class ZorgplichtPerBeleggingscategorie extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function ZorgplichtPerBeleggingscategorie()
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
	($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
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
    $this->data['table']  = "ZorgplichtPerBeleggingscategorie";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

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

		$this->addField('Vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder, concat(Vermogensbeheerder,' - ',naam) FROM Vermogensbeheerders ORDER BY Vermogensbeheerder",
													"form_type"=>"selectKeyed",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"form_extra"=>"onChange='javascript:vermogensbeheerderChanged();'",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));

		$this->addField('Zorgplicht',
													array("description"=>"Zorgplicht",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Zorgplicht,Zorgplicht FROM Zorgplichtcategorien",
													"form_type"=>"selectKeyed",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Zorgplichtcategorien"));

		$this->addField('Beleggingscategorie',
													array("description"=>"Hoofd / Beleggingscategorie",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Beleggingscategorie,Beleggingscategorie FROM Beleggingscategorien ORDER BY Beleggingscategorie",
													"form_type"=>"selectKeyed",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Beleggingscategorien"));

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



  }
}
?>