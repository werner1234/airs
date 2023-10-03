<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 september 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/07/03 15:42:47 $
    File Versie         : $Revision: 1.6 $
 		
    $Log: doorkijk_koppelingPerVermogensbeheerder.php,v $
    Revision 1.6  2019/07/03 15:42:47  rvv
    *** empty log message ***

    Revision 1.5  2018/07/12 05:48:40  rvv
    *** empty log message ***

    Revision 1.4  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.3  2017/12/16 18:34:43  rvv
    *** empty log message ***

    Revision 1.2  2017/12/04 14:47:20  cvs
    vertaling verwijderd

    Revision 1.1  2017/12/04 10:39:16  cvs
    Update van Ben ingelezen dd 4-12-2017

 		
 	
*/

class doorkijk_koppelingPerVermogensbeheerder extends Table
{
  /*
  * Object vars
  */
  var $tableName = "doorkijk_koppelingPerVermogensbeheerder";
  var $data = array();
  
  /*
  * Constructor
  */
  function doorkijk_koppelingPerVermogensbeheerder()
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
		($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		($this->get("systeem")=="")?$this->setError("systeem",vt("Mag niet leeg zijn!")):true;
		($this->get("doorkijkCategoriesoort")=="")?$this->setError("doorkijkCategoriesoort",vt("Mag niet leeg zijn!")):true;
		($this->get("doorkijkCategorie")=="")?$this->setError("doorkijkCategorie",vt("Mag niet leeg zijn!")):true;
		($this->get("bronKoppeling")=="")?$this->setError("bronKoppeling",vt("Mag niet leeg zijn!")):true;
		$query  = "SELECT id 
                   FROM doorkijk_koppelingPerVermogensbeheerder 
                   WHERE Vermogensbeheerder     = '" . $this->get("Vermogensbeheerder")     . "' AND 
                         systeem                = '" . $this->get("systeem")                . "' AND
                         doorkijkCategoriesoort = '" . $this->get("doorkijkCategoriesoort") . "' AND 
                         bronKoppeling          = '" . $this->get("bronKoppeling")          . "'
                   ";
		//debug($query);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") != $data['id'])
		{
			$this->setError("Vermogensbeheerder", vtb("%s - %s - %s - %s bestaat al", array($this->get("Vermogensbeheerder"), $this->get("systeem"), $this->get("doorkijkCategoriesoort"), $this->get("bronKoppeling"))));
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
    $this->data['name']  = "Doorkijk_koppelingPerVermogensbeheerder";
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
													"list_order"=>"false"));

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
													"list_order"=>"false"));

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
													"list_order"=>"false"));

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
													"list_order"=>"false"));

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
													"list_order"=>"false"));

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
													"keyIn"=>"Vermogensbeheerders",
													"list_order"=>"true"));

		$this->addField('systeem',
													array("description"=>"systeem",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_options" => array ("AIRS" => "Airs",
														                     "MS"   => "Extern"),

													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('doorkijkCategoriesoort',
													array("description"=>"doorkijkCategoriesoort",
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
													"list_order"=>"true"));

		$this->addField('doorkijkCategorie',
													array("description"=>"doorkijkCategorie",
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
													"keyIn"=>"doorkijk_categoriePerVermogensbeheerder"));

	  	$this->addField('bronKoppeling',
												  array("description"=>"bronKoppeling",
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
														"keyCondition"=>'doorkijkCategoriesoort',
														'keyUpdateWhere'=>array('systeem'=>'airs'),
														'keyIn'=>'Beleggingscategorien,Beleggingssectoren,Regios,Valutas'
													));



  }
	function initModule()
	{
		$tst = new SQLman();
		$tst->tableExist($this->tableName,true);
		$tst->changeField($this->tableName,"Vermogensbeheerder",array("Type"=>"varchar(10)","Null"=>false));
		$tst->changeField($this->tableName,"systeem",array("Type"=>"varchar(4)","Null"=>false));
		$tst->changeField($this->tableName,"doorkijkCategoriesoort",array("Type"=>"varchar(60)","Null"=>true));
		$tst->changeField($this->tableName,"doorkijkCategorie",array("Type"=>"varchar(60)","Null"=>true));
		$tst->changeField($this->tableName,"bronKoppeling",array("Type"=>"varchar(30)","Null"=>true));

	}
}
?>