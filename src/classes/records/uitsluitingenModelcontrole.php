<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 18 april 2020
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/04/30 05:32:27 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: uitsluitingenModelcontrole.php,v $
    Revision 1.2  2020/04/30 05:32:27  rvv
    *** empty log message ***

    Revision 1.1  2020/04/18 16:59:16  rvv
    *** empty log message ***

 		
 	
*/

class UitsluitingenModelcontrole extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function UitsluitingenModelcontrole()
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
		($this->get("portefeuille")=="")?$this->setError("portefeuille",vt("Mag niet leeg zijn!")):true;
		if($this->get("bedrag")<>0 && $this->get("rekening")=="")
    {
      $this->setError("rekening",vt("Bij gebruik van een bedrag dient een rekening gekozen te zijn!"));
    }
    $db=new DB();
		if($this->get("rekening")<>'')
    {
      $query = "SELECT id, bedrag FROM uitsluitingenModelcontrole WHERE portefeuille='".mysql_real_escape_string($this->get("portefeuille"))."' AND rekening='" . mysql_real_escape_string($this->get("rekening")). "' AND id <> '" . $this->get("id") . "'";
      $db->SQL($query);
      $rekData=$db->lookupRecord();
      if($rekData['bedrag']<>0)
      {
        $this->setError("bedrag",vt("Er is al een bedrag voor deze rekening vastgelegd.")."(".$rekData['bedrag']." id:".$rekData['id'].")");
      }
    }
    elseif($this->get("fonds")<>'')
    {
      $query = "SELECT id, fonds FROM uitsluitingenModelcontrole WHERE portefeuille='".mysql_real_escape_string($this->get("portefeuille"))."' AND fonds='" . mysql_real_escape_string($this->get("fonds")). "' AND id <> '" . $this->get("id") . "'";
      $db->SQL($query);
      $rekData=$db->lookupRecord();
      if($rekData['fonds']<>'')
      {
        $this->setError("fonds",vt("Dit fonds is al voor deze portefeuille vastgelegd.")."(".$rekData['fonds']." id:".$rekData['id'].")");
      }
    }
    elseif($this->get("Beleggingscategorie")<>'')
    {
      $query = "SELECT id, Beleggingscategorie FROM uitsluitingenModelcontrole WHERE portefeuille='".mysql_real_escape_string($this->get("portefeuille"))."' AND Beleggingscategorie='" . mysql_real_escape_string($this->get("Beleggingscategorie")). "' AND id <> '" . $this->get("id") . "'";
      $db->SQL($query);
      $rekData=$db->lookupRecord();
      if($rekData['Beleggingscategorie']<>'')
      {
        $this->setError("Beleggingscategorie",vt("De Beleggingscategorie is al voor deze portefeuille vastgelegd.")."(".$rekData['Beleggingscategorie']." id:".$rekData['id'].")");
      }
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
	  return checkAccess();
	
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "uitsluitingenModelcontrole";
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
  
    $this->addField('portefeuille',
                    array("description"=>"Portefeuille",
                          "db_size"=>"24",
                          "db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          'select_query' => "SELECT Portefeuille,Portefeuille FROM Portefeuilles WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Portefeuille",
                          'select_query_ajax' => "SELECT Portefeuille,Portefeuille FROM Portefeuilles WHERE Portefeuille='{value}'",
                          "form_size"=>"24",
                          "form_visible"=>true,"list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "form_extra"=>"onchange='javascript:portefeuilleChanged();'",
                          "list_order"=>"true",
                          "keyIn"=>"Portefeuilles"));
  
    $this->addField('fonds',
                    array("description"=>"Fonds",
                          "default_value"=>"",
                          "db_size"=>"25",
                          "db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          'select_query' => "SELECT Fonds,Fonds FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Fonds",
                          'select_query_ajax' => "SELECT Fonds,Fonds FROM Fondsen WHERE Fonds='{value}'",
                          "form_size"=>"25",
                          "form_visible"=>true,
                          "form_extra"=>'onchange=checkFonds($(this));',
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "keyIn"=>"Fondsen"));

    $this->addField('Beleggingscategorie',
                    array("description"=>"Beleggingscategorie",
                          "default_value"=>"",
                          "db_size"=>"25",
                          "db_type"=>"varchar",
                          "select_query"=>"SELECT Beleggingscategorie,Beleggingscategorie FROM Beleggingscategorien ORDER BY Beleggingscategorie",
                          "form_type"=>"selectKeyed",
                          "form_size"=>"25",
                          "form_visible"=>true,
                          "form_extra"=>'onchange=checkBeleggingscategorie($(this));',
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "keyIn"=>"Beleggingscategorien"));

		$this->addField('rekening',
													array("description"=>"Rekening",
                                "default_value"=>"",
                                "db_size"=>"25",
                                "db_type"=>"varchar",
                                "form_type"=>"selectKeyed",
                                "form_size"=>"25",
                                "form_visible"=>true,
                                "form_extra"=>'onchange=rekeningChanged($(this));',
                                "list_visible"=>true,
                                "list_width"=>"100",
                                "list_align"=>"left",
                                "list_search"=>false,
                                "list_order"=>"true",
                                'keyIn'=>'Rekeningen'));

		$this->addField('bedrag',
													array("description"=>"Bedrag",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
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