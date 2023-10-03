<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 4 september 2021
    Author              : $Author:  $
    Laatste aanpassing  : $Date:  $
    File Versie         : $Revision:  $
 		
    $Log:  $
 		
 	
*/

class PortefeuillesGeconsolideerd extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function PortefeuillesGeconsolideerd()
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
    
    ($this->get("VirtuelePortefeuille")=="")?$this->setError("VirtuelePortefeuille",vt("Mag niet leeg zijn!")):true;
    ($this->get("Portefeuille")=="")?$this->setError("Portefeuille",vt("Mag niet leeg zijn!")):true;
    
    $DB=new DB();
    $query  = "SELECT id FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille = '".$this->get("VirtuelePortefeuille")."' ";
    ($DB->QRecords($query)>0)?$this->setError("VirtuelePortefeuille",vt("Is al aanwezig in GeconsolideerdePortefeuilles.")):true;
    
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
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
    return checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "PortefeuillesGeconsolideerd";
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
  
    $this->addField('VirtuelePortefeuille',
                    array("description"=>"Virtuele Portefeuille",
                          "default_value"=>"",
                          "db_size"=>"24",
                          "db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          "form_extra"=>" onChange=\"javascript:VirtuelePortefeuilleChanged();\" ",
                          "select_query"=>"SELECT Portefeuille,Portefeuille FROM Portefeuilles WHERE consolidatie=1 ORDER BY Portefeuille ",
                          "form_size"=>"20",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "keyIn"=>"Portefeuilles"));

  
    $this->addField("Portefeuille",
                    array("description"=>"Portefeuille",
                          "default_value"=>"",
                          "db_size"=>"24",
                          "db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          "form_extra"=>" onChange=\"javascript:VirtuelePortefeuilleChanged();\" ",
                  //        "select_query"=>"SELECT Portefeuille,Portefeuille FROM Portefeuilles WHERE consolidatie=0 AND Einddatum>now() AND Startdatum>'1990-01-01'  ORDER BY Portefeuille",
                          "form_size"=>"24",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "keyIn"=>"Portefeuilles"));
    

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