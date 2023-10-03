<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 20 juli 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2014/07/20 13:05:31 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: VragenVragenlijsten.php,v $
    Revision 1.1  2014/07/20 13:05:31  rvv
    *** empty log message ***

 		
 	
*/

class VragenVragenlijsten extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function VragenVragenlijsten()
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
		$DB = new DB();
		$query  = "SELECT id FROM VragenVragenlijsten WHERE omschrijving = '".$this->get("omschrijving")."' ";
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
			$this->setError("omschrijving", vtb("%s bestaat al", array($this->get("omschrijving"))));
      
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
		if($_SESSION['usersession']['superuser'])
		  return true;
		else
		{
		  switch ($type)
		  {
		    case "edit":
		      if($this->get('debiteur')==0)
		        return true;
          return GetCRMAccess(1);
          break;
        case "delete":
          return GetCRMAccess(2);
          break;
        default:
          return false;
          break;
      }
		}
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "VragenVragenlijsten";
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

		$this->addField('omschrijving',
													array("description"=>"Omschrijving",
													"default_value"=>"",
													"db_size"=>"200",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		
    $this->addField('titel',
                    array("description"=>"Titel",
                          "default_value"=>"",
                          "db_size"=>"255",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"50",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
    $this->addField('tekstRisicoprofiel',
                    array("description"=>"Tekst risicoprofiel tonen",
                          "db_size"=>"1",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_visible"=>true,"list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "crm_readonly"=>false));
  
    $this->addField('extraInfo',
                    array("description"=>"Extra info",
                          "db_size"=>"255",
                          "db_type"=>"text",
                          "form_type"=>"textarea",
                          "form_size"=>"50",
                          "form_rows"=>"4",
                          "form_visible"=>true,"list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"right",
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