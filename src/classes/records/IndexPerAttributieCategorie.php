<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 7 mei 2008
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/08/23 11:34:05 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: IndexPerAttributieCategorie.php,v $
    Revision 1.3  2015/08/23 11:34:05  rvv
    *** empty log message ***

    Revision 1.2  2014/11/30 13:04:47  rvv
    *** empty log message ***

    Revision 1.1  2008/05/16 07:55:00  rvv
    *** empty log message ***

 		
 	
*/

class IndexPerAttributieCategorie extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function IndexPerAttributieCategorie()
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
	  ($this->get("AttributieCategorie")=="")?$this->setError("AttributieCategorie",vt("Mag niet leeg zijn!")):true;
	  ($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;
	  
	  $DB = new DB();
	  $query = "SELECT id FROM IndexPerAttributieCategorie
	            WHERE 
	            Vermogensbeheerder   = '".$this->get("Vermogensbeheerder")."' AND
	            AttributieCategorie  = '".$this->get("AttributieCategorie")."' AND
	            Fonds                = '".$this->get("Fonds")."'";
	  $DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("Vermogensbeheerder",vtb("%s bestaat al", array($this->get("Vermogensbeheerder"))));
			$this->setError("AttributieCategorie", vtb("%s bestaat al", array($this->get("AttributieCategorie"))));
			$this->setError("Fonds", vtb("%s bestaat al", array($this->get("Fonds"))));
		}
		
		$DB->lookupRecordByQuery("SELECT Fonds FROM Fondsen WHERE Fonds = '".mysql_real_escape_string($this->get("Fonds"))."' ");
		if($DB->records() <= 0) {
			$this->setError("Fonds", vtb("%s is een onbekend fonds", array($this->get("Fonds"))));
		}

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
    $this->data['table']  = "IndexPerAttributieCategorie";
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
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder ",
													"form_type"=>"selectKeyed",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Vermogensbeheerders"));

		$this->addField('AttributieCategorie',
													array("description"=>"AttributieCategorie",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"select_query"=>"SELECT AttributieCategorie,AttributieCategorie FROM AttributieCategorien ORDER BY AttributieCategorie ",
													"form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"AttributieCategorien"));

		$this->addField('Fonds',
													array("description"=>"Fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													//													'select_query' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Omschrijving",
													'autocomplete' => array(
														'table'        => 'Fondsen',
														'label'        => array(
															'Fondsen.Fonds',
															'Fondsen.ISINCode',
															'combine' => '({Valuta})'
														),
														'extra_fields' => array('*'),
														'searchable'   => array('Fondsen.Fonds', 'Fondsen.ISINCode', 'Fondsen.Omschrijving', 'Fondsen.FondsImportCode'),
														'field_value' => array(
															'Fonds',
														),
														'value' => 'Fonds',
														'conditions'   => array(
															'AND' => array(
																'(Fondsen.EindDatum >= now() OR Fondsen.EindDatum = "0000-00-00")',
															)
														)
													),
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

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