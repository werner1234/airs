<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 23 mei 2020
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/06/20 12:12:39 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: FondsenFundInformatie.php,v $
    Revision 1.2  2020/06/20 12:12:39  rvv
    *** empty log message ***

    Revision 1.1  2020/05/23 16:41:57  rvv
    *** empty log message ***

 		
 	
*/

class FondsenFundInformatie extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function FondsenFundInformatie()
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
		($this->get("fonds")=="")?$this->setError("fonds",vt("Mag niet leeg zijn!")):true;
    
    $vanaf = $this->get("datumVanaf");
    if($vanaf == '')
      $vanaf='0000-00-00';
    
    $query  = "SELECT id FROM FondsenFundInformatie WHERE datumVanaf = '$vanaf' AND fonds = '".$this->get("fonds")."' ";
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $data = $DB->nextRecord();
    if($DB->records() >0 && $this->get("id") <> $data['id'])
    {
      $this->setError("fonds",vt("deze combinatie bestaat al"));
      $this->setError("datumVanaf",vt("deze combinatie bestaat al"));
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
    $this->data['table']  = "FondsenFundInformatie";
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

		$this->addField('fonds',
                    array("description"=>"Fonds",
                          "db_size"=>"25",
                          "db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          "select_query"=>'SELECT Fonds,Fonds FROM Fondsen ORDER BY Fonds',
                          'select_query_ajax' => "SELECT Fonds,Fonds FROM Fondsen WHERE Fonds='{value}'",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>true,
                          "list_order"=>"true",
                          "keyIn"=>"Fondsen"));

		$this->addField('datumVanaf',
													array("description"=>"datumVanaf",
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

		$this->addField('MSFondswaarde',
													array("description"=>"MSFondswaarde",
                                "default_value"=>"",
                                "db_size"=>"11,3",
                                "db_type"=>"decimal",
                                "form_type"=>"text",
                                "form_size"=>"11,3",
                                "form_visible"=>true,
                                "list_visible"=>true,
                                "list_width"=>"100",
                                "list_align"=>"left",
                                "list_search"=>false,
                                "list_order"=>"true"));
		
		$this->addField('MSAantalIntr',
													array("description"=>"MSAantalIntr",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('koersFrequentie',
                    array("description"=>"Koersfrequentie",
                          "default_value"=>"",
                          "db_size"=>"20",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"20",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

		$this->addField('MSManFeeFonds',
													array("description"=>"MSManFeeFonds",
													"default_value"=>"",
													"db_size"=>"11,3",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"11,3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('YieldtoMaturity',
													array("description"=>"YieldtoMaturity",
                                "default_value"=>"",
                                "db_size"=>"11,3",
                                "db_type"=>"decimal",
                                "form_type"=>"text",
                                "form_size"=>"11,3",
                                "form_visible"=>true,
                                "list_visible"=>true,
                                "list_width"=>"100",
                                "list_align"=>"left",
                                "list_search"=>false,
                                "list_order"=>"true"));

		$this->addField('AverageCreditQuality',
													array("description"=>"AverageCreditQuality",
                                "default_value"=>"",
                                "db_size"=>"103",
                                "db_type"=>"varchar",
                                "form_type"=>"text",
                                "form_visible"=>true,
                                "list_visible"=>true,
                                "list_width"=>"100",
                                "list_align"=>"left",
                                "list_search"=>false,
                                "list_order"=>"true"));

		$this->addField('AverageEffDuration',
													array("description"=>"AverageEffDuration",
                                "default_value"=>"",
                                "db_size"=>"11,3",
                                "db_type"=>"decimal",
                                "form_type"=>"text",
                                "form_size"=>"11,3",
                                "form_visible"=>true,
                                "list_visible"=>true,
                                "list_width"=>"100",
                                "list_align"=>"left",
                                "list_search"=>false,
                                "list_order"=>"true"));

		$this->addField('AverageEffMaturity',
													array("description"=>"AverageEffMaturity",
                                "default_value"=>"",
                                "db_size"=>"11,3",
                                "db_type"=>"decimal",
                                "form_type"=>"text",
                                "form_size"=>"11,3",
                                "form_visible"=>true,
                                "list_visible"=>true,
                                "list_width"=>"100",
                                "list_align"=>"left",
                                "list_search"=>false,
                                "list_order"=>"true"));

		$this->addField('AverageCoupon',
													array("description"=>"AverageCoupon",
                                "default_value"=>"",
                                "db_size"=>"11,3",
                                "db_type"=>"decimal",
                                "form_type"=>"text",
                                "form_size"=>"11,3",
                                "form_visible"=>false,
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
													"form_visible"=>false,
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
													"form_visible"=>false,
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
													"form_visible"=>false,
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
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>