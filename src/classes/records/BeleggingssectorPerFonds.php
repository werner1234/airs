<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/07/21 15:51:44 $
 		File Versie					: $Revision: 1.18 $

*/

class BeleggingssectorPerFonds extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function BeleggingssectorPerFonds()
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
//		($this->get("Beleggingssector")=="")?$this->setError("Beleggingssector","Mag niet leeg zijn!"):true;
		($this->get("Fonds")=="")?$this->setError("Fonds","Mag niet leeg zijn!"):true;

		$vanaf = $this->get("Vanaf");
		if($vanaf == '')
		  $vanaf='0000-00-00';

		$query  = "SELECT id FROM BeleggingssectorPerFonds WHERE ".
//								" Beleggingssector = '".$this->get("Beleggingssector")."' AND ".
								" Vermogensbeheerder = '".$this->get("Vermogensbeheerder")."' AND ".
								" Fonds = '".$this->get("Fonds")."' AND Vanaf = '".$vanaf."'";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Vermogensbeheerder",vt("deze combinatie bestaat al"));
			$this->setError("Beleggingssector",vt("deze combinatie bestaat al"));
			$this->setError("Fonds",vt("deze combinatie bestaat al"));
			$this->setError("Vanaf",vt("deze combinatie bestaat al"));
		}

    if ( ! isset ($_GET['newFonds']) || (int) $_GET['newFonds'] !== 1 ) {
      $DB->lookupRecordByQuery("SELECT Fonds FROM Fondsen WHERE Fonds = '".mysql_real_escape_string($this->get("Fonds"))."' ");
      if($DB->records() <= 0) {
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
    $this->data['table']  = "BeleggingssectorPerFonds";
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
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder ",
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
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('Beleggingssector',
													array("description"=>"Beleggingssector",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Beleggingssector,concat(Beleggingssector,' - ',omschrijving) FROM Beleggingssectoren ORDER BY Beleggingssector  ",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Beleggingssectoren"));

		$this->addField('Regio',
													array("description"=>"Regio",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Regio,concat(Regio,' - ',omschrijving) FROM Regios ORDER BY Regio ",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Regios"));

		$this->addField('AttributieCategorie',
													array("description"=>"Attributiecategorie",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT AttributieCategorie,concat(AttributieCategorie,' - ',omschrijving) FROM AttributieCategorien ",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"AttributieCategorien"));

		$this->addField('DuurzaamCategorie',
										array("description"=>"Duurzaamcategorie",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT DuurzaamCategorie,concat(DuurzaamCategorie,' - ',omschrijving) FROM DuurzaamCategorien ",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"DuurzaamCategorien"));

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