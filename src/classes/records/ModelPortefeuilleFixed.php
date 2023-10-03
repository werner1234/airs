<?php
/*
    AE-ICT CODEX source module versie 1.6, 10 april 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2014/12/03 17:09:47 $
    File Versie         : $Revision: 1.5 $

    $Log: ModelPortefeuilleFixed.php,v $
    Revision 1.5  2014/12/03 17:09:47  rvv
    *** empty log message ***

    Revision 1.4  2013/12/08 13:00:47  rvv
    *** empty log message ***

    Revision 1.3  2013/10/05 15:54:50  rvv
    *** empty log message ***

    Revision 1.2  2013/08/28 15:56:34  rvv
    *** empty log message ***

    Revision 1.1  2011/04/27 17:51:37  rvv
    *** empty log message ***

    Revision 1.5  2010/09/18 15:09:15  rvv
    *** empty log message ***

    Revision 1.4  2010/09/04 08:02:05  rvv
    *** empty log message ***

    Revision 1.3  2010/05/23 13:56:01  rvv
    *** empty log message ***

    Revision 1.2  2010/04/25 10:51:11  rvv
    *** empty log message ***

    Revision 1.1  2010/04/11 11:57:45  rvv
    *** empty log message ***



*/

class ModelPortefeuilleFixed extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function ModelPortefeuilleFixed()
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
    if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
      return true;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "ModelPortefeuilleFixed";
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
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"24",
													"form_visible"=>true,
													"form_extra"=>'READONLY',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles"));

		$this->addField('Fonds',
													array("description"=>"Fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "(SELECT 'LIQ','Liquiditeiten') UNION (SELECT Fonds,Omschrijving FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Fonds)",
													'select_query_ajax' => "SELECT Fonds,Omschrijving FROM ((SELECT Fonds,Omschrijving FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Fonds) UNION (SELECT 'LIQ','Liquiditeiten')) as fixedFondsen WHERE fixedFondsen.Fonds='{value}'",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('Percentage',
													array("description"=>"Percentage",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Datum',
													array("description"=>"Datum",
													"default_value"=>"",
													"db_size"=>"16",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_extra"=>"READONLY",
													"form_size"=>"0",
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