<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 november 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/03/18 18:06:46 $
    File Versie         : $Revision: 1.7 $
 		
    $Log: signaleringPortRend.php,v $
    Revision 1.7  2020/03/18 18:06:46  rvv
    *** empty log message ***

    Revision 1.6  2019/08/11 07:05:22  rvv
    *** empty log message ***

    Revision 1.5  2019/08/10 17:23:57  rvv
    *** empty log message ***

    Revision 1.4  2018/12/07 14:25:13  rm
    Toevoegen rendementDetails

    Revision 1.3  2018/03/21 16:58:11  rvv
    *** empty log message ***

    Revision 1.2  2017/12/09 17:47:37  rvv
    *** empty log message ***

    Revision 1.1  2017/11/25 20:20:06  rvv
    *** empty log message ***

 		
 	
*/

class SignaleringPortRend extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function SignaleringPortRend()
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
    return checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "signaleringPortRend";
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

		$this->addField('portefeuille',
													array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"24",
													"form_visible"=>true,
													"form_extra"=>'readonly',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Portefeuilles"));

		$this->addField('periode',
													array("description"=>"Periode",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"varchar",
												  "form_type"=>"selectKeyed",
													"form_options"=>array('QTD'=>'QTD','MTD'=>'MTD'),
													"form_extra"=>'disabled',
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('signaleringsPercentage',
													array("description"=>"SignaleringsPercentage",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"form_extra"=>'readonly',
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('datum',
													array("description"=>"Datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"form_extra"=>'disabled',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('status',
													array("description"=>"Status",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"tinyint",
											  	"form_type"=>"selectKeyed",
													"form_extra"=>'',
												  "form_options"=>array(0=>'Nieuw',1=>'Per mail Airs',3=>'Zelf verstuurd',4=>'Negeren',2=>'Verwijderd'),
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('toelichting',
										array("description"=>"Toelichting",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"50",
													"form_rows"=>"4",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('rendementDetails',
										array("description"=>"rendementDetails",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"50",
													"form_rows"=>"4",
													"form_visible"=>false,
													"list_width"=>"150",
													"list_visible"=>false,
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