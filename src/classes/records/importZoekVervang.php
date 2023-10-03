<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 7 december 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/10 10:35:18 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: importZoekVervang.php,v $
    Revision 1.3  2018/10/10 10:35:18  cvs
    call 7160

    Revision 1.2  2018/03/21 15:23:29  cvs
    call 6313

    Revision 1.1  2017/03/24 09:34:32  cvs
    call 5731

 		
 	
*/

class ImportZoekVervang extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  var $typeVervangArray =  array("a" => "hele veld",
                                 "z" => "vervang zoekbegrip",
                                 "l" => "vervang zoekbegrip + linkerdeel",
                                 "r" => "vervang zoekbegrip + rechterdeel");
  
  /*
  * Constructor
  */
  function ImportZoekVervang()
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
		($this->get("depotbank")=="")?$this->setError("depotbank",vt("Mag niet leeg zijn!")):true;
		($this->get("vermogensBeheerder")=="")?$this->setError("vermogensBeheerder",vt("Mag niet leeg zijn!")):true;

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
    $this->data['name']  = "zoek vervang bij import";
    $this->data['table']  = "importZoekVervang";
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
//"select_query"=>"SELECT Depotbank,Depotbank FROM Depotbanken ORDER BY Depotbank",
    $this->addField('depotbank',
                    array("description"=>"depotbank",
                          "default_value"=>"",
                          "db_size"=>"10",
                          "db_type"=>"varchar",
                          "form_options" => array("AAB"=>"AAB - ABNAMRO", "UBP" => "UBP"),
                          "form_type"=>"selectKeyed",
                          'form_select_option_notempty'=>true,
                          "form_size"=>"10",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"70",
                          "list_align"=>"center",
                          "list_search"=>false,
                          "list_order"=>false));

    $this->addField('vermogensBeheerder',
                    array("description"=>"vermogensBeheerder",
                          "default_value"=>"",
                          "db_size"=>"10",
                          "db_type"=>"varchar",
                          "select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder, concat(Vermogensbeheerder,' - ',naam) FROM Vermogensbeheerders ORDER BY Vermogensbeheerder",
                          "form_type"=>"selectKeyed",
                          'form_select_option_notempty'=>true,
                          "form_size"=>"10",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"50",
                          "list_align"=>"center",
                          "list_search"=>false,
                          "list_order"=>false));

		$this->addField('actief',
													array("description"=>"actief",
													"default_value"=> 1,
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"50",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>false));

		$this->addField('zoek',
													array("description"=>"zoek",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"300",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('vervang',
													array("description"=>"vervang",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"300",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('typeVervang',
													array("description"=>"typeVervang",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "form_select_option_notempty" => true,
                          "form_options" => $this->typeVervangArray,
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('veldAanduiding',
													array("description"=>"veldAanduiding",
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

		$this->addField('memo',
													array("description"=>"memo",
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



  }
}
?>