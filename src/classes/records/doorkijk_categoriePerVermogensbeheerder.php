<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 6 oktober 2017
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2019/10/24 14:08:50 $
    File Versie         : $Revision: 1.8 $
 		
    $Log: doorkijk_categoriePerVermogensbeheerder.php,v $
    Revision 1.8  2019/10/24 14:08:50  rm
    8206

    Revision 1.7  2019/09/04 15:27:19  rvv
    *** empty log message ***

    Revision 1.6  2019/04/03 16:00:44  rvv
    *** empty log message ***

    Revision 1.5  2019/03/02 18:26:45  rvv
    *** empty log message ***

    Revision 1.4  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.3  2017/12/16 18:34:43  rvv
    *** empty log message ***

    Revision 1.2  2017/12/04 15:09:19  cvs
    vt( verwijderen

    Revision 1.1  2017/12/04 10:39:16  cvs
    Update van Ben ingelezen dd 4-12-2017

 		
 	
*/

class doorkijk_categoriePerVermogensbeheerder extends Table
{
  /*
  * Object vars
  */
  var $tableName = "doorkijk_categoriePerVermogensbeheerder";
  var $data = array();
  
  /*
  * Constructor
  */
  function doorkijk_categoriePerVermogensbeheerder()
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
	{   //debug($_POST);
		$kleuren = ($_POST['grafiekKleur']);
		//debug( $kleuren[0]);
		$kleur =  $kleuren[0]+  $kleuren[1] + $kleuren[2];

		if($kleur < 0 || $kleur > 765) $this->setError("grafiekKleur",vt("Kleurcodes buiten range 0-255!"));

		($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		($this->get("doorkijkCategoriesoort")=="")?$this->setError("doorkijkCategoriesoort",vt("Mag niet leeg zijn!")):true;
		($this->get("doorkijkCategorie")=="")?$this->setError("doorkijkCategorie",vt("Mag niet leeg zijn!")):true;

		$query  = "SELECT id 
                   FROM doorkijk_categoriePerVermogensbeheerder 
                   WHERE Vermogensbeheerder = '" . $this->get("Vermogensbeheerder") . "' AND 
                         doorkijkCategoriesoort = '" . $this->get("doorkijkCategoriesoort") . "' AND 
                         doorkijkCategorie = '" . $this->get("doorkijkCategorie") . "'
                   ";
		//debug($query);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") != $data['id'])
		{
			$this->setError("Vermogensbeheerder", vtb('%s - %s - %s bestaat al', array($this->get("Vermogensbeheerder"), $this->get("doorkijkCategoriesoort"), $this->get("doorkijkCategorie"))));
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
    $this->data['name']  = "";
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

		$this->addField('Vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder ",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));

		$this->addField('doorkijkCategoriesoort',
													array("description"=>"doorkijkCategoriesoort",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT DISTINCT msCategoriesoort, msCategoriesoort
																		FROM doorkijk_msCategoriesoort",
                          "form_extra"=>"onchange='checkDoorkijkCategoriesoort();'",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('doorkijkCategorie',
													array("description"=>"doorkijkCategorie",
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
													"extra_keys"=>array('Vermogensbeheerder'),
													"key_field"=>true));

		$this->addField('afdrukVolgorde',
													array("description"=>"afdrukVolgorde",
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

		$this->addField('grafiekKleur',
													array("description"=>"grafiekKleur",
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
  
    $this->addField('min',
                    array("description"=>"Min (vanaf,inclusief)",
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
  
    $this->addField('max',
                    array("description"=>"Max (tot)",
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

  }
	
	function initModule()
	{
		$tst = new SQLman();
		$tst->tableExist($this->tableName,true);
		$tst->changeField($this->tableName,"Vermogensbeheerder",array("Type"=>"varchar(10)","Null"=>true));
		$tst->changeField($this->tableName,"doorkijkCategoriesoort",array("Type"=>"varchar(60)","Null"=>true));
		$tst->changeField($this->tableName,"doorkijkCategorie",array("Type"=>"varchar(60)","Null"=>true));
		$tst->changeField($this->tableName,"afdrukVolgorde",array("Type"=>"tinyint","Null"=>true));
		$tst->changeField($this->tableName,"grafiekKleur",array("Type"=>"text","Null"=>true));
    $tst->changeField($this->tableName,"min",array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,"max",array("Type"=>"double","Null"=>false));
    
  }
}
?>