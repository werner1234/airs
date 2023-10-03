<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/01/24 15:07:55 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: cawTransactieCodes.php,v $
    Revision 1.2  2020/01/24 15:07:55  cvs
    call 8226

    Revision 1.1  2019/11/06 07:37:31  cvs
    update 6-11-2019

    Revision 1.1  2019/10/09 09:53:06  cvs
    call 8025

    Revision 1.1  2019/05/06 09:16:42  cvs
    call 7739


 	
*/

class cawTransactieCodes extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  var $tableName;
  /*
  * Constructor
  */
  function cawTransactieCodes()
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
    $tst->changeField($this->tableName,"bankCode",array("Type"=>"varchar(30)","Null"=>false));
    $tst->changeField($this->tableName,"doActie",array("Type"=>"varchar(10)","Null"=>false));

  }

	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']     = "cawTransactieCodes";
    $this->data['table']    = "cawTransactieCodes";
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
													"db_size"=>"30",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"30",
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
                            "A"        =>  "A &nbsp;- Aankoop van stukken",
                            "BEH"      => "BEH - Beheerfee",
                            "D"        => "D - Deponering",
                            "DIV"      => "DIV - Contant dividend",
                            "DIVBE"    => "DIVBE - Dividendbelasting",
                            "Dnul"     => "Dnul - Deponering met fondskoers 0",
                            "FX"       => "FX - Spot transactie",
                            "KNBA"     => "KNBA - Bankkosten",
                            "KOBU"     => "KOBU - Kosten buitenland",
                            "KO"       => "KO - Portefeuillekosten",
                            "L"        => "L - Lichting",
                            "Lnul"     => "Lnul - Lichting met fondskoers 0",
                            "MUT"      => "MUT - Geld/Stukken mutaties",
                            "R"        =>  "R &nbsp;- Rente op geldrekeningen",
                            "SA"       => "SA &nbsp;- Sluiting aankoop",
                            "SV"       => "SV &nbsp;- Sluiting verkoop",
                            "V"        => "V &nbsp;- Verkoop van stukken",
                            ""         => "------------------------------",
                            "EXE"      =>"N.v.t.",
                            "NVT"      =>"N.v.t."),

													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));



  }
}
