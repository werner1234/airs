<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/02/17 19:13:54 $
 		File Versie					: $Revision: 1.15 $

*/

class ZorgplichtPerPortefeuille extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function ZorgplichtPerPortefeuille()
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
		if($type=='verzenden')
		{
			global $USR;
			$db=new DB();
			$query="SELECT MAX(Vermogensbeheerders.CrmTerugRapportage) as CrmTerugRapportage FROM Vermogensbeheerders
              Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
              WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
			$db->SQL($query);
			$db->Query();
			$data=$db->lookupRecord();
			if($data['CrmTerugRapportage'] > 0)
				return true;
		}
		return checkAccess($type);
	}

	function validate()
	{
		($this->get("Portefeuille")=="")?$this->setError("Portefeuille",vt("Mag niet leeg zijn!")):true;
		($this->get("Zorgplicht")=="")?$this->setError("Zorgplicht",vt("Mag niet leeg zijn!")):true;

		$vanaf=$this->get("Vanaf");
		if($vanaf=='')
		  $vanaf='0000-00-00';

		//Vermogensbeheerder én zorgplicht én fonds
		$query  = "SELECT id FROM ZorgplichtPerPortefeuille WHERE ".
							" Portefeuille = '".$this->get("Portefeuille")."' AND ".
							" Zorgplicht = '".$this->get("Zorgplicht")."' AND
						  	Vanaf = '$vanaf' ";


		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();

		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Zorgplicht",vt("combinatie bestaat al"));
			$this->setError("Portefeuille",vt("combinatie bestaat al"));
			$this->setError("Vanaf",vt("combinatie bestaat al"));
		}

		$DB->lookupRecordByQuery("SELECT Portefeuille FROM Portefeuilles WHERE Portefeuille = '".mysql_real_escape_string($this->get("Portefeuille"))."' ");
		if($DB->records() <= 0) {
			$this->setError("Portefeuille", vtb("%s is een onbekende portefeuille", array($this->get("Portefeuille"))));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "ZorgplichtPerPortefeuille";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
/*
		$this->addField('Portefeuille',
													array("description"=>"Portefeuille",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles,GeconsolideerdePortefeuilles"));
*/
		$this->addField('Portefeuille',
													array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"text",
													'autocomplete' => array(
														'table'        => 'Portefeuilles',
														'prefix'       => true,
														'returnType'   => 'expanded',
														'extra_fields' => array(
															'Portefeuille',
															'Client',
															'id',
														),
														'label'        => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
														'searchable'   => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
														'field_value'  => array('Portefeuilles.Portefeuille'),
														'value'        => 'Portefeuilles.Portefeuille',
													),
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles"));

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

		$this->addField('Zorgplicht',
													array("description"=>"Zorgplicht",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Zorgplichtcategorien"));

		$this->addField('Minimum',
													array("description"=>"Minimum %",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Maximum',
													array("description"=>"Maximum %",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
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

		$this->addField('norm',
													array("description"=>"Norm %",
													"db_size"=>"4",
													"db_type"=>"double",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Vanaf',
													array("description"=>"Vanaf",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
  	$this->addField('extra',
													array("description"=>"Extra grens",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('maxBedrag',
										array("description"=>"Max. bedrag",
													"db_size"=>"4",
													"db_type"=>"double",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('minBedrag',
										array("description"=>"Min. bedrag",
													"db_size"=>"4",
													"db_type"=>"double",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('procentueleOpslag',
										array("description"=>"Procentuele opslag",
													"db_size"=>"4",
													"db_type"=>"double",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
  }
}
?>