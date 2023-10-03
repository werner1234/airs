<?php
/*
    AE-ICT CODEX source module versie 1.6, 21 april 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/08/10 17:23:57 $
    File Versie         : $Revision: 1.2 $

    $Log: EigendomPerPortefeuille.php,v $
    Revision 1.2  2019/08/10 17:23:57  rvv
    *** empty log message ***

    Revision 1.1  2012/05/20 06:39:42  rvv
    *** empty log message ***

    Revision 1.1  2012/04/22 07:51:22  rvv
    *** empty log message ***



*/

class EigendomPerPortefeuille extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function EigendomPerPortefeuille()
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
    $this->data['table']  = "EigendomPerPortefeuille";
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

		$this->addField('Portefeuille',
													array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"12",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Portefeuille, concat(Portefeuille,' - ',Client) FROM Portefeuilles WHERE Einddatum > now() ORDER BY Portefeuille",
													"select_query_ajax"=>"SELECT Portefeuille, concat(Portefeuille,' - ',Client) FROM Portefeuilles WHERE Portefeuille='{value}'",
													"form_type"=>"selectKeyed",
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Portefeuilles"));

		$this->addField('Eigenaar',
													array("description"=>"Eigenaar",
													"default_value"=>"",
													"db_size"=>"16",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Eigenaar, concat(Eigenaar,' - ',Naam) FROM Eigenaars ORDER BY Eigenaar",
													"select_query_ajax"=>"SELECT Eigenaar, concat(Eigenaar,' - ',Naam) FROM Eigenaars WHERE Eigenaar='{value}'",
													"form_type"=>"selectKeyed",
													"form_size"=>"16",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Eigenaars"));

		$this->addField('percentage',
													array("description"=>"percentage",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
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