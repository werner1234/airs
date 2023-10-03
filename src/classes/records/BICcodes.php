<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 16 september 2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/10/25 13:30:54 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: BICcodes.php,v $
    Revision 1.2  2015/10/25 13:30:54  rvv
    *** empty log message ***

    Revision 1.1  2015/09/16 16:20:12  rvv
    *** empty log message ***

 		
 	
*/

class BICcodes extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function BICcodes()
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
		($this->get("code")=="")?$this->setError("code",vt("Mag niet leeg zijn!")):true;

		$query  = "SELECT id FROM BICcodes WHERE code = '".$this->get("code")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("code",vtb("%s bestaat al", array($this->get("code"))));
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
    $this->data['table']  = "BICcodes";
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
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder ",
													"form_type"=>"selectKeyed",
													"form_extra"=>" onChange=\"javascript:vermogensbeheerderChanged();\" ",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders",
													"categorie"=>"Gegevens"));

		$this->addField('code',
													array("description"=>"code",
													"default_value"=>"",
													"db_size"=>"12",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "key_field"=>true));

		$this->addField('naam',
													array("description"=>"naam",
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

		$this->addField('BICcode',
													array("description"=>"BICcode",
													"default_value"=>"",
													"db_size"=>"32",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"32",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('PSET',
													array("description"=>"PSET",
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

		$this->addField('PSAF',
													array("description"=>"PSAF",
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
  
    $this->addField('landcode',
                    array("description"=>"Landcode",
                          "default_value"=>"",
                          "db_size"=>"32",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"32",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
    $this->addField('correspondent',
                    array("description"=>"Correspondent",
                          "default_value"=>"",
                          "db_size"=>"25",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"32",
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