<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 september 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/03/02 18:26:45 $
    File Versie         : $Revision: 1.7 $
 		
    $Log: doorkijk_msCategoriesoort.php,v $
    Revision 1.7  2019/03/02 18:26:45  rvv
    *** empty log message ***

    Revision 1.6  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.5  2018/02/07 16:01:35  rm
    6478

    Revision 1.4  2018/01/03 14:17:55  rvv
    *** empty log message ***

    Revision 1.3  2017/12/16 18:34:43  rvv
    *** empty log message ***

    Revision 1.2  2017/12/04 15:09:19  cvs
    vt( verwijderen

    Revision 1.1  2017/12/04 10:39:16  cvs
    Update van Ben ingelezen dd 4-12-2017

 		
 	
*/

class doorkijk_msCategoriesoort extends Table
{
  /*
  * Object vars
  */
  var $tableName = "doorkijk_msCategoriesoort";
  var $data = array();
  
  /*
  * Constructor
  */
  function doorkijk_msCategoriesoort()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
	//$this->initModule();
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

		$kleuren = ($_POST['grafiekKleur']);
		$kleur =  $kleuren[0]+  $kleuren[1] + $kleuren[2];
		//debug($kleur);
		if($kleur <=0 || $kleur > 756) $this->setError("grafiekKleur",vt("Kleurcodes buiten range 0-255!"));

		($this->get("msCategoriesoort")=="")?$this->setError("msCategoriesoort",vt("Mag niet leeg zijn!")):true;
		($this->get("msCategorie")=="")?$this->setError("msCategorie",vt("Mag niet leeg zijn!")):true;
		$query  = "SELECT id 
                   FROM doorkijk_msCategoriesoort 
                   WHERE msCategoriesoort = '" . $this->get("msCategoriesoort") . "' AND msCategorie = '" . $this->get("msCategorie") . "'
                   ";
		//debug($query);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") != $data['id'])
		{
			$this->setError("msCategoriesoort", vtb("%s - %s bestaat al", array($this->get("msCategorie"), $this->get("msCategoriesoort"))));
		}
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	  return checkAccess($type);
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "Doorkijk";
    $this->data['table']  = $this->tableName;
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>false,
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
													"list_visible"=>false,
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
													"list_visible"=>false,
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
													"list_width"=>"false",
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
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('msCategoriesoort',
													array("description"=>"Categoriesoort",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
														"form_type"=>"selectKeyed",
														"form_options" => array (
															"Beleggingscategorien"  => "Beleggingscategorien",
															"Beleggingssectoren"    => "Beleggingssectoren",
															"Regios"                => "Regios",
															"Valutas"               => "Valuta",
															"Rating"                => "Rating",
															"Looptijd"              => "Looptijd",
                              "Coupon"                => "Coupon",
                              "Subtype obligaties"    => "Subtype obligaties",
														),
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('msCategorie',
													array("description"=>"Categorie",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"extra_keys"=>array('msCategoriesoort'),
													"key_field"=>true));


		$this->addField('omschrijving',
										array("description"=>"Omschrijving",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('grafiekKleur',
										array("description"=>"Grafiek kleur",
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

  }
	function initModule()
	{
		$tst = new SQLman();
		$tst->tableExist($this->tableName,true);
		$tst->changeField($this->tableName,"msCategoriesoort",array("Type"=>"varchar(60)","Null"=>true));
		$tst->changeField($this->tableName,"msCategorie",array("Type"=>"varchar(60)","Null"=>true));
		$tst->changeField($this->tableName,"omschrijving",array("Type"=>"varchar(50)","Null"=>true));
		$tst->changeField($this->tableName,"grafiekKleur",array("Type"=>"text","Null"=>true));
	}
}
?>