<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 24 juli 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/12/16 17:23:49 $
    File Versie         : $Revision: 1.6 $
 		
    $Log: StandaardVeldVulling.php,v $
    Revision 1.6  2015/12/16 17:23:49  rvv
    *** empty log message ***

    Revision 1.5  2014/08/09 14:44:18  rvv
    *** empty log message ***

    Revision 1.4  2014/07/30 09:49:30  rvv
    *** empty log message ***

    Revision 1.3  2014/07/27 11:25:09  rvv
    *** empty log message ***

    Revision 1.2  2013/11/27 16:24:33  rvv
    *** empty log message ***

    Revision 1.1  2013/07/24 15:44:27  rvv
    *** empty log message ***

 		
 	
*/

class StandaardVeldVulling extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function StandaardVeldVulling()
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
		($this->get("tabel")=="")?$this->setError("tabel",vt("Mag niet leeg zijn!")):true;
		($this->get("veld")=="")?$this->setError("veld",vt("Mag niet leeg zijn!")):true;
		($this->get("waarde")=="")?$this->setError("waarde",vt("Mag niet leeg zijn!")):true;

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
	
  
  //foreach ($__appvar['tabelObjecten'] as $tabel)
	/*
  * Table definition
  */
  function defineData()
  {
    global $__appvar;
    $this->data['name']  = "";
    $this->data['table']  = "StandaardVeldVulling";
    $this->data['identity'] = "id";

    $tabellen=$__appvar['tabelObjecten'];
    natcasesort($tabellen);
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

		$this->addField('tabel',
													array("description"=>"tabel",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_type"=>"select",
                          "form_options"=>$tabellen,
													"form_size"=>"50",
                          "form_extra"=>"onchange='javascript:laadVelden();'",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('veld',
													array("description"=>"veld",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "select_query"=>'SELECT veld,veld FROM StandaardVeldVulling GROUP BY veld',
													"form_size"=>"50",
													"form_visible"=>true,
                          "form_extra"=>"onchange='javascript:laadWaarden();'",
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('waarde',
													array("description"=>"waarde",
													"default_value"=>"",
													"db_size"=>"200",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"50",
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