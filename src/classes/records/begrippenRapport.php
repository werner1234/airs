<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 27 april 2019
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/05/18 16:23:14 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: begrippenRapport.php,v $
    Revision 1.2  2019/05/18 16:23:14  rvv
    *** empty log message ***

    Revision 1.1  2019/04/27 18:37:19  rvv
    *** empty log message ***

 		
 	
*/

class BegrippenRapport extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function BegrippenRapport()
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
    ($this->get("vermogensbeheerder")=="")?$this->setError("vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
    
    $query  = "SELECT id FROM begrippenRapport WHERE Vermogensbeheerder = '".$this->get("Vermogensbeheerder")."' AND  begrip = '".$this->get("begrip")."' ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $data = $DB->nextRecord();
    if($DB->records() >0 && $this->get("id") <> $data['id'])
    {
      $this->setError("vermogensbeheerder",vt("deze combinatie bestaat al"));
      $this->setError("begrip",vt("deze combinatie bestaat al"));
    }
    
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
    return checkAccess($type);
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "begrippenRapport";
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
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          "select_query"=>"SELECT Vermogensbeheerder,concat(Vermogensbeheerder,' - ',Naam) FROM Vermogensbeheerders ORDER BY Vermogensbeheerder",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "form_extra"=>" onChange=\"javascript:vermogensbeheerderChanged();\" ",
                          "keyIn"=>"Vermogensbeheerders"));

		$this->addField('begrip',
													array("description"=>"Begrip",
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

		$this->addField('omschrijving',
													array("description"=>"Omschrijving",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('afdrukVolgorde',
                    array("description"=>"Afdrukvolgorde",
                          "default_value"=>"",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"text",
                          "form_size"=>"4",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

		$this->addField('categorieId',
													array("description"=>"Categorie",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
                          "form_type"=>"selectKeyed",
                     //           "select_query"=>"SELECT id, concat(vermogensbeheerder,' - ',categorie) FROM begrippenCategorie ORDER BY vermogensbeheerder,categorie",
													"form_size"=>"11",
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