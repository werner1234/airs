<?php
/*
    AE-ICT CODEX source module versie 1.6, 1 november 2013
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/03/30 10:51:35 $
    File Versie         : $Revision: 1.12 $

    $Log: advent_FondsMapping.php,v $
    Revision 1.12  2020/03/30 10:51:35  cvs
    by code toegevoegd

    Revision 1.11  2018/11/30 07:35:05  cvs
    toevoegen pp

    Revision 1.10  2018/10/24 13:26:24  cvs
    extra code pw toegevoegd

    Revision 1.9  2018/03/29 06:32:53  cvs
    st toegevoegd

    Revision 1.8  2017/12/12 14:42:40  cvs
    if - Index future toegevoegd

    Revision 1.7  2017/11/10 15:29:54  cvs
    fondsmapping sg toegevoegd

    Revision 1.6  2017/04/19 10:59:55  cvs
    no message

    Revision 1.5  2014/11/15 18:53:41  rvv
    *** empty log message ***

    Revision 1.4  2014/07/07 09:11:51  cvs
    *** empty log message ***

    Revision 1.3  2014/02/05 15:29:28  cvs
    *** empty log message ***

    Revision 1.2  2013/12/11 10:09:51  cvs
    *** empty log message ***

    Revision 1.1  2013/11/15 10:20:21  cvs
    aanpassing tbv Adventexport



*/

class Advent_FondsMapping extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Advent_FondsMapping()
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
		($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;

    ///
    $DB = new DB();

		$query  = "SELECT id FROM advent_FondsMapping WHERE Fonds = '".$this->get("Fonds")."' ";
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") <> $data['id'])
			$this->setError("Fonds",vtb("%s bestaat al", array($this->get("Fonds"))));
    ///
		($this->get("adventCode")=="")?$this->setError("adventCode",vt("Mag niet leeg zijn!")):true;

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

    $secTypeArray = array(
"aa" => "aa - Asset Allocation",
"ai" => "ai - Individuele aandelen",
"aw" => "aw - CLIENT AND NONE",
"bb" => "bb - Bedrijfsobligaties en overige",
"bc" => "bc - Individuele converteerbare obligaties",
"be" => "be - Staatsobligaties",
"bf" => "bf - Individuele obligaties met floating rate",
"bg" => "bg - Notes met (gedeeltelijke) kapitaalsgarantie",
"bh" => "bh - Notes/Cert. zonder kapitaalsgarantie",
"bi" => "bi - Bedrijfsobligaties",
"bj" => "bj - Staats (gegarandeerde) obligaties",
"bk" => "bk - Converteerbare bedrijfsobl. en overige (fondsen)",
"bm" => "bm - Mixfondsen, Staats- en bedrijfsobligaties",
"br" => "br - BROKERAGE ACCOUNTS",
"bw" => "bw - Niet meer gebruiken..",
"bx" => "bx - Hoog renderende obligaties(indiv)",
"by" => "by - Hoog renderende individuele obligaties (100)",
"bz" => "bz - Hoog renderende obligaties(fonds)",
"ca" => "ca - Liquide middelen",
"cb" => "cb - Deposito",
"cd" => "cd - Depositos",
"cg" => "cg - Bankgarantie long",
"cl" => "cl - Calls",
"cm" => "cm - Calls / 5",
"cs" => "cs - cs - do not remove",
"ct" => "ct - Treasury Bills",
"dv" => "dv - Direct Vastgoed",
"ea" => "ea - Aandelen Azië ex Japan",
"ee" => "ee - Aandelen Europa",
"ej" => "ej - Aandelen Japan",
"em" => "em - Aandelen Emerging Markets",
"en" => "en - Aandelen Noord Amerika",
"ep" => "ep - AFTER FEE PERFORMANCE EXPENSE ACCOUNTS",
"es" => "es - Specialties",
"ev" => "ev - Vastgoed aandelen",
"ew" => "ew - Aandelen Wereldwijd",
"ex" => "ex - EXPENSE ACCOUNTS",
"ez" => "ez - Geldmarktfondsen",
"fc" => "fc - Valuta termijn affaire",
"hc" => "hc - Alternatieve Beleggingen Fixed Income",
"hh" => "hh - Alternatieve Beleggingen Multi Strategy - High Volatility",
"hl" => "hl - Alternatieve Beleggingen Multi Strategy",
"hm" => "hm - Alternatieve Beleggingen Multi Strategy - Medium Volatility",
"hs" => "hs - Alternatieve Beleggingen Single Strategy",
"ht" => "ht - Alternatieve Beleggingen Single Strategy - High Volatility",
"hv" => "hv - Hypotheek Vastgoed",
"hx" => "hx - Fairfield - Sigma A",
"if" => "if - Index future",
"ku" => "ku - Kunst",
"lb" => "lb - Verplichtingen op lange termijn (latente belasting ontvangst)",
"le" => "le - Lening/Vordering (UG)",
"lv" => "lv - Verplichtingen op korte termijn (< 2 jaar), te ontvangen",
"ov" => "ov - Overige Beleggingen",
"pa" => "pa - Participaties (register aandelen)",
"pe" => "pe - Private Equity",
"pi" => "pi - PERFORMANCE INFORMATION",
"po" => "po - Private Equity Vastrentend",
"pr" => "pr - Preferente aandelen",
"pp" => "pp - put opties / 500",
"pt" => "pt - Puts",
"pv" => "pv - Puts / 5",
"pw" => "pw - Calls / 5",
"px" => "px - Private Equity Commitments",
"py" => "py - Private Equity Commitment",
"rt" => "rt - Rights",
"sb" => "sb - Synthetische obligaties",
"se" => "se - Aandelen Europa - Inschrijvingen",
"sg" => "sg - Structured Products met kapitaalgarantie",
"sm" => "sm - Mortgage Backed Securities",
"sp" => "sp - Structured Products",
"st" => "st - Structured products ZW",
"su" => "su - Structured products ZW (Factor 0,01)",
"ua" => "ua - Commodities",
"wt" => "wt - Warrants"
);
    $this->data['name']  = "Advent fondsmapping";
    $this->data['table']  = "advent_FondsMapping";
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
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"mutatie",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>false,
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
													"form_visible"=>false,
													"list_visible"=>true,
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
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
                          "keyIn"=>"Fondsen"));

		$this->addField('Omschrijving',
													array("description"=>"Omschrijving",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('adventCode',
													array("description"=>"AD Code",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('adventSecCode',
													array("description"=>"AD SecCode",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "form_options"=> $secTypeArray,
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));



  }
}
?>