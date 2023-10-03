<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 24 januari 2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/09/16 17:53:34 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: FondsParameterHistorie.php,v $
    Revision 1.3  2017/09/16 17:53:34  rvv
    *** empty log message ***

    Revision 1.2  2015/02/18 16:50:40  rvv
    *** empty log message ***

    Revision 1.1  2015/02/15 10:15:29  rvv
    *** empty log message ***

    Revision 1.1  2015/01/24 19:37:58  rvv
    *** empty log message ***

 		
 	
*/

class FondsParameterHistorie extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function FondsParameterHistorie()
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
    ($this->get("GebruikTot")=="")?$this->setError("GebruikTot",vt("Mag niet leeg zijn!")):true;

		$query  = "SELECT id FROM FondsParameterHistorie WHERE Fonds = '".$this->get("Fonds")."' AND GebruikTot = '".$this->get("GebruikTot")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		
		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("GebruikTot",vt("Fonds datum combinatie bestaat al."));
		}
    
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
    return checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "FondsParameterHistorie";
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

		$this->addField('Fonds',
													array("description"=>"Fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>'SELECT Fonds,Fonds FROM Fondsen ORDER BY Fonds',
													'select_query_ajax' => "SELECT Fonds,Fonds FROM Fondsen WHERE Fonds='{value}'",
                          "beperkt"=>true,
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('GebruikTot',
													array("description"=>"GebruikTot",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Rentedatum',
													array("description"=>"Coupondatum",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
                          "beperkt"=>true,
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

	  $this->addField('Renteperiode',
													array("description"=>"Renteperiode",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"default_value"=>12,
													"form_options"=>array(1,2,3,4,5,6,7,8,9,10,11,12),
													"form_size"=>"8",
													"form_type"=>"select",
                          "beperkt"=>true,
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('EersteRentedatum',
													array("description"=>"Eerste Coupondatum",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
                          "beperkt"=>true,
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('Lossingsdatum',
													array("description"=>"Lossingsdatum",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_size"=>"25",
													"form_type"=>"calendar",
                          "beperkt"=>true,
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
  	$this->addField('lossingskoers',
													array("description"=>"Lossingskoers",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_size"=>"8",
													"form_type"=>"text",
                          "beperkt"=>true,
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                                                  
		$this->addField('Rente30_360',
													array("description"=>"30/360 renteberekening",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"1",
                          "form_type"     => "selectKeyed",
                          "form_select_option_notempty" => true,
                          "form_options"                => array(0 => 'Act/365',1=>'30/360', 2 => 'Act/360',3=>'Act/Act'),
                          "beperkt"=>true,
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('variabeleCoupon',
													array("description"=>"Variabele coupon",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
                          "beperkt"=>true,
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('OblSoortFloater',
													array("description"=>"Soort Floater",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_size"=>"20",
													"form_type"=>"selectKeyed",
                          "beperkt"=>true,
                          "form_options"=>array("A"=>'Waarde A','B'=>'Waarde B','FixToFlo'=>'FixedToFloater','FloToFix'=>'FloaterToFixed'),
													"form_visible"=>true,
                          "list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true")); 
  
		$this->addField('inflatieGekoppeld',
													array("description"=>"Inflatie gekoppeld",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
                          "beperkt"=>true,
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                                                    
    $this->addField('OblDirtyPr',
													array("description"=>"Dirty Pr.",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
                          "beperkt"=>true,
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true")); 
                          
    $this->addField('OblPerpetual',
													array("description"=>"Perpetual",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
                          "beperkt"=>true,
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));   
                                                  
 		$this->addField('OblMemo',
													array("description"=>"Memo Obligaties",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
                          "beperkt"=>true,
													"form_size"=>"30",
													"form_rows"=>"2",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          
  	$this->addField('datumControleStatics',
													array("description"=>"Datum controle statics",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_size"=>"25",
													"form_type"=>"calendar",
                          "beperkt"=>true,
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
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