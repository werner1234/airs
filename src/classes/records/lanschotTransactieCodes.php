<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/06/15 08:04:42 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: lanschotTransactieCodes.php,v $
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

class LanschotTransactieCodes extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function LanschotTransactieCodes()
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
		($this->get("FVLCode")=="")?$this->setError("FVLCode",vt("Mag niet leeg zijn!")):true;
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
    $this->data['name']  = "lanschot transactiecodes";
    $this->data['table']  = "lanschotTransactieCodes";
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

		$this->addField('FVLCode',
													array("description"=>"FVLCode",
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
                                              "A"       =>  "A &nbsp;- Aankoop van stukken",
                                              "DO"      => "DO - Stock dividend",
                                              "DV"      => "DV - Contant dividend",
                                              "E"       =>  "E &nbsp;- Emissie van stukken of claims",
                                              "KD"      => "KD - Kosten depot",
                                              "KO"      => "KO - Kosten algemeen",
                                              "L"       =>  "L &nbsp;- Lossing van obligaties",
                                              "OA"      => "OA - Aankoop openen bij opties en futures",
                                              "OP"      => "OP - Opname van geld of stukken",
                                              "OV"      => "OV - Verkoop openen bij opties en futures",
                                              "R"       =>  "R &nbsp;- Rente of couponrente",
                                              "SA"      => "SA - Aankoop sluiten bij opties en futures",
                                              "ST"      => "ST - Storting van geld of stukken",
                                              "SV"      => "SV - Verkoop sluiten bij opties en futures",
                                              "TL"      => "TL - Expiratie Time Long bij opties en futures",
                                              "TS"      => "TS - Expiratie Time Short bij opties en futures",
                                              "V"       =>  "V &nbsp;- Verkoop van stukken",
                                              "VM"      => "VM - Variation margin",
                                              "NVT"     =>"N.v.t.",
                                              ""        => "------------------------------"),
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));



  }
}
