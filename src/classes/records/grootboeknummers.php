<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 maart 2013
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/10/28 11:55:01 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: grootboeknummers.php,v $
    Revision 1.3  2019/10/28 11:55:01  cvs
    call 8211

    Revision 1.2  2016/11/30 16:49:28  rvv
    *** empty log message ***

    Revision 1.1  2013/03/27 18:49:35  rvv
    *** empty log message ***

 		
 	
*/

class Grootboeknummers extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Grootboeknummers()
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
		($this->get("vermogensbeheerder")=="")?$this->setError("vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		($this->get("grootboekrekening")=="")?$this->setError("grootboekrekening",vt("Mag niet leeg zijn!")):true;
		($this->get("rekeningnummer")=="")?$this->setError("rekeningnummer",vt("Mag niet leeg zijn!")):true;

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
    $this->data['table']  = "grootboeknummers";
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

		$this->addField('vermogensbeheerder',
													array("description"=>"vermogensbeheerder",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder",
													"form_type"=>"selectKeyed",
													'form_select_option_notempty'=>true,
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Vermogensbeheerders"));

		$this->addField('grootboekrekening',
													array("description"=>"grootboek",
													"default_value"=>"",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Grootboekrekening,Grootboekrekening FROM Grootboekrekeningen ORDER BY Grootboekrekening",
													"form_type"=>"selectKeyed",
													'form_select_option_notempty'=>true,
													"form_size"=>"5",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Grootboekrekeningen"));

		$this->addField('rekeningnummer',
													array("description"=>"rekeningnummer",
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

		$this->addField('omschrijving',
										array("description"=>"omschrijving",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
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