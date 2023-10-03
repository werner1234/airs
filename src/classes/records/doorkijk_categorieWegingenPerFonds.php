<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 22 september 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/08/07 15:47:09 $
    File Versie         : $Revision: 1.6 $
 		
    $Log: doorkijk_categorieWegingenPerFonds.php,v $
    Revision 1.6  2019/08/07 15:47:09  rvv
    *** empty log message ***

    Revision 1.5  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.4  2017/12/16 18:34:43  rvv
    *** empty log message ***

    Revision 1.3  2017/12/07 07:39:57  rvv
    *** empty log message ***

    Revision 1.2  2017/12/04 14:47:20  cvs
    vertaling verwijderd

    Revision 1.1  2017/12/04 10:39:16  cvs
    Update van Ben ingelezen dd 4-12-2017

 		
 	
*/

class doorkijk_categorieWegingenPerFonds extends Table
{
  /*
  * Object vars
  */
  var $tableName = "doorkijk_categorieWegingenPerFonds";
  var $data = array();
  
  /*
  * Constructor
  */
  function doorkijk_categorieWegingenPerFonds()
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
		($this->get("datumVanaf")=="")?$this->setError("datumVanaf",vt("Mag niet leeg zijn!")):true;
		($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;
		($this->get("msCategoriesoort")=="")?$this->setError("msCategoriesoort",vt("Mag niet leeg zijn!")):true;
		($this->get("msCategorie")=="")?$this->setError("msCategorie",vt("Mag niet leeg zijn!")):true;
		($this->get("weging")=="")?$this->setError("weging",vt("Mag niet leeg zijn!")):true;
		$query  = "SELECT id 
                   FROM doorkijk_categorieWegingenPerFonds 
                   WHERE datumVanaf       = '" . $this->get("datumVanaf")       . "' AND 
                         Fonds            = '" . $this->get("Fonds")            . "' AND 
                         msCategoriesoort = '" . $this->get("msCategoriesoort") . "'
                   ";
		//debug($query);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") != $data['id'])
		{
			$this->setError("datumVanaf", vtb("%s - %s bestaat al", array($this->get("datumVanaf"), $this->get("Fonds"))));
		}

		$query  = "SELECT id 
                   FROM Fondsen 
                   WHERE Fonds  = '" . $this->get("Fonds") . "'
                   ";
		//debug($query);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() ==0)
		{
			$this->setError("Fonds",vtb("Fonds %s bestaat niet", array($this->get("Fonds"))));
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
												  "list_visible"=>false,
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
												  "list_visible"=>false,
												  "list_width"=>"100",
												  "list_align"=>"left",
												  "list_search"=>false,
												  "list_order"=>"true"));


		$this->addField('datumVanaf',
												array("description"=>"datumVanaf",
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
	  $this->addField('Fonds',
												  array("description"=>"Fonds",
													  "default_value"=>"",
													  "db_size"=>"60",
													  "db_type"=>"varchar",
													  "form_type"=>"text",
													  "form_size"=>"60",
													  "form_visible"=>true,
													  "list_visible"=>true,
													  "list_width"=>"100",
													  "list_align"=>"left",
													  "list_search"=>false,
													  "list_order"=>"true",
														"keyIn"=>"Fondsen",
													  "autocomplete" => array(
														  'table'        => 'Fondsen',
														  'prefix'       => true,
														  'returnType'   => 'expanded',
														  'extra_fields' => array('Fondsen.Valuta', 'Fondsen.ISINCode'),
														  'label'        => array('Fondsen.Fonds', 'Fondsen.omschrijving'),
														  'searchable'   => array('Fondsen.Fonds', 'Fondsen.omschrijving'),
														  'field_value'  => array('Fondsen.Fonds'),
														  'value'        => 'Fondsen.Fonds',
														  'actions'      => array(
															  'select' => '
																	  event.preventDefault();
																	  console.log(ui.item);
																	  $("#ISINCode").val(ui.item.data.Fondsen.ISINCode);
																	  $("#Valuta").val(ui.item.data.Fondsen.Valuta);
																	  $("#Fonds").val(ui.item.value);
															'
														  )
													  )
												  ));


		$this->addField('msCategoriesoort',
												array("description"=>"msCategoriesoort",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT DISTINCT msCategoriesoort, msCategoriesoort
																		FROM doorkijk_msCategoriesoort
																		ORDER BY msCategoriesoort",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('msCategorie',
												array("description"=>"msCategorie",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
		                      "keyIn"=>"doorkijk_msCategoriesoort"));


		  $this->addField('ISINCode',
											  array("description"=>"ISINCode",
												  "default_value"=>"",
												  "db_size"=>"12",
												  "db_type"=>"varchar",
												  "form_type"=>"text",
												  "form_size"=>"12",
												  "form_visible"=>true,
												  "list_visible"=>true,
												  "form_extra"=>'READONLY',
												  "list_width"=>"100",
												  "list_align"=>"left",
												  "list_search"=>false,
												  "list_order"=>"true"));

	  $this->addField('Valuta',
											  array("description"=>"Valuta",
												  "default_value"=>"",
												  "db_size"=>"3",
												  "db_type"=>"varchar",
												  "form_type"=>"text",
												  "form_size"=>"3",
												  "form_visible"=>true,
												  "list_visible"=>true,
												  "form_extra"=>'READONLY',
												  "list_width"=>"100",
												  "list_align"=>"left",
												  "list_search"=>false,
												  "list_order"=>"true",
												  "keyIn"=>"Valuta"));

	  $this->addField('weging',
													array("description"=>"weging",
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
  
    $this->addField('datumProvider',
                    array("description"=>"datum provider",
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

	function initModule()
	{
		$tst = new SQLman();
		$tst->tableExist($this->tableName,true);
		$tst->changeField($this->tableName,"datumVanaf",array("Type"=>"datetime","Null"=>false));
		$tst->changeField($this->tableName,"Fonds",array("Type"=>"varchar(60)","Null"=>false));
		$tst->changeField($this->tableName,"ISINCode",array("Type"=>"varchar(12)","Null"=>false));
		$tst->changeField($this->tableName,"msCategoriesoort",array("Type"=>"varchar(60)","Null"=>true));
		$tst->changeField($this->tableName,"msCategorie",array("Type"=>"varchar(60)","Null"=>true));
		$tst->changeField($this->tableName,"Valuta",array("Type"=>"varchar(3)","Null"=>false));
		$tst->changeField($this->tableName,"weging",array("Type"=>"double","Null"=>true));

	}
}
?>