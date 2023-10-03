<?php
/*
    AE-ICT CODEX source module versie 1.6, 21 juli 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/07/13 17:44:52 $
    File Versie         : $Revision: 1.4 $

    $Log: NormwegingPerBeleggingscategorie.php,v $
    Revision 1.4  2019/07/13 17:44:52  rvv
    *** empty log message ***

    Revision 1.3  2019/06/08 16:02:01  rvv
    *** empty log message ***

    Revision 1.2  2012/07/25 15:56:46  rvv
    *** empty log message ***

    Revision 1.1  2012/07/22 12:53:11  rvv
    *** empty log message ***



*/

class NormwegingPerBeleggingscategorie extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function NormwegingPerBeleggingscategorie()
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
		($this->get("Portefeuille")=="")?$this->setError("Portefeuille",vt("Mag niet leeg zijn!")):true;
    
    if($this->get("DatumVanaf") <> '')
    {
      $datumJul=db2jul($this->get("DatumVanaf"));
      $datum=mktime(0,0,0,date('m',$datumJul)+1,0,date('Y',$datumJul));
      $datum=date("Y-m-d",$datum);
      if($this->get("DatumVanaf") <> $datum)
        $this->setError("DatumVanaf",vt("Moet de laatste dag van een mand zijn."));
    }
    
    if($this->get("DatumVanaf") <> '')
      $datumFilter="DatumVanaf                = '".$this->get("DatumVanaf")."' AND";
    else
      $datumFilter='';
    
    $DB = new DB();
    $query = "SELECT id FROM NormwegingPerBeleggingscategorie
	            WHERE
	            Beleggingscategorie  = '".$this->get("Beleggingscategorie")."' AND
	            $datumFilter
              Portefeuille         = '".$this->get("Portefeuille")."'";
    $DB->SQL($query);
    $DB->Query();
    $data = $DB->nextRecord();

    if($DB->records() >0 && $this->get("id") <> $data['id'])
    {
      $this->setError("Beleggingscategorie", vt("%s bestaat al", array($this->get("Beleggingscategorie"))));
      $this->setError("DatumVanaf", vt("%s bestaat al", array($this->get("DatumVanaf"))));
      $this->setError("Portefeuille", vt(" bestaat al", array($this->get("Portefeuille"))));
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
    $this->data['table']  = "NormwegingPerBeleggingscategorie";
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

		$this->addField('Portefeuille',
													array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Portefeuille,Portefeuille FROM Portefeuilles ORDER BY Portefeuille",
													"form_size"=>"24",
													"form_visible"=>true,
													"form_extra"=>"onChange='javascript:portefeuilleChanged();'",
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Portefeuilles"
                            ));

		$this->addField('Beleggingscategorie',
													array("description"=>"Beleggingscategorie",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Beleggingscategorie,Beleggingscategorie FROM Beleggingscategorien ORDER BY Beleggingscategorie",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Normweging',
													array("description"=>"Normweging",
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
  
    $this->addField('DatumVanaf',
                    array("description"=>"DatumVanaf",
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