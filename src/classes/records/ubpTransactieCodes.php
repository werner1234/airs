<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/03/07 16:48:06 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: ubpTransactieCodes.php,v $
    Revision 1.4  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.3  2017/09/20 06:11:01  cvs
    megaupdate 2722

    Revision 1.2  2017/03/09 07:53:16  cvs
    call 5639

    Revision 1.1  2016/12/05 12:45:34  cvs
    call 5294

    Revision 1.2  2016/07/01 14:36:09  cvs
    call 5005

    Revision 1.1  2016/04/04 08:22:13  cvs
    call 4712

    Revision 1.1  2015/12/01 08:56:03  cvs
    update 2540, call 4352



 		
 	
*/

class UbpTransactieCodes extends Table
{
  /*
  * Object vars
  */





  var $data = array();
  
  /*
  * Constructor
  */
  function UbpTransactieCodes()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
    //$this->initModule();
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
		($this->get("bankCode")=="")?$this->setError("ubpCode",vt("Mag niet leeg zijn!")):true;
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
    $sqlman->changeField($this->data['table'],"bankCode",array("Type"=>" varchar(10)","Null"=>false));
    $sqlman->changeField($this->data['table'],"omschrijving",array("Type"=>" varchar(50)","Null"=>false));
    $sqlman->changeField($this->data['table'],"doActie",array("Type"=>" varchar(10)","Null"=>false));

  }


	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "UBP transactiecodes";
    $this->data['table']  = "ubpTransactieCodes";
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
													array("description"=>"UBPcode",
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
																							"CONV" => "CONV - Verwisseling van stukken",
																							"DIV" => "DIV - Dividend",
                                              "FEES" => "FEES - div. kosten",
                                              "FXKRUIS"=> "FX naar kruispost",
                                              "GELDMUT" => "GELDMUT - Geld mutaties",
                                              "ILO" => "ILO - Rente leningen",
                                              "LOAN" => "LOAN - Aangaan/afwikkelen leningen",
                                              "RENOB" => "RENOB - Coupon / Meeverk. Rente",
																							"RENTE" => "RENTE - Rente",
																							"STUKMUT" => "STUKMUT - stukken mutatie",
																							"STUKMUT0" => "STUKMUT0 - stukken mutatie koers 0",
																							"V" =>  "V &nbsp;- Verkoop van stukken",
                                              "NVT" =>"N.v.t.",
																							"-" => "-----------------------------",
                                              "FX" => "FX - Forex",
                                              "KOBU" => "KOBU - Kosten Buitenland",
                                              "KOST" => "KOST - Kosten",
                                              "KRUIS" => "KRUIS - Kruispost",





                                              "VKSTO" => "VKSTO - Verkoop stockjes"
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
?>