<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 9 augustus 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/12/16 18:34:43 $
    File Versie         : $Revision: 1.8 $
 		
    $Log: veldopmaak.php,v $
    Revision 1.8  2017/12/16 18:34:43  rvv
    *** empty log message ***

    Revision 1.7  2015/11/08 16:39:40  rvv
    *** empty log message ***

    Revision 1.6  2014/11/01 22:08:02  rvv
    *** empty log message ***

    Revision 1.5  2014/09/14 15:16:18  rvv
    *** empty log message ***

    Revision 1.4  2014/08/30 16:23:34  rvv
    *** empty log message ***

    Revision 1.3  2014/08/13 15:52:32  rvv
    *** empty log message ***

    Revision 1.2  2014/08/11 10:07:59  rvv
    *** empty log message ***

    Revision 1.1  2014/08/09 14:44:18  rvv
    *** empty log message ***

 		
 	
*/
//ini_set('max_execution_time',10);
class Veldopmaak extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Veldopmaak()
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
		  return GetCRMAccess(2);
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    global $__appvar;
    $this->data['name']  = "";
    $this->data['table']  = "veldopmaak";
    $this->data['identity'] = "id";


    //$tabellen=$__appvar['tabelObjecten'];
    //natsort($tabellen);
    $tabellen=array('Naw'=>'NAW','CRM_naw_adressen'=>'adressen','CRM_naw_kontaktpersoon'=>'kontaktpersonen','CRM_naw_rekeningen'=>'rekeningen');
    
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
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "form_options"=>$tabellen,
                          "form_extra"=>"onchange='javascript:laadVelden();'",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('veld',
													array("description"=>"veld",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "select_query"=>'SELECT veld,veld FROM veldopmaak GROUP BY veld',
													"form_size"=>"50",
													"form_visible"=>true,
                          "form_extra"=>"onchange='javascript:laadWaarden();'",
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

 		$this->addField('uitlijning',
													array("description"=>"uitlijning",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"varchar",
													"form_options"=>array('L'=>'Links','R'=>'Rechts','C'=>'Gecentreerd'),
													"form_type"=>"selectKeyed",
													"form_size"=>"60",
													"form_visible"=>true,
													"form_select_option_notempty"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                       
 		$this->addField('getalformat',
													array("description"=>"getalformat",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"varchar",
													"form_options"=>array('0d'=>'hele getallen','2d'=>'twee decimalen'),
													"form_type"=>"selectKeyed",
													"form_size"=>"60",
													"form_visible"=>true,
													"form_select_option_notempty"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('headerBreedte',
													array("description"=>"lijst header breedte",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('weergaveBreedte',
													array("description"=>"formulier weergave breedte",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
 
 		$this->addField('aantalRegels',
													array("description"=>"memoveld aantal regels",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('nietLeeg',
													array("description"=>"Verplichte vulling",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                                                    
  		$this->addField('formExtraTxt',
													array("description"=>"extra veld formule",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"list_width"=>"150",
													"form_type"=>"textarea",
													"form_size"=>"100",
													"form_rows"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Algemeen"));
                           
  		$this->addField('formExtra',
													array("description"=>"extra veld code",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"list_width"=>"150",
													"form_type"=>"textarea",
													"form_size"=>"100",
													"form_rows"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Algemeen"));
                                                                                                     
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