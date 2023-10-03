<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 6 december 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/01/20 12:12:33 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: ValutaPerRegio.php,v $
    Revision 1.4  2019/01/20 12:12:33  rvv
    *** empty log message ***

    Revision 1.3  2015/11/29 13:05:52  rvv
    *** empty log message ***

    Revision 1.2  2007/08/02 14:14:04  rvv
    *** empty log message ***

    Revision 1.1  2006/12/14 11:48:08  rvv
    *** empty log message ***

 		
 	
*/

class ValutaPerRegio extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function ValutaPerRegio()
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
    $this->data['table']  = "ValutaPerRegio";
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
													
	  $this->addField('Vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Vermogensbeheerder, concat(Vermogensbeheerder,' - ',Naam) FROM Vermogensbeheerders ORDER BY Vermogensbeheerder",
													"form_type"=>"selectKeyed",
													"form_size"=>"10",
                          "form_extra"=>"onChange='javascript:vermogensbeheerderChanged();'",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));		
																							
		$this->addField('Regio',
													array("description"=>"Regio",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Regio,Omschrijving FROM Regios ",
													"form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Regios"));
													
		$this->addField('Valuta',
													array("description"=>"Valuta",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Valuta, concat(Valuta,' - ',Omschrijving) FROM Valutas ORDER BY valuta",
													"form_type"=>"selectKeyed",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Valutas"));

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
													"form_type"=>"calendar",
													"form_size"=>"0",
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
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>