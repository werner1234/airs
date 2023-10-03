<?php
/* 	
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/11/08 12:33:21 $
 		File Versie					: $Revision: 1.5 $
 				
*/

class CategorienPerHoofdcategorie extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function CategorienPerHoofdcategorie()
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
		return checkAccess($type);
	}
  
	function validate()
	{
		($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		($this->get("Hoofdcategorie")=="")?$this->setError("Hoofdcategorie",vt("Mag niet leeg zijn!")):true;
		($this->get("Beleggingscategorie")=="")?$this->setError("Beleggingscategorie",vt("Mag niet leeg zijn!")):true;

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "CategorienPerHoofdcategorie";
    $this->data['identity'] = "id";

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
                          "form_extra"=>" onChange=\"javascript:vermogensbeheerderChanged();\" ",
													"keyIn"=>"Vermogensbeheerders"));

		$this->addField('Hoofdcategorie',
													array("description"=>"Hoofdcategorie",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "select_query"=>"SELECT Beleggingscategorie,concat(Beleggingscategorie,' - ',Omschrijving)  FROM Beleggingscategorien ORDER BY Beleggingscategorie",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('Beleggingscategorie',
													array("description"=>"Beleggingscategorie",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "select_query"=>"SELECT Beleggingscategorie,concat(Beleggingscategorie,' - ',Omschrijving)  FROM Beleggingscategorien ORDER BY Beleggingscategorie",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Beleggingscategorien"));

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