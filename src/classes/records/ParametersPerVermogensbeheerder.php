<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 25 juni 2022
    Author              : $Author:  $
    Laatste aanpassing  : $Date:  $
    File Versie         : $Revision:  $
 		
    $Log:  $
 		
 	
*/

class ParametersPerVermogensbeheerder extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function ParametersPerVermogensbeheerder()
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
    ($this->get("Categoriesoort")=="")?$this->setError("Categoriesoort",vt("Mag niet leeg zijn!")):true;
    ($this->get("Categorie")=="")?$this->setError("Categorie",vt("Mag niet leeg zijn!")):true;
    ($this->get("Veld")=="")?$this->setError("Veld",vt("Mag niet leeg zijn!")):true;
    
    $query  = "SELECT id FROM ParametersPerVermogensbeheerder WHERE ".
      " Categoriesoort = '".$this->get("Categoriesoort")."' AND ".
      " Categorie = '".$this->get("Categorie")."' AND ".
      " Veld = '".$this->get("Veld")."' AND ".
      " Datum = '".$this->get("Datum")."' AND ".
      " Vermogensbeheerder = '".$this->get("Vermogensbeheerder")."'";
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $data = $DB->nextRecord();
    if($DB->records() >0 && $this->get("id") <> $data['id'])
    {
      $this->setError("Vermogensbeheerder",vt("deze combinatie bestaat al"));
      $this->setError("Categorie",vt("deze combinatie bestaat al"));
      $this->setError("Categoriesoort",vt("deze combinatie bestaat al"));
      $this->setError("Veld",vt("deze combinatie bestaat al"));
      $this->setError("Datum",vt("deze combinatie bestaat al"));
    }
    
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
    $this->data['table']  = "ParametersPerVermogensbeheerder";
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
                          "select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder, concat(Vermogensbeheerder,' - ',naam) FROM Vermogensbeheerders ORDER BY Vermogensbeheerder",
                          "form_type"=>"selectKeyed",
                          //'form_select_option_notempty'=>true,
                          "form_extra"=>"onchange='javascript:selectieChanged();'",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Vermogensbeheerders"));

		$this->addField('Categoriesoort',
													array("description"=>"Categoriesoort",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          'form_select_option_notempty'=>true,
                          "form_options"=>Array("Risicoklasse"=>"Risicoklasse"),
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Categorie',
													array("description"=>"Categorie",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Veld',
													array("description"=>"Veld",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          'form_select_option_notempty'=>true,
                          "form_options"=>Array("Drawdown"=>"Drawdown"),
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Datum',
													array("description"=>"Datum",
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

		$this->addField('Waarde',
													array("description"=>"Waarde",
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