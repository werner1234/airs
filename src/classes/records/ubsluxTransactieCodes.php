<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/06/09 09:01:13 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: ubsluxTransactieCodes.php,v $
    Revision 1.3  2020/06/09 09:01:13  cvs
    call 8413

    Revision 1.2  2020/03/02 13:58:28  cvs
    call 8413

    Revision 1.1  2019/12/11 10:57:16  cvs
    call 7606


 	
*/

class UbsluxTransactieCodes extends Table
{
  /*
  * Object vars
  */





  var $data = array();
  
  /*
  * Constructor
  */
  function UbsluxTransactieCodes()
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
		($this->get("bankCode")=="")?$this->setError("ubsluxCode",vt("Mag niet leeg zijn!")):true;
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
    $sqlman = new SQLman();
    $sqlman->tableExist($this->data['table'],true);
    $sqlman->changeField($this->data['table'],"bankCode",array("Type"=>" varchar(40)","Null"=>false));
    $sqlman->changeField($this->data['table'],"omschrijving",array("Type"=>" varchar(50)","Null"=>false));
    $sqlman->changeField($this->data['table'],"doActie",array("Type"=>" varchar(10)","Null"=>false));

  }


	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "UBSLUX transactiecodes";
    $this->data['table']  = "ubsluxTransactieCodes";
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
													array("description"=>"UBSLUX code",
													"default_value"=>"",
													"db_size"=>"40",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"40",
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
                                "A"       =>  "A &nbsp;- Aankoop van stukken",
                                "D"       => "D - Deponering van stukken",
                                "DIV"     => "DIV - Dividend",
                                "GELDMUT" => "GELDMUT - Geld mutaties",
                                "KRUIS"   => "KRUIS - Kruispost",
                                "L"       => "L - Lichting van stukken",
                                "V"       =>  "V &nbsp;- Verkoop van stukken",
                                "-"       => "-----------------------------",



                                              "RENOB" => "RENOB - Coupon / Meeverk. Rente",
																							"RENTE" => "RENTE - Rente",
																							"STUKMUT" => "STUKMUT - stukken mutatie",

                                              "NVT" =>"N.v.t.",
                                              "KOBU" => "KOBU - Kosten Buitenland",
                                              "KOST" => "KOST - Kosten",


                                              ),
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
