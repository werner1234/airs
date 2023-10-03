<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 29 november 2007
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2016/12/21 16:29:00 $
    File Versie         : $Revision: 1.7 $
 		
    $Log: fixDepotbankenPerVermogensbeheerder.php,v $
    Revision 1.7  2016/12/21 16:29:00  rvv
    *** empty log message ***

    Revision 1.6  2016/09/28 08:55:28  rvv
    *** empty log message ***

    Revision 1.5  2016/06/25 16:25:35  rvv
    *** empty log message ***

    Revision 1.4  2015/11/06 12:11:27  rvv
    *** empty log message ***

    Revision 1.3  2015/07/15 13:11:55  rvv
    *** empty log message ***

    Revision 1.2  2015/07/15 13:10:28  rvv
    *** empty log message ***

    Revision 1.1  2015/07/15 13:03:25  rm
    ordersV2


 		
 	
*/

class FixDepotbankenPerVermogensbeheerder extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function FixDepotbankenPerVermogensbeheerder()
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
		return true;
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
    $this->data['table']  = "fixDepotbankenPerVermogensbeheerder";
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

  	$this->addField('vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
 													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder  FROM Vermogensbeheerders ORDER BY Vermogensbeheerder",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));
    
    		$this->addField('depotbank',
													array("description"=>"Depotbank",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
 													"select_query"=>"SELECT Depotbank,Depotbank FROM Depotbanken ORDER BY Depotbank",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Depotbanken"));

	   	$this->addField('rekeningNrTonen',
													array("description"=>"rekeningnr tonen",
													"default_value"=>"",
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

		$this->addField('meervoudigViaFix',
										array("description"=>"Meervoudig via Fix",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));
  
    $this->addField('meervNominaalFIX',
                    array("description"=>"Meervoudig nominaal via Fix",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"center",
                          "list_search"=>true,
                          "list_order"=>"true"));
    
		$this->addField('nominaalViaFix',
										array("description"=>"Nominaal via Fix",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('fixDefaultAan',
										array("description"=>"Fix default aan",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('careOrderVerplicht',
										array("description"=>"Altijd Care",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));
  }
}
?>