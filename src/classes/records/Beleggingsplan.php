<?php
/*
    AE-ICT CODEX source module versie 1.6, 17 december 2008
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/07/11 08:58:31 $
    File Versie         : $Revision: 1.6 $

    $Log: Beleggingsplan.php,v $
    Revision 1.6  2020/07/11 08:58:31  rm
    8696

    Revision 1.5  2020/07/08 14:55:04  rm
    8696

    Revision 1.4  2020/07/03 14:22:26  rm
    8696

    Revision 1.3  2017/11/22 17:05:43  rvv
    *** empty log message ***

    Revision 1.2  2009/02/05 15:34:08  cvs
    procent velden

    Revision 1.1  2008/12/17 13:43:49  rvv
    *** empty log message ***



*/

class Beleggingsplan extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Beleggingsplan()
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
    //validatie of fonds bestaat
    if ( $this->get("Portefeuille") !== "" ) {
      $db = new DB();
      $q = 'SELECT `Portefeuille` FROM `Portefeuilles` WHERE `Portefeuille` = "' . mysql_escape_string($this->get("Portefeuille")) .'" ';
      $rec = $db->lookupRecordByQuery($q);
      
      if ( empty ($rec) || empty ($rec['Portefeuille']) ) {
        $this->setError("Portefeuille",vt("Portefeuille niet gevonden"));
      }
    } else {
      $this->setError("Portefeuille",vt("Mag niet leeg zijn."));
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
    $this->data['table']  = "Beleggingsplan";
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
													"db_size"=>"12",
													"db_type"=>"varchar",
//													"form_type"=>"selectKeyed",
//													"select_query"=>"SELECT Portefeuille,Portefeuille  FROM Portefeuilles ",
                          "form_type"=>"text",
                          'autocomplete' => array(
                            'table'        => 'Portefeuilles',
                            'prefix'       => true,
                            'returnType'   => 'expanded',
                            'extra_fields' => array(
                              'Portefeuille',
                              'Client',
                              'id',
                            ),
                            'label'        => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
                            'searchable'   => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
                            'field_value'  => array('Portefeuilles.Portefeuille'),
                            'value'        => 'Portefeuilles.Portefeuille',
                          ),
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles"));

		$this->addField('Datum',
													array("description"=>"Datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Waarde',
													array("description"=>"Waarde",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
	 $this->addField('ProcentRisicoDragend',
													array("description"=>"% RisicoDragend",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"tinyint",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
	 $this->addField('ProcentRisicoMijdend',
													array("description"=>"% RisicoMijdend",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"tinyint",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"",
													"list_align"=>"right",
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