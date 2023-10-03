<?php
/*
    AE-ICT CODEX source module versie 1.6, 20 oktober 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2012/01/08 10:12:38 $
    File Versie         : $Revision: 1.7 $

    $Log: CRM_uur_registratie.php,v $
    Revision 1.7  2012/01/08 10:12:38  rvv
    *** empty log message ***

    Revision 1.6  2012/01/04 16:26:09  rvv
    *** empty log message ***

    Revision 1.5  2011/12/03 08:16:35  rvv
    *** empty log message ***

    Revision 1.4  2011/11/30 18:42:43  rvv
    *** empty log message ***

    Revision 1.3  2011/11/30 18:41:12  rvv
    *** empty log message ***

    Revision 1.2  2011/11/19 15:36:03  rvv
    *** empty log message ***

    Revision 1.1  2011/10/22 06:45:49  cvs
    Urenregistratie voor TRA



*/

class CRM_uur_registratie extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function CRM_uur_registratie()
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
		($this->get("datum")=="")?$this->setError("datum",vt("Mag niet leeg zijn!")):true;
		($this->get("deb_id")=="")?$this->setError("debiteur",vt("Mag niet leeg zijn!")):true;
		($this->get("act_id")=="")?$this->setError("activiteit",vt("Mag niet leeg zijn!")):true;
		($this->get("tijd")=="")?$this->setError("werktijd",vt("Mag niet leeg zijn!")):true;

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	  global $USR;
	  if($this->get('add_user'))
	  {
	   if($this->get('add_user') <> $USR)
	     return false;
	  }

	  return true;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "Uur registratie";
    $this->data['table']  = "CRM_uur_registratie";
    $this->data['identity'] = "id";
    $this->data['alias'] = array('debiteur'=>array("sql_alias"=>"concat(CRM_naw.debiteurnr,': ',CRM_naw.naam)"),
                                 'actitviteit'=>array("sql_alias"=>"concat(CRM_uur_activiteiten.code,': ',CRM_uur_activiteiten.omschrijving)"));


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

		$this->addField('wn_code',
													array("description"=>"werknemer",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",

													"form_size"=>"10",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('datum',
													array("description"=>"datum",
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

		$this->addField('deb_id',
													array("description"=>"debiteur",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"select_query"=>"SELECT id, concat(debiteurnr,': ',naam) as naam FROM CRM_naw WHERE debiteurnr <> '' AND debiteur = 1 AND aktief = 1 ORDER BY debiteurnr",
													"form_select_option_notempty" => true,
                          "form_type"=>"selectKeyed",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('act_id',
													array("description"=>"activiteit",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"select_query"=>"SELECT id, concat(code,': ',omschrijving) FROM (CRM_uur_activiteiten) ORDER BY code",
													"form_type"=>"selectKeyed",
                          "form_select_option_notempty" => true,
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('tijd',
													array("description"=>"gewerkte tijd",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('memo',
													array("description"=>"bijzonderheden",
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

		$this->addField('verwerkt',
													array("description"=>"verwerkt",
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



  }
}
?>