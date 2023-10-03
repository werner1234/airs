<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/05/20 09:32:16 $
 		File Versie					: $Revision: 1.24 $

 		$Log: TijdelijkeRekeningmutaties.php,v $
 		Revision 1.24  2019/05/20 09:32:16  cvs
 		call 7816
 		
 		Revision 1.23  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.22  2017/09/19 08:24:39  cvs
 		megaupdate
 		
 		Revision 1.21  2014/12/18 07:23:35  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2014/02/07 09:00:16  cvs
 		*** empty log message ***
 		
 		Revision 1.19  2014/02/05 15:29:28  cvs
 		*** empty log message ***

 		Revision 1.18  2013/11/15 10:20:21  cvs
 		aanpassing tbv Adventexport

 		Revision 1.17  2012/03/09 09:08:56  cvs
 		*** empty log message ***

 		Revision 1.16  2008/07/02 07:21:31  rvv
 		*** empty log message ***

 		Revision 1.15  2006/03/21 15:09:21  cvs
 		*** empty log message ***

 		Revision 1.14  2005/12/16 14:43:09  jwellner
 		classes aangepast

 		Revision 1.13  2005/11/18 15:15:01  jwellner
 		no message

 		Revision 1.12  2005/11/17 07:30:04  cvs
 		*** empty log message ***
*/

class TijdelijkeRekeningmutaties extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function TijdelijkeRekeningmutaties()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	function checkAccess($type)
	{
		return checkAccess($type);
	}

	/*
	 * Veldvalidatie
	 */
	function validate()
	{
	  $DB = new DB();
    if ($this->get("Grootboekrekening") == "FONDS")
	  {
	    $DB->SQL("SELECT Fonds FROM Fondsen WHERE Fonds = '".$this->get("Fonds")."' ");
		  $DB->Query();
		  if($DB->records() <= 0)
			  $this->setError("Fonds", vtb("%s is een onbekend fonds", array($this->get("Fonds"))));
		  $this->set("Transactietype" ,strtoupper($this->get("Transactietype")));
		  $DB->SQL("SELECT Transactietype FROM Transactietypes WHERE Transactietype = '".$this->get("Transactietype")."' ");
		  $DB->Query();
		  if($DB->records() <= 0)
			  $this->setError("Transactietype",vtb("%s is een onbekend transactietype", array($this->get("Transactietype"))));
	  }
		// check of velden in koppeltabellen bestaan

		$DB->SQL("SELECT Rekening FROM Rekeningen WHERE Rekening = '".$this->get("Rekening")."' AND consolidatie=0 ");
		$DB->Query();
		if($DB->records() <= 0)
			$this->setError("Rekening",vtb("%s is een onbekend rekeningnummer", array($this->get("Rekening"))));

		$DB->SQL("SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekening = '".$this->get("Grootboekrekening")."' ");
		$DB->Query();
		if($DB->records() <= 0)
			$this->setError("Grootboekrekening",vtb("%s is een onbekende grootboekrekening", array($this->get("Grootboekrekening"))));

		$DB->SQL("SELECT Valuta FROM Valutas WHERE Valuta = '".$this->get("Valuta")."' ");
		$DB->Query();
		if($DB->records() <= 0)
			$this->setError("Valuta",vtb("%s is een onbekende valuta", array($this->get("Valuta"))));

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "TijdelijkeRekeningmutaties";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Rekening',
													array("description"=>"Rekening",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Omschrijving',
													array("description"=>"Omschrijving",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_function"=>"Rclip({value},25)",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
    $this->addField('OmschrijvingOrg',
													array("description"=>"OmschrijvingOrg",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"70",
                          "form_rows" => "3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_function"=>"Rclip({value},25)",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Boekdatum',
													array("description"=>"Boekdatum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
                          "form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
//"db_mysqlDatetimeValidate"=>true,



		$this->addField('Grootboekrekening',
													array("description"=>"Grootboekrekening",
													"default_value"=>"",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Valuta',
													array("description"=>"Valuta",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"char",
													"form_type"=>"text",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Valutakoers',
													array("description"=>"Valutakoers",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_format"=>"%01.8f",
													"list_format"=>"%01.5f",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.5f",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Fonds',
													array("description"=>"Fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"16",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Aantal',
													array("description"=>"Aantal",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Fondskoers',
													array("description"=>"Fondskoers",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_format"=>"%01.8f",
													"list_format"=>"%01.5f",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.5f",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Debet',
													array("description"=>"Debet",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Credit',
													array("description"=>"Credit",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Bedrag',
													array("description"=>"Bedrag",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Transactietype',
													array("description"=>"Transactietype",
													"default_value"=>"",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
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
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
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
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('aktie',
													array("description"=>"aktie",
													"default_value"=>"",
													"db_size"=>"6",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"6",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bestand',
													array("description"=>"bestand",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('regelnr',
													array("description"=>"regelnr",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Verwerkt',
													array("description"=>"Verwerkt",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Memoriaalboeking',
													array("description"=>"Memoriaalboeking",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
	$this->addField('bankTransactieId',
													array("description"=>"bankTransactieId",
													"default_value"=>"",
													"db_size"=>"40",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"40",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
    $this->addField('bankTransactieCode',
                    array("description"=>"bankTransactieCode",
                          "default_value"=>"",
                          "db_size"=>"15",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"15",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
	$this->addField('settlementDatum',
													array("description"=>"settlementDatum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));


    $this->addField('bankOmschrijving',
      array("description"=>"bankOmschrijving",
        "default_value"=>"",
        "db_size"=>"255",
        "db_type"=>"text",
        "form_type"=>"textarea",
        "form_size"=>"70",
        "form_rows" => "3",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_function"=>"Rclip({value},50)",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));



  }
}
