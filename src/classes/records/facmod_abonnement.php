<?php
/*
    AE-ICT CODEX source module versie 1.2, 26 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/12/06 14:18:27 $
    File Versie         : $Revision: 1.3 $

    $Log: facmod_abonnement.php,v $
    Revision 1.3  2019/12/06 14:18:27  cvs
    call 7675

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

class facmod_abonnement extends Table
{
  /*
  * Object vars
  */

  var $data = array();
  var $tableName = "facmod_abonnement";
  /*
  * Constructor
  */
  function facmod_abonnement()
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
    $tst->changeField($this->tableName,"artnr",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"txt",array("Type"=>" text","Null"=>false));
    $tst->changeField($this->tableName,"btw",array("Type"=>" char(2)","Null"=>false));
    $tst->changeField($this->tableName,"totaal_excl",array("Type"=>" decimal(8,2)","Null"=>false));
    $tst->changeField($this->tableName,"stuksprijs",array("Type"=>" decimal(8,2)","Null"=>false));
    $tst->changeField($this->tableName,"volgnr",array("Type"=>" int","Null"=>false));
    $tst->changeField($this->tableName,"vorigeVerwerkdatum",array("Type"=>" datetime","Null"=>false));
    $tst->changeField($this->tableName,"door",array("Type"=>" varchar(15)","Null"=>false));
    $tst->changeField($this->tableName,"actief",array("Type"=>" varchar(1)","Null"=>false));
    $tst->changeField($this->tableName,"periode",array("Type"=>" varchar(1)","Null"=>false));
    $tst->changeField($this->tableName,"opmerking",array("Type"=>" text","Null"=>false));
    $tst->changeField($this->tableName,"factor",array("Type"=>" int","Null"=>false));
    $tst->changeField($this->tableName,"rubriek",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"achteraf",array("Type"=>" tinyint","Null"=>false));
  }
	/*
  * Table definition
  */
  function defineData()
  {
    global $__facmod;
    $this->data['name']  = "abonnement";
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

    $this->addField('factor',
                    array("description"=>"factor",
                          "default_value"=>"1",
                          "db_size"=>"11",
                          "db_type"=>"int",
                          "form_type"=>"text",
                          "form_size"=>"2",
                          "form_format"=>"%1d",
                          "form_visible"=>false,
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
													"form_type"=>"selectKeyed",
													"form_options"=>$__facmod["eenheden"],
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('artnr',
                          array("description"=>"artikel code",
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
													"default_value"=>"H",
													"db_size"=>"2",
													"db_type"=>"char",
													"form_type"=>"selectKeyed",
													"form_size"=>"2",
													"form_visible"=>true,
													"form_options"=>$__facmod["btw"],
													"form_select_option_notempty"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('stuksprijs',
													array("description"=>"stuksprijs\nmaand",
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

		$this->addField('volgnr',
													array("description"=>"volgnr",
													"default_value"=>"10",
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

		$this->addField('vorigeVerwerkdatum',
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

		$this->addField('periode',
													array("description"=>"periode",
													"default_value"=>"K",
													"db_size"=>"1",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_options" => $__facmod["periodes"],
                          "form_select_option_notempty"=>true,
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('actief',
													array("description"=>"actief",
													"default_value"=>"1",
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

		$this->addField('opmerking',
													array("description"=>"opmerking",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"70",
													"form_rows" => "4",
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

    $this->addField('achteraf',
                    array("description"=>"achteraf",
                          "default_value"=>"0",
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



  }
}
?>