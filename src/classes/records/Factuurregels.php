<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 8 april 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/04/08 18:18:22 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: Factuurregels.php,v $
    Revision 1.1  2017/04/08 18:18:22  rvv
    *** empty log message ***

 		
 	
*/

class Factuurregels extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Factuurregels()
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
		return true;
		checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "Factuurregels";
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

		$this->addField('portefeuille',
													array("description"=>"portefeuille",
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
													"list_order"=>"true",
																'autocomplete' => array(
																	'query' => "
      SELECT Client, Portefeuilles.Portefeuille, Portefeuille AS subPortefeuille
      FROM Portefeuilles
      LEFT JOIN `fixDepotbankenPerVermogensbeheerder` ON `Portefeuilles`.`Vermogensbeheerder` = `fixDepotbankenPerVermogensbeheerder`.`vermogensbeheerder`
      AND `Portefeuilles`.`depotbank` = `fixDepotbankenPerVermogensbeheerder`.`depotbank`
      WHERE (Client LIKE '%{find}%' OR Portefeuilles.Portefeuille LIKE '%{find}%')
      AND (Portefeuilles.EindDatum >= now() OR Portefeuilles.EindDatum = '0000-00-00')
      AND (SELECT COUNT(*) FROM `Rekeningen` WHERE Portefeuille = Portefeuilles.Portefeuille AND inactief = 0) > 0
      ORDER BY Client  ",
																	'label' => array(
																		'Client',
																		'Portefeuille',
																	),
																	'searchable' => array(
																		'Client',
																		'Portefeuille',
																	),
																	'field_value' => array(
																																		'Portefeuille',
																	),
																	'value'             => 'Portefeuille',
																	'actions' => array(	'select_addon' => ' $("#portefeuille").val(ui.item.data.Portefeuille); console.log(ui.item.data.Portefeuille);')
																),
													"keyIn"=>"Portefeuilles"));

		$this->addField('datum',
													array("description"=>"datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_class"=> "AIRSdatepicker",
													"form_extra"=>" onchange=\"date_complete(this);\"",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('omschrijving',
													array("description"=>"omschrijving",
													"default_value"=>"",
													"db_size"=>"200",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"80",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bedrag',
													array("description"=>"bedrag",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('btw',
													array("description"=>"btw",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"3",
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



  }
}
?>