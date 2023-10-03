<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/09/18 10:32:00 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: degiroTransactieCodes.php,v $
    Revision 1.3  2019/09/18 10:32:00  cvs
    call 8103

    Revision 1.2  2018/06/15 07:44:55  cvs
    call 6063

    Revision 1.1  2015/06/03 13:23:03  cvs
    *** empty log message ***

    Revision 1.2  2015/06/02 06:52:13  cvs
    *** empty log message ***

    Revision 1.1  2015/04/13 13:22:16  cvs
    *** empty log message ***

    Revision 1.1  2014/11/05 12:53:50  cvs
    dbs 2751

 		
 	
*/

class DegiroTransactieCodes extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function DegiroTransactieCodes()
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
		($this->get("giroCode")=="")?$this->setError("giroCode",vt("Mag niet leeg zijn!")):true;
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
    $this->data['name']  = "DeGiro transactiecodes";
    $this->data['table']  = "degiroTransactieCodes";
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

		$this->addField('giroCode',
													array("description"=>"giroCode",
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
														"A"        =>  "A &nbsp;- Aankoop van stukken",
														"BEH"      => "BEH - Beheer",
														"BEW"      => "BEW - Bewaarloon",
														"DIV"      => "DIV - Dividend",
														"DIVBE"    => "DIVBE - Div. Belasting",
														"FX"       => "FX - Forex",
														"KNBA"     => "KNBA - Bankkosten",
														"KOBU"     => "KOBU - Kosten Buitenland",
														"KOST"     => "KOST - Kosten",
														"KRUIS"    => "KRUIS - Kruispost",
														"MUT"      => "MUT - Overige geldmutaties",
														"ONTTR"    => "ONTTR - Ontrekking geld",
														"RENTE_KV" => "RENTE_KV - Meegek./verk. Rente",
														"RENOB"    => "RENOB - Coupon / Meeverk. Rente",
														"RENTE"    => "RENTE - Rente",
														"STORT"    => "STORT - Storting Geld",
														"V"        =>  "V &nbsp;- Verkoop van stukken",
														"VKSTO"    => "VKSTO - Verkoop stockjes",
														"NVT"      =>"N.v.t.",
                            ""         => "------------------------------"),
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