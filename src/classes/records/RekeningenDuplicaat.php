<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 7 februari 2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/05/12 15:29:35 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: RekeningenDuplicaat.php,v $
    Revision 1.3  2018/05/12 15:29:35  rvv
    *** empty log message ***

    Revision 1.2  2015/03/04 16:45:30  rvv
    *** empty log message ***

    Revision 1.1  2015/02/07 20:34:20  rvv
    *** empty log message ***

 		
 	
*/

class RekeningenDuplicaat extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function RekeningenDuplicaat()
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
	  if($this->get("Rekening") == $this->get("RekeningDuplicaat"))
    {
      $this->setError("Rekening",vt("Kan een rekening niet aan zichzelf koppelen"));
      $this->setError("RekeningDuplicaat",vt("Kan een rekening niet aan zichzelf koppelen"));
    }
    
    ($this->get("Rekening")=="")?$this->setError("Rekening",vt("Mag niet leeg zijn!")):true;
    ($this->get("RekeningDuplicaat")=="")?$this->setError("RekeningDuplicaat",vt("Mag niet leeg zijn!")):true;
    $query  = "SELECT id FROM RekeningenDuplicaat WHERE Rekening = '".$this->get("Rekening")."' AND RekeningDuplicaat = '".$this->get("RekeningDuplicaat")."'";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Rekening",vtb("%s bestaat al", array($this->get("Rekening"))));
      $this->setError("RekeningDuplicaat",vtb("%s bestaat al", array($this->get("RekeningDuplicaat"))));
		}


		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
     return checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "RekeningenDuplicaat";
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

		$this->addField('Rekening',
													array("description"=>"Rekening",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Rekening,concat(Rekening,' | inactief: ',inactief) FROM Rekeningen WHERE consolidatie=0 ORDER BY Rekening",
													'select_query_ajax' => "SELECT Rekening,concat(Rekening,' | inactief: ',inactief)  FROM Rekeningen WHERE consolidatie=0 AND Rekening='{value}'",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Rekeningen"));

		$this->addField('RekeningDuplicaat',
													array("description"=>"RekeningDuplicaat",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Rekening,concat(Rekening,' | inactief: ',inactief) FROM Rekeningen WHERE consolidatie=0 ORDER BY Rekening",
													'select_query_ajax' => "SELECT Rekening,concat(Rekening,' | inactief: ',inactief)  FROM Rekeningen WHERE consolidatie=0 AND Rekening='{value}'",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Rekeningen"));

		$this->addField('Memo',
													array("description"=>"Memo",
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

		$this->addField('actief',
													array("description"=>"actief",
													"default_value"=>"1",
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