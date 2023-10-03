<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/04/29 10:34:29 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: abnV2TransactieCodes.php,v $
    Revision 1.2  2019/04/29 10:34:29  cvs
    call 7746

    Revision 1.1  2018/11/23 13:31:48  cvs
    call 7047

    Revision 1.4  2018/06/15 08:04:42  cvs
    call 6572

    Revision 1.3  2018/06/15 07:49:07  cvs
    call 6063

    Revision 1.2  2015/06/02 06:52:13  cvs
    *** empty log message ***

    Revision 1.1  2015/04/13 13:22:16  cvs
    *** empty log message ***

    Revision 1.1  2014/11/05 12:53:50  cvs
    dbs 2751

 		
 	
*/

class ABNv2TransactieCodes extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  var $tableName;
  /*
  * Constructor
  */
  function ABNv2TransactieCodes()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
    $this->initModule();
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
		($this->get("bankCode")=="")?$this->setError("bankCode",vt("Mag niet leeg zijn!")):true;
		($this->get("omschrijving")=="")?$this->setError("omschrijving",vt("Mag niet leeg zijn!")):true;

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

  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"omschrijving",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"bankCode",array("Type"=>"varchar(10)","Null"=>false));
    $tst->changeField($this->tableName,"doActie",array("Type"=>"varchar(10)","Null"=>false));

  }

	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']     = "ABN v2 transactiecodes";
    $this->data['table']    = "abnV2TransactieCodes";
    $this->tableName        = $this->data['table'];
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
													"list_visible"=>false,
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
													"list_visible"=>false,
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
													"list_visible"=>false,
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
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bankCode',
													array("description"=>"bankCode",
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

		$this->addField('omschrijving',
													array("description"=>"omschrijving",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('doActie',
													array("description"=>"doActie",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "form_options" => array(
                                "A"     =>"A - aankoop van stukken",
                                "AO"    =>"AO - aankoop van opties",
                                "CD"    =>"CD - Contant dividend",
                                "CR"    =>"CR - betaling couponrente",
                                "D_nul" =>"D_nul - deponering van stukken met koers 0",
                                "D"     =>"D - deponering van stukken",
                                "DRIP"  => "DRIP - dividend of deponering (1045261)",
                                "DVP"   =>"DVP - Delivery versus Payment",
                                "KOBU"  =>"KOBU - kosten buitenland",
                                "L"     =>"L - lichting van stukken",
                                "L_nul" =>"L_nul - lichting van stukken met koers 0",
                                "RVP"   =>"RVP - Receive versus Payment",
                                "V"     =>"V - verkoop van stukken",
                                "VO"    =>"VO - verkoop van opties",
                                "NVT"     =>"N.v.t.",
                                ""        => "------------------------------",
                            ),

													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));



  }
}
