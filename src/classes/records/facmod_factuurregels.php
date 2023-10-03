<?php
/*
    AE-ICT CODEX source module versie 1.2, 26 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/11/13 15:14:20 $
    File Versie         : $Revision: 1.2 $

    $Log: facmod_factuurregels.php,v $
    Revision 1.2  2019/11/13 15:14:20  cvs
    call 7675

    Revision 1.1  2019/07/22 09:11:00  cvs
    call 7675

    Revision 1.4  2008/02/15 12:57:58  cvs
    *** empty log message ***

    Revision 1.3  2006/06/01 14:12:11  cvs
    *** empty log message ***

    Revision 1.2  2005/11/30 08:48:54  cvs
    *** empty log message ***

    Revision 1.1  2005/11/28 07:33:43  cvs
    *** empty log message ***



*/

class facmod_factuurregels extends Table
{
  /*
  * Object vars
  */

  var $data = array();
  var $tableName  = "facmod_factuurregels";

  /*
  * Constructor
  */
  function facmod_factuurregels()
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
		($this->get("txt")=="")?$this->setError("txt",vt("Mag niet leeg zijn!")):true;

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

    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"rel_id",array("Type"=>" int","Null"=>false));
    $tst->changeField($this->tableName,"aantal",array("Type"=>" decimal(8,2)","Null"=>false));
    $tst->changeField($this->tableName,"eenheid",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField($this->tableName,"artnr",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField($this->tableName,"txt",array("Type"=>" text","Null"=>false));
    $tst->changeField($this->tableName,"btw",array("Type"=>" char(2)","Null"=>false));
    $tst->changeField($this->tableName,"btw_per",array("Type"=>" decimal(4,2)","Null"=>false));
    $tst->changeField($this->tableName,"totaal_excl",array("Type"=>" decimal(9,2)","Null"=>false));
    $tst->changeField($this->tableName,"totaal_incl",array("Type"=>" decimal(9,2)","Null"=>false));
    $tst->changeField($this->tableName,"stuksprijs",array("Type"=>" decimal(8,2)","Null"=>false));
    $tst->changeField($this->tableName,"volgnr",array("Type"=>" int","Null"=>false));
    $tst->changeField($this->tableName,"vorigeVerwerkdatum",array("Type"=>" datetime","Null"=>false));
    $tst->changeField($this->tableName,"door",array("Type"=>" varchar(15)","Null"=>false));
    $tst->changeField($this->tableName,"actief",array("Type"=>" varchar(1)","Null"=>false));
    $tst->changeField($this->tableName,"periode",array("Type"=>" varchar(1)","Null"=>false));
    $tst->changeField($this->tableName,"opmerking",array("Type"=>" text","Null"=>false));
    $tst->changeField($this->tableName,"factor",array("Type"=>" int","Null"=>false));
    $tst->changeField($this->tableName,"wachtstand",array("Type"=>" tinyint","Null"=>false));
    $tst->changeField($this->tableName,"facnr",array("Type"=>" int","Null"=>false));
    $tst->changeField($this->tableName,"datum",array("Type"=>" datetime","Null"=>false));
    $tst->changeField($this->tableName,"afdeling",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField($this->tableName,"auto",array("Type"=>" varchar(1)","Null"=>false));
    $tst->changeField($this->tableName,"module",array("Type"=>" varchar(20)","Null"=>false));
    $tst->changeField($this->tableName,"module_id",array("Type"=>" varchar(20)","Null"=>false));
    $tst->changeField($this->tableName,"inkoopstuksprijs",array("Type"=>" decimal(9,2)","Null"=>false));
    $tst->changeField($this->tableName,"inkooptotaal_excl",array("Type"=>" decimal(9,2)","Null"=>false));
    $tst->changeField($this->tableName,"inkooptotaal_incl",array("Type"=>" decimal(9,2)","Null"=>false));
    $tst->changeField($this->tableName,"rubriek",array("Type"=>" varchar(50)","Null"=>false));
  }


	/*
  * Table definition
  */
  function defineData()
  {
    global $__facmod;
    $this->data['name']  = "factuurregel";
    $this->data['table']  = $this->tableName;

    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('rel_id',
													array("description"=>"rel_id",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('factor',
                    array("description"=>"factor",
                          "default_value"=>"1",
                          "db_size"=>"11",
                          "db_type"=>"int",
                          "form_type"=>"text",
                          "form_size"=>"5",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    $this->addField('rubriek',
                    array("description"=>"rubriek",
                          "default_value"=>"",
                          "db_size"=>"50",
                          "db_type"=>"varchar",
                          "form_type"=>"select",
                          "form_options" => $__facmod["rubriek"],

                          "form_size"=>"10",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

		$this->addField('aantal',
													array("description"=>"aantal",
													"default_value"=>"",
													"db_size"=>"8,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"8",
                                "form_format"=>"%01.2f",
                                "list_format"=>"%01.2f",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('eenheid',
													array("description"=>"eenheid",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_options"=>array(),
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('artnr',
													array("description"=>"artnr",
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

		$this->addField('txt',
													array("description"=>"factuurtekst",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"tinytext",
													"form_type"=>"textarea",
													"form_size"=>"70",
													"form_rows"=>"7",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('btw',
													array("description"=>"btw",
													"default_value"=>"",
													"db_size"=>"2",
													"db_type"=>"char",
													"form_type"=>"selectKeyed",
													"form_size"=>"2",
													"form_visible"=>true,
													"form_options"=>array(),
													"form_select_option_notempty"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('btw_per',
													array("description"=>"btw_per",
													"default_value"=>"",
													"db_size"=>"4,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"4,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('stuksprijs',
													array("description"=>"stuksprijs",
													"default_value"=>"",
													"db_size"=>"9,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"9",
                                "form_format"=>"%01.2f",
                                "list_format"=>"%01.2f",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('totaal_excl',
													array("description"=>"totaal_excl",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10",
                                "form_format"=>"%01.2f",
                                "list_format"=>"%01.2f",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('totaal_incl',
													array("description"=>"totaal_incl",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10",
                                "form_format"=>"%01.2f",
                                "list_format"=>"%01.2f",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
    $this->addField('inkoopstuksprijs',
													array("description"=>"inkoop stuksprijs",
													"default_value"=>"",
													"db_size"=>"9",
                                "form_format"=>"%01.2f",
                                "list_format"=>"%01.2f",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"9,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('inkooptotaal_excl',
													array("description"=>"inkoop totaal_excl",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10",
                                "form_format"=>"%01.2f",
                                "list_format"=>"%01.2f",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('inkooptotaal_incl',
													array("description"=>"inkoop totaal_incl",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10",
                                "form_format"=>"%01.2f",
                                "list_format"=>"%01.2f",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('wachtstand',
													array("description"=>"wachtstand",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('facnr',
													array("description"=>"facnr",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('volgnr',
													array("description"=>"volgnr",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('datum',
													array("description"=>"datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('door',
													array("description"=>"door",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('afdeling',
													array("description"=>"afdeling",
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

		$this->addField('auto',
													array("description"=>"auto",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"char",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('module',
													array("description"=>"module",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('module_id',
													array("description"=>"module_id",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
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
													"form_type"=>"datum",
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

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_size"=>"0",
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



  }
}
?>