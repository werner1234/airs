<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 16 november 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/02/25 17:23:18 $
    File Versie         : $Revision: 1.8 $
 		
    $Log: CRM_naw_cashflow.php,v $
    Revision 1.8  2015/02/25 17:23:18  rvv
    *** empty log message ***

    Revision 1.7  2015/02/22 09:45:11  rvv
    *** empty log message ***

    Revision 1.6  2014/05/29 12:14:50  rvv
    *** empty log message ***

    Revision 1.5  2014/05/25 14:33:55  rvv
    *** empty log message ***

    Revision 1.4  2014/04/05 15:23:22  rvv
    *** empty log message ***

    Revision 1.3  2014/03/29 16:24:32  rvv
    *** empty log message ***

    Revision 1.2  2013/11/30 14:19:35  rvv
    *** empty log message ***

    Revision 1.1  2013/11/16 16:05:47  rvv
    *** empty log message ***

 		
 	
*/

class CRM_naw_cashflow extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function CRM_naw_cashflow()
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

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
    return true;
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
    $this->data['table']  = "CRM_naw_cashflow";
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

		$this->addField('rel_id',
													array("description"=>"rel_id",
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
  
    $jaren=array();
    for($i=2010;$i<2100;$i++)
      $jaren[$i."-00-00"]=$i;
                          
		$this->addField('datum',
													array("description"=>"datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"text",
													"form_type"=>"selectKeyed",
                          "form_options"=>$jaren,
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('totDatum',
													array("description"=>"doorlopend t/m",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"text",
													"form_type"=>"selectKeyed",
                          "form_options"=>array_merge(array('9999-00-00'=>'Doeljaar'),$jaren),
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('bedrag',
													array("description"=>"bedrag",
													"default_value"=>"",
													"db_size"=>"11,2",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"11,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('indexatie',
													array("description"=>"% indexatie",
													"default_value"=>"",
													"db_size"=>"11,2",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"11,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
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
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
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



  }
}
?>