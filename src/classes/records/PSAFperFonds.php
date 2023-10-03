<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 17 oktober 2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/10/25 13:30:54 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: PSAFperFonds.php,v $
    Revision 1.3  2015/10/25 13:30:54  rvv
    *** empty log message ***

    Revision 1.2  2015/10/19 06:11:56  rvv
    *** empty log message ***

    Revision 1.1  2015/10/18 13:38:35  rvv
    *** empty log message ***

 		
 	
*/

class PSAFperFonds extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function PSAFperFonds()
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
		$query  = "SELECT id FROM PSAFperFonds WHERE ".
							" type = '".$this->get("type")."' AND ".
							" vermogensbeheerder = '".$this->get("vermogensbeheerder")."' AND ".
							" fonds = '".$this->get("fonds")."' ";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("vermogensbeheerder",vt("deze combinatie bestaat al"));
			$this->setError("fonds",vt("deze combinatie bestaat al"));
			$this->setError("code",vt("deze combinatie bestaat al"));

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
    $this->data['table']  = "PSAFperFonds";
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

		$this->addField('fonds',
													array("description"=>"Fonds",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Fonds,Fonds FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Fonds",
													'select_query_ajax' => "SELECT Fonds,Fonds FROM Fondsen WHERE Fonds='{value}'",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('code',
													array("description"=>"code",
													"default_value"=>"",
													"db_size"=>"12",
													"db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          "select_query"=>"SELECT code,concat(code,' - ',naam,' (',BICcode,')') FROM BICcodes ORDER BY code",
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"BICcodes"));

		$this->addField('type',
													array("description"=>"PSAF/PSET",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
													"form_options"=>array("PSAF"=>"PSAF","PSET"=>"PSET"),
													"form_size"=>"4",
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