<?php
/* 	
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/08/31 15:18:50 $
 		File Versie					: $Revision: 1.9 $
*/

class Valuta extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Valuta()
  {
    $this->defineData();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}
	
	function checkAccess($type)
	{
		return checkAccess($type);
	}
  
	
	function validate()
	{
		($this->get("Valuta")=="")?$this->setError("Valuta",vt("Mag niet leeg zijn!")):true;
		($this->get("ValutaImportCode")=="")?$this->setError("ValutaImportCode",vt("Mag niet leeg zijn!")):true;
		(!is_numeric($this->get("Koerseenheid")))?$this->setError("Koerseenheid",vt("Moet een getal zijn.")):true;
		(!is_numeric($this->get("Afdrukvolgorde")))?$this->setError("Afdrukvolgorde",vt("Moet een getal zijn.")):true;

		$query  = "SELECT id FROM Valutas WHERE Valuta = '".$this->get("Valuta")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();
		
		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Valuta",vtb("%s bestaat al", array($this->get("Valuta"))));
		}
		
		if(count($this->error) > 0 )
			return false;
			
		return true;
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "Valutas";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_size"=>"11",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Valuta',
													array("description"=>"Valuta",
													"db_size"=>"4",
													"db_type"=>"char",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"key_field"=>true));

		$this->addField('Omschrijving',
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

		$this->addField('ValutaImportCode',
													array("description"=>"Valuta-importcode",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_size"=>"10",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Valutateken',
													array("description"=>"Valutateken",
													"db_size"=>"2",
													"db_type"=>"char",
													"form_size"=>"2",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Koerseenheid',
													array("description"=>"Koerseenheid",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_size"=>"20",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Afdrukvolgorde',
													array("description"=>"Afdrukvolgorde",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AABgrens',
													array("description"=>"AAB-grens",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AABcorrectie',
													array("description"=>"AAB-correctie",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
													
		$this->addField('AABcorrectie',
													array("description"=>"AAB-correctie",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('TermijnValuta',
													array("description"=>"Termijnvaluta",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

  }
}
?>