<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 18 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/03/07 18:49:22 $
    File Versie         : $Revision: 1.6 $

    $Log: taken.php,v $
    Revision 1.6  2015/03/07 18:49:22  rvv
    *** empty log message ***

    Revision 1.5  2013/12/14 17:13:55  rvv
    *** empty log message ***

    Revision 1.4  2013/08/18 12:18:37  rvv
    *** empty log message ***

    Revision 1.3  2013/08/10 15:44:59  rvv
    *** empty log message ***

    Revision 1.2  2012/04/28 15:54:55  rvv
    *** empty log message ***

    Revision 1.1  2010/01/24 17:00:49  rvv
    *** empty log message ***



*/

class Taken extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Taken()
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
		($this->get("kop")=="")?$this->setError("kop",vt("Mag niet leeg zijn!")):true;

    // Portefeuille mag leeg zijn maar wanner gevuld moet deze wel aanwezig zijn.
    $relId = $this->get("rel_id");
    $relatie = $this->get("relatie");
    if ( ! empty ($relId) || ! empty ($relatie) ) {
      $DB = new DB();
      $DB->lookupRecordByQuery("SELECT id FROM CRM_naw WHERE id = '".mysql_real_escape_string($relId)."' ");
      if($DB->records() <= 0) {
      $this->setError("relatie",vtb("%s is een onbekende relatie", array($this->get("relatie"))));
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
		return true;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    global $USR;
    $this->data['name']  = "Taaklijst";
    $this->data['table']  = "taken";
    $this->data['identity'] = "id";


		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('rel_id',
													array("description"=>"rel_id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('gebruiker',
													array("description"=>"gebruiker",
													"default_value"=>"$USR",
													"db_size"=>"15",
													"db_type"=>"tinytext",
													"select_query"=>"SELECT gebruiker, if(naam='',gebruiker,naam) FROM Gebruikers ORDER BY gebruiker",
													"form_type"=>"selectKeyed",

													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('kop',
													array("description"=>"betreft",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"tinytext",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('txt',
													array("description"=>"tekst",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
													"form_rows"=>"12",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('afgewerkt',
													array("description"=>"afgewerkt",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('relatie',
													array("description"=>"relatie",
													"default_value"=>"",
													"db_size"=>"128",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('soort',
													array("description"=>"soort",
													"default_value"=>"",
													"db_size"=>"40",
													"db_type"=>"varchar",
		    									"select_query"=>"SELECT if(waarde<>'',waarde,omschrijving) as waarde ,omschrijving FROM CRM_selectievelden WHERE module IN('agenda afspraak','standaardTaken') ORDER BY omschrijving",
													"form_type"=>"selectKeyed",
													"form_size"=>"40",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('spoed',
													array("description"=>"spoed",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('zichtbaar',
													array("description"=>"zichtbaar na",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_extra"=>" onchange=\"date_complete(this);\"",
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
													"form_type"=>"datum",
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
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
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
													"form_type"=>"datum",
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