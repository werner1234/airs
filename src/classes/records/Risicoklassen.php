<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/07/31 14:32:46 $
 		File Versie					: $Revision: 1.16 $

*/

class Risicoklassen extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Risicoklassen()
  {
    $this->defineData();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	function checkAccess($type)
	{
		global $__appvar;
		if($__appvar["crmOnly"])
		{
			return GetCRMAccess(2);
		}
		return checkAccess($type);
	}

	function validate()
	{
		($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		($this->get("Risicoklasse")=="")?$this->setError("Risicoklasse",vt("Mag niet leeg zijn!")):true;

		$query  = "SELECT id FROM Risicoklassen WHERE Risicoklasse = '".$this->get("Risicoklasse")."' AND Vermogensbeheerder = '".$this->get("Vermogensbeheerder")."'";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Risicoklasse",vt("combinatie bestaat al"));
			$this->setError("Vermogensbeheerder",vt("combinatie bestaat al"));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "Risicoklassen";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',	array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));

		$this->addField('Risicoklasse',
													array("description"=>"Risicoklasse",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"key_field"=>true,
                          "extra_keys"=>array('Vermogensbeheerder')));
                          
 		$this->addField('afkorting',
													array("description"=>"Afkorting",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_size"=>"20",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));
                          
		$this->addField('Minimaal',
													array("description"=>"Minimale score (%)",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Maximaal',
													array("description"=>"Maximale score (%)",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('verwachtRendement',
													array("description"=>"Verwacht rendement (%)",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('verwachtBrutoRendement',
                    array("description"=>"Verwacht bruto rendement (%)",
                          "db_size"=>"0",
                          "db_type"=>"double",
                          "form_size"=>"8",
                          "form_type"=>"text",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

		$this->addField('verwachtMaxVerlies',
													array("description"=>"verwachtMaxVerlies(%)",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('verwachtMaxWinst',
													array("description"=>"verwachtMaxWinst (%)",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 
		$this->addField('klasseStd',
													array("description"=>"klasseStd (%)",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 		$this->addField('uitsluitenScenario',
													array("description"=>"uitsluiten in Scenario",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('verwachtKostenPercentage',
										array("description"=>"Verwacht kostenpercentage",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('kleur',
                    array("description"=>"Kleur",
                          "db_size"=>"0",
                          "db_type"=>"text",
                          "form_size"=>"8",
                          "form_type"=>"text",
                          "form_visible"=>false,
                          "list_visible"=>false,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
  
    $this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>