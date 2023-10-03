<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 29 april 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/06/26 06:26:37 $
    File Versie         : $Revision: 1.13 $
 		
    $Log: importAfwijkingen.php,v $
    Revision 1.13  2020/06/26 06:26:37  cvs
    call 8713

    Revision 1.12  2020/03/30 14:46:03  cvs
    call 8355

    Revision 1.11  2020/03/20 09:08:00  cvs
    call 8300

    Revision 1.10  2019/10/30 13:13:09  cvs
    call 7605

    Revision 1.9  2019/10/28 12:34:34  cvs
    call 7867

    Revision 1.8  2019/10/25 15:11:26  cvs
    call 8196

    Revision 1.7  2019/06/05 12:53:36  cvs
    call 7844

    Revision 1.6  2018/10/26 06:50:42  cvs
    call 7173

    Revision 1.5  2018/07/20 07:29:35  cvs
    call 7054

    Revision 1.4  2018/02/07 13:07:23  cvs
    call 6578

    Revision 1.3  2018/01/03 15:46:27  cvs
    call 6472

    Revision 1.2  2017/04/05 15:07:40  cvs
    call 5570

    Revision 1.1  2017/03/24 09:34:32  cvs
    call 5731

 		
 	
*/

class ImportAfwijkingen extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  var $veldArray;
  var $tableName = "importAfwijkingen";
  /*
  * Constructor
  */
  function ImportAfwijkingen()
  {
		$this->veldArray = array(
			"Grootboekrekening" => "Grootboekrekening",
			"Omschrijving"      => "Omschrijving"
		);
    $this->defineData();
    $this->setDefaults();
    $this->initModule();
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
		($this->get("depotbank")=="")?$this->setError("depotbank",vt("Mag niet leeg zijn!")):true;
		($this->get("vermogensBeheerder")=="")?$this->setError("vermogensBeheerder",vt("Mag niet leeg zijn!")):true;

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
    $tst->changeField($this->tableName,"actief",array("Type"=>"tinyint","Null"=>false));
    $tst->changeField($this->tableName,"depotbank",array("Type"=>"varchar(10)","Null"=>false));
    $tst->changeField($this->tableName,"functie",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"subInFunctie",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"testConditie",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"testVeld",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"testSoort",array("Type"=>"varchar(20)","Null"=>false));
    $tst->changeField($this->tableName,"targetVeld",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"targetWaarde",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"prio",array("Type"=>"int","Null"=>false));
    $tst->changeField($this->tableName,"vermogensBeheerder",array("Type"=>"varchar(10)","Null"=>false));
    $tst->changeField($this->tableName,"memo",array("Type"=>"text","Null"=>false));
  }
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
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

		$this->addField('depotbank',
													array("description"=>"depotbank",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
                          "form_options"=>array(
                            "AAB"   => "AAB",
                            "AABBE" => "AABBE",
                            "BGL"   => "BGL",
                            "BIN"   => "BIN",
                            "CAW"   => "CAW",
                            "CS"    => "CS",
                            "FVL"   => "FVL",
                            "GIRO"  => "GIRO",
                            "HSBC"  => "HSBC",
                            "ING"   => "ING",
                            "JB"    => "JB",
                            "JBLUX" => "JBLUX",
                            "KAS"   => "KAS",
                            "KBC"   => "KBC",
                            "LOM"   => "LOM",
                            "PIC"   => "PIC",
                            "RABO"  => "RABO",
                            "TGB"   => "TGB",
                            "VLCH"  => "VLCH",
                            "VP"    => "VP",
                            "UBP"   => "UBP",
                            "UBS"   => "UBS",
                            "UBSL"  => "UBSL",
                          ),
                          "form_type"=>"selectKeyed",
                          'form_select_option_notempty'=>true,
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"70",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>true));

		$this->addField('vermogensBeheerder',
													array("description"=>"vermogensBeheerder",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder, concat(Vermogensbeheerder,' - ',naam) FROM Vermogensbeheerders ORDER BY Vermogensbeheerder",
													"form_type"=>"selectKeyed",
													'form_select_option_notempty'=>true,
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"50",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>true));

		$this->addField('actief',
													array("description"=>"actief",
													"default_value"=>"1",
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

		$this->addField('functie',
													array("description"=>"functie",
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

		$this->addField('subInFunctie',
													array("description"=>"trigger",
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

		$this->addField('testVeld',
													array("description"=>"TEST veld",
													"default_value"=>"grootboek",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_options"=>$this->veldArray,
													"form_select_option_notempty"=>true,
													"form_type"=>"selectKeyed",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"110",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('testConditie',
													array("description"=>"TEST conditie",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('testSoort',
                    array("description"=>"TEST soort",
                          "default_value"=>"",
                          "db_size"=>"20",
                          "db_type"=>"varchar",
                          "form_select_option_notempty"=>true,
                          "form_type"=>"selectKeyed",
                          "form_size"=>"50",
                          "form_options"=>array(
                            "begint met" => "begint met",
                            "bevat"      => "bevat"
                          ),
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

		$this->addField('targetVeld',
													array("description"=>"TARGET Veld",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_options"=>$this->veldArray,
													"form_select_option_notempty"=>true,
													"form_type"=>"selectKeyed",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"110",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('targetWaarde',
													array("description"=>"TARGET Waarde",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('prio',
													array("description"=>"prio",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"50",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('memo',
													array("description"=>"memo",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"80",
													"form_rows"=>"5",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"50",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>