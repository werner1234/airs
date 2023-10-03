<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/01/28 15:04:48 $
    File Versie         : $Revision: 1.7 $
 		
    $Log: lynxTransactieCodes.php,v $
    Revision 1.7  2019/01/28 15:04:48  cvs
    call 7206

    Revision 1.6  2018/06/20 12:44:02  cvs
    call 6999

    Revision 1.5  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.4  2018/02/02 12:25:56  cvs
    call 6556

    Revision 1.3  2017/11/27 10:08:54  cvs
    call 6224

    Revision 1.2  2017/10/20 10:17:40  cvs
    call 6224

    Revision 1.1  2017/09/29 12:14:51  cvs
    call 6224

    Revision 1.1  2016/04/04 08:22:13  cvs
    call 4712

    Revision 1.1  2015/12/01 08:56:03  cvs
    update 2540, call 4352



 		
 	
*/

class lynxTransactieCodes extends Table
{
  /*
  * Object vars
  */
  var $tableName = "lynxTransactieCodes";
  var $data = array();
  
  /*
  * Constructor
  */
  function lynxTransactieCodes()
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
		($this->get("LYNXcode")=="")?$this->setError("LYNXcode",vt("Mag niet leeg zijn!")):true;
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
    $this->data['name']  = "BIL transactiecodes";
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

		$this->addField('LYNXcode',
													array("description"=>"LYNXcode",
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
                                              "A"         =>  "A &nbsp;- Aankoop van stukken",
                                              "ASS"       => "ASS - assignment opties",
                                              "BEH"       => "BEH - Beheer",
                                              "COUP"      => "COUP - Coupon / Meeverk/gek. Rente",
                                              "DIV"       => "DIV - Dividend",
                                              "DIVBE"     => "DIVBE - Div. Belasting",
                                              "EXE"       => "EXE - exercise opties",
                                              "FTT"       => "FTT - Fin. tax",
                                              "KNBA"      => "KNBA - Bankkosten",
                                              "MUT"       => "MUT - Geld mutaties",
                                              "RENTE"     => "RENTE - Rente",
                                              "STUKMUT"   => "STUKMUT - Dep/lichting van stukken",
                                              "STUKMUT0"  => "STUKMUT0 - Dep/lichting van stukken",
                                              "V"         =>  "V &nbsp;- Verkoop van stukken",

                                              "==="  => "=============================",

                                						  "BEW" => "BEW - Bewaarloon",
                                              "KOBU" => "KOBU - Kosten Buitenland",
                                              "KOST" => "KOST - Kosten",
                                              "KRUIS" => "KRUIS - Kruispost",

                                              "ONTTR" => "ONTTR - Ontrekking geld",

                                              "STORT" => "STORT - Storting Geld",

                                              "VKSTO" => "VKSTO - Verkoop stockjes",
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
    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);

    $tst->changeField($this->tableName,"omschrijving",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"LYNXcode",array("Type"=>"varchar(10)","Null"=>false));
    $tst->changeField($this->tableName,"doActie",array("Type"=>"varchar(10)","Null"=>false));

  }
}





?>