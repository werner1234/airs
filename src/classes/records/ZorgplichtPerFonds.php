<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/16 15:53:26 $
 		File Versie					: $Revision: 1.14 $

*/

class ZorgplichtPerFonds extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function ZorgplichtPerFonds()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	function checkAccess($type)
	{
	  if($type=='verzenden')
	  {
	    global $USR;
	    $db=new DB();
      $query="SELECT fondsmutatiesAanleveren FROM Gebruikers WHERE Gebruiker='$USR'";
      $db->SQL($query);
      $db->Query();
      $data=$db->lookupRecord();
      if($data['fondsmutatiesAanleveren'] > 0)
         return true;
	  }
		return checkAccess($type);
	}

	function validate()
	{
		($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		($this->get("Zorgplicht")=="")?$this->setError("Zorgplicht",vt("Mag niet leeg zijn!")):true;
		($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;

		//Vermogensbeheerder én zorgplicht én fonds
		$query  = "SELECT id FROM ZorgplichtPerFonds WHERE ".
							" Zorgplicht = '".$this->get("Zorgplicht")."' AND ".
							" Vermogensbeheerder = '".$this->get("Vermogensbeheerder")."' AND ".
							" Fonds = '".$this->get("Fonds")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();

		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("Zorgplicht",vt("combinatie bestaat al"));
			$this->setError("Vermogensbeheerder",vt("combinatie bestaat al"));
			$this->setError("Fonds",vt("combinatie bestaat al"));
		}

		$DB->lookupRecordByQuery("SELECT Fonds FROM Fondsen WHERE Fonds = '".mysql_real_escape_string($this->get("Fonds"))."' ");
		if($DB->records() <= 0) {
			$this->setError("Fonds", vtb("%s is een onbekend fonds", array($this->get("Fonds"))));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "ZorgplichtPerFonds";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
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
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));

		$this->addField('Zorgplicht',
													array("description"=>"Zorgplicht",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Zorgplichtcategorien"));

		$this->addField('Fonds',
													array("description"=>"Fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
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
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('Percentage',
													array("description"=>"Percentage",
													"db_size"=>"11",
													"db_type"=>"int",
													"default_value"=>"100",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>