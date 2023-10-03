<?php
/*
    AE-ICT CODEX source module versie 1.6, 24 april 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/01/15 16:26:07 $
    File Versie         : $Revision: 1.6 $

    $Log: FondsenBuitenBeheerfee.php,v $
    Revision 1.6  2020/01/15 16:26:07  rvv
    *** empty log message ***

    Revision 1.5  2019/05/11 06:23:11  rvv
    *** empty log message ***

    Revision 1.4  2019/04/10 16:01:59  rvv
    *** empty log message ***

    Revision 1.3  2018/10/07 08:31:05  rvv
    *** empty log message ***

    Revision 1.2  2014/11/30 13:04:47  rvv
    *** empty log message ***

    Revision 1.1  2010/04/24 19:12:18  rvv
    *** empty log message ***



*/

class FondsenBuitenBeheerfee extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function FondsenBuitenBeheerfee()
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
    return checkAccess();
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "FondsenBuitenBeheerfee";
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
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder FROM Vermogensbeheerders ",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Vermogensbeheerders"));

		$this->addField('Fonds',
													array("description"=>"Fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE (EindDatum > NOW() OR EindDatum = '0000-00-00') ORDER BY Fonds",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Fondsen"));
  
    $this->addField('uitsluitenFee',
                    array("description"  => "UitsluitenFee",
                          "db_size"      => "4",
                          "db_type"      => "tinyint",
                          "form_size"    => "4",
                          "form_type"    => "checkbox",
                          "form_visible" => true, "list_width" => "150",
                          "list_visible" => true,
                          "list_align"   => "left",
                          "list_search"  => false,
                          "list_order"   => "true"));
    $this->addField('huisfonds',
                    array("description"  => "Huisfonds",
                          "db_size"      => "4",
                          "db_type"      => "tinyint",
                          "form_size"    => "4",
                          "form_type"    => "checkbox",
                          "form_visible" => true, "list_width" => "150",
                          "list_visible" => true,
                          "list_align"   => "left",
                          "list_search"  => false,
                          "list_order"   => "true"));
    $this->addField('layoutNr',
                    array("description"  => "LayoutNr",
                          "db_size"      => "4",
                          "db_type"      => "tinyint",
                          "form_size"    => "4",
                          "form_type"    => "text",
                          "form_visible" => true, "list_width" => "150",
                          "list_visible" => true,
                          "list_align"   => "left",
                          "list_search"  => false,
                          "list_order"   => "true"));

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