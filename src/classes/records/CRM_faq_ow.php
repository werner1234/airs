<?php
/* 	
    AE-ICT CODEX source module versie 1.2, 21 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2006/01/05 16:00:34 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: CRM_faq_ow.php,v $
    Revision 1.1  2006/01/05 16:00:34  cvs
    *** empty log message ***

    Revision 1.1.1.1  2005/12/06 18:20:55  cvs
    no message

    Revision 1.1  2005/11/21 16:35:06  cvs
    *** empty log message ***

 		
 	
*/

class Faq_ow extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Faq_ow()
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
		($this->get("onderwerp")=="")?$this->setError("onderwerp",vt("Mag niet leeg zijn!")):true;

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
    $this->data['name']  = "Kennisbank onderwerpen";
    $this->data['table']  = "CRM_faq_ow";
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

		$this->addField('onderwerp',
													array("description"=>"onderwerp",
													"default_value"=>"",
													"db_size"=>"30",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"30",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>