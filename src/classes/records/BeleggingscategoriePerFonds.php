<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/02/11 13:23:01 $
 		File Versie					: $Revision: 1.18 $

*/

class BeleggingscategoriePerFonds extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function BeleggingscategoriePerFonds()
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
			if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0)
         return true;
	  }
		return checkAccess($type);
	}

	function validate()
	{
		($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		($this->get("Beleggingscategorie")=="")?$this->setError("Beleggingscategorie",vt("Mag niet leeg zijn!")):true;
		($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;

		$vanaf = $this->get("Vanaf");
		if($vanaf == '')
		  $vanaf='0000-00-00';

		$query  = "SELECT id FROM BeleggingscategoriePerFonds WHERE ".
		//					" Beleggingscategorie = '".$this->get("Beleggingscategorie")."' AND ".
							" Vermogensbeheerder = '".$this->get("Vermogensbeheerder")."' AND ".
							" Vanaf = '$vanaf' AND ".
							" Fonds = '".$this->get("Fonds")."' ";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Vermogensbeheerder",vt("deze combinatie bestaat al"));
			$this->setError("Beleggingscategorie",vt("deze combinatie bestaat al"));
			$this->setError("Fonds",vt("deze combinatie bestaat al"));
			$this->setError("Vanaf",vt("deze combinatie bestaat al"));
		}

    if ( ! isset ($_GET['newFonds']) || (int) $_GET['newFonds'] !== 1 ) {
      $DB->lookupRecordByQuery("SELECT Fonds FROM Fondsen WHERE Fonds = '" . mysql_real_escape_string($this->get("Fonds")) . "' ");
      if ($DB->records() <= 0) {
			$this->setError("Fonds",vtb("%s is een onbekend fonds", array($this->get("Fonds"))));
      }
    }
		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "BeleggingscategoriePerFonds";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "select_query"=>"SELECT Vermogensbeheerder,concat(Vermogensbeheerder,' - ',Naam) FROM Vermogensbeheerders ORDER BY Vermogensbeheerder",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"form_extra"=>" onChange=\"javascript:vermogensbeheerderChanged();\" ",
													"keyIn"=>"Vermogensbeheerders"));

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

		$this->addField('Beleggingscategorie',
													array("description"=>"Beleggingscategorie",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "select_query"=>"SELECT Beleggingscategorie,concat(Beleggingscategorie,' - ',Omschrijving)  FROM Beleggingscategorien ORDER BY Beleggingscategorie",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Beleggingscategorien"));

			$this->addField('afmCategorie',
													array("description"=>"AFM categorie",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "select_query"=>"SELECT afmCategorie,concat(afmCategorie,' - ',Omschrijving)  FROM afmCategorien ORDER BY afmCategorie",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"afmCategorien"));

		$this->addField('RisicoPercentageFonds',
													array("description"=>"Risico Percentage Fonds",
													"db_size"=>"5",
													"db_type"=>"double",
													"form_size"=>"5",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('belgTOB',
										array("description"=>"BelgTOB",
													"db_size"=>"5",
													"db_type"=>"double",
													"form_size"=>"5",
													"form_type"=>"select",
													"form_select_option_notempty"=>true,
													"form_options"=>array(0.00,0.09,0.27,1.32),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('grafiekKleur',
													array("description"=>"Grafiek Kleur (RGB)",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

	 $this->addField('Vanaf',
													array("description"=>"Vanaf",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"default_value"=>"now()",
													"form_type"=>"calendar",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('duurzaamheid',
													array("description"=>"duurzaamheid",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		
		$this->addField('duurzaamEcon',
													array("description"=>"duurzaam-econ",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('duurzaamSociaal',
													array("description"=>"duurzaam-sociaal",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('duurzaamMilieu',
													array("description"=>"duurzaam-milieu",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
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