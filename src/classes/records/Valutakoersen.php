<?php
/* 	
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/11/11 17:16:28 $
 		File Versie					: $Revision: 1.12 $
 				
*/

class Valutakoersen extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Valutakoersen()
  {
    $this->defineData();
    $this->setDefaults();
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
		($this->get("Koers")=="")?$this->setError("Koers",vt("Mag niet leeg zijn!")):true;
		(!isNumeric($this->get("Koers")))?$this->setError("Koers",vt("Moet een getal zijn.")):true;
		
    
    $cfg=new AE_config();
		$lockDatum=$cfg->getData('fondskoersLockDatum');
		if($this->get('id') && db2jul($lockDatum) >= db2jul($this->get('Datum')))
		{
		  $this->setError("Datum", vtb("Het aanpassen van koersen met een datum <= '%s' is niet meer mogelijk.", array($lockDatum)));
		}
    
    $this->set('oorspKrsDt',$this->get("Datum"));
    
		$query = " SELECT id,Koers FROM Valutakoersen WHERE ".
						 " Valuta = '".$this->get("Valuta")."' AND ".
						 " Datum = '".$this->get("Datum")."' ";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$data = $DB->NextRecord();
		if($DB->Records() > 0 && $this->get("id") <> $data['id'])
		{
			$this->setError("Datum", vtb("Op deze datum is al een Valuta toegevoegd (%s)", array($data['Koers'])));
		}
		
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "Valutakoersen";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
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
													"form_type"=>"select",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Valutas"));

		$this->addField('Datum',
													array("description"=>"Datum",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
                          "form_class"=> "AIRSdatepicker AIRSdatepickerPreviousMonth",
													"form_extra"=>" onchange=\"date_complete(this);\"",  
													"default_value"=>"now()",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('oorspKrsDt',
													array("description"=>"Oorspronkelijke koersdatum",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"default_value"=>"lastworkday",
													"form_type"=>"calendar",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Koers',
													array("description"=>"Koers",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_format"=>"%01.8f",
													"list_format"=>"%01.8f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
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