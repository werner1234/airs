<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 14 mei 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/19 08:54:23 $
    File Versie         : $Revision: 1.5 $
 		
    $Log: tijdelijkeRecon.php,v $
    Revision 1.5  2018/10/19 08:54:23  cvs
    call 7167

    Revision 1.4  2015/11/25 08:05:43  cvs
    *** empty log message ***

    Revision 1.3  2015/04/13 13:22:16  cvs
    *** empty log message ***

    Revision 1.2  2014/10/15 08:20:49  cvs
    *** empty log message ***

    Revision 1.1  2014/08/06 12:39:18  cvs
    *** empty log message ***

 		
 	
*/

class TijdelijkeRecon extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function TijdelijkeRecon()
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
   /*
	 $level = getMyLevel("Default");
	  switch ($type) 
	  {
	  	case "edit":
	  		return ($level >=3 )?true:false;
	  		break;
	  	case "delete":
	  		return ($level >=7 )?true:false;
	  		break;
	  	default:
	  	  return false;
	  		break;
	  }
    */
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "tijdelijk recon overzicht";
    $this->data['table']  = "tijdelijkeRecon";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
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
													"form_visible"=>false,
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
													"form_visible"=>false,
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
													"form_visible"=>false,
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
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('vermogensbeheerder',
													array("description"=>"vermogensbeheerder",
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

		$this->addField('depotbank',
													array("description"=>"depotbank",
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
    
		$this->addField('Accountmanager',
													array("description"=>"Accountmanager",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));        

 		$this->addField('client',
													array("description"=>"client",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('portefeuille',
													array("description"=>"portefeuille",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('rekeningnummer',
													array("description"=>"rekeningnummer",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));


		$this->addField('cashPositie',
													array("description"=>"cashPositie",
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

		$this->addField('isinCode',
													array("description"=>"isinCode",
													"default_value"=>"",
													"db_size"=>"26",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"26",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('valuta',
													array("description"=>"valuta",
													"default_value"=>"",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
    

    		$this->addField('positieBank',
													array("description"=>"positieBank",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.4f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('positieAirs',
													array("description"=>"positieAirs",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.4f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		
		$this->addField('verschil',
													array("description"=>"verschil",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('fondsCodeMatch',
													array("description"=>"Match",
													"default_value"=>"",
													"db_size"=>"75",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"75",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('positieAirsGisteren',
													array("description"=>"positieAirsGisteren",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    
		$this->addField('fonds',
													array("description"=>"fonds",
													"default_value"=>"",
													"db_size"=>"75",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"75",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

  		$this->addField('fondsImportcode',
													array("description"=>"fondsImportcode",
													"default_value"=>"",
													"db_size"=>"16",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"16",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));      
    

		$this->addField('depotbankFondsCode',
													array("description"=>"bankCode",
													"default_value"=>"",
													"db_size"=>"26",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"26",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('fileBankCode',
													array("description"=>"fileBankCode",
													"default_value"=>"",
													"db_size"=>"26",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"26",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


                      
		$this->addField('Einddatum',
													array("description"=>"Einddatum",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));                          
		$this->addField('reconDatum',
													array("description"=>"reconDatum",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));   
		$this->addField('koersDatum',
													array("description"=>"koersDatum",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));   
    
    		$this->addField('koers',
													array("description"=>"koers",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.6f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('batch',
													array("description"=>"batch",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));                          
    
		$this->addField('importCode',
													array("description"=>"importCode",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

$this->addField('Opmerking',
													array("description"=>"Opmerking",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


  }
}
?>