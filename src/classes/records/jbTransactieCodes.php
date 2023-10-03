<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/06/12 06:56:08 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: jbTransactieCodes.php,v $
    Revision 1.3  2020/06/12 06:56:08  cvs
    call 8680

    Revision 1.2  2018/09/11 14:58:59  cvs
    q

    Revision 1.1  2018/05/23 13:18:26  cvs
    call 5912

    Revision 1.1  2016/04/04 08:22:13  cvs
    call 4712

    Revision 1.1  2015/12/01 08:56:03  cvs
    update 2540, call 4352



 		
 	
*/

class jbTransactieCodes extends Table
{
  /*
  * Object vars
  */
  var $tableName = "jbTransactieCodes";
  var $data = array();
  
  /*
  * Constructor
  */
  function jbTransactieCodes()
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
		($this->get("JBcode")=="")?$this->setError("JBcode",vt("Mag niet leeg zijn!")):true;
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
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "JB transactiecodes";
    $this->data['table']  = $this->tableName;
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

		$this->addField('JBcode',
													array("description"=>"JBcode",
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
                                              "A" =>  "A &nbsp;- Aankoop van stukken",
                                              "BEH" => "BEH - Beheer",
                                              "CPDV" => "CPDV - Coupon - Dividend",
                                              "D" => "D - Deponeringen",
                                              "E" => "E - Emissie",
                                              "KNBA" => "KNBA - Bankkosten",
                                              "KRUIS" => "KRUIS - kruisposten",
                                              "L" => "L - Lichting",
                                              "LOS" => "LOS - Lossing",
                                              "MUT" => "MUT - Geld mutaties",
                                              "RENTE" => "RENTE - Rente",
                                              "V" =>  "V &nbsp;- Verkoop van stukken",
                                              "-" => "-------------",
                                              "FEE_KNBA" => "FEE_KNBA - Bankkosten",
                                              "FEE_KOBU" => "FEE_KOBU - Kosten Buitenland",
                                              "FEE_KOST" => "FEE_KOST - Kosten",
                                              "FEE_VALK" => "FEE_VALK - Valuta kosten",
                                              "-" => "-------------",

                                              "NVT" =>"N.v.t."),
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }

  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"omschrijving",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"JBcode",array("Type"=>"varchar(10)","Null"=>false));
    $tst->changeField($this->tableName,"doActie",array("Type"=>"varchar(10)","Null"=>false));

  }
}



